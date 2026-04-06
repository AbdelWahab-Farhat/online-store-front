<script setup lang="ts">
definePageMeta({
  title: 'لوحة التحكم',
  description: 'عرض سريع للمؤشرات الرئيسية داخل إدارة المتجر.',
})

const authStore = useAuthStore()

const stats = [
  {
    label: 'طلبات اليوم',
    value: '18',
    note: '6 طلبات ما زالت قيد المعالجة.',
  },
  {
    label: 'منتجات تحتاج تحديث',
    value: '11',
    note: 'صور أو أسعار أو مخزون يحتاج مراجعة.',
  },
  {
    label: 'كوبونات فعالة',
    value: '4',
    note: 'عرضان منها يقتربان من تاريخ الانتهاء.',
  },
  {
    label: 'رسائل متابعة',
    value: '12',
    note: 'استفسارات مرتبطة بالدفع والشحن.',
  },
] as const

const quickLinks = [
  {
    label: 'فتح الطلبات',
    description: 'مراجعة آخر الطلبات وتحديث الحالات.',
    to: '/orders',
  },
  {
    label: 'إدارة المنتجات',
    description: 'إضافة أو تعديل المنتجات والمخزون.',
    to: '/products',
  },
  {
    label: 'التصنيفات',
    description: 'ترتيب أقسام المتجر وإبراز التصنيفات الرئيسية.',
    to: '/categories',
  },
  {
    label: 'الكوبونات',
    description: 'إنشاء العروض وإدارة نسب الخصم.',
    to: '/coupons',
  },
] as const
</script>

<template>
  <div class="grid">
    <section class="surface-card hero-panel">
      <div>
        <span class="section-eyebrow">لوحة تشغيل سريعة</span>
        <h2>الواجهة أصبحت الآن مبنية كمنطقة إدارة حقيقية</h2>
        <p>
          `Sidebar + AppBar` ثابتان، والمصادقة مرتبطة مع Laravel عبر `axios` و`pinia`
          و`cookies`، ويمكننا الآن توسيع الصفحات الداخلية بسهولة.
        </p>
      </div>

      <div class="hero-actions">
        <NuxtLink to="/orders" class="button-primary">عرض الطلبات</NuxtLink>
        <NuxtLink to="/products" class="button-secondary">المنتجات</NuxtLink>
      </div>
    </section>

    <section class="stats-grid">
      <article v-for="item in stats" :key="item.label" class="surface-card stat-card">
        <span class="stat-label">{{ item.label }}</span>
        <strong class="stat-value">{{ item.value }}</strong>
        <p class="stat-note">{{ item.note }}</p>
      </article>
    </section>

    <section class="content-grid">
      <article class="surface-card content-card">
        <div class="page-heading">
          <div>
            <span class="section-eyebrow">تنقل سريع</span>
            <h2>اختصارات العمل اليومي</h2>
          </div>
          <p>روابط مباشرة إلى أهم الصفحات الحالية داخل لوحة الإدارة.</p>
        </div>

        <div class="quick-grid">
          <NuxtLink v-for="item in quickLinks" :key="item.to" :to="item.to" class="quick-link">
            <strong>{{ item.label }}</strong>
            <span>{{ item.description }}</span>
          </NuxtLink>
        </div>
      </article>

      <article class="surface-card content-card">
        <div class="page-heading">
          <div>
            <span class="section-eyebrow">جلسة الدخول</span>
            <h2>حالة المصادقة</h2>
          </div>
          <p>المستخدم الحالي محفوظ في المتجر والكوكيز ومتزامن مع التوكن.</p>
        </div>

        <div class="session-card">
          <div class="session-row">
            <span>الاسم</span>
            <strong>{{ authStore.user?.name || 'غير متوفر' }}</strong>
          </div>
          <div class="session-row">
            <span>البريد</span>
            <strong>{{ authStore.user?.email || 'غير متوفر' }}</strong>
          </div>
          <div class="session-row">
            <span>الدور</span>
            <strong>{{ authStore.user?.role_label || 'غير متوفر' }}</strong>
          </div>
        </div>
      </article>
    </section>
  </div>
</template>

<style scoped>
.hero-panel,
.content-card,
.stat-card {
  padding: 24px;
}

.hero-panel {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
}

.hero-panel h2 {
  margin: 14px 0 10px;
  line-height: 1.2;
}

.hero-panel p {
  margin: 0;
  max-width: 58ch;
  color: var(--color-slate-700);
  line-height: 1.9;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.stat-card {
  display: grid;
  gap: 12px;
}

.stat-label {
  color: var(--color-slate-700);
  font-weight: 700;
}

.stat-value {
  font-size: 2rem;
  line-height: 1.2;
}

.stat-note {
  margin: 0;
  color: var(--color-slate-700);
  line-height: 1.8;
}

.quick-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
  margin-top: 18px;
}

.quick-link {
  display: grid;
  gap: 8px;
  padding: 18px;
  border: 1px solid rgba(213, 222, 234, 0.94);
  border-radius: 18px;
  background: rgba(244, 247, 251, 0.9);
  transition:
    transform var(--transition-base),
    border-color var(--transition-base);
}

.quick-link:hover {
  transform: translateY(-3px);
  border-color: rgba(196, 107, 23, 0.3);
}

.quick-link span {
  color: var(--color-slate-700);
  line-height: 1.8;
}

.session-card {
  display: grid;
  gap: 12px;
  margin-top: 18px;
}

.session-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px;
  border-radius: 16px;
  background: rgba(244, 247, 251, 0.9);
}

.session-row span {
  color: var(--color-slate-700);
}

@media (max-width: 760px) {
  .hero-panel,
  .session-row {
    flex-direction: column;
    align-items: start;
  }

  .quick-grid {
    grid-template-columns: 1fr;
  }
}
</style>
