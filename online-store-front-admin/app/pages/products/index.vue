<script setup lang="ts">
import {
  Package,
  Pencil,
  TrendingDown,
  Plus,
  Trash2,
} from 'lucide-vue-next'
import type { Product } from '~/types/product'

definePageMeta({
  title: 'المنتجات',
  description: 'إدارة المنتجات والمخزون داخل لوحة التحكم.',
})

const productsStore = useProductsStore()

const route = useRoute()
const dialog = useDialog()
const productImageErrors = reactive<Record<number, boolean>>({})

onMounted(() => {
  if (route.query.category_id) {
    productsStore.selectedCategoryId = Number(route.query.category_id)
  }
  productsStore.fetchProducts()
})
function formatPrice(price: number) {
  return new Intl.NumberFormat('ar-LY', {
    style: 'currency',
    currency: 'LYD',
    minimumFractionDigits: 2,
  }).format(price)
}

function getFirstImage(images: { url: string }[]) {
  return images?.[0]?.url || null
}

const expandedProducts = ref<Set<string | number>>(new Set())

function toggleCategories(productId: number | string) {
  if (expandedProducts.value.has(productId)) {
    expandedProducts.value.delete(productId)
  } else {
    expandedProducts.value.add(productId)
  }
}

function openProduct(productId: number | string) {
  void navigateTo(`/products/${productId}`)
}

function getProductImage(product: Product) {
  if (productImageErrors[product.id]) {
    return null
  }

  return getFirstImage(product.images)
}

function handleDeleteProduct(product: Product) {
  dialog.confirm({
    title: 'حذف المنتج',
    message: `هل أنت متأكد من حذف المنتج "${product.name}"؟ لا يمكن التراجع عن هذا الإجراء.`,
    confirmText: 'حذف',
    onConfirm: async () => {
      const result = await productsStore.deleteProduct(product.id)

      if (result.success) {
        dialog.success({
          title: 'تم الحذف',
          message: result.message,
        })
        return
      }

      dialog.error({
        title: 'فشل الحذف',
        message: result.message,
      })
    },
  })
}
</script>

<template>
  <div class="products-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-actions">
        <AppSearch 
          v-model="productsStore.searchQuery" 
          v-model:categoryId="productsStore.selectedCategoryId" 
          @search="productsStore.fetchProducts(1)" 
        />
        <NuxtLink to="/products/manage" class="button-primary add-btn">
          <Plus :size="18" :stroke-width="2.5" />
          إضافة منتج
        </NuxtLink>
      </div>
    </div>

    <!-- ═══════ Loading State ═══════ -->
    <Transition name="fade" mode="out-in">
      <LoadingState v-if="productsStore.loading" message="جاري تحميل المنتجات..." key="loading" />

      <!-- ═══════ Error State ═══════ -->
      <ErrorState
        v-else-if="productsStore.error"
        title="تعذر تحميل المنتجات"
        :message="productsStore.error"
        @retry="productsStore.fetchProducts()"
        key="error"
      />

      <!-- ═══════ Empty State ═══════ -->
      <EmptyState
        v-else-if="productsStore.products.length === 0"
        title="لا توجد منتجات"
        description="ابدأ بإضافة منتجاتك لعرضها في المتجر."
        action-label="إضافة منتج"
        action-to="/products/manage"
        key="empty"
      >
        <template #icon>
          <Package :size="40" :stroke-width="1.5" />
        </template>
      </EmptyState>

      <!-- ═══════ Success State (Products Grid) ═══════ -->
      <div v-else class="products-section" key="success">
        <div class="products-grid">
          <article
            v-for="product in productsStore.products"
            :key="product.id"
            class="product-card surface-card"
            role="link"
            tabindex="0"
            :aria-label="`عرض تفاصيل ${product.name}`"
            @click="openProduct(product.id)"
            @keydown.enter.prevent="openProduct(product.id)"
            @keydown.space.prevent="openProduct(product.id)"
          >
            <!-- Image -->
            <div class="card-image-wrap">
              <img
                v-if="getProductImage(product)"
                :src="getProductImage(product)!"
                :alt="product.name"
                class="card-image"
                loading="lazy"
                @error="productImageErrors[product.id] = true"
              />
              <div v-else class="card-image-placeholder">
                <Package :size="36" :stroke-width="1.4" />
              </div>

              <!-- Status Badge -->
              <span
                class="status-chip"
                :class="product.is_active ? 'status-active' : 'status-inactive'"
              >
                {{ product.is_active ? 'نشط' : 'معطّل' }}
              </span>

              <!-- Discount Badge -->
              <span v-if="product.has_discount" class="discount-chip">
                <TrendingDown :size="13" :stroke-width="2.5" />
                {{ product.discount_percentage }}%-
              </span>
            </div>

            <!-- Card Body -->
            <div class="card-body">
              <h3 class="card-title">{{ product.name }}</h3>

              <div class="card-meta">
                <template v-if="product.categories?.length">
                  <span 
                    v-for="cat in (expandedProducts.has(product.id) ? product.categories : product.categories.slice(0, 3))" 
                    :key="cat.id" 
                    class="meta-category"
                  >
                    {{ cat.name }}
                  </span>
                  <button 
                    v-if="product.categories.length > 3" 
                    type="button"
                    class="meta-category more-category-btn"
                    @click.prevent.stop="toggleCategories(product.id)"
                  >
                    {{ expandedProducts.has(product.id) ? 'عرض أقل' : `عرض المزيد +${product.categories.length - 3}` }}
                  </button>
                </template>
                <span class="meta-stock" :class="product.in_stock ? 'in-stock' : 'out-of-stock'">
                  {{ product.in_stock ? `${product.quantity} بالمخزن` : 'نفذ' }}
                </span>
              </div>

              <div class="card-footer">
                <div class="price-group">
                  <span class="current-price">{{ formatPrice(product.price) }}</span>
                  <span v-if="product.has_discount && product.compare_price" class="original-price">
                    {{ formatPrice(product.compare_price) }}
                  </span>
                </div>
                <div class="card-actions">
                  <NuxtLink
                    :to="`/products/manage?id=${product.id}`"
                    class="action-btn"
                    title="تعديل"
                    @click.stop
                  >
                    <Pencil :size="16" :stroke-width="2" />
                  </NuxtLink>
                  <button
                    class="action-btn delete-btn"
                    title="حذف"
                    :disabled="productsStore.isDeleting(product.id)"
                    @click.stop="handleDeleteProduct(product)"
                  >
                    <Trash2 :size="16" :stroke-width="2" />
                  </button>
                </div>
              </div>
            </div>
          </article>
        </div>

        <AppPagination
          :current-page="productsStore.currentPage"
          :last-page="productsStore.lastPage"
          :total="productsStore.total"
          :from="productsStore.from"
          :to="productsStore.to"
          :loading="productsStore.loading"
          @change="productsStore.fetchProducts"
        />
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.products-page {
  display: grid;
  gap: 24px;
}

