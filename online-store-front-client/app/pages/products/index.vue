<template>
  <div class="products-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
      <div class="container breadcrumb-inner">
        <NuxtLink to="/" class="breadcrumb-link">الرئيسية</NuxtLink>
        <Icon name="mdi:chevron-left" class="breadcrumb-sep" />
        <NuxtLink to="/products" class="breadcrumb-link">المنتجات</NuxtLink>
        <template v-if="activeCategory">
          <Icon name="mdi:chevron-left" class="breadcrumb-sep" />
          <span class="breadcrumb-current">{{ categoriesStore.getCategoryName(activeCategory) }}</span>
        </template>
        <template v-if="searchQuery">
          <Icon name="mdi:chevron-left" class="breadcrumb-sep" />
          <span class="breadcrumb-current">بحث: {{ searchQuery }}</span>
        </template>
      </div>
    </div>

    <!-- Toolbar: Sort + Count -->
    <div class="toolbar-bar">
      <div class="container toolbar-inner">
        <div class="toolbar-right">
          <div class="sort-dropdown">
            <label class="sort-label">ترتيب حسب</label>
            <select v-model="sortBy" class="sort-select">
              <option value="newest">الأحدث</option>
              <option value="price-low">السعر: الأقل أولاً</option>
              <option value="price-high">السعر: الأعلى أولاً</option>
              <option value="name">الاسم</option>
            </select>
          </div>
        </div>
        <div class="toolbar-left">
          <span class="results-count">{{ productsStore.pagination.total }} منتج</span>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="container products-content">
      <!-- Loading -->
      <div v-if="productsStore.loading && !products.length" class="loading-state">
        <Icon name="mdi:loading" class="spin loading-icon" />
        <span>جاري تحميل المنتجات...</span>
      </div>

      <!-- Error State -->
      <div v-else-if="productsStore.error && !products.length" class="empty-state">
        <div class="empty-icon-wrap" style="color: #c9a84c;">
          <Icon name="mdi:alert-circle-outline" class="empty-icon" />
        </div>
        <h3 class="empty-title">عذراً، فشل تحميل المنتجات</h3>
        <p class="empty-desc">{{ productsStore.error }}</p>
        <button class="btn btn-primary" style="margin-top: 15px;" @click="loadProducts()">
          إعادة المحاولة
        </button>
      </div>

      <template v-else>
        <div v-if="products.length" class="products-grid">
          <div
            v-for="product in products"
            :key="product.id"
            class="product-item"
          >
            <NuxtLink :to="`/products/${product.slug}`" class="product-link">
              <!-- Image -->
              <div class="product-img-wrap">
                <img
                  v-if="!productImageErrors[product.id]"
                  :src="product.images?.[0]?.url || '/images/placeholder.png'"
                  :alt="product.name"
                  class="product-img"
                  loading="lazy"
                  @error="productImageErrors[product.id] = true"
                />
                <img
                  v-else
                  src="/images/placeholder.png"
                  :alt="product.name"
                  class="product-img placeholder-img"
                />
                <!-- Badge -->
                <span
                  v-if="product.has_discount"
                  class="product-badge badge-sale"
                >
                  تخفيض
                </span>
                <!-- Discount percentage -->
                <span v-if="product.discount_percentage" class="discount-tag">
                  -{{ product.discount_percentage }}%
                </span>
                <!-- Favorite Button -->
                <button class="fav-btn" :class="{ 'is-fav': isFavorite(product.id) }" @click.prevent="toggleFavorite(product.id)" :aria-label="isFavorite(product.id) ? 'إزالة من المفضلة' : 'إضافة للمفضلة'">
                  <Icon :name="isFavorite(product.id) ? 'mdi:heart' : 'mdi:heart-outline'" />
                </button>
                <!-- Quick Add -->
                <button class="quick-add" @click.prevent="addToCart(product)" aria-label="أضف للسلة">
                  <Icon name="mdi:cart-plus" />
                </button>
              </div>
              <!-- Info -->
              <div class="product-details">
                <h3 class="product-name">{{ product.name }}</h3>
                <div class="product-pricing">
                  <span class="price-current">{{ product.price }} د.ل</span>
                  <span v-if="product.compare_price" class="price-old">{{ product.compare_price }} د.ل</span>
                </div>
              </div>
            </NuxtLink>
          </div>
        </div>

        <!-- Show More -->
        <div v-if="productsStore.hasMore" class="show-more-wrap">
          <button class="show-more-btn" :disabled="productsStore.loadingMore" @click="productsStore.loadMore()">
            <Icon v-if="productsStore.loadingMore" name="mdi:loading" class="show-more-icon spin" />
            <Icon v-else name="mdi:arrow-down" class="show-more-icon" />
            {{ productsStore.loadingMore ? 'جاري التحميل...' : 'عرض المزيد' }}
          </button>
        </div>

        <!-- Empty State -->
        <div v-if="!products.length && !productsStore.loading" class="empty-state">
          <div class="empty-icon-wrap">
            <Icon name="mdi:package-variant-remove" class="empty-icon" />
          </div>
          <h3 class="empty-title">لا توجد منتجات</h3>
          <p class="empty-desc">لم نعثر على منتجات مطابقة لبحثك</p>
          <button class="btn btn-primary" @click="clearFilters">عرض جميع المنتجات</button>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCategoriesStore } from '~/stores/categories'
