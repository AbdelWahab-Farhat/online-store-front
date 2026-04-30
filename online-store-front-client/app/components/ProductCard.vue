<template>
  <NuxtLink :to="`/products/${product.slug}`" class="product-card">
    <!-- Badge -->
    <span
      v-if="product.has_discount"
      class="badge badge-sale"
    >
      تخفيض
    </span>

    <!-- Favorite Button -->
    <button
      class="fav-btn"
      :class="{ 'is-fav': isFavorite(product.id) }"
      @click.prevent="toggleFavorite(product.id)"
      :aria-label="isFavorite(product.id) ? 'إزالة من المفضلة' : 'إضافة للمفضلة'"
    >
      <Icon :name="isFavorite(product.id) ? 'mdi:heart' : 'mdi:heart-outline'" />
    </button>

    <!-- Quick Add Button -->
    <button class="quick-add" @click.prevent="addToCart(product)" aria-label="أضف للسلة">
      <Icon name="mdi:cart-plus" />
    </button>

    <!-- Image -->
    <div class="product-image-wrapper">
      <img
        v-if="!imageError"
        :src="productImage"
        :alt="product.name"
        class="product-image"
        loading="lazy"
        @error="imageError = true"
      />
      <img
        v-else
        src="/images/placeholder.png"
        :alt="product.name"
        class="product-image placeholder-img"
      />
    </div>

    <!-- Info -->
    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      <div class="product-price">
        <span class="price-current">{{ product.price }} د.ل</span>
        <span v-if="product.compare_price" class="price-old">{{ product.compare_price }} د.ل</span>
      </div>
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
import type { Product } from '~/stores/products'

const props = defineProps<{
  product: Product
}>()

const { isFavorite, toggleFavorite } = useFavorites()
const { addToCart } = useCart()

const imageError = ref(false)

const productImage = computed(() => {
  if (props.product.images?.length && props.product.images[0]?.url) {
    return props.product.images[0].url
  }
  return '/images/placeholder.png'
})

// Reset error state when product changes
watch(() => props.product.id, () => {
  imageError.value = false
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

/* Favorite Button */
.fav-btn {
  position: absolute;
  top: 8px;
  left: 8px;
  z-index: 3;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  color: #444;
  cursor: pointer;
  border: none;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.fav-btn.is-fav {
  color: #c9a84c;
}

.fav-btn:hover {
  transform: scale(1.15);
  color: #c9a84c;
}

/* Quick Add Button */
.quick-add {
  position: absolute;
  bottom: 8px;
  left: 8px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #fff;
  color: #222;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  z-index: 3;
  border: none;
  cursor: pointer;
}

.quick-add:hover {
  background: #c9a84c;
  color: #fff;
  transform: scale(1.1);
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

.placeholder-img {
  object-fit: contain;
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
  line-clamp: 2;
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
