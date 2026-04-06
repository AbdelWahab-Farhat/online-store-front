<script setup lang="ts">
definePageMeta({
  title: 'المنتجات',
  description: 'واجهة أولية لإدارة المنتجات والمخزون داخل لوحة التحكم.',
})

const highlights = [
  'الصفحة جاهزة لربط إنشاء المنتجات وتحديثها.',
  'يمكن لاحقًا إضافة رفع الصور وربط التصنيفات.',
  'المصادقة جاهزة لإرسال التوكن مع كل طلب إدارة.',
] as const
</script>

<template>
  <div class="grid">
    <section class="surface-card panel">
      <div class="page-heading">
        <div>
          <span class="section-eyebrow">Products</span>
          <h2>إدارة المنتجات</h2>
        </div>
        <p>من هنا سنبني لاحقًا CRUD المنتجات اعتمادًا على API الإدارة الحالية.</p>
      </div>

      <div class="note-list">
        <article v-for="item in highlights" :key="item" class="note-item">
          <strong>{{ item }}</strong>
        </article>
      </div>
    </section>
  </div>
</template>

<style scoped>
.panel {
  padding: 24px;
}

.note-list {
  display: grid;
  gap: 12px;
  margin-top: 18px;
}

.note-item {
  padding: 18px;
  border-radius: 18px;
  background: rgba(244, 247, 251, 0.94);
  line-height: 1.8;
}
</style>
