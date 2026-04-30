<template>
  <div class="home-page">
    <!-- Hero Carousel -->
    <HeroBanner />

    <!-- All Categories Section -->
    <section class="categories-section">
      <div class="container">
        <div class="section-heading">
          <div class="heading-content">
            <Icon name="mdi:shape-outline" class="heading-icon" />
            <h2 class="heading-title">جميع التصنيفات</h2>
          </div>
          <div class="heading-line"></div>
        </div>

        <div v-if="categoriesStore.loading" class="loading-state">
          <Icon name="mdi:loading" class="spin" />
          <span>جاري تحميل التصنيفات...</span>
        </div>

        <div v-else-if="categoriesStore.error && !categories.length" class="loading-state" style="color: #c9a84c;">
          <Icon name="mdi:alert-circle-outline" style="font-size: 2rem; margin-bottom: 10px;" />
          <h3 style="font-weight: bold; margin-bottom: 5px;">عذراً، فشل تحميل التصنيفات</h3>
          <p style="margin-bottom: 15px; font-size: 0.9rem;">{{ categoriesStore.error }}</p>
          <button class="btn btn-primary" @click="categoriesStore.fetchCategories()">إعادة المحاولة</button>
        </div>

        <div v-else class="categories-grid">
          <NuxtLink
            v-for="cat in categories"
            :key="cat.id"
            :to="`/products?category=${cat.slug}`"
            class="category-card"
          >
            <div class="category-img-wrap">
              <img
                v-if="hasCategoryImage(cat)"
                :src="cat.image?.url"
                :alt="cat.name"
                class="category-image"
                loading="lazy"
                @error="categoryImageErrors[cat.id] = true"
              />
              <img
                v-else
                src="/images/placeholder.png"
                :alt="cat.name"
                class="category-image category-img-placeholder"
              />
              <div class="category-overlay"></div>
            </div>
            <div class="category-info">
              <span class="category-name">{{ cat.name }}</span>
              <span v-if="cat.products_count" class="category-count">{{ cat.products_count }} منتج</span>
            </div>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Latest Products Section -->
    <section class="products-section">
      <div class="container">
        <div class="section-heading">
          <div class="heading-content">
            <Icon name="mdi:sparkles" class="heading-icon" />
            <h2 class="heading-title">أحدث المنتجات</h2>
          </div>
          <div class="heading-line"></div>
        </div>

        <div v-if="productsStore.loading && !products.length" class="loading-state">
          <Icon name="mdi:loading" class="spin" />
          <span>جاري تحميل المنتجات...</span>
        </div>

        <div v-else-if="productsStore.error && !products.length" class="loading-state" style="color: #c9a84c;">
          <Icon name="mdi:alert-circle-outline" style="font-size: 2rem; margin-bottom: 10px;" />
          <h3 style="font-weight: bold; margin-bottom: 5px;">عذراً، فشل تحميل المنتجات</h3>
          <p style="margin-bottom: 15px; font-size: 0.9rem;">{{ productsStore.error }}</p>
          <button class="btn btn-primary" @click="productsStore.fetchProducts({ per_page: 8 })">إعادة المحاولة</button>
        </div>

        <div v-else class="products-grid">
          <ProductCard
            v-for="product in products"
            :key="product.id"
            :product="product"
            class="product-animate"
          />
        </div>

        <!-- Show More Button -->
        <div v-if="productsStore.hasMore" class="show-more-wrap">
          <button class="show-more-btn" :disabled="productsStore.loadingMore" @click="productsStore.loadMore()">
            <Icon v-if="productsStore.loadingMore" name="mdi:loading" class="show-more-icon spin" />
            <Icon v-else name="mdi:arrow-down" class="show-more-icon" />
            {{ productsStore.loadingMore ? 'جاري التحميل...' : 'عرض المزيد' }}
          </button>
        </div>

        <!-- All loaded message -->
        <div v-else-if="products.length > 0 && !productsStore.hasMore && products.length > 8" class="all-loaded">
          <Icon name="mdi:check-circle-outline" />
          <span>تمّ عرض جميع المنتجات</span>
        </div>
      </div>
    </section>

    <!-- Promo Banner -->
    <section class="promo-section">
      <div class="container">
        <div class="promo-card">
          <div class="promo-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
          </div>
          <div class="promo-content">
            <span class="promo-badge">عرض خاص ✨</span>
            <h2 class="promo-title">خصم يصل إلى ٤٠٪</h2>
            <p class="promo-text">على جميع المنتجات المختارة</p>
            <NuxtLink to="/products" class="btn btn-primary promo-btn">
              تسوّق العرض
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { useCategoriesStore } from '~/stores/categories'
import { useProductsStore } from '~/stores/products'

