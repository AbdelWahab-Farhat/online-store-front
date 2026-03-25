<template>
  <div>
    <!-- Category Circles (homepage only) -->
    <CategoriesBar />

    <!-- Hero Carousel -->
    <HeroBanner />

    <!-- Featured Category Sections (admin-togglable via showOnHome) -->
    <template v-for="(cat, idx) in homeCategories" :key="cat.id">
      <section class="featured-section" :class="{ 'alt-bg': idx % 2 !== 0 }">
        <div class="container">
          <!-- Section Header with decorative line -->
          <div class="section-header">
            <div class="section-title-group">
              <Icon :name="cat.icon" class="section-icon" />
              <h2 class="section-title">{{ cat.name }}</h2>
            </div>
            <NuxtLink :to="`/products?category=${cat.slug}`" class="view-all-btn">
              عرض الكل
              <Icon name="mdi:arrow-left" />
            </NuxtLink>
          </div>

          <!-- Decorative divider -->
          <div class="section-divider">
            <span class="divider-line"></span>
            <span class="divider-dot"></span>
            <span class="divider-line"></span>
          </div>

          <!-- Products -->
          <div class="products-row">
            <ProductCard
              v-for="product in getProductsByCategory(cat.slug)"
              :key="product.id"
              :product="product"
              class="product-animate"
            />
          </div>
        </div>
      </section>

      <!-- Visual separator between sections -->
      <div v-if="idx < homeCategories.length - 1" class="wave-separator" :class="`wave-${idx % 3}`"></div>
    </template>

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
            <p class="promo-text">على جميع منتجات العناية بالبشرة والمكياج</p>
            <NuxtLink to="/products?category=skincare" class="btn btn-primary promo-btn">
              تسوّقي العرض
            </NuxtLink>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { products, categories } from '~/data/store'

const homeCategories = computed(() => categories.filter((c) => c.showOnHome))

function getProductsByCategory(slug: string) {
  return products.filter((p) => p.category === slug).slice(0, 5)
}
</script>

<style scoped>
/* Featured Sections */
.featured-section {
  padding: var(--space-3xl) 0 var(--space-2xl);
  background: #fff;
  position: relative;
}

.alt-bg {
  background: var(--color-bg-soft);
}

/* Section Header */
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: var(--space-md);
}

.section-title-group {
  display: flex;
  align-items: center;
  gap: var(--space-md);
}

.section-icon {
  font-size: 1.6rem;
  color: var(--color-primary);
  background: linear-gradient(135deg, rgba(194, 24, 91, 0.1), rgba(194, 24, 91, 0.05));
  padding: 10px;
  border-radius: var(--radius-md);
}

.section-title {
  font-size: var(--font-size-2xl);
  font-weight: 800;
  color: var(--color-text);
  position: relative;
}

/* Decorative Divider */
.section-divider {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin: var(--space-lg) 0 var(--space-xl);
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

.view-all-btn {
  display: inline-flex;
  align-items: center;
  gap: var(--space-xs);
  font-size: var(--font-size-sm);
  font-weight: 700;
  color: var(--color-primary);
  padding: var(--space-sm) var(--space-lg);
  border-radius: var(--radius-full);
  border: 1.5px solid var(--color-primary);
  transition: all var(--transition-fast);
}

.view-all-btn:hover {
  background: var(--color-primary);
  color: #fff;
  transform: translateX(-4px);
}

/* Products Row */
.products-row {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: var(--space-lg);
}

.product-animate {
  animation: slideUp 0.5s ease both;
}

.product-animate:nth-child(1) { animation-delay: 0s; }
.product-animate:nth-child(2) { animation-delay: 0.05s; }
.product-animate:nth-child(3) { animation-delay: 0.1s; }
.product-animate:nth-child(4) { animation-delay: 0.15s; }
.product-animate:nth-child(5) { animation-delay: 0.2s; }

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(24px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Wave Separators */
.wave-separator {
  height: 40px;
  position: relative;
  overflow: hidden;
}

.wave-0 {
  background: var(--color-bg-soft);
}

.wave-0::before {
  content: '';
  position: absolute;
  top: -20px;
  left: 0;
  right: 0;
  height: 40px;
  background: #fff;
  border-radius: 0 0 50% 50%;
}

.wave-1 {
  background: #fff;
}

.wave-1::before {
  content: '';
  position: absolute;
  top: -20px;
  left: 0;
  right: 0;
  height: 40px;
  background: var(--color-bg-soft);
  border-radius: 0 0 50% 50%;
}

.wave-2 {
  background: var(--color-bg-soft);
}

.wave-2::before {
  content: '';
  position: absolute;
  top: -20px;
  left: 0;
  right: 0;
  height: 40px;
  background: #fff;
  border-radius: 0 0 50% 50%;
}

/* Promo */
.promo-section {
  padding: var(--space-3xl) 0;
  background: var(--color-bg);
}

.promo-card {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #880e4f 0%, #c2185b 40%, #e91e80 100%);
  border-radius: var(--radius-lg);
  padding: var(--space-3xl) var(--space-2xl);
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
  opacity: 0.1;
}

.shape-1 {
  width: 300px;
  height: 300px;
  background: #fff;
  top: -100px;
  right: -50px;
  animation: float 6s ease-in-out infinite;
}

.shape-2 {
  width: 200px;
  height: 200px;
  background: var(--color-accent);
  bottom: -80px;
  left: -40px;
  animation: float 8s ease-in-out infinite reverse;
}

.shape-3 {
  width: 150px;
  height: 150px;
  background: #fff;
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
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(8px);
  padding: var(--space-sm) var(--space-lg);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 700;
  margin-bottom: var(--space-lg);
}

.promo-title {
  font-size: var(--font-size-3xl);
  font-weight: 800;
  margin-bottom: var(--space-sm);
}

.promo-text {
  font-size: var(--font-size-lg);
  opacity: 0.9;
  margin-bottom: var(--space-xl);
}

.promo-btn {
  padding: var(--space-md) var(--space-2xl);
  font-size: var(--font-size-base);
}

/* Responsive */
@media (max-width: 1024px) {
  .products-row {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 768px) {
  .products-row {
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-md);
  }
  .featured-section {
    padding: var(--space-2xl) 0 var(--space-xl);
  }
  .promo-title {
    font-size: var(--font-size-2xl);
  }
  .section-title {
    font-size: var(--font-size-xl);
  }
}

@media (max-width: 480px) {
  .products-row {
    grid-template-columns: repeat(2, 1fr);
  }
  .section-icon {
    display: none;
  }
}

@media (max-width: 768px) {
  /* Prevent content hiding behind mobile bottom nav */
  div:last-child {
    padding-bottom: 70px;
  }
}
</style>
