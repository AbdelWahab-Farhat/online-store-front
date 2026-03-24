<template>
  <div>
    <!-- Hero -->
    <HeroBanner />

    <!-- Categories -->
    <section class="section">
      <div class="container">
        <h2 class="section-title">تسوّقي حسب القسم</h2>
        <div class="grid grid-3" style="margin-top: var(--space-xl);">
          <CategoryCard
            v-for="cat in categories"
            :key="cat.id"
            :category="cat"
          />
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="section" style="background: var(--color-bg-soft);">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">منتجات مميزة</h2>
          <NuxtLink to="/products" class="btn btn-outline">
            عرض الكل
            <Icon name="mdi:arrow-left" />
          </NuxtLink>
        </div>
        <div class="grid grid-4" style="margin-top: var(--space-xl);">
          <ProductCard
            v-for="product in featuredProducts"
            :key="product.id"
            :product="product"
          />
        </div>
      </div>
    </section>

    <!-- Promo Banner -->
    <section class="promo-section">
      <div class="container">
        <div class="promo-card">
          <div class="promo-content">
            <span class="promo-badge">عرض خاص ✨</span>
            <h2 class="promo-title">خصم يصل إلى ٤٠٪</h2>
            <p class="promo-text">على جميع منتجات العناية بالبشرة والمكياج</p>
            <NuxtLink to="/products?category=skincare" class="btn btn-primary">
              تسوّقي العرض
            </NuxtLink>
          </div>
          <div class="promo-decoration">
            <span>🌸</span>
            <span>💄</span>
            <span>💎</span>
          </div>
        </div>
      </div>
    </section>

    <!-- New Arrivals -->
    <section class="section">
      <div class="container">
        <h2 class="section-title">وصل حديثاً</h2>
        <div class="grid grid-4" style="margin-top: var(--space-xl);">
          <ProductCard
            v-for="product in newArrivals"
            :key="product.id"
            :product="product"
          />
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { products, categories } from '~/data/store'

const featuredProducts = computed(() => products.slice(0, 4))
const newArrivals = computed(() => products.slice(4))
</script>

<style scoped>
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: var(--space-md);
}

/* Promo */
.promo-section {
  padding: var(--space-2xl) 0;
}

.promo-card {
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #880e4f, #c2185b, #e91e80);
  border-radius: var(--radius-lg);
  padding: var(--space-3xl) var(--space-2xl);
  color: #fff;
  text-align: center;
}

.promo-badge {
  display: inline-block;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(8px);
  padding: var(--space-sm) var(--space-lg);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 700;
  margin-bottom: var(--space-md);
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

.promo-decoration {
  position: absolute;
  top: 50%;
  left: 5%;
  transform: translateY(-50%);
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
  font-size: 2.5rem;
  opacity: 0.3;
}

@media (max-width: 768px) {
  .promo-title {
    font-size: var(--font-size-2xl);
  }
  .promo-decoration {
    display: none;
  }
}
</style>