import { useProductsStore } from '~/stores/products'

const route = useRoute()
const router = useRouter()
const { addToCart: addItem } = useCart()
const { isFavorite, toggleFavorite } = useFavorites()
const categoriesStore = useCategoriesStore()
const productsStore = useProductsStore()
const { products } = storeToRefs(productsStore)

const productImageErrors = reactive<Record<number, boolean>>({})

const sortBy = ref('newest')
const activeCategory = computed(() => (route.query.category as string) || '')
const searchQuery = computed(() => (route.query.search as string) || '')

// Map sort UI values to API params
const sortMapping: Record<string, { sort_by: string; sort_dir: string }> = {
  'newest': { sort_by: 'created_at', sort_dir: 'desc' },
  'price-low': { sort_by: 'price', sort_dir: 'asc' },
  'price-high': { sort_by: 'price', sort_dir: 'desc' },
  'name': { sort_by: 'name', sort_dir: 'asc' },
}

function buildFilters() {
  const sort = sortMapping[sortBy.value] ?? { sort_by: 'created_at', sort_dir: 'desc' }
  return {
    per_page: 15,
    category_slug: activeCategory.value || undefined,
    search: searchQuery.value || undefined,
    sort_by: sort.sort_by,
    sort_dir: sort.sort_dir,
  }
}

async function loadProducts() {
  await productsStore.fetchProducts(buildFilters())
}

function clearFilters() {
  sortBy.value = 'newest'
  router.push({ path: '/products' })
}

function addToCart(product: any) {
  addItem(product)
}

// Watch route changes (category / search)
watch(
  () => [route.query.category, route.query.search],
  () => {
    loadProducts()
  }
)

// Watch sort changes
watch(sortBy, () => {
  loadProducts()
})

onMounted(async () => {
  await categoriesStore.fetchCategories()
  await loadProducts()
})

useSeoMeta({
  title: 'جميع المنتجات — متجر كيان',
  description: 'اكتشف أحدث وأفضل المنتجات من كيان. جودة عالية وأسعار منافسة.',
  ogTitle: 'جميع المنتجات — متجر كيان',
  ogDescription: 'تسوق أفضل المنتجات، ملابس، وأدوات زينة من متجر كيان بأفضل الأسعار.',
  ogImage: '/images/og-image.jpg',
  twitterCard: 'summary_large_image',
})
</script>

<style scoped>
/* ==========================================
   SHEIN-Style Products Page
   ========================================== */

/* Breadcrumb */
.breadcrumb-bar {
  background: #fff;
  border-bottom: 1px solid #f0f0f0;
  padding: 14px 0;
}

.breadcrumb-inner {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.82rem;
}

