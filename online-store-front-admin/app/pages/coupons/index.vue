<script setup lang="ts">
definePageMeta({
  title: 'الكوبونات',
  description: 'صفحة مبدئية لإدارة العروض والكوبونات.',
})

const coupons = [
  { code: 'SPRING10', value: '10%', status: 'نشط' },
  { code: 'WELCOME25', value: '25 د.ل', status: 'نشط' },
] as const
</script>

<template>
  <div class="grid">
    <section class="surface-card panel">
      <div class="page-heading">
        <div>
          <span class="section-eyebrow">Coupons</span>
          <h2>إدارة الكوبونات</h2>
        </div>
        <p>نقطة بداية مناسبة لإضافة جدول الكوبونات وربط نموذج الإنشاء والتعديل.</p>
      </div>

      <div class="coupon-list">
        <article v-for="item in coupons" :key="item.code" class="coupon-item">
          <strong>{{ item.code }}</strong>
          <span>{{ item.value }}</span>
          <span>{{ item.status }}</span>
        </article>
      </div>
    </section>
  </div>
</template>

<style scoped>
.panel {
  padding: 24px;
}

.coupon-list {
  display: grid;
  gap: 12px;
  margin-top: 18px;
}

.coupon-item {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
  padding: 16px 18px;
  border-radius: 18px;
  background: rgba(244, 247, 251, 0.94);
}

@media (max-width: 640px) {
  .coupon-item {
    grid-template-columns: 1fr;
  }
}
</style>
