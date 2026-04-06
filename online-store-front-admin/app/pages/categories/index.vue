<script setup lang="ts">
definePageMeta({
  title: 'التصنيفات',
  description: 'مساحة أولية لمراجعة أقسام المتجر وإدارتها.',
})
</script>

<template>
  <div class="grid">
    <section class="surface-card panel">
      <div class="page-heading">
        <div>
          <span class="section-eyebrow">Categories</span>
          <h2>إدارة التصنيفات</h2>
        </div>
        <p>صفحة تمهيدية سنربطها لاحقًا بمسارات إنشاء وتحديث وحذف التصنيفات.</p>
      </div>

      <p class="empty-note">
        البنية الحالية جاهزة لإضافة جدول التصنيفات وربطها بالمنتجات عند الخطوة القادمة.
      </p>
    </section>
  </div>
</template>

<style scoped>
.panel {
  padding: 24px;
}

.empty-note {
  margin: 18px 0 0;
}
</style>
