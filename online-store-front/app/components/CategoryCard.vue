<template>
  <NuxtLink :to="`/products?category=${category.slug}`" class="category-card">
    <div class="category-icon">
      <Icon :name="category.icon" />
    </div>
    <span class="category-name">{{ category.name }}</span>
  </NuxtLink>
</template>

<script setup lang="ts">
defineProps({
  category: {
    type: Object,
    required: true,
  },
})
</script>

<style scoped>
.category-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-md);
  padding: var(--space-xl) var(--space-md);
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  transition: all var(--transition-base);
  cursor: pointer;
}

.category-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
  border-color: var(--color-primary);
  color: var(--color-primary);
}

.category-icon {
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-full);
  background: var(--color-bg-soft);
  font-size: 1.6rem;
  color: var(--color-primary);
  transition: all var(--transition-base);
}

.category-card:hover .category-icon {
  background: var(--color-primary);
  color: #fff;
}

.category-name {
  font-size: var(--font-size-sm);
  font-weight: 700;
}
</style>
