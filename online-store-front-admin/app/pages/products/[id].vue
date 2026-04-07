<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductsStore } from '~/stores/products'
import {
  ArrowRight,
  Package,
  Activity,
  Tags,
  Hash,
  Box,
  Image as ImageIcon,
  CheckCircle,
  XCircle,
  Percent,
  Banknote
} from 'lucide-vue-next'
import type { Product } from '~/types/product'

definePageMeta({
  title: 'تفاصيل المنتج',
  description: 'عرض بيانات المنتج بالتفصيل.',
})

const route = useRoute()
const router = useRouter()
const productsStore = useProductsStore()

const product = ref<Product | null>(null)
const loading = ref(true)
const activeImageIndex = ref(0)
const showAllCategories = ref(false)
const initialCategoriesCount = 3

const productId = route.params.id as string

const formatPrice = (value: number) => {
  return new Intl.NumberFormat('ar-LY', { style: 'currency', currency: 'LYD' }).format(value)
}

onMounted(async () => {
  loading.value = true
  const fetched = await productsStore.fetchProduct(productId)
  if (!fetched) {
    // Optionally redirect back if not found
    router.replace('/products')
  } else {
    product.value = fetched
  }
  loading.value = false
})

const mainImage = computed(() => {
  if (!product.value?.images?.length) return null
  return product.value.images[activeImageIndex.value]?.url
})

const visibleCategories = computed(() => {
  const categories = product.value?.categories ?? []
  return showAllCategories.value ? categories : categories.slice(0, initialCategoriesCount)
})

const hiddenCategoriesCount = computed(() => {
  const count = (product.value?.categories?.length ?? 0) - initialCategoriesCount
  return count > 0 ? count : 0
})

const hasMoreCategories = computed(() => hiddenCategoriesCount.value > 0)
</script>

<template>
  <div class="product-detail-container">
    <div class="header-actions">
      <button class="back-btn" @click="router.back()">
        <ArrowRight :size="20" class="me-2" />
        الرجوع للمنتجات
      </button>
      <div v-if="product" class="actions-group">
        <NuxtLink :to="`/products/manage?id=${product.id}`" class="button-primary">
          تعديل المنتج
        </NuxtLink>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>جاري تحميل تفاصيل المنتج...</p>
    </div>

    <!-- Product Details -->
    <div v-else-if="product" class="product-layout">
      <!-- 📸 Gallery Section -->
      <section class="gallery-section surface-card">
        <div class="main-image-wrapper">
          <img v-if="mainImage" :src="mainImage" :alt="product.name" class="main-image" />
          <div v-else class="empty-image">
            <ImageIcon :size="48" :stroke-width="1.5" />
            <span>لا توجد صورة للمنتج</span>
          </div>
        </div>

        <div v-if="product.images.length > 1" class="thumbnails-grid">
          <button
            v-for="(img, idx) in product.images"
            :key="img.id"
            class="thumbnail-btn"
            :class="{ 'thumb-active': idx === activeImageIndex }"
            @click="activeImageIndex = idx"
          >
            <img :src="img.url" :alt="`Thumbnail ${idx + 1}`" />
          </button>
        </div>
      </section>

      <!-- 📝 Information Section -->
      <section class="info-section">
        <div class="title-card surface-card">
          <div class="title-header">
            <h1 class="product-title">{{ product.name }}</h1>
            <span class="status-badge" :class="product.is_active ? 'badge-active' : 'badge-inactive'">
              <CheckCircle v-if="product.is_active" :size="16" />
              <XCircle v-else :size="16" />
              {{ product.is_active ? 'نشط' : 'معطل' }}
            </span>
          </div>
          <p v-if="product.slug" class="product-slug">/{{ product.slug }}</p>

          <div v-if="product.categories?.length" class="categories-section">
            <div class="categories-header">
              <div class="categories-heading">
                <Tags :size="18" class="icon-accent" />
                <span>التصنيفات</span>
              </div>

              <button
                v-if="hasMoreCategories"
                type="button"
                class="categories-toggle"
                @click="showAllCategories = !showAllCategories"
              >
                {{ showAllCategories ? 'عرض أقل' : `عرض المزيد +${hiddenCategoriesCount}` }}
              </button>
            </div>

            <div class="categories-list">
              <span v-for="cat in visibleCategories" :key="cat.id" class="category-pill">
                {{ cat.name }}
              </span>
            </div>
          </div>
        </div>

        <!-- Price Card -->
        <div class="price-card surface-card">
          <div class="price-header">
            <Banknote :size="20" class="icon-accent" />
            <h2>التسعير</h2>
          </div>
          <div class="price-details" :class="{ 'has-discount': product.has_discount }">
            <div class="price-block main-price">
              <span class="price-label">{{ product.has_discount ? 'السعر مع التخفيض' : 'السعر الأساسي' }}</span>
              <span class="price-value">{{ formatPrice(product.price) }}</span>
            </div>
            
            <div v-if="product.has_discount" class="price-block old-price">
              <span class="price-label">السعر قبل التخفيض</span>
              <span class="price-value cross-out">{{ formatPrice(product.compare_price!) }}</span>
            </div>

            <div v-if="product.has_discount" class="discount-badge">
              <Percent :size="16" />
              تخفيض {{ product.discount_percentage }}%
            </div>
          </div>
        </div>

        <!-- Inventory Card -->
        <div class="inventory-card surface-card">
           <div class="info-grid">
            <div class="info-item">
              <div class="info-icon"><Hash :size="18" /></div>
              <div class="info-content">
                <span class="info-label">رمز المنتج (SKU)</span>
                <span class="info-val" dir="ltr">{{ product.sku || 'لا يوجد' }}</span>
              </div>
            </div>

            <div class="info-item">
              <div class="info-icon"><Box :size="18" /></div>
              <div class="info-content">
                <span class="info-label">الكمية المتوفرة</span>
                <span class="info-val quantity-val" :class="{'out-of-stock': !product.in_stock}">
                  {{ product.quantity }} قطع
                  <span class="stock-indicator" v-if="!product.in_stock">(نفدت الكمية)</span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Description Card -->
        <div class="desc-card surface-card" v-if="product.description">
          <div class="desc-header">
            <Activity :size="20" class="icon-accent" />
            <h2>الوصف</h2>
          </div>
          <div class="desc-body">
            <!-- Utilizing pre-wrap to keep text formatting if raw text -->
            <p>{{ product.description }}</p>
          </div>
        </div>

      </section>
    </div>
  </div>
