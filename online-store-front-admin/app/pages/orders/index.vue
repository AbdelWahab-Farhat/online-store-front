<script setup lang="ts">
definePageMeta({
  title: 'الطلبات',
  description: 'مساحة أولية لإدارة الطلبات داخل لوحة الإدارة.',
})

const statuses = [
  { label: 'طلبات جديدة', value: '8' },
  { label: 'قيد المعالجة', value: '6' },
  { label: 'تم الشحن', value: '13' },
] as const
</script>

<template>
  <div class="grid">
    <section class="surface-card panel">
      <div class="page-heading">
        <div>
          <span class="section-eyebrow">Orders</span>
          <h2>إدارة الطلبات</h2>
        </div>
        <p>صفحة مهيأة للربط لاحقًا مع `/api/admin/orders` وقوائم الحالة والتصفية.</p>
      </div>

      <div class="mini-stats">
        <article v-for="item in statuses" :key="item.label" class="mini-card">
          <span>{{ item.label }}</span>
          <strong>{{ item.value }}</strong>
        </article>
      </div>
    </section>
  </div>
</template>

<style scoped>
.panel {
  padding: 24px;
}

.mini-stats {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
  margin-top: 18px;
}

.mini-card {
  display: grid;
  gap: 10px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(244, 247, 251, 0.94);
}

.mini-card span {
  color: var(--color-slate-700);
}

.mini-card strong {
  font-size: 1.6rem;
}

@media (max-width: 760px) {
  .mini-stats {
    grid-template-columns: 1fr;
  }
}
</style>
