<template>
  <div class="favorites-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
      <div class="container breadcrumb-inner">
        <NuxtLink to="/" class="breadcrumb-link">الرئيسية</NuxtLink>
        <Icon name="mdi:chevron-left" class="breadcrumb-sep" />
        <span class="breadcrumb-current">المفضلة</span>
      </div>
    </div>

    <!-- Page Header -->
    <div class="container">
      <div class="page-header">
        <div class="page-header-content">
          <div class="page-title-group">
            <div class="page-icon-wrap">
              <Icon name="mdi:heart" class="page-icon" />
            </div>
            <div>
              <h1 class="page-title">المفضلة</h1>
              <p class="page-subtitle" v-if="favoriteProducts.length > 0">
                لديك <strong>{{ favoriteProducts.length }}</strong> {{ favoriteProducts.length === 1 ? 'منتج' : 'منتجات' }} في قائمتك
              </p>
            </div>
          </div>
          <button
            v-if="favoriteProducts.length > 0"
            class="clear-favs-btn"
            @click="showClearConfirm = true"
          >
            <Icon name="mdi:heart-off-outline" />
            <span>مسح الكل</span>
          </button>
        </div>
        <!-- Decorative divider -->
        <div class="section-divider">
          <span class="divider-line"></span>
          <span class="divider-dot"></span>
          <span class="divider-line"></span>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="container favorites-content">
      <!-- Loading -->
      <div v-if="isLoading" class="loading-state">
        <div class="loading-spinner">
          <Icon name="mdi:loading" class="spin loading-icon" />
        </div>
        <span>جاري تحميل المنتجات المفضلة...</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="favoriteProducts.length === 0 && !isLoading" class="empty-state">
        <div class="empty-icon-wrap">
          <Icon name="mdi:heart-outline" class="empty-icon" />
        </div>
        <h2 class="empty-title">لا توجد منتجات مفضلة</h2>
        <p class="empty-text">لم تضيفي أي منتجات للمفضلة بعد. تصفّحي مجموعتنا واضغطي على <Icon name="mdi:heart-outline" style="vertical-align: middle; color: var(--color-primary);" /> لحفظها هنا.</p>
        <NuxtLink to="/products" class="btn btn-primary empty-btn">
          <Icon name="mdi:shopping-outline" />
          تصفّحي المنتجات
        </NuxtLink>
      </div>

      <!-- Products Grid -->
      <template v-else>
        <div class="products-grid">
          <TransitionGroup name="fav-item">
            <div
              v-for="product in favoriteProducts"
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
                  <!-- Remove from Favorites -->
                  <button
                    class="fav-btn is-fav"
                    @click.prevent="handleRemoveFavorite(product.id)"
                    aria-label="إزالة من المفضلة"
                  >
                    <Icon name="mdi:heart" />
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
          </TransitionGroup>
        </div>
      </template>
    </div>

    <!-- Clear Confirm Modal -->
    <Transition name="modal">
      <div v-if="showClearConfirm" class="modal-overlay" @click.self="showClearConfirm = false">
        <div class="modal-card">
          <div class="modal-icon-wrap">
            <Icon name="mdi:heart-off" class="modal-icon" />
          </div>
          <h3 class="modal-title">مسح جميع المفضلات؟</h3>
          <p class="modal-text">سيتم إزالة جميع المنتجات من قائمة المفضلة. هل أنتِ متأكدة؟</p>
          <div class="modal-actions">
            <button class="btn btn-outline modal-cancel" @click="showClearConfirm = false">
              إلغاء
            </button>
            <button class="btn btn-primary modal-confirm" @click="confirmClearAll">
              <Icon name="mdi:heart-off-outline" />
              نعم، مسح الكل
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import type { Product } from '~/stores/products'

const { $axios } = useNuxtApp()
const { favoriteIds, removeFavorite, clearFavorites } = useFavorites()
const { addToCart: addItem } = useCart()

const favoriteProducts = ref<Product[]>([])
const isLoading = ref(false)
const showClearConfirm = ref(false)
const productImageErrors = reactive<Record<number, boolean>>({})

function addToCart(product: Product) {
  addItem(product)
}

function handleRemoveFavorite(productId: number) {
  removeFavorite(productId)
  favoriteProducts.value = favoriteProducts.value.filter(p => p.id !== productId)
}

function confirmClearAll() {
  clearFavorites()
  favoriteProducts.value = []
  showClearConfirm.value = false
}

async function fetchFavoriteProducts() {
  if (favoriteIds.value.length === 0) {
    favoriteProducts.value = []
    return
  }

  isLoading.value = true
  try {
    // Fetch all favorites in one request using ids[] query param
    const idsParam = favoriteIds.value.map(id => `ids[]=${id}`).join('&')
    const { data } = await $axios.get(`/products?${idsParam}&per_page=100`)
    favoriteProducts.value = data.data || []

    // Clean up any favorite IDs that no longer exist in the backend
    const existingIds = new Set(favoriteProducts.value.map((p: Product) => p.id))
    const staleIds = favoriteIds.value.filter(id => !existingIds.has(id))
    staleIds.forEach(id => removeFavorite(id))
  } catch (e) {
    console.error('Failed to fetch favorite products:', e)
    favoriteProducts.value = []
  } finally {
    isLoading.value = false
  }
}

// Watch for external changes to favorite IDs (e.g., toggling from another page)
watch(favoriteIds, () => {
  fetchFavoriteProducts()
}, { deep: true })

onMounted(() => {
  fetchFavoriteProducts()
})