</template>

<style scoped>
.product-detail-container {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* ─── Headers & Actions ─── */
.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: transparent;
  border: none;
  color: var(--color-slate-600);
  font-weight: 600;
  cursor: pointer;
  padding: 8px 12px;
  border-radius: var(--radius-md);
  transition: background 0.2s, color 0.2s;
}

.back-btn:hover {
  background: var(--color-slate-100);
  color: var(--color-slate-900);
}



/* ─── Loading State ─── */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 0;
  gap: 16px;
  color: var(--color-slate-500);
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--color-slate-200);
  border-top-color: var(--color-amber-500);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* ─── Layout ─── */
.product-layout {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 24px;
  align-items: start;
}

@media (max-width: 900px) {
  .product-layout {
    grid-template-columns: 1fr;
  }
}

.surface-card {
  background: var(--color-white);
  border: 1px solid rgba(213, 222, 234, 0.5);
  border-radius: var(--radius-lg);
  padding: 24px;
  box-shadow: 0 4px 12px rgba(20, 32, 51, 0.04);
}

/* ─── Gallery Section ─── */
.gallery-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
  position: sticky;
  top: 24px;
}

.main-image-wrapper {
  aspect-ratio: 1;
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--color-slate-50);
  border: 1px solid var(--color-slate-100);
  display: flex;
  align-items: center;
  justify-content: center;
}

.main-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.empty-image {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  color: var(--color-slate-400);
}

.thumbnails-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
  gap: 12px;
}

.thumbnail-btn {
  aspect-ratio: 1;
  padding: 0;
  border: 2px solid transparent;
  border-radius: var(--radius-sm);
  overflow: hidden;
  background: var(--color-slate-50);
  cursor: pointer;
  transition: all 0.2s;
}

.thumbnail-btn img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.thumbnail-btn:hover {
  opacity: 0.8;
}

.thumb-active {
  border-color: var(--color-amber-500);
  box-shadow: 0 0 0 2px rgba(236, 159, 67, 0.2);
}

/* ─── Info Section ─── */
.info-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Title Card */
.title-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 8px;
}

.product-title {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--color-slate-900);
  line-height: 1.4;
  margin: 0;
}

.product-slug {
  font-size: 0.95rem;
  color: var(--color-slate-500);
  margin: 0 0 16px 0;
  font-family: monospace;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 700;
}

.badge-active {
  background: rgba(34, 197, 94, 0.1);
  color: rgb(21, 128, 61);
}

.badge-inactive {
  background: rgba(239, 68, 68, 0.1);
  color: rgb(185, 28, 28);
}

.categories-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.categories-section {
  display: grid;
  gap: 12px;
}

.categories-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.categories-heading {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--color-slate-800);
}

.categories-toggle {
  min-height: 34px;
  padding: 0 14px;
  border-radius: 999px;
  background: rgba(236, 159, 67, 0.12);
  color: var(--color-amber-700);
  font-size: 0.82rem;
  font-weight: 800;
  cursor: pointer;
  transition: background var(--transition-base), transform var(--transition-base);
}

.categories-toggle:hover {
  background: rgba(236, 159, 67, 0.2);
  transform: translateY(-1px);
}

.category-pill {
  background: var(--color-slate-100);
  color: var(--color-slate-700);
  padding: 4px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
}

/* Common Section Styles */
.price-header,
.desc-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.price-header h2,
.desc-header h2 {
  font-size: 1.15rem;
  font-weight: 700;
  margin: 0;
  color: var(--color-slate-800);
}

.icon-accent {
  color: var(--color-amber-600);
}

/* Price Card */
.price-details {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 24px;
}

.price-block {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.price-label {
  font-size: 0.85rem;
  color: var(--color-slate-500);
  font-weight: 600;
}

.price-value {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--color-slate-900);
}

.main-price .price-value {
  color: var(--color-amber-600);
  font-size: 1.6rem;
}

.cross-out {
  text-decoration: line-through;
  color: var(--color-slate-400);
}

.discount-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background: rgba(239, 68, 68, 0.1);
  color: rgb(220, 38, 38);
  border-radius: 8px;
  font-weight: 800;
  font-size: 0.9rem;
  margin-right: auto;
}

/* Inventory Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 16px;
}

.info-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: var(--color-slate-50);
  color: var(--color-slate-600);
  display: flex;
  align-items: center;
  justify-content: center;
}

.info-content {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.info-label {
  font-size: 0.85rem;
  color: var(--color-slate-500);
  font-weight: 600;
}

.info-val {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-slate-900);
}

.quantity-val.out-of-stock {
  color: rgb(220, 38, 38);
}

.stock-indicator {
  font-size: 0.8rem;
  font-weight: 600;
  margin-right: 6px;
}

/* Description */
.desc-body p {
  margin: 0;
  line-height: 1.8;
  color: var(--color-slate-700);
  white-space: pre-wrap;
}
</style>
