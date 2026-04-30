<template>
  <header class="header" :class="{ scrolled: isScrolled }">
    <!-- Top Bar: Logo + Search + Icons -->
    <div class="header-top">
      <div class="container header-top-inner">
        <!-- Logo -->
        <NuxtLink to="/" class="logo">
          <div class="logo-avatar">
            <img src="/images/new-logo.jpeg" alt="متجر كيان" class="logo-img" />
          </div>
          <span class="logo-text">متجر كيان</span>
        </NuxtLink>

        <!-- Search -->
        <div class="header-search">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="ابحث عن منتجات..."
            class="search-input"
            @keyup.enter="handleSearch"
          />
          <button class="search-btn" @click="handleSearch" aria-label="بحث">
            <Icon name="mdi:magnify" />
          </button>
          <button v-if="searchQuery" class="search-clear" @click="searchQuery = ''">
            <Icon name="mdi:close" />
          </button>
        </div>

        <!-- Action Icons -->
        <div class="header-actions">
          <NuxtLink to="/favorites" class="action-btn" aria-label="المفضلة">
            <Icon :name="favoritesCount > 0 ? 'mdi:heart' : 'mdi:heart-outline'" :class="{ 'has-favs': favoritesCount > 0 }" />
            <span v-if="favoritesCount > 0" class="action-badge">{{ favoritesCount }}</span>
          </NuxtLink>
          <NuxtLink to="/Cart" class="action-btn" aria-label="سلة التسوق">
            <Icon name="mdi:cart-outline" />
            <span v-if="cartCount > 0" class="action-badge">{{ cartCount }}</span>
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Bottom Bar: Categories Navigation -->
    <div class="header-nav-bar">
      <div class="container header-nav-inner">
        <NuxtLink to="/" class="nav-link" exact-active-class="nav-active">
          <Icon name="mdi:home-outline" class="nav-link-icon" />
          الرئيسية
        </NuxtLink>
        <NuxtLink
          v-for="cat in categories"
          :key="cat.id"
          :to="`/products?category=${cat.slug}`"
          class="nav-link"
          :class="{ 'nav-active': route.query.category === cat.slug }"
          :active-class="''"
          :exact-active-class="''"
        >
          {{ cat.name }}
        </NuxtLink>
      </div>
    </div>
  </header>

  <!-- Mobile Bottom Navigation -->
  <nav class="mobile-bottom-nav">
    <NuxtLink to="/" class="mobile-nav-item">
      <Icon name="mdi:home-outline" />
      <span>الرئيسية</span>
    </NuxtLink>
    <NuxtLink to="/products" class="mobile-nav-item">
      <Icon name="mdi:shopping-outline" />
      <span>المنتجات</span>
    </NuxtLink>
    <NuxtLink to="/favorites" class="mobile-nav-item">
      <div class="mobile-cart-wrap">
        <Icon :name="favoritesCount > 0 ? 'mdi:heart' : 'mdi:heart-outline'" :class="{ 'has-favs-mobile': favoritesCount > 0 }" />
        <span v-if="favoritesCount > 0" class="mobile-cart-badge">{{ favoritesCount }}</span>
      </div>
      <span>المفضلة</span>
    </NuxtLink>
    <NuxtLink to="/Cart" class="mobile-nav-item">
      <div class="mobile-cart-wrap">
        <Icon name="mdi:cart-outline" />
        <span v-if="cartCount > 0" class="mobile-cart-badge">{{ cartCount }}</span>
      </div>
      <span>سلتي</span>
    </NuxtLink>
  </nav>

  <!-- Mobile Search Overlay -->
  <Transition name="slide-down">
    <div v-if="showMobileSearch" class="mobile-search-overlay">
      <div class="mobile-search-bar">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="ابحث عن منتجات..."
          class="mobile-search-input"
          @keyup.enter="handleMobileSearch"
          ref="mobileSearchInput"
        />
        <button class="mobile-search-close" @click="showMobileSearch = false">
          <Icon name="mdi:close" />
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { useCategoriesStore } from '~/stores/categories'

const route = useRoute()
const router = useRouter()
const { cartCount } = useCart()
const { favoritesCount } = useFavorites()
const categoriesStore = useCategoriesStore()
const { categories } = storeToRefs(categoriesStore)
const searchQuery = ref('')
const isScrolled = ref(false)
const showMobileSearch = ref(false)
const mobileSearchInput = ref<HTMLInputElement | null>(null)

function handleSearch() {
  if (searchQuery.value.trim()) {
    router.push({ path: '/products', query: { search: searchQuery.value.trim() } })
    searchQuery.value = ''
  }
}

function handleMobileSearch() {
  if (searchQuery.value.trim()) {
    router.push({ path: '/products', query: { search: searchQuery.value.trim() } })
    searchQuery.value = ''
    showMobileSearch.value = false
  }
}

function toggleMobileSearch() {
  showMobileSearch.value = !showMobileSearch.value
  if (showMobileSearch.value) {
    nextTick(() => {
      mobileSearchInput.value?.focus()
    })
  }
}

onMounted(() => {
  categoriesStore.fetchCategories()
  window.addEventListener('scroll', () => {
    isScrolled.value = window.scrollY > 20
  })
})
</script>

<style scoped>
/* ==========================================
   SHEIN-Style Header
   ========================================== */

