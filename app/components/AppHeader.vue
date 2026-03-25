<template>
  <header class="header" :class="{ scrolled: isScrolled }">
    <div class="container header-inner">
      <!-- Right group: Logo + Nav -->
      <div class="header-right">
        <NuxtLink to="/" class="logo">
          <div class="logo-placeholder">LOGO</div>
        </NuxtLink>
        <span class="nav-divider"></span>
        <nav class="header-nav">
          <NuxtLink to="/" class="nav-link">الرئيسية</NuxtLink>
          <NuxtLink to="/products" class="nav-link">المنتجات</NuxtLink>
        </nav>
      </div>

      <!-- Search (center) -->
      <div class="header-search">
        <Icon name="mdi:magnify" class="search-icon" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="ابحثي عن منتجات..."
          class="search-input"
          @keyup.enter="handleSearch"
        />
        <button v-if="searchQuery" class="search-clear" @click="searchQuery = ''">
          <Icon name="mdi:close" />
        </button>
      </div>

      <!-- Left group: Cart -->
      <div class="header-left">
        <button class="cart-btn" aria-label="سلة التسوق">
          <Icon name="mdi:cart-outline" />
          <span class="cart-count">0</span>
        </button>
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
    <button class="mobile-nav-item">
      <div class="mobile-cart-wrap">
        <Icon name="mdi:cart-outline" />
        <span class="mobile-cart-badge">0</span>
      </div>
      <span>سلتي</span>
    </button>
  </nav>
</template>

<script setup lang="ts">
const router = useRouter()
const searchQuery = ref('')
const isScrolled = ref(false)

function handleSearch() {
  if (searchQuery.value.trim()) {
    router.push({ path: '/products', query: { q: searchQuery.value.trim() } })
  }
}

onMounted(() => {
  window.addEventListener('scroll', () => {
    isScrolled.value = window.scrollY > 20
  })
})
</script>

<style scoped>
.header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(255, 255, 255, 0.97);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--color-border);
  transition: all var(--transition-base);
}

.header.scrolled {
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 56px;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-shrink: 0;
}

.header-left {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

/* Logo */
.logo {
  flex-shrink: 0;
}

.logo-placeholder {
  width: 66px;
  height: 32px;
  border: 2px dashed var(--color-primary);
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.6rem;
  font-weight: 800;
  color: var(--color-primary);
  letter-spacing: 2px;
}

.nav-divider {
  width: 1px;
  height: 20px;
  background: var(--color-border);
}

/* Nav */
.header-nav {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-shrink: 0;
}

.nav-link {
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--color-text);
  text-decoration: none;
  padding: 4px 0;
  position: relative;
  transition: color var(--transition-fast);
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: -2px;
  right: 0;
  left: 0;
  height: 2px;
  background: var(--color-primary);
  transform: scaleX(0);
  transition: transform 0.3s ease;
  transform-origin: right;
  border-radius: 1px;
}

.nav-link:hover,
.nav-link.router-link-exact-active {
  color: var(--color-primary);
}

.nav-link:hover::after,
.nav-link.router-link-exact-active::after {
  transform: scaleX(1);
}

/* Search */
.header-search {
  flex: 1;
  margin: 0 24px;
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  right: 14px;
  font-size: 1.1rem;
  color: var(--color-text-light);
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 9px 38px 9px 36px;
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-full);
  font-family: var(--font-family);
  font-size: var(--font-size-sm);
  color: var(--color-text);
  background: var(--color-bg-soft);
  transition: all var(--transition-fast);
  outline: none;
}

.search-input::placeholder {
  color: var(--color-text-light);
}

.search-input:focus {
  border-color: var(--color-primary);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(194, 24, 91, 0.08);
}

.search-clear {
  position: absolute;
  left: 10px;
  background: none;
  font-size: 1rem;
  color: var(--color-text-light);
  display: flex;
  align-items: center;
  padding: 4px;
  border-radius: 50%;
  transition: all var(--transition-fast);
}

.search-clear:hover {
  background: var(--color-bg-soft);
  color: var(--color-primary);
}

/* Cart */
.cart-btn {
  position: relative;
  background: none;
  font-size: 1.4rem;
  color: var(--color-text);
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  transition: all var(--transition-fast);
  flex-shrink: 0;
}

.cart-btn:hover {
  background: var(--color-bg-soft);
  color: var(--color-primary);
}

.cart-count {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 17px;
  height: 17px;
  background: var(--color-primary);
  color: #fff;
  font-size: 0.6rem;
  font-weight: 700;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

/* Mobile */
@media (max-width: 768px) {
  .header-inner {
    gap: var(--space-xs);
  }
  .header-nav,
  .nav-divider {
    display: none;
  }
  .header-search {
    margin: 0 10px;
  }
  .logo-placeholder {
    width: 56px;
    height: 28px;
    font-size: 0.55rem;
  }
}

/* Mobile Bottom Nav */
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
    border-top: 1px solid var(--color-border);
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
    color: var(--color-text-light);
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
    color: var(--color-primary);
  }

  .mobile-cart-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .mobile-cart-badge {
    position: absolute;
    top: -6px;
    left: -6px;
    width: 16px;
    height: 16px;
    background: var(--color-primary);
    color: #fff;
    font-size: 0.58rem;
    font-weight: 700;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}
</style>
