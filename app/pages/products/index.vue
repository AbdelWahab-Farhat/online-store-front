<template>
  <div class="products-page">
    <div class="container">
      <!-- Page Header -->
      <div class="page-header">
        <h1 class="page-title">
          {{ activeCategory ? getCategoryName(activeCategory) : 'جميع المنتجات' }}
        </h1>
        <p class="page-subtitle">{{ filteredProducts.length }} منتج</p>
      </div>

      <!-- Filters -->
      <div class="filters-bar">
        <div class="filter-chips">
          <button
            class="chip"
            :class="{ active: !activeCategory }"
            @click="clearFilters"
          >
            الكل
          </button>
          <button
            v-for="cat in categories"
            :key="cat.id"
            class="chip"
            :class="{ active: activeCategory === cat.slug }"
            @click="setCategory(cat.slug)"
          >
            <Icon :name="cat.icon" />
            {{ cat.name }}
          </button>
        </div>
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

  // Also check for ?q= from header search
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

function setCategory(slug: string) {
  router.push({ query: { category: slug } })
}

function clearFilters() {
  searchQuery.value = ''
  router.push({ path: '/products' })
}
</script>

<style scoped>
.products-page {
  padding: var(--space-2xl) 0;
}

.page-header {
  margin-bottom: var(--space-xl);
}

.page-title {
  font-size: var(--font-size-2xl);
  font-weight: 800;
}

.page-subtitle {
  color: var(--color-text-light);
  font-size: var(--font-size-sm);
  margin-top: var(--space-xs);
}

/* Filters */
.filters-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-lg);
  margin-bottom: var(--space-xl);
  flex-wrap: wrap;
}

.filter-chips {
  display: flex;
  gap: var(--space-sm);
  flex-wrap: wrap;
}

.chip {
  display: inline-flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-full);
  background: var(--color-bg-card);
  border: 1.5px solid var(--color-border);
  font-family: inherit;
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--color-text);
  transition: all var(--transition-fast);
}

.chip:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
}

.chip.active {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: #fff;
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
  .products-search {
    width: 100%;
    max-width: 100%;
  }
}
</style>
