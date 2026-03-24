<template>
  <header class="header" :class="{ scrolled: isScrolled }">
    <div class="container header-inner">
      <!-- Logo -->
      <NuxtLink to="/" class="logo">
        <span class="logo-icon">✿</span>
        <span class="logo-text">أناقة</span>
      </NuxtLink>

      <!-- Nav -->
      <nav class="nav" :class="{ open: menuOpen }">
        <NuxtLink to="/" class="nav-link" @click="menuOpen = false">الرئيسية</NuxtLink>
        <NuxtLink to="/products" class="nav-link" @click="menuOpen = false">المنتجات</NuxtLink>
        <NuxtLink to="/products?category=dresses" class="nav-link" @click="menuOpen = false">فساتين</NuxtLink>
        <NuxtLink to="/products?category=bags" class="nav-link" @click="menuOpen = false">حقائب</NuxtLink>
        <NuxtLink to="/products?category=makeup" class="nav-link" @click="menuOpen = false">مكياج</NuxtLink>
      </nav>

      <!-- Actions -->
      <div class="header-actions">
        <SearchBar class="header-search" @search="handleSearch" />
        <button class="menu-toggle" @click="menuOpen = !menuOpen">
          <Icon :name="menuOpen ? 'mdi:close' : 'mdi:menu'" />
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
const router = useRouter()
const menuOpen = ref(false)
const isScrolled = ref(false)

function handleSearch(q: string) {
  if (q) {
    router.push({ path: '/products', query: { q } })
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
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid var(--color-border);
  transition: all var(--transition-base);
}

.header.scrolled {
  box-shadow: var(--shadow-sm);
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  gap: var(--space-lg);
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: var(--font-size-xl);
  font-weight: 800;
  color: var(--color-primary);
  flex-shrink: 0;
}

.logo:hover {
  color: var(--color-primary-dark);
}

.logo-icon {
  font-size: 1.6rem;
}

/* Nav */
.nav {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
}

.nav-link {
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--color-text);
  transition: all var(--transition-fast);
  white-space: nowrap;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: var(--color-primary);
  background: var(--color-bg-soft);
}

/* Actions */
.header-actions {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  flex-shrink: 0;
}

.menu-toggle {
  display: none;
  background: none;
  font-size: 1.5rem;
  color: var(--color-text);
}

@media (max-width: 768px) {
  .header-search {
    display: none;
  }

  .nav {
    position: fixed;
    top: 64px;
    right: 0;
    left: 0;
    bottom: 0;
    background: var(--color-bg);
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: var(--space-lg);
    transform: translateX(100%);
    transition: transform var(--transition-base);
    z-index: 99;
  }

  .nav.open {
    transform: translateX(0);
  }

  .nav-link {
    font-size: var(--font-size-lg);
  }

  .menu-toggle {
    display: flex;
  }
}
</style>
