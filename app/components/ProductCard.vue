<template>
  <NuxtLink :to="`/products/${product.id}`" class="product-card">
    <!-- Badge -->
    <span
      v-if="product.badge"
      class="badge"
      :class="{
        'badge-sale': product.oldPrice,
        'badge-new': !product.oldPrice,
      }"
    >
      {{ product.badge }}
    </span>

    <!-- Image -->
    <div class="product-image-wrapper">
      <img
        :src="product.image"
        :alt="product.name"
        class="product-image"
        loading="lazy"
      />
    </div>

    <!-- Info -->
    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      <div class="product-price">
        <span class="price-current">{{ product.price }} د.ل</span>
        <span v-if="product.oldPrice" class="price-old">{{ product.oldPrice }} د.ل</span>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
defineProps({
  product: {
    type: Object,
    required: true,
  },
})
</script>

<style scoped>
.product-card {
  position: relative;
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  overflow: hidden;
  transition: all var(--transition-base);
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  border-color: transparent;
}

.badge {
  position: absolute;
  top: var(--space-md);
  right: var(--space-md);
  z-index: 2;
}

.product-image-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 1;
  overflow: hidden;
  background: var(--color-bg-soft);
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.product-info {
  padding: var(--space-md);
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
  flex: 1;
}

.product-name {
  font-size: var(--font-size-sm);
  font-weight: 700;
  color: var(--color-text);
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-price {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-top: auto;
}

.price-current {
  font-size: var(--font-size-lg);
  font-weight: 800;
  color: var(--color-primary);
}

.price-old {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  text-decoration: line-through;
}
</style>