const categoriesStore = useCategoriesStore()
const productsStore = useProductsStore()
const { categories } = storeToRefs(categoriesStore)
const { products } = storeToRefs(productsStore)

const categoryImageErrors = reactive<Record<number, boolean>>({})

function hasCategoryImage(category: { id: number; image: { url?: string | null } | null }) {
  return Boolean(category.image?.url) && !categoryImageErrors[category.id]
}

onMounted(async () => {
  await Promise.all([
    categoriesStore.fetchCategories(),
    productsStore.fetchProducts({ per_page: 8 }),
  ])
})

useSeoMeta({
  title: 'متجر كيان — الصفحة الرئيسية',
  description: 'متجر كيان — وجهتك الأولى للتسوّق أونلاين. أحدث المنتجات بأفضل الأسعار وتوصيل سريع!',
  ogTitle: 'متجر كيان — تسوّق أونلاين',
  ogDescription: 'تسوق أفضل المنتجات، ملابس، وأدوات زينة من متجر كيان بأفضل الأسعار.',
  ogImage: '/images/og-image.jpg',
  twitterCard: 'summary_large_image',
})
</script>

<style scoped>
/* ==========================================
   Categories Section
   ========================================== */
.categories-section {
  padding: 48px 0 40px;
  background: #fff;
}

.section-heading {
  margin-bottom: 32px;
}

.heading-content {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
}

.heading-icon {
  font-size: 1.5rem;
  color: #c9a84c;
  background: linear-gradient(135deg, rgba(201, 168, 76, 0.12), rgba(201, 168, 76, 0.04));
  padding: 8px;
  border-radius: 10px;
}

.heading-title {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--color-text);
}

.heading-line {
  height: 3px;
  background: linear-gradient(to left, #c9a84c, #1a1a2e, transparent);
  border-radius: 3px;
}

/* Categories Grid */
.categories-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 16px;
}

.category-card {
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  background: #fff;
  border: 1px solid #f0f0f0;
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  text-decoration: none;
}

.category-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
  border-color: transparent;
}

.category-img-wrap {
  position: relative;
  aspect-ratio: 1;
  overflow: hidden;
}

.category-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.category-card:hover .category-image {
  transform: scale(1.08);
}

