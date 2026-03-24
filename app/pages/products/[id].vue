<template>
  <div class="product-detail" v-if="product">
    <div class="container">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <NuxtLink to="/">الرئيسية</NuxtLink>
        <Icon name="mdi:chevron-left" />
        <NuxtLink to="/products">المنتجات</NuxtLink>
        <Icon name="mdi:chevron-left" />
        <span>{{ product.name }}</span>
      </nav>

      <div class="detail-grid">
        <!-- Image -->
        <div class="detail-image-wrapper">
          <span
            v-if="product.badge"
            class="badge"
            :class="{ 'badge-sale': product.oldPrice, 'badge-new': !product.oldPrice }"
          >
            {{ product.badge }}
          </span>
          <img
            :src="product.image"
            :alt="product.name"
            class="detail-image"
          />
        </div>

        <!-- Info -->
        <div class="detail-info">
          <h1 class="detail-name">{{ product.name }}</h1>

          <div class="detail-rating">
            <div class="stars">
              <Icon v-for="i in 5" :key="i" name="mdi:star" class="star" :class="{ filled: i <= Math.round(product.rating) }" />
            </div>
            <span>{{ product.rating }}</span>
            <span class="review-count">({{ product.reviews }} تقييم)</span>
          </div>

          <div class="detail-price">
            <span class="price-current">{{ product.price }} د.ل</span>
            <span v-if="product.oldPrice" class="price-old">{{ product.oldPrice }} د.ل</span>
            <span v-if="product.oldPrice" class="price-discount">
              خصم {{ Math.round((1 - product.price / product.oldPrice) * 100) }}٪
            </span>
          </div>

          <p class="detail-description">{{ product.description }}</p>

          <!-- Quantity -->
          <div class="quantity-section">
            <span class="quantity-label">الكمية:</span>
            <div class="quantity-controls">
              <button class="qty-btn" @click="qty = Math.max(1, qty - 1)">
                <Icon name="mdi:minus" />
              </button>
              <span class="qty-value">{{ qty }}</span>
              <button class="qty-btn" @click="qty++">
                <Icon name="mdi:plus" />
              </button>
            </div>
          </div>

          <!-- Actions -->
          <div class="detail-actions">
            <button class="btn btn-primary btn-lg">
              <Icon name="mdi:cart-outline" />
              أضيفي إلى السلة
            </button>
            <button class="btn btn-outline btn-icon">
              <Icon name="mdi:heart-outline" />
            </button>
          </div>

          <!-- Features -->
          <div class="detail-features">
            <div class="feature">
              <Icon name="mdi:truck-fast-outline" />
              <span>توصيل سريع</span>
            </div>
            <div class="feature">
              <Icon name="mdi:shield-check-outline" />
              <span>منتج أصلي 100٪</span>
            </div>
            <div class="feature">
              <Icon name="mdi:cash-refund" />
              <span>إرجاع سهل</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <section class="section" v-if="relatedProducts.length">
        <h2 class="section-title">منتجات مشابهة</h2>
        <div class="grid grid-4" style="margin-top: var(--space-xl);">
          <ProductCard
            v-for="p in relatedProducts"
            :key="p.id"
            :product="p"
          />
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { products } from '~/data/store'

const route = useRoute()
const id = Number(route.params.id)
const product = computed(() => products.find((p) => p.id === id))
const qty = ref(1)

const relatedProducts = computed(() =>
  products
    .filter((p) => p.category === product.value?.category && p.id !== id)
    .slice(0, 4)
)

useHead({
  title: () => product.value ? `${product.value.name} — أناقة` : 'أناقة',
})
</script>

<style scoped>
.product-detail {
  padding: var(--space-xl) 0;
}

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin-bottom: var(--space-xl);
}

.breadcrumb a:hover {
  color: var(--color-primary);
}

/* Detail grid */
.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-2xl);
  align-items: start;
}

/* Image */
.detail-image-wrapper {
  position: relative;
  border-radius: var(--radius-lg);
  overflow: hidden;
  background: var(--color-bg-soft);
}

.detail-image-wrapper .badge {
  position: absolute;
  top: var(--space-md);
  right: var(--space-md);
  z-index: 2;
}

.detail-image {
  width: 100%;
  aspect-ratio: 1;
  object-fit: cover;
}

/* Info */
.detail-info {
  display: flex;
  flex-direction: column;
  gap: var(--space-lg);
}

.detail-name {
  font-size: var(--font-size-2xl);
  font-weight: 800;
  line-height: 1.4;
}

.detail-rating {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: var(--font-size-sm);
}

.stars {
  display: flex;
  gap: 2px;
}

.star {
  color: var(--color-border);
  font-size: 1.1rem;
}

.star.filled {
  color: var(--color-accent);
}

.review-count {
  color: var(--color-text-light);
}

.detail-price {
  display: flex;
  align-items: center;
  gap: var(--space-md);
}

.price-current {
  font-size: var(--font-size-3xl);
  font-weight: 800;
  color: var(--color-primary);
}

.price-old {
  font-size: var(--font-size-lg);
  color: var(--color-text-light);
  text-decoration: line-through;
}

.price-discount {
  background: #fce4ec;
  color: var(--color-primary);
  padding: var(--space-xs) var(--space-sm);
  border-radius: var(--radius-full);
  font-size: var(--font-size-xs);
  font-weight: 700;
}

.detail-description {
  font-size: var(--font-size-base);
  color: var(--color-text-light);
  line-height: 1.8;
}

/* Quantity */
.quantity-section {
  display: flex;
  align-items: center;
  gap: var(--space-md);
}

.quantity-label {
  font-weight: 700;
  font-size: var(--font-size-sm);
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0;
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.qty-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: var(--color-bg-soft);
  color: var(--color-text);
  font-size: 1rem;
  transition: all var(--transition-fast);
}

.qty-btn:hover {
  background: var(--color-primary);
  color: #fff;
}

.qty-value {
  width: 50px;
  text-align: center;
  font-weight: 700;
  font-size: var(--font-size-base);
}

/* Actions */
.detail-actions {
  display: flex;
  gap: var(--space-md);
}

.btn-lg {
  padding: var(--space-md) var(--space-2xl);
  font-size: var(--font-size-base);
  flex: 1;
}

.btn-icon {
  width: 50px;
  height: 50px;
  padding: 0;
  font-size: 1.3rem;
  border-radius: var(--radius-md);
}

/* Features */
.detail-features {
  display: flex;
  gap: var(--space-xl);
  padding-top: var(--space-lg);
  border-top: 1px solid var(--color-border);
}

.feature {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
}

.feature .icon {
  color: var(--color-primary);
  font-size: 1.2rem;
}

@media (max-width: 768px) {
  .detail-grid {
    grid-template-columns: 1fr;
    gap: var(--space-xl);
  }

  .detail-features {
    flex-wrap: wrap;
    gap: var(--space-md);
  }

  .price-current {
    font-size: var(--font-size-2xl);
  }
}
</style>
