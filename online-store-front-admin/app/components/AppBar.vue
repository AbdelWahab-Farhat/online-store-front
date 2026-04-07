<script setup lang="ts">
import {
  Package,
  ShoppingCart,
  Tag,
  Ticket,
  CalendarDays,
  User,
} from 'lucide-vue-next'
import { adminNavigation } from '~/data/admin-navigation'

const route = useRoute()
const authStore = useAuthStore()

const userName = computed(() => authStore.user?.name || 'مدير المتجر')

const currentDate = computed(() =>
  new Intl.DateTimeFormat('ar-EG', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
  }).format(new Date()),
)

function isActiveLink(path: string) {
  return route.path === path || route.path.startsWith(`${path}/`)
}

const iconMap: Record<string, any> = {
  Package,
  ShoppingCart,
  Tag,
  Ticket,
}
</script>

<template>
  <header class="top-navbar">
    <div class="navbar-inner">
      <!-- Right Section: Menu Toggle + Logo -->
      <div class="navbar-start">
        <NuxtLink to="/products" class="navbar-brand">
          <span class="brand-logo">OS</span>
          <span class="brand-text">Online Store</span>
        </NuxtLink>
      </div>

      <!-- Center Section: Quick Links -->
      <nav class="navbar-center">
        <NuxtLink
          v-for="item in adminNavigation"
          :key="item.to"
          :to="item.to"
          class="quick-nav-link"
          :class="{ 'is-active': isActiveLink(item.to) }"
        >
          <component :is="iconMap[item.icon]" :size="17" :stroke-width="2" />
          <span class="quick-nav-label">{{ item.label }}</span>
        </NuxtLink>
      </nav>

      <!-- Left Section: Date + Profile -->
      <div class="navbar-end">
        <span class="date-badge">
          <CalendarDays :size="14" :stroke-width="2" />
          {{ currentDate }}
        </span>
        <div class="profile-chip">
          <span class="profile-name">{{ userName }}</span>
          <span class="profile-avatar">
            <User :size="16" :stroke-width="2.2" />
          </span>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
.top-navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  padding: 10px 24px;
  background: rgba(255, 255, 255, 0.72);
  backdrop-filter: blur(24px) saturate(1.4);
  border-bottom: 1px solid rgba(213, 222, 234, 0.6);
}

.navbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  max-width: var(--container-max);
  margin: 0 auto;
  min-height: 56px;
}

/* ─── Right: Logo ─── */
.navbar-start {
  display: flex;
  align-items: center;
  gap: 14px;
}

.navbar-brand {
  display: flex;
  align-items: center;
  gap: 10px;
  transition: transform var(--transition-base);
}

.navbar-brand:hover {
  transform: translateY(-1px);
}

.brand-logo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--color-amber-700), var(--color-amber-500));
  color: var(--color-white);
  font-weight: 800;
  font-size: 0.82rem;
  box-shadow: 0 4px 12px rgba(196, 107, 23, 0.25);
}

.brand-text {
  font-weight: 800;
  font-size: 1rem;
  color: var(--color-slate-950);
  letter-spacing: -0.01em;
}

/* ─── Center: Quick Nav ─── */
.navbar-center {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px;
  border-radius: 16px;
  background: rgba(20, 32, 51, 0.04);
}

.quick-nav-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 12px;
  font-size: 0.88rem;
  font-weight: 700;
  color: var(--color-slate-700);
  transition: all var(--transition-base);
  white-space: nowrap;
}

.quick-nav-link:hover {
  color: var(--color-slate-950);
  background: rgba(255, 255, 255, 0.8);
}

.quick-nav-link.is-active {
  color: var(--color-amber-700);
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

/* ─── Left: Date + Profile ─── */
.navbar-end {
  display: flex;
  align-items: center;
  gap: 12px;
}

.date-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 10px;
  background: rgba(20, 32, 51, 0.05);
  color: var(--color-slate-700);
  font-size: 0.82rem;
  font-weight: 700;
  white-space: nowrap;
}

.profile-chip {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 5px;
  padding-inline-start: 14px;
  border-radius: 999px;
  background: rgba(20, 32, 51, 0.06);
  cursor: default;
  transition: background var(--transition-base);
}

.profile-chip:hover {
  background: rgba(20, 32, 51, 0.1);
}

.profile-avatar {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-slate-900), var(--color-slate-800));
  color: var(--color-white);
}

.profile-name {
  font-size: 0.88rem;
  font-weight: 700;
  color: var(--color-slate-950);
  white-space: nowrap;
}

/* ─── Responsive ─── */
@media (max-width: 1024px) {
  .navbar-center {
    display: none;
  }
}

@media (max-width: 640px) {
  .top-navbar {
    padding: 8px 14px;
  }

  .brand-text,
  .date-badge {
    display: none;
  }

  .profile-name {
    display: none;
  }

  .profile-chip {
    padding: 4px;
  }
}
</style>