.breadcrumb-link {
  color: #666;
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumb-link:hover {
  color: #222;
}

.breadcrumb-sep {
  font-size: 0.7rem;
  color: #ccc;
}

.breadcrumb-current {
  color: #222;
  font-weight: 600;
}

/* Toolbar */
.toolbar-bar {
  background: #fff;
  border-bottom: 1px solid #f0f0f0;
  padding: 12px 0;
}

.toolbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.toolbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.sort-dropdown {
  display: flex;
  align-items: center;
  gap: 8px;
}

.sort-label {
  font-size: 0.82rem;
  color: #666;
  font-weight: 600;
  white-space: nowrap;
}

.sort-select {
  padding: 7px 32px 7px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: var(--font-family);
  font-size: 0.82rem;
  font-weight: 600;
  color: #222;
  background: #fff;
  cursor: pointer;
  outline: none;
  transition: border-color 0.2s;
  appearance: auto;
}

.sort-select:hover,
.sort-select:focus {
  border-color: #222;
}

.results-count {
  font-size: 0.82rem;
  color: #999;
  font-weight: 600;
}

/* Category Tabs */
.category-tabs-bar {
  background: #fff;
  border-bottom: 1px solid #f0f0f0;
  padding: 10px 0;
}

.category-tabs {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.category-tabs::-webkit-scrollbar {
  display: none;
}

.tab-chip {
  display: inline-flex;
  align-items: center;
  padding: 7px 20px;
  border-radius: 20px;
  border: 1.5px solid #e0e0e0;
  background: #fff;
  font-family: var(--font-family);
  font-size: 0.82rem;
  font-weight: 600;
  color: #555;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s ease;
}

.tab-chip:hover {
  border-color: #222;
  color: #222;
}

.tab-chip.active {
  background: #222;
  border-color: #222;
  color: #fff;
}

/* Products Content */
.products-content {
  padding-top: 24px;
  padding-bottom: 60px;
  min-height: 60vh;
}

/* Products Grid — SHEIN-style 4-column waterfall */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

.product-item {
  background: #fff;
  border-radius: 0;
  overflow: hidden;
  transition: box-shadow 0.3s ease;
}

.product-item:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.product-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

/* Product Image */
.product-img-wrap {
  position: relative;
  width: 100%;
  aspect-ratio: 3 / 4;
  overflow: hidden;
  background: #f5f5f5;
}

.product-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.product-item:hover .product-img {
  transform: scale(1.05);
}

.placeholder-img {
  object-fit: contain;
}

/* Favorite Button */
.fav-btn {
  position: absolute;
  top: 8px;
  left: 48px;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  color: #444;
  cursor: pointer;
  border: none;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 3;
}

.fav-btn.is-fav {
  color: #c9a84c;
}

.fav-btn:hover {
  transform: scale(1.15);
  color: #c9a84c;
}

/* Badge */
.product-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  padding: 3px 10px;
  border-radius: 2px;
  font-size: 0.7rem;
  font-weight: 700;
  z-index: 2;
}

.badge-sale {
  background: #c9a84c;
  color: #fff;
}

.badge-new {
  background: #d4a853;
  color: #fff;
}

/* Discount tag */
.discount-tag {
  position: absolute;
  top: 50px;
  left: 8px;
  padding: 3px 8px;
  background: rgba(0, 0, 0, 0.7);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 700;
  border-radius: 2px;
  z-index: 2;
}

/* Quick Add Button */
.quick-add {
  position: absolute;
  bottom: 8px;
  left: 8px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #fff;
  color: #222;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  z-index: 2;
  border: none;
  cursor: pointer;
}

.quick-add:hover {
  background: #c9a84c;
  color: #fff;
  transform: scale(1.1);
}

/* Product Details */
.product-details {
  padding: 10px 8px 14px;
}

.product-name {
  font-size: 0.82rem;
  font-weight: 600;
  color: #333;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 6px;
}

.product-pricing {
  display: flex;
  align-items: center;
  gap: 8px;
}

.price-current {
  font-size: 0.95rem;
  font-weight: 800;
  color: #c9a84c;
}

.price-old {
  font-size: 0.78rem;
  color: #999;
  text-decoration: line-through;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 80px 20px;
}

.empty-icon-wrap {
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  background: #f5f5f5;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-icon {
  font-size: 2.5rem;
  color: #ccc;
}

.empty-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 8px;
}

.empty-desc {
  font-size: 0.9rem;
  color: #999;
  margin-bottom: 24px;
}

/* ==========================================
   Responsive
   ========================================== */
@media (max-width: 1024px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }

  .toolbar-inner {
    flex-wrap: wrap;
    gap: 10px;
  }

  .products-content {
    padding-bottom: calc(60px + 70px);
  }

  .product-details {
    padding: 8px 6px 12px;
  }

  .product-name {
    font-size: 0.78rem;
  }

  .price-current {
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
  }

  .sort-label {
    display: none;
  }
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 60px 20px;
  color: #999;
  font-size: 0.95rem;
}

.loading-icon {
  font-size: 2rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.spin {
  animation: spin 1s linear infinite;
}

/* Show More */
.show-more-wrap {
  display: flex;
  justify-content: center;
  padding: 32px 0;
}

.show-more-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 48px;
  background: #111;
  color: #fff;
  border: none;
  border-radius: 50px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: inherit;
}

.show-more-btn:hover:not(:disabled) {
  background: #333;
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.2);
}

.show-more-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.show-more-icon {
  font-size: 1.1rem;
}
</style>
