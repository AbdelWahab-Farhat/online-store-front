<script setup lang="ts">
import {
  Package,
  ShoppingCart,
  Tag,
  Ticket,
  Megaphone,
  CalendarDays,
  User,
  Settings,
  Menu,
  X,
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
  Megaphone,
  Settings,
}

// Mobile Drawer
const isDrawerOpen = ref(false)

function toggleDrawer() {
  isDrawerOpen.value = !isDrawerOpen.value
}

function closeDrawer() {
  isDrawerOpen.value = false
}

// Close drawer on route change
watch(() => route.path, () => {
  closeDrawer()
})
</script>

<template>
  <header class="top-navbar">
    <div class="navbar-inner">
      <!-- Right Section: Menu Toggle + Logo -->
      <div class="navbar-start">
        <button class="menu-toggle" @click="toggleDrawer" aria-label="فتح القائمة">
          <Menu :size="22" :stroke-width="2" />
        </button>
        <NuxtLink to="/products" class="navbar-brand">
          <div class="brand-logo-avatar">
            <img src="/new-logo.jpeg" alt="متجر كيان" class="brand-logo" />
          </div>
          <span class="brand-text">متجر كيان</span>
        </NuxtLink>
      </div>

      <!-- Center Section: Quick Links (Desktop Only) -->
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

  <!-- Mobile Drawer Overlay -->
  <Transition name="overlay-fade">
    <div v-if="isDrawerOpen" class="drawer-overlay" @click="closeDrawer" />
  </Transition>

  <!-- Mobile Drawer -->
  <Transition name="drawer-slide">
    <aside v-if="isDrawerOpen" class="mobile-drawer">
      <div class="drawer-header">
        <NuxtLink to="/products" class="navbar-brand" @click="closeDrawer">
          <div class="brand-logo-avatar">
            <img src="/new-logo.jpeg" alt="متجر كيان" class="brand-logo" />
          </div>
          <span class="brand-text">متجر كيان</span>
        </NuxtLink>
        <button class="drawer-close" @click="closeDrawer" aria-label="إغلاق القائمة">
          <X :size="20" :stroke-width="2.5" />
        </button>
      </div>

      <nav class="drawer-nav">
        <NuxtLink
          v-for="item in adminNavigation"
          :key="item.to"
          :to="item.to"
          class="drawer-link"
          :class="{ 'is-active': isActiveLink(item.to) }"
          @click="closeDrawer"
        >
          <component :is="iconMap[item.icon]" :size="20" :stroke-width="2" />
          <div class="drawer-link-text">
            <span class="drawer-link-label">{{ item.label }}</span>
            <span class="drawer-link-desc">{{ item.description }}</span>
          </div>
        </NuxtLink>
      </nav>

      <div class="drawer-footer">
        <div class="drawer-profile">
          <span class="profile-avatar">
            <User :size="16" :stroke-width="2.2" />
          </span>
          <div class="drawer-profile-info">
            <span class="drawer-profile-name">{{ userName }}</span>
            <span class="drawer-profile-date">{{ currentDate }}</span>
          </div>
        </div>
      </div>
    </aside>
  </Transition>
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

/* ─── Menu Toggle (Mobile Only) ─── */
.menu-toggle {
  display: none;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba(20, 32, 51, 0.06);
  color: var(--color-slate-800);
  cursor: pointer;
  transition: all var(--transition-base);
}

.menu-toggle:hover {
  background: rgba(20, 32, 51, 0.1);
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

.brand-logo-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid #c9a84c;
  box-shadow: 0 4px 12px rgba(201, 168, 76, 0.2);
  flex-shrink: 0;
}

.brand-logo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.brand-text {
  font-weight: 800;
  font-size: 1rem;
  background: linear-gradient(135deg, #142033 0%, #c46b17 55%, #ec9f43 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
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

/* ═══════ Mobile Drawer ═══════ */
.drawer-overlay {
  position: fixed;
  inset: 0;
  z-index: 200;
  background: rgba(20, 32, 51, 0.45);
  backdrop-filter: blur(4px);
}

.mobile-drawer {
  position: fixed;
  top: 0;
  right: 0;
  z-index: 201;
  width: min(320px, 85vw);
  height: 100vh;
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(24px) saturate(1.4);
  border-left: 1px solid rgba(213, 222, 234, 0.6);
  display: flex;
  flex-direction: column;
  box-shadow: -12px 0 40px rgba(20, 32, 51, 0.15);
  overflow-y: auto;
}

.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 20px;
  border-bottom: 1px solid rgba(213, 222, 234, 0.6);
}

.drawer-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(20, 32, 51, 0.06);
  color: var(--color-slate-700);
  cursor: pointer;
  transition: all var(--transition-base);
}

.drawer-close:hover {
  background: rgba(202, 61, 84, 0.1);
  color: var(--color-red-600);
}

.drawer-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 12px;
  gap: 4px;
}

.drawer-link {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
  border-radius: 14px;
  color: var(--color-slate-700);
  transition: all var(--transition-base);
}

.drawer-link:hover {
  background: rgba(20, 32, 51, 0.05);
  color: var(--color-slate-950);
}

.drawer-link.is-active {
  background: rgba(236, 159, 67, 0.1);
  color: var(--color-amber-700);
}

.drawer-link-text {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.drawer-link-label {
  font-size: 0.95rem;
  font-weight: 800;
}

.drawer-link-desc {
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--color-slate-500);
}

.drawer-link.is-active .drawer-link-desc {
  color: var(--color-amber-500);
}

.drawer-footer {
  padding: 16px 20px;
  border-top: 1px solid rgba(213, 222, 234, 0.6);
}

.drawer-profile {
  display: flex;
  align-items: center;
  gap: 12px;
}

.drawer-profile-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.drawer-profile-name {
  font-size: 0.92rem;
  font-weight: 800;
  color: var(--color-slate-950);
}

.drawer-profile-date {
  font-size: 0.78rem;
  color: var(--color-slate-500);
  font-weight: 600;
}

/* ─── Drawer Transitions ─── */
.overlay-fade-enter-active,
.overlay-fade-leave-active {
  transition: opacity 0.3s ease;
}
.overlay-fade-enter-from,
.overlay-fade-leave-to {
  opacity: 0;
}

.drawer-slide-enter-active {
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-slide-leave-active {
  transition: transform 0.25s ease-in;
}
.drawer-slide-enter-from,
.drawer-slide-leave-to {
  transform: translateX(100%);
}

/* ─── Responsive ─── */
@media (max-width: 1024px) {
  .navbar-center {
    display: none;
  }

  .menu-toggle {
    display: flex;
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
