<script setup lang="ts">
const route = useRoute()
const authStore = useAuthStore()

const pageTitle = computed(() => String(route.meta.title || 'لوحة التحكم'))
const pageDescription = computed(() =>
  String(route.meta.description || 'إدارة يومية سريعة للطلبات والمنتجات والتشغيل.'),
)
const currentDate = computed(() =>
  new Intl.DateTimeFormat('ar-EG', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  }).format(new Date()),
)
const welcomeName = computed(() => authStore.user?.name || 'فريق الإدارة')
</script>

<template>
  <header class="admin-appbar surface-card">
    <div class="appbar-copy">
      <span class="section-eyebrow">مساحة الإدارة</span>
      <h1>{{ pageTitle }}</h1>
      <p>{{ pageDescription }}</p>
    </div>

    <div class="appbar-meta">
      <span class="pill">{{ currentDate }}</span>
      <span class="pill">مرحبًا، {{ welcomeName }}</span>
    </div>
  </header>
</template>

<style scoped>
.admin-appbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  padding: 22px 24px;
  position: sticky;
  top: 20px;
  z-index: 5;
}

.appbar-copy h1 {
  margin: 10px 0 6px;
  font-size: clamp(1.4rem, 2vw, 2rem);
  line-height: 1.2;
}

.appbar-copy p {
  margin: 0;
  color: var(--color-slate-700);
  line-height: 1.8;
}

.appbar-meta {
  display: flex;
  flex-wrap: wrap;
  justify-content: end;
  gap: 10px;
}

@media (max-width: 820px) {
  .admin-appbar {
    flex-direction: column;
    align-items: start;
  }

  .appbar-meta {
    justify-content: start;
  }
}
</style>
