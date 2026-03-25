<template>
  <div class="products-page">
    <!-- Products Hero Banner -->
    <div class="products-hero">
      <div class="hero-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
      </div>
      <div class="container hero-content">
        <div class="breadcrumb">
          <NuxtLink to="/">الرئيسية</NuxtLink>
          <Icon name="mdi:chevron-left" class="breadcrumb-sep" />
          <span>المنتجات</span>
          <template v-if="activeCategory">
            <Icon name="mdi:chevron-left" class="breadcrumb-sep" />
            <span>{{ getCategoryName(activeCategory) }}</span>
          </template>
        </div>
        <h1 class="hero-title">
          {{ activeCategory ? getCategoryName(activeCategory) : 'تسوقي جميع المنتجات' }}
        </h1>
        <div class="hero-line"></div>
        <p class="hero-subtitle">{{ filteredProducts.length }} منتج متاح للتسوق</p>
      </div>
    </div>

    <!-- Sticky Category Tabs -->
    <div class="category-tabs-wrapper">
      <div class="container">
        <div class="category-tabs">
          <button
            class="tab"
            :class="{ active: !activeCategory }"
            @click="clearFilters"
          >
            <Icon name="mdi:view-grid-outline" />
            <span>الكل</span>
            <span class="tab-count">{{ products.length }}</span>
          </button>
          <button
            v-for="cat in categories"
            :key="cat.id"
            class="tab"
            :class="{ active: activeCategory === cat.slug }"
            @click="setCategory(cat.slug)"
          >
            <Icon :name="cat.icon" />
            <span>{{ cat.name }}</span>
            <span class="tab-count">{{ getCategoryCount(cat.slug) }}</span>
          </button>
        </div>
      </div>
    </div>

    <div class="container products-content">
      <!-- Search & Sort Bar -->
      <div class="toolbar">
        <SearchBar class="products-search" @search="searchQuery = $event" />
      </div>

      <!-- Products Grid -->
      <div v-if="filteredProducts.length" class="grid grid-4 products-grid">
        <ProductCard
          v-for="product in filteredProducts"
          :key="product.id"
          :product="product"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <Icon name="mdi:package-variant-remove" class="empty-icon" />
        <p>لا توجد منتجات مطابقة للبحث</p>
        <button class="btn btn-outline" @click="clearFilters">عرض جميع المنتجات</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { products, categories } from '~/data/store'

const route = useRoute()
const router = useRouter()

const searchQuery = ref('')
const activeCategory = computed(() => (route.query.category as string) || '')

const filteredProducts = computed(() => {
  let result = products

  if (activeCategory.value) {
    result = result.filter((p) => p.category === activeCategory.value)
  }

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(
      (p) =>
        p.name.includes(q) ||
        p.description.includes(q) ||
        p.category.includes(q)
    )
  }

  const headerQuery = route.query.q as string
  if (headerQuery) {
    const q = headerQuery.toLowerCase()
    result = result.filter(
      (p) =>
        p.name.includes(q) ||
        p.description.includes(q)
    )
  }

  return result
})

function getCategoryName(slug: string) {
  return categories.find((c) => c.slug === slug)?.name || 'المنتجات'
}

function getCategoryCount(slug: string) {
  return products.filter((p) => p.category === slug).length
}

function setCategory(slug: string) {
  router.push({ query: { category: slug } })
}

function clearFilters() {
  searchQuery.value = ''
  router.push({ path: '/products' })
}
</script>

<style scoped>
/* Products Hero */
.products-hero {
  position: relative;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  padding: 40px 0 36px;
  overflow: hidden;
}

.hero-bg-shapes .shape {
  position: absolute;
  border-radius: 50%;
  opacity: 0.07;
  background: var(--color-primary);
}

.shape-1 {
  width: 300px;
  height: 300px;
  top: -120px;
  left: 10%;
  animation: floatShape 8s ease-in-out infinite;
}

.shape-2 {
  width: 200px;
  height: 200px;
  bottom: -80px;
  right: 15%;
  animation: floatShape 10s ease-in-out infinite reverse;
}

.shape-3 {
  width: 120px;
  height: 120px;
  top: 20%;
  right: 30%;
  opacity: 0.04;
  animation: floatShape 6s ease-in-out infinite;
}

@keyframes floatShape {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-15px) scale(1.05); }
}

.hero-content {
  position: relative;
  z-index: 2;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-bottom: 16px;
  font-size: 0.78rem;
  color: rgba(255, 255, 255, 0.45);
}

.breadcrumb a {
  color: rgba(255, 255, 255, 0.5);
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumb a:hover {
  color: rgba(255, 255, 255, 0.85);
}

.breadcrumb-sep {
  font-size: 0.7rem;
  opacity: 0.5;
}

.hero-title {
  font-size: 2rem;
  font-weight: 800;
  color: #fff;
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}

.hero-line {
  width: 50px;
  height: 3px;
  background: linear-gradient(90deg, var(--color-primary), #f48fb1);
  border-radius: 3px;
  margin-bottom: 12px;
  position: relative;
  overflow: hidden;
}

.hero-line::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
  animation: shimmer 2.5s ease-in-out infinite;
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 200%; }
}

.hero-subtitle {
  color: rgba(255, 255, 255, 0.55);
  font-size: 0.88rem;
}

/* Category Tabs */
.category-tabs-wrapper {
  position: sticky;
  top: 56px;
  z-index: 90;
  background: #fff;
  border-bottom: 1px solid var(--color-border);
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
}

.category-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  padding: 12px 0;
}

@media (max-width: 768px) {
  .category-tabs {
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding: 10px 0;
    gap: 6px;
  }

  .category-tabs::-webkit-scrollbar {
    display: none;
  }
}

.tab {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 18px;
  border-radius: 100px;
  background: var(--color-bg-alt);
  border: 1.5px solid var(--color-border);
  font-family: inherit;
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--color-text);
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.25s ease;
}

.tab:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
  background: rgba(194, 24, 91, 0.04);
}

.tab.active {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: #fff;
  box-shadow: 0 2px 12px rgba(194, 24, 91, 0.3);
}

.tab-count {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 1px 7px;
  border-radius: 100px;
  background: rgba(0, 0, 0, 0.06);
  color: var(--color-text-light);
  transition: all 0.25s ease;
}

.tab.active .tab-count {
  background: rgba(255, 255, 255, 0.25);
  color: #fff;
}

/* Content */
.products-content {
  padding-top: var(--space-xl);
  padding-bottom: var(--space-2xl);
}

.toolbar {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  margin-bottom: var(--space-xl);
}

.products-search {
  max-width: 320px;
}

.products-grid {
  margin-top: var(--space-md);
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: var(--space-3xl) 0;
  color: var(--color-text-light);
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: var(--space-md);
  opacity: 0.4;
}

.empty-state p {
  margin-bottom: var(--space-lg);
  font-size: var(--font-size-lg);
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 1.4rem;
  }

  .products-search {
    max-width: 100%;
    width: 100%;
  }

  .toolbar {
    justify-content: stretch;
  }

  /* Add bottom padding so content isn't hidden behind the bottom nav */
  .products-content {
    padding-bottom: calc(var(--space-2xl) + 70px);
  }

  .tab {
    padding: 7px 14px;
    font-size: 0.78rem;
  }
}
</style>