/* Top Bar */
.header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: #fff;
  transition: box-shadow 0.3s ease;
}

.header.scrolled {
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
}

.header-top {
  border-bottom: 1px solid #f0f0f0;
}

.header-top-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  gap: 20px;
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
  text-decoration: none;
}

.logo-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid var(--color-primary);
  box-shadow: 0 4px 12px rgba(201, 168, 76, 0.2);
  flex-shrink: 0;
}

.logo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.logo-text {
  font-size: 1.3rem;
  font-weight: 800;
  background: linear-gradient(135deg, #1a1a2e 0%, #c9a84c 55%, #ddc070 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  white-space: nowrap;
}

/* Search */
.header-search {
  flex: 1;
  max-width: 600px;
  position: relative;
  display: flex;
  align-items: center;
}

.search-input {
  width: 100%;
  height: 42px;
  padding: 0 110px 0 40px;
  border: 2px solid #222;
  border-radius: 24px;
  font-family: var(--font-family);
  font-size: 0.9rem;
  color: var(--color-text);
  background: #fff;
  outline: none;
  transition: all 0.3s ease;
}

.search-input::placeholder {
  color: #999;
}

.search-input:focus {
  border-color: #c9a84c;
  box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.15);
}

.search-btn {
  position: absolute;
  left: 4px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #222;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  transition: all 0.2s ease;
}

.search-btn:hover {
  background: #c9a84c;
}

.search-clear {
  position: absolute;
  left: 46px;
  background: none;
  font-size: 1rem;
  color: #999;
  display: flex;
  align-items: center;
  padding: 4px;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.search-clear:hover {
  color: #c9a84c;
}

/* Action buttons */
.header-actions {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-shrink: 0;
}

.action-btn {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  font-size: 1.35rem;
  color: #333;
  transition: all 0.2s ease;
  text-decoration: none;
}

.action-btn:hover {
  background: #f5f5f5;
  color: #c9a84c;
}

.has-favs {
  color: #c9a84c;
}

.action-badge {
  position: absolute;
  top: 3px;
  left: 3px;
  min-width: 18px;
  height: 18px;
  padding: 0 4px;
  background: #c9a84c;
  color: #fff;
  font-size: 0.6rem;
  font-weight: 700;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

/* Navigation Bar */
.header-nav-bar {
  background: #000;
  overflow-x: auto;
  scrollbar-width: none;
}

.header-nav-bar::-webkit-scrollbar {
  display: none;
}

.header-nav-inner {
  display: flex;
  align-items: center;
  gap: 0;
  white-space: nowrap;
  padding: 0;
}

.nav-link {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 11px 18px;
  font-size: 0.85rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  transition: all 0.2s ease;
  position: relative;
  white-space: nowrap;
}

.nav-link:hover,
.nav-link.nav-active {
  color: #fff;
  background: rgba(255, 255, 255, 0.08);
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  right: 0;
  left: 0;
  height: 2px;
  background: #fff;
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.nav-link:hover::after,
.nav-link.nav-active::after {
  transform: scaleX(1);
}

.nav-link-icon {
  font-size: 1rem;
}

/* ==========================================
   Mobile Bottom Nav
   ========================================== */
.mobile-bottom-nav {
  display: none;
}

@media (max-width: 768px) {
  .mobile-bottom-nav {
    display: flex;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 200;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-top: 1px solid #eee;
    padding: 8px 0 max(8px, env(safe-area-inset-bottom));
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.06);
  }

  .mobile-nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    text-decoration: none;
    color: #888;
    font-size: 0.68rem;
    font-weight: 600;
    font-family: inherit;
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.2s ease;
    padding: 4px 0;
  }

  .mobile-nav-item .iconify {
    font-size: 1.4rem;
  }

  .mobile-nav-item:hover,
  .mobile-nav-item.router-link-exact-active {
    color: #c9a84c;
  }

  .mobile-cart-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .has-favs-mobile {
    color: #c9a84c;
  }

  .mobile-cart-badge {
    position: absolute;
    top: -6px;
    left: -6px;
    width: 16px;
    height: 16px;
    background: #c9a84c;
    color: #fff;
    font-size: 0.58rem;
    font-weight: 700;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}

/* Mobile Search Overlay */
.mobile-search-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 300;
  background: #fff;
  padding: 12px 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.mobile-search-bar {
  display: flex;
  align-items: center;
  gap: 10px;
}

.mobile-search-input {
  flex: 1;
  height: 42px;
  padding: 0 16px;
  border: 2px solid #222;
  border-radius: 24px;
  font-family: var(--font-family);
  font-size: 0.9rem;
  outline: none;
}

.mobile-search-input:focus {
  border-color: #c9a84c;
}

.mobile-search-close {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f0f0f0;
  color: #333;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from,
.slide-down-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

/* ==========================================
   Responsive
   ========================================== */
@media (max-width: 768px) {
  .header-top-inner {
    height: 54px;
    gap: 10px;
  }

  .header-search {
    display: none;
  }

  .logo-img {
    width: 36px;
    height: 36px;
  }

  .logo-text {
    font-size: 1.05rem;
  }

  .header-nav-bar {
    /* Keep visible on mobile — scrollable */
  }

  .action-btn {
    width: 36px;
    height: 36px;
    font-size: 1.2rem;
  }
}
</style>