// Lock body scroll when modal is open
watch(showClearConfirm, (val) => {
  if (import.meta.client) {
    document.body.style.overflow = val ? 'hidden' : ''
  }
})

useSeoMeta({
  title: 'المفضلة — متجر كيان',
  description: 'منتجاتك المفضلة في متجر كيان. احتفظي بقائمة المنتجات التي أعجبتك وتسوّقيها بسهولة.',
  ogTitle: 'المفضلة — متجر كيان',
  ogDescription: 'قائمة المنتجات المفضلة لديك في متجر كيان.',
})
</script>

<style scoped>
/* ==========================================
   Favorites Page — SHEIN-Style
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

/* Page Header */
.page-header {
  padding-top: var(--space-xl);
  margin-bottom: var(--space-lg);
}

.page-header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: var(--space-md);
}

.page-title-group {
  display: flex;
  align-items: center;
  gap: var(--space-md);
}

.page-icon-wrap {
  width: 52px;
  height: 52px;
  border-radius: var(--radius-md);
  background: linear-gradient(135deg, rgba(194, 24, 91, 0.12), rgba(194, 24, 91, 0.04));
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-icon {
  font-size: 1.6rem;
  color: var(--color-primary);
}

.page-title {
  font-size: var(--font-size-2xl);
  font-weight: 800;
  color: var(--color-text);
}

.page-subtitle {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin-top: 2px;
}

.page-subtitle strong {
  color: var(--color-primary);
}

.clear-favs-btn {
  display: inline-flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-sm) var(--space-lg);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: #dc3545;
  background: rgba(220, 53, 69, 0.08);
  transition: all var(--transition-fast);
}

.clear-favs-btn:hover {
  background: rgba(220, 53, 69, 0.15);
  transform: translateY(-1px);
}

/* Divider */
.section-divider {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-top: var(--space-lg);
}

.divider-line {
  flex: 1;
  height: 1px;
  background: linear-gradient(to left, var(--color-border), transparent);
}

.divider-line:last-child {
  background: linear-gradient(to right, var(--color-border), transparent);
}

.divider-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
  flex-shrink: 0;
}

/* Content Area */
.favorites-content {
  padding-top: 24px;
  padding-bottom: 60px;
  min-height: 50vh;
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  padding: 80px 20px;
  color: #999;
  font-size: 0.95rem;
}

.loading-spinner {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(194, 24, 91, 0.08), rgba(212, 168, 83, 0.08));
  display: flex;
  align-items: center;
  justify-content: center;
}

.loading-icon {
  font-size: 2rem;
  color: var(--color-primary);
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.spin {
  animation: spin 1s linear infinite;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: var(--space-3xl) var(--space-lg);
  animation: fadeInUp var(--transition-slow) both;
}

.empty-icon-wrap {
  width: 120px;
  height: 120px;
  margin: 0 auto var(--space-xl);
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(194, 24, 91, 0.08), rgba(212, 168, 83, 0.08));
  display: flex;
  align-items: center;
  justify-content: center;
  animation: pulse 2s ease-in-out infinite;
}

.empty-icon {
  font-size: 3.5rem;
  color: var(--color-primary);
  opacity: 0.7;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.empty-title {
  font-size: var(--font-size-2xl);
  font-weight: 800;
  color: var(--color-text);
  margin-bottom: var(--space-sm);
}

.empty-text {
  font-size: var(--font-size-base);
  color: var(--color-text-light);
  margin-bottom: var(--space-xl);
  max-width: 440px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.8;
}

.empty-btn {
  padding: var(--space-md) var(--space-2xl);
  font-size: var(--font-size-base);
  gap: var(--space-sm);
}

/* Products Grid — SHEIN-style 4-column */
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
  animation: fadeInUp 0.4s ease both;
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
  left: 8px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  color: #c9a84c;
  cursor: pointer;
  border: none;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 3;
}

.fav-btn:hover {
  transform: scale(1.15);
  background: #c9a84c;
  color: #fff;
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

/* TransitionGroup animations */
.fav-item-enter-active {
  transition: all 0.4s ease;
}

.fav-item-leave-active {
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.fav-item-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.fav-item-leave-to {
  opacity: 0;
  transform: scale(0.9);
}

.fav-item-move {
  transition: transform 0.4s ease;
}

/* ==========================================
   Modal
   ========================================== */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-lg);
}

.modal-card {
  background: #fff;
  border-radius: var(--radius-lg);
  padding: var(--space-2xl);
  max-width: 420px;
  width: 100%;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.modal-icon-wrap {
  width: 72px;
  height: 72px;
  margin: 0 auto var(--space-lg);
  border-radius: 50%;
  background: rgba(194, 24, 91, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-icon {
  font-size: 2.2rem;
  color: var(--color-primary);
}

.modal-title {
  font-size: var(--font-size-xl);
  font-weight: 800;
  color: var(--color-text);
  margin-bottom: var(--space-sm);
}

.modal-text {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin-bottom: var(--space-xl);
  line-height: 1.7;
}

.modal-actions {
  display: flex;
  gap: var(--space-md);
  justify-content: center;
}

.modal-cancel {
  flex: 1;
  padding: var(--space-sm) var(--space-lg);
}

.modal-confirm {
  flex: 1;
  padding: var(--space-sm) var(--space-lg);
}

/* Modal transition */
.modal-enter-active {
  transition: all 0.3s ease;
}

.modal-leave-active {
  transition: all 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-card,
.modal-leave-to .modal-card {
  transform: scale(0.9) translateY(20px);
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

  .favorites-content {
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

  .page-title {
    font-size: var(--font-size-xl);
  }
}

@media (max-width: 480px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
  }
}
</style>