.products-section {
  display: grid;
  gap: 20px;
}

/* ─── Header ─── */
.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.page-header h1 {
  margin: 8px 0 0;
  font-size: 1.5rem;
  color: var(--color-slate-950);
}

.header-desc {
  margin: 4px 0 0;
  color: var(--color-slate-700);
  font-size: 0.92rem;
}

.product-count-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 999px;
  background: rgba(20, 32, 51, 0.06);
  font-size: 0.88rem;
  font-weight: 800;
  color: var(--color-slate-800);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  justify-content: flex-end;
}

.add-btn {
  font-size: 0.88rem;
  min-height: 42px;
  padding: 0 20px;
}

/* ─── Fade Transition ─── */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* ─── Products Grid ─── */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 18px;
}

/* ─── Product Card ─── */
.product-card {
  display: flex;
  flex-direction: column;
  border-radius: var(--radius-lg);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.28s ease, box-shadow 0.28s ease;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow:
    0 20px 50px rgba(20, 32, 51, 0.12),
    0 8px 16px rgba(20, 32, 51, 0.06);
}

.product-card:focus-visible {
  outline: 3px solid rgba(236, 159, 67, 0.34);
  outline-offset: 3px;
}

/* Image */
.card-image-wrap {
  position: relative;
  aspect-ratio: 4 / 3;
  background: var(--color-slate-100);
  overflow: hidden;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.product-card:hover .card-image {
  transform: scale(1.04);
}

.card-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-slate-500);
  background: linear-gradient(135deg, var(--color-slate-100), var(--color-slate-200));
}

/* Badges on Image */
.status-chip {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 0.76rem;
  font-weight: 800;
  backdrop-filter: blur(8px);
}

.status-active {
  background: rgba(32, 128, 94, 0.16);
  color: var(--color-green-600);
}

.status-inactive {
  background: rgba(202, 61, 84, 0.14);
  color: var(--color-red-600);
}

.discount-chip {
  position: absolute;
  top: 12px;
  left: 12px;
  display: inline-flex;
  align-items: center;
  gap: 3px;
  padding: 4px 10px;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--color-amber-700), var(--color-amber-500));
  color: var(--color-white);
  font-size: 0.76rem;
  font-weight: 800;
  box-shadow: 0 4px 12px rgba(196, 107, 23, 0.3);
}

/* Body */
.card-body {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 16px 18px 18px;
  flex: 1;
}

.card-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 800;
  color: var(--color-slate-950);
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.meta-category {
  padding: 3px 10px;
  border-radius: 8px;
  background: rgba(236, 159, 67, 0.12);
  color: var(--color-amber-700);
  font-size: 0.78rem;
  font-weight: 700;
}

.more-category-btn {
  background: rgba(20, 32, 51, 0.05); /* Grayish transparent background instead of amber */
  color: var(--color-slate-700);
  border: none;
  cursor: pointer;
  transition: background 0.2s, transform 0.2s;
}

.more-category-btn:hover {
  background: rgba(20, 32, 51, 0.1);
  transform: translateY(-1px);
}

.meta-stock {
  font-size: 0.78rem;
  font-weight: 700;
}

.delete-btn {
  color: var(--color-red-600);
}

.delete-btn:hover {
  background: rgba(202, 61, 84, 0.12);
  color: var(--color-red-700);
}

.delete-btn:disabled {
  opacity: 0.55;
  cursor: wait;
}

.in-stock {
  color: var(--color-green-600);
}

.out-of-stock {
  color: var(--color-red-600);
}

/* Footer */
.card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: auto;
  padding-top: 12px;
  border-top: 1px solid rgba(213, 222, 234, 0.5);
}

.price-group {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.current-price {
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--color-slate-950);
}

.original-price {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--color-slate-500);
  text-decoration: line-through;
}

.card-actions {
  display: flex;
  gap: 4px;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: rgba(20, 32, 51, 0.05);
  color: var(--color-slate-700);
  cursor: pointer;
  transition: all var(--transition-base);
}

.action-btn:hover {
  background: rgba(20, 32, 51, 0.1);
  color: var(--color-slate-950);
  transform: scale(1.08);
}

/* States are now handled by reusable components */

/* ─── Responsive ─── */
@media (max-width: 640px) {
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