.category-img-placeholder {
  object-fit: contain;
  padding: 14px;
  background: linear-gradient(135deg, #f7f3ee 0%, #ece6dd 100%);
}

.category-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 40%, rgba(0, 0, 0, 0.35) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.category-card:hover .category-overlay {
  opacity: 1;
}

.category-info {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 10px;
  background: #fafafa;
  transition: all 0.3s ease;
}

.category-card:hover .category-info {
  background: #1a1a2e;
  color: #fff;
}

.category-icon {
  font-size: 1.1rem;
  color: #c9a84c;
  transition: color 0.3s ease;
}

.category-card:hover .category-icon {
  color: #fff;
}

.category-name {
  font-size: 0.88rem;
  font-weight: 700;
  color: var(--color-text);
  transition: color 0.3s ease;
}

.category-card:hover .category-name {
  color: #fff;
}

/* ==========================================
   Products Section
   ========================================== */
.products-section {
  padding: 48px 0 56px;
  background: var(--color-bg-soft);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.product-animate {
  animation: slideUp 0.5s ease both;
}

.product-animate:nth-child(1) { animation-delay: 0s; }
.product-animate:nth-child(2) { animation-delay: 0.05s; }
.product-animate:nth-child(3) { animation-delay: 0.1s; }
.product-animate:nth-child(4) { animation-delay: 0.15s; }
.product-animate:nth-child(5) { animation-delay: 0.2s; }
.product-animate:nth-child(6) { animation-delay: 0.25s; }
.product-animate:nth-child(7) { animation-delay: 0.3s; }
.product-animate:nth-child(8) { animation-delay: 0.35s; }

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Show More */
.show-more-wrap {
  text-align: center;
  margin-top: 40px;
}

.show-more-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 48px;
  background: #fff;
  color: #222;
  border: 2px solid #222;
  border-radius: 30px;
  font-family: var(--font-family);
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.show-more-btn:hover {
  background: #222;
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.show-more-icon {
  font-size: 1.1rem;
  animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(4px); }
}

/* All loaded */
.all-loaded {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 36px;
  color: #999;
  font-size: 0.9rem;
  font-weight: 600;
}

.all-loaded .iconify {
  font-size: 1.2rem;
  color: #4caf50;
}

/* ==========================================
   Promo Section
   ========================================== */
.promo-section {
  padding: 56px 0;
  background: #fff;
}

.promo-card {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  border-radius: 20px;
  padding: 60px 40px;
  color: #fff;
  text-align: center;
}

.promo-bg-shapes {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.shape {
  position: absolute;
  border-radius: 50%;
  opacity: 0.08;
}

.shape-1 {
  width: 300px;
  height: 300px;
  background: #c9a84c;
  top: -100px;
  right: -50px;
  animation: float 6s ease-in-out infinite;
}

.shape-2 {
  width: 200px;
  height: 200px;
  background: #1a1a2e;
  bottom: -80px;
  left: -40px;
  animation: float 8s ease-in-out infinite reverse;
}

.shape-3 {
  width: 150px;
  height: 150px;
  background: #c9a84c;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation: float 7s ease-in-out infinite 2s;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

.promo-content {
  position: relative;
  z-index: 1;
}

.promo-badge {
  display: inline-block;
  background: rgba(201, 168, 76, 0.25);
  backdrop-filter: blur(8px);
  padding: 8px 24px;
  border-radius: 20px;
  font-size: 0.88rem;
  font-weight: 700;
  margin-bottom: 20px;
  border: 1px solid rgba(201, 168, 76, 0.35);
}

.promo-title {
  font-size: 2.2rem;
  font-weight: 800;
  margin-bottom: 10px;
}

.promo-text {
  font-size: 1.1rem;
  opacity: 0.8;
  margin-bottom: 28px;
}

.promo-btn {
  padding: 14px 40px;
  font-size: 1rem;
}

/* ==========================================
   Responsive
   ========================================== */
@media (max-width: 1024px) {
  .categories-grid {
    grid-template-columns: repeat(3, 1fr);
  }
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .categories-section,
  .products-section {
    padding: 32px 0;
  }
  .categories-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
  }
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  .heading-title {
    font-size: 1.2rem;
  }
  .promo-title {
    font-size: 1.6rem;
  }
  .promo-card {
    padding: 40px 24px;
  }
}

@media (max-width: 480px) {
  .categories-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }
  .category-info {
    padding: 10px 8px;
  }
  .category-name {
    font-size: 0.78rem;
  }
}

@media (max-width: 768px) {
  /* Prevent content hiding behind mobile bottom nav */
  .home-page {
    padding-bottom: 70px;
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

@keyframes spin {
  to { transform: rotate(360deg); }
}

.spin {
  animation: spin 1s linear infinite;
}

/* Category Count */
.category-count {
  font-size: 0.72rem;
  color: var(--color-text-light);
  font-weight: 400;
}
</style>
