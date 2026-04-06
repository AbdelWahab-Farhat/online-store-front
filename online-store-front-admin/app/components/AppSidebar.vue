<script setup lang="ts">
import { adminNavigation } from '~/data/admin-navigation'

const route = useRoute()
const authStore = useAuthStore()

const userName = computed(() => authStore.user?.name || 'مدير المتجر')
const roleLabel = computed(() => authStore.user?.role_label || 'إدارة النظام')
const isLoggingOut = ref(false)

function isActiveLink(path: string) {
  if (path === '/') {
    return route.path === '/'
  }

  return route.path === path || route.path.startsWith(`${path}/`)
}

async function handleLogout() {
  isLoggingOut.value = true

  try {
    await authStore.logout()
    await navigateTo('/login')
  } finally {
    isLoggingOut.value = false
  }
}
</script>

<template>
  <aside class="admin-sidebar">
    <div class="sidebar-top">
      <NuxtLink to="/" class="brand-card">
        <span class="brand-mark">OS</span>
        <div class="brand-copy">
          <strong>Online Store</strong>
          <span>لوحة إدارة المتجر</span>
        </div>
      </NuxtLink>

      <div class="manager-card">
        <span class="manager-avatar">{{ userName.charAt(0) }}</span>
        <div class="manager-copy">
          <strong>{{ userName }}</strong>
          <span>{{ roleLabel }}</span>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <NuxtLink
        v-for="item in adminNavigation"
        :key="item.to"
        :to="item.to"
        class="nav-link"
        :class="{ 'is-active': isActiveLink(item.to) }"
      >
        <span class="nav-marker">{{ item.short }}</span>
        <span>{{ item.label }}</span>
      </NuxtLink>
    </nav>

    <div class="sidebar-footer">
      <button type="button" class="logout-button" :disabled="isLoggingOut" @click="handleLogout">
        {{ isLoggingOut ? 'جارٍ تسجيل الخروج...' : 'تسجيل الخروج' }}
      </button>
    </div>
  </aside>
</template>

<style scoped>
.admin-sidebar {
  position: fixed;
  inset-block: 20px;
  inset-inline-end: 20px;
  width: min(var(--sidebar-width), calc(100vw - 24px));
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 24px;
  padding: 22px;
  border: 1px solid rgba(213, 222, 234, 0.92);
  border-radius: 30px;
  background: linear-gradient(180deg, rgba(20, 32, 51, 0.98), rgba(28, 43, 67, 0.95));
  box-shadow: 0 24px 50px rgba(20, 32, 51, 0.24);
  color: #f8fbff;
}

.sidebar-top,
.sidebar-nav,
.sidebar-footer {
  display: grid;
  gap: 16px;
}

.brand-card,
.manager-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.06);
}

.brand-card {
  transition: transform var(--transition-base);
}

.brand-card:hover {
  transform: translateY(-2px);
}

.brand-mark,
.manager-avatar,
.nav-marker {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 18px;
  font-weight: 800;
  flex-shrink: 0;
}

.brand-mark {
  width: 52px;
  height: 52px;
  background: linear-gradient(135deg, var(--color-amber-700), var(--color-amber-500));
  color: var(--color-white);
}

.brand-copy,
.manager-copy {
  display: grid;
  gap: 4px;
}

.brand-copy strong,
.manager-copy strong {
  font-size: 1rem;
}

.brand-copy span,
.manager-copy span {
  color: rgba(248, 251, 255, 0.72);
  font-size: 0.88rem;
}

.manager-avatar {
  width: 46px;
  height: 46px;
  background: rgba(255, 255, 255, 0.14);
  color: var(--color-white);
}

.sidebar-nav {
  align-content: start;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  min-height: 56px;
  padding: 0 14px;
  border: 1px solid transparent;
  border-radius: 18px;
  color: rgba(248, 251, 255, 0.84);
  transition:
    background var(--transition-base),
    transform var(--transition-base),
    border-color var(--transition-base);
}

.nav-link:hover {
  transform: translateX(4px);
  background: rgba(255, 255, 255, 0.08);
}

.nav-link.is-active {
  border-color: rgba(236, 159, 67, 0.28);
  background: linear-gradient(135deg, rgba(236, 159, 67, 0.16), rgba(255, 255, 255, 0.08));
  color: var(--color-white);
}

.nav-marker {
  width: 34px;
  height: 34px;
  background: rgba(255, 255, 255, 0.1);
  font-size: 0.76rem;
}

.logout-button {
  width: 100%;
  min-height: 50px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.08);
  color: #f8fbff;
  font-weight: 800;
  cursor: pointer;
  transition:
    transform var(--transition-base),
    background var(--transition-base);
}

.logout-button:hover {
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 0.14);
}

.logout-button:disabled {
  cursor: wait;
  opacity: 0.7;
}

@media (max-width: 920px) {
  .admin-sidebar {
    position: static;
    width: 100%;
    min-height: auto;
  }
}
</style>
