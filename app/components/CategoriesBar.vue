<template>
  <div class="categories-bar">
    <div class="categories-track">
      <NuxtLink
        v-for="cat in categories"
        :key="cat.id"
        :to="`/products?category=${cat.slug}`"
        class="category-item"
      >
        <div class="category-circle">
          <img :src="cat.image" :alt="cat.name" class="category-img" loading="lazy" />
          <div class="category-overlay"></div>
        </div>
        <span class="category-label">{{ cat.name }}</span>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { categories } from '~/data/store'
</script>

<style scoped>
.categories-bar {
  background: linear-gradient(180deg, #fff 0%, #fdf2f4 100%);
  border-bottom: 1px solid rgba(194, 24, 91, 0.06);
  padding: 28px 0 24px;
}

.categories-track {
  display: flex;
  align-items: flex-start;
  justify-content: center;
  gap: 0;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  padding: 0 24px;
}

.categories-track::-webkit-scrollbar {
  display: none;
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  flex: 1;
  min-width: 0;
  max-width: 160px;
  padding: 4px 8px;
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.category-item:hover {
  transform: translateY(-4px);
}

.category-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  position: relative;
  border: 3px solid transparent;
  background: linear-gradient(#fff, #fff) padding-box,
              linear-gradient(135deg, var(--color-primary), #e8a0b4) border-box;
  box-shadow: 0 4px 15px rgba(194, 24, 91, 0.1);
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.category-item:hover .category-circle {
  box-shadow: 0 8px 25px rgba(194, 24, 91, 0.2);
  transform: scale(1.06);
}

.category-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 60%, rgba(194, 24, 91, 0.08) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
  border-radius: 50%;
}

.category-item:hover .category-overlay {
  opacity: 1;
}

.category-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.category-item:hover .category-img {
  transform: scale(1.12);
}

.category-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--color-text);
  text-align: center;
  transition: color 0.3s ease;
  white-space: nowrap;
}

.category-item:hover .category-label {
  color: var(--color-primary);
}

/* Responsive */
@media (max-width: 768px) {
  .categories-bar {
    padding: 20px 0 16px;
  }
  .categories-track {
    justify-content: flex-start;
    gap: 0;
    padding: 0 12px;
  }
  .category-item {
    flex: 0 0 auto;
    min-width: 80px;
    max-width: 100px;
  }
  .category-circle {
    width: 66px;
    height: 66px;
  }
}

@media (max-width: 480px) {
  .category-circle {
    width: 58px;
    height: 58px;
  }
  .category-label {
    font-size: 0.7rem;
  }
}
</style>
