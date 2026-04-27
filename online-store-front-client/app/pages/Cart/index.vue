<template>
  <div class="cart-page">
    <div class="container">
      <!-- Page Header -->
      <div class="page-header">
        <div class="page-header-content">
          <div class="page-title-group">
            <Icon name="mdi:cart-outline" class="page-icon" />
            <div>
              <h1 class="page-title">سلة التسوق</h1>
              <p class="page-subtitle" v-if="cartCount > 0">
                لديك <strong>{{ cartCount }}</strong> {{ cartCount === 1 ? 'منتج' : 'منتجات' }} في سلتك
              </p>
            </div>
          </div>
          <button v-if="cartItems.length > 0" class="clear-cart-btn" @click="showClearConfirm = true">
            <Icon name="mdi:delete-outline" />
            <span>إفراغ السلة</span>
          </button>
        </div>
        <!-- Decorative divider -->
        <div class="section-divider">
          <span class="divider-line"></span>
          <span class="divider-dot"></span>
          <span class="divider-line"></span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="cartItems.length === 0" class="empty-state">
        <div class="empty-icon-wrap">
          <Icon name="mdi:cart-off" class="empty-icon" />
        </div>
        <h2 class="empty-title">سلتك فارغة!</h2>
        <p class="empty-text">لم تضيفي أي منتجات بعد. تصفّحي مجموعتنا واختاري ما يناسبك.</p>
        <NuxtLink to="/products" class="btn btn-primary empty-btn">
          <Icon name="mdi:shopping-outline" />
          تصفّحي المنتجات
        </NuxtLink>
      </div>

      <!-- Cart Content -->
      <div v-else class="cart-content">
        <!-- Cart Items -->
        <div class="cart-items">
          <TransitionGroup name="cart-item" tag="div" class="items-list">
            <div v-for="item in cartItems" :key="item.product.id" class="cart-item">
              <!-- Product Image -->
              <NuxtLink :to="`/products/${item.product.id}`" class="item-image-link">
                <img
                  :src="getProductImage(item.product)"
                  :alt="item.product.name"
                  class="item-image"
                  @error="setImageFallback"
                />
                <span v-if="getProductBadge(item.product)" class="item-badge" :class="{ 'badge-sale': getProductOriginalPrice(item.product) }">
                  {{ getProductBadge(item.product) }}
                </span>
              </NuxtLink>

              <!-- Product Details -->
              <div class="item-details">
                <div class="item-top">
                  <NuxtLink :to="`/products/${item.product.id}`" class="item-name">
                    {{ item.product.name }}
                  </NuxtLink>
                  <button class="item-remove" @click="removeFromCart(item.product.id)" aria-label="حذف المنتج">
                    <Icon name="mdi:close" />
                  </button>
                </div>

                <p class="item-category">
                  <Icon name="mdi:tag-outline" class="category-icon" />
                  {{ getProductCategoryName(item.product) }}
                </p>

                <div class="item-bottom">
                  <!-- Quantity Controls -->
                  <div class="quantity-control">
                    <button
                      class="qty-btn"
                      @click="updateQuantity(item.product.id, item.quantity - 1)"
                      :aria-label="item.quantity <= 1 ? 'حذف المنتج' : 'تقليل الكمية'"
                    >
                      <Icon :name="item.quantity <= 1 ? 'mdi:delete-outline' : 'mdi:minus'" />
                    </button>
                    <span class="qty-value">{{ item.quantity }}</span>
                    <button
                      class="qty-btn qty-btn-add"
                      @click="updateQuantity(item.product.id, item.quantity + 1)"
                      aria-label="زيادة الكمية"
                    >
                      <Icon name="mdi:plus" />
                    </button>
                  </div>

                  <!-- Price -->
                  <div class="item-price-group">
                    <span class="item-price">{{ (item.product.price * item.quantity).toFixed(0) }} د.ل</span>
                    <span v-if="getProductOriginalPrice(item.product)" class="item-old-price">
                      {{ (getProductOriginalPrice(item.product)! * item.quantity).toFixed(0) }} د.ل
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </TransitionGroup>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="cart-summary">
          <div class="summary-card">
            <h3 class="summary-title">
              <Icon name="mdi:receipt-text-outline" />
              ملخص الطلب
            </h3>

            <div class="summary-rows">
              <div class="summary-row">
                <span>المجموع الفرعي</span>
                <span>{{ (cartTotal + cartSavings).toFixed(0) }} د.ل</span>
              </div>
              <div v-if="cartSavings > 0" class="summary-row savings-row">
                <span>
                  <Icon name="mdi:tag-outline" />
                  التوفير
                </span>
                <span class="savings-amount">- {{ cartSavings.toFixed(0) }} د.ل</span>
              </div>
              <div class="summary-row">
                <span>
                  <Icon name="mdi:truck-delivery-outline" />
                  التوصيل
                </span>
                <span class="free-shipping">سيتم تحديده لاحقًا</span>
              </div>
            </div>

            <div class="summary-total">
              <span>الإجمالي</span>
              <span class="total-amount">{{ cartTotal.toFixed(0) }} د.ل</span>
            </div>

            <button class="btn btn-primary checkout-btn" @click="handleCheckout">
              <Icon name="mdi:lock-outline" />
              إتمام الطلب
            </button>

            <div class="trust-badges">
              <div class="trust-item">
                <Icon name="mdi:shield-check-outline" />
                <span>دفع آمن</span>
              </div>
              <div class="trust-item">
                <Icon name="mdi:truck-fast-outline" />
                <span>توصيل سريع</span>
              </div>
              <div class="trust-item">
                <Icon name="mdi:refresh" />
                <span>إرجاع سهل</span>
              </div>
            </div>
          </div>

          <!-- Continue Shopping -->
          <NuxtLink to="/products" class="continue-shopping">
            <Icon name="mdi:arrow-right" />
            متابعة التسوق
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Clear Confirm Modal -->
    <Transition name="modal">
      <div v-if="showClearConfirm" class="modal-overlay" @click.self="showClearConfirm = false">
        <div class="modal-card">
          <div class="modal-icon-wrap">
            <Icon name="mdi:delete-alert-outline" class="modal-icon" />
          </div>
          <h3 class="modal-title">إفراغ السلة؟</h3>
          <p class="modal-text">سيتم حذف جميع المنتجات من سلة التسوق. هل أنتِ متأكدة؟</p>
          <div class="modal-actions">
            <button class="btn btn-outline modal-cancel" @click="showClearConfirm = false">
              إلغاء
            </button>
            <button class="btn btn-primary modal-confirm" @click="confirmClear">
              <Icon name="mdi:delete-outline" />
              نعم، إفراغ السلة
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Checkout Modal -->
    <Transition name="modal">
      <div v-if="showCheckoutModal" class="modal-overlay" @click.self="showCheckoutModal = false">
        <div class="modal-card checkout-modal-card">
          <div class="modal-icon-wrap" style="background: rgba(37, 211, 102, 0.1);">
            <Icon name="mdi:whatsapp" class="modal-icon" style="color: #25d366;" />
          </div>
          <h3 class="modal-title">معلومات الطلب</h3>
          <p class="modal-text">الرجاء إدخال بياناتك لإتمام الطلب عبر واتساب</p>
          
          <form @submit.prevent="submitCheckout" class="checkout-form">
            <div class="form-group">
              <label>الإسم بالكامل <span class="required">*</span></label>
              <input v-model="checkoutForm.name" type="text" autocomplete="name" required placeholder="مثال: أحمد محمد" @input="filterTextOnly($event, 'name')" />
            </div>
            <div class="form-group">
              <label>رقم الهاتف <span class="required">*</span></label>
              <input v-model="checkoutForm.phone" type="tel" autocomplete="tel" required placeholder="مثال: 0912345678" inputmode="numeric" @input="filterPhoneOnly" />
            </div>
            <div class="form-group">
              <label>المدينة <span class="required">*</span></label>
              <input v-model="checkoutForm.city" type="text" autocomplete="address-level2" required placeholder="مثال: طرابلس" @input="filterTextOnly($event, 'city')" />
            </div>
            <div class="form-group">
              <label>المنطقة (اختياري)</label>
              <input v-model="checkoutForm.area" type="text" autocomplete="street-address" placeholder="مثال: حي الأندلس" @input="filterTextOnly($event, 'area')" />
            </div>

            <div class="modal-actions">
              <button type="button" class="btn btn-outline modal-cancel" @click="showCheckoutModal = false">
                إلغاء
              </button>
              <button type="submit" class="btn btn-primary modal-confirm" style="background: #25d366; border-color: #25d366; color: #fff;" :disabled="isSubmitting">
                <Icon v-if="isSubmitting" name="mdi:loading" class="spin" />
                <Icon v-else name="mdi:whatsapp" />
                {{ isSubmitting ? 'جاري التحضير...' : 'متابعة للواتساب' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script lang="ts" setup>
import { categories } from '~/data/store'
import type { CartProduct } from '~/composables/useCart'

const { cartItems, cartCount, cartTotal, cartSavings, removeFromCart, updateQuantity, clearCart } = useCart()

const showClearConfirm = ref(false)
const showCheckoutModal = ref(false)
const isSubmitting = ref(false)
const checkoutForm = reactive({
  name: '',
  phone: '',
  city: '',
  area: ''
})

// Lock body scroll when any modal is open
watch([showClearConfirm, showCheckoutModal], ([clear, checkout]) => {
  if (import.meta.client) {
    document.body.style.overflow = (clear || checkout) ? 'hidden' : ''
  }
})

// Input validation: strip numbers from text fields
function filterTextOnly(event: Event, field: 'name' | 'city' | 'area') {
  const input = event.target as HTMLInputElement
  // Allow Arabic, Latin letters, spaces, hyphens, dots
  const cleaned = input.value.replace(/[0-9٠-٩]/g, '')
  checkoutForm[field] = cleaned
  input.value = cleaned
}

// Input validation: strip non-numeric from phone
function filterPhoneOnly(event: Event) {
  const input = event.target as HTMLInputElement
  // Allow only digits and +
  const cleaned = input.value.replace(/[^0-9+]/g, '')
  checkoutForm.phone = cleaned
  input.value = cleaned
}

onMounted(() => {
  if (import.meta.client) {
    const savedCustomer = localStorage.getItem('checkout_customer_info')
    if (savedCustomer) {
      try {
        const parsed = JSON.parse(savedCustomer)
        checkoutForm.name = parsed.name || ''
        checkoutForm.phone = parsed.phone || ''
        checkoutForm.city = parsed.city || ''
        checkoutForm.area = parsed.area || ''
      } catch (e) {}
    }
  }
})

function getCategoryName(slug?: string | null) {
  if (!slug) {
    return 'تصنيف غير محدد'
  }
  const cat = categories.find((c) => c.slug === slug)
  return cat ? cat.name : slug
}

function getProductImage(product: CartProduct) {
  return product.image || product.images?.find((image) => image?.url)?.url || '/images/placeholder.png'
}

function getProductOriginalPrice(product: CartProduct) {
  return product.oldPrice ?? product.compare_price ?? null
}

function getProductBadge(product: CartProduct) {
  return product.badge ?? (product.has_discount || Boolean(getProductOriginalPrice(product)) ? 'تخفيض' : '')
}

function getProductCategoryName(product: CartProduct) {
  if (product.categories?.length) {
    const categoryNames = product.categories
      .map((category) => category?.name)
      .filter((name): name is string => Boolean(name))

    if (categoryNames.length) {
      return categoryNames.join('، ')
    }
  }

  return getCategoryName(product.category)
}

function setImageFallback(event: Event) {
  const image = event.target as HTMLImageElement | null
  if (!image) {
    return
  }

  image.onerror = null
  image.src = '/images/placeholder.png'
}

function formatCurrency(value: number) {
  return `${value.toFixed(0)} د.ل`
}

function buildWhatsAppOrderMessage() {
  const lines: string[] = [
    'مرحباً، أود إتمام هذا الطلب:',
    '',
    '*بيانات العميل:*',
    `الاسم: ${checkoutForm.name}`,
    `الهاتف: ${checkoutForm.phone}`,
    `المدينة: ${checkoutForm.city}`,
  ]

  if (checkoutForm.area) {
    lines.push(`المنطقة: ${checkoutForm.area}`)
  }

  lines.push('', '*الطلبات:*')

  cartItems.value.forEach((item, index) => {
    lines.push(`${index + 1}. ${item.product.name} (الكمية: ${item.quantity})`)

    let img = item.product.image || item.product.images?.[0]?.url
    if (img && img !== '/images/placeholder.png') {
      lines.push(`رابط الصورة: ${img}`)
    }
  })

  const total = formatCurrency(cartTotal.value)
  const subtotal = formatCurrency(cartTotal.value + cartSavings.value)

  lines.push('', '*تفاصيل الفاتورة:*', `المجموع الفرعي: ${subtotal}`)

  if (cartSavings.value > 0) {
    lines.push(`الخصم: - ${formatCurrency(cartSavings.value)}`)
  }

  lines.push(`*الإجمالي المطلوب: ${total}*`)

  return lines.join('\n')
}

function confirmClear() {
  clearCart()
  showClearConfirm.value = false
}

function handleCheckout() {
  if (cartItems.value.length === 0) return
  showCheckoutModal.value = true
}

async function submitCheckout() {
  if (!checkoutForm.name || !checkoutForm.phone || !checkoutForm.city) return

  isSubmitting.value = true
  try {
    const { $axios } = useNuxtApp()
    const { data } = await $axios.get('/settings')
    let whatsappRecord = data.data?.find((s: any) => s.key === 'whatsapp_number')
    
    // Default fallback if not defined in settings
    let phoneNumber = whatsappRecord ? whatsappRecord.value : '218910000000'

    // Clean phone number: remove any +, spaces, or leading zeros
    phoneNumber = phoneNumber.replace(/\D/g, '')
    const message = buildWhatsAppOrderMessage()

    const encodedMessage = encodeURIComponent(message)
    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`
    
    // حفظ معلومات الزبون للمرات القادمة
    if (import.meta.client) {
      localStorage.setItem('checkout_customer_info', JSON.stringify({
        name: checkoutForm.name,
        phone: checkoutForm.phone,
        city: checkoutForm.city,
        area: checkoutForm.area
      }))
    }

    showCheckoutModal.value = false
    window.open(whatsappUrl, '_blank')
    
    // Optionally clear cart after successful redirect
    // clearCart()
  } catch (error) {
    console.error('Failed to submit order', error)
    const { addToast } = useToast()
    addToast('حدث خطأ أثناء الاتصال بالخادم، يُرجى المحاولة لاحقاً', 'error')
  } finally {
    isSubmitting.value = false
  }
}

useHead({
  title: 'سلة التسوق — متجر كيان',
})
</script>

<style scoped>
/* Page Layout */
.cart-page {
  padding: var(--space-xl) 0 var(--space-3xl);
  min-height: 60vh;
}

/* Page Header */
.page-header {
  margin-bottom: var(--space-xl);
}

.page-header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: var(--space-md);
}

.page-title-group {
  display: flex;
  align-items: center;
  gap: var(--space-md);
}

.page-icon {
  font-size: 1.8rem;
  color: var(--color-primary);
  background: linear-gradient(135deg, rgba(194, 24, 91, 0.1), rgba(194, 24, 91, 0.05));
  padding: 12px;
  border-radius: var(--radius-md);
}

.page-title {
  font-size: var(--font-size-2xl);
  font-weight: 800;
  color: var(--color-text);
}

.page-subtitle {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin-top: 2px;
}

.page-subtitle strong {
  color: var(--color-primary);
}

.clear-cart-btn {
  display: inline-flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-sm) var(--space-lg);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: #dc3545;
  background: rgba(220, 53, 69, 0.08);
  transition: all var(--transition-fast);
}

.clear-cart-btn:hover {
  background: rgba(220, 53, 69, 0.15);
  transform: translateY(-1px);
}

/* Divider */
.section-divider {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-top: var(--space-lg);
}

.divider-line {
  flex: 1;
  height: 1px;
  background: linear-gradient(to left, var(--color-border), transparent);
}

.divider-line:last-child {
  background: linear-gradient(to right, var(--color-border), transparent);
}

.divider-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
  flex-shrink: 0;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: var(--space-3xl) var(--space-lg);
  animation: fadeInUp var(--transition-slow) both;
}

.empty-icon-wrap {
  width: 120px;
  height: 120px;
  margin: 0 auto var(--space-xl);
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(194, 24, 91, 0.08), rgba(212, 168, 83, 0.08));
  display: flex;
  align-items: center;
  justify-content: center;
  animation: pulse 2s ease-in-out infinite;
}

.empty-icon {
  font-size: 3.5rem;
  color: var(--color-primary);
  opacity: 0.7;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.empty-title {
  font-size: var(--font-size-2xl);
  font-weight: 800;
  color: var(--color-text);
  margin-bottom: var(--space-sm);
}

.empty-text {
  font-size: var(--font-size-base);
  color: var(--color-text-light);
  margin-bottom: var(--space-xl);
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

.empty-btn {
  padding: var(--space-md) var(--space-2xl);
  font-size: var(--font-size-base);
  gap: var(--space-sm);
}

/* Cart Content - 2 column layout */
.cart-content {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: var(--space-xl);
  align-items: start;
}

/* Cart Items */
.cart-items {
  min-width: 0;
}

.cart-item {
  display: flex;
  gap: var(--space-lg);
  padding: var(--space-lg);
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  margin-bottom: var(--space-md);
  transition: all var(--transition-base);
  animation: fadeInUp 0.4s ease both;
}

.cart-item:hover {
  border-color: rgba(194, 24, 91, 0.15);
  box-shadow: var(--shadow-sm);
}

/* Item Image */
.item-image-link {
  position: relative;
  flex-shrink: 0;
  width: 130px;
  height: 130px;
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--color-bg-soft);
}

.item-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.item-image-link:hover .item-image {
  transform: scale(1.05);
}

.item-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-size: 0.65rem;
  font-weight: 700;
  background: var(--color-accent);
  color: var(--color-text);
}

.item-badge.badge-sale {
  background: var(--color-primary);
  color: #fff;
}

/* Item Details */
.item-details {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

.item-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: var(--space-md);
}

.item-name {
  font-size: var(--font-size-base);
  font-weight: 700;
  color: var(--color-text);
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  transition: color var(--transition-fast);
}

.item-name:hover {
  color: var(--color-primary);
}

.item-remove {
  flex-shrink: 0;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  color: var(--color-text-light);
  font-size: 1.1rem;
  transition: all var(--transition-fast);
}

.item-remove:hover {
  background: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}

.item-category {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: var(--font-size-xs);
  color: var(--color-text-light);
  margin-top: var(--space-xs);
}

.category-icon {
  font-size: 0.85rem;
}

.item-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: auto;
  padding-top: var(--space-md);
}

/* Quantity Controls */
.quantity-control {
  display: flex;
  align-items: center;
  gap: 0;
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-full);
  overflow: hidden;
  background: var(--color-bg-soft);
}

.qty-btn {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  font-size: 1rem;
  color: var(--color-text);
  transition: all var(--transition-fast);
}

.qty-btn:hover {
  background: rgba(194, 24, 91, 0.08);
  color: var(--color-primary);
}

.qty-btn-add:hover {
  background: rgba(194, 24, 91, 0.1);
}

.qty-value {
  min-width: 40px;
  text-align: center;
  font-size: var(--font-size-base);
  font-weight: 700;
  color: var(--color-text);
  user-select: none;
}

/* Item Price */
.item-price-group {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 2px;
}

.item-price {
  font-size: var(--font-size-lg);
  font-weight: 800;
  color: var(--color-primary);
}

.item-old-price {
  font-size: var(--font-size-xs);
  color: var(--color-text-light);
  text-decoration: line-through;
}

/* Summary Card */
.cart-summary {
  position: sticky;
  top: 80px;
}

.summary-card {
  background: var(--color-bg-card);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-xl);
  box-shadow: var(--shadow-sm);
}

.summary-title {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: var(--font-size-lg);
  font-weight: 800;
  color: var(--color-text);
  margin-bottom: var(--space-xl);
  padding-bottom: var(--space-md);
  border-bottom: 1px solid var(--color-border);
}

.summary-title .iconify {
  color: var(--color-primary);
  font-size: 1.3rem;
}

.summary-rows {
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
  margin-bottom: var(--space-xl);
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
}

.summary-row .iconify {
  font-size: 1rem;
  margin-left: 4px;
}

.savings-row {
  color: #28a745;
}

.savings-amount {
  font-weight: 700;
  color: #28a745;
}

.free-shipping {
  font-weight: 700;
  color: #28a745;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--space-md) 0;
  border-top: 2px solid var(--color-border);
  margin-bottom: var(--space-xl);
}

.summary-total span:first-child {
  font-size: var(--font-size-base);
  font-weight: 700;
  color: var(--color-text);
}

.total-amount {
  font-size: var(--font-size-2xl);
  font-weight: 800;
  color: var(--color-primary);
}

.checkout-btn {
  width: 100%;
  padding: var(--space-md) var(--space-xl);
  font-size: var(--font-size-base);
  gap: var(--space-sm);
  margin-bottom: var(--space-lg);
}

/* Trust Badges */
.trust-badges {
  display: flex;
  justify-content: space-around;
  gap: var(--space-sm);
  padding-top: var(--space-md);
  border-top: 1px dashed var(--color-border);
}

.trust-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  font-size: 0.68rem;
  font-weight: 600;
  color: var(--color-text-light);
}

.trust-item .iconify {
  font-size: 1.3rem;
  color: var(--color-primary);
  opacity: 0.7;
}

.continue-shopping {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  margin-top: var(--space-md);
  padding: var(--space-md);
  font-size: var(--font-size-sm);
  font-weight: 700;
  color: var(--color-primary);
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-md);
  transition: all var(--transition-fast);
}

.continue-shopping:hover {
  border-color: var(--color-primary);
  background: rgba(194, 24, 91, 0.04);
  color: var(--color-primary);
}

/* Cart Item Transitions */
.cart-item-enter-active {
  transition: all 0.4s ease;
}

.cart-item-leave-active {
  transition: all 0.3s ease;
}

.cart-item-enter-from {
  opacity: 0;
  transform: translateX(30px);
}

.cart-item-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: var(--space-lg);
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.modal-card {
  background: var(--color-bg-card);
  border-radius: var(--radius-lg);
  padding: var(--space-2xl);
  max-width: 400px;
  width: 100%;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  margin: auto 0;
  flex-shrink: 0;
}

.modal-icon-wrap {
  width: 70px;
  height: 70px;
  margin: 0 auto var(--space-lg);
  border-radius: 50%;
  background: rgba(220, 53, 69, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-icon {
  font-size: 2.2rem;
  color: #dc3545;
}

.modal-title {
  font-size: var(--font-size-xl);
  font-weight: 800;
  color: var(--color-text);
  margin-bottom: var(--space-sm);
}

.modal-text {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin-bottom: var(--space-xl);
  line-height: 1.6;
}

.modal-actions {
  display: flex;
  gap: var(--space-md);
}

.modal-actions .btn {
  flex: 1;
  padding: var(--space-md);
  font-size: var(--font-size-sm);
  gap: 6px;
}

.checkout-modal-card {
  max-width: 500px;
  text-align: right;
}

.checkout-form {
  text-align: right;
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-top: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--color-text);
}

.form-group .required {
  color: #dc3545;
}

.form-group input {
  padding: 12px 16px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  font-family: var(--font-family);
  font-size: 0.95rem;
  outline: none;
  transition: all 0.3s ease;
}

.form-group input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(194, 24, 91, 0.1);
}

.checkout-modal-card .modal-icon-wrap {
  margin: 0 auto 20px;
}

.checkout-modal-card .modal-title,
.checkout-modal-card .modal-text {
  text-align: center;
}

.modal-icon-wrap {
  width: 70px;
  height: 70px;
  margin: 0 auto var(--space-lg);
  border-radius: 50%;
  background: rgba(220, 53, 69, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-icon {
  font-size: 2rem;
  color: #dc3545;
}

.modal-title {
  font-size: var(--font-size-xl);
  font-weight: 800;
  color: var(--color-text);
  margin-bottom: var(--space-sm);
}

.modal-text {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin-bottom: var(--space-xl);
  line-height: 1.6;
}

.modal-actions {
  display: flex;
  gap: var(--space-md);
}

.modal-cancel,
.modal-confirm {
  flex: 1;
  padding: var(--space-md) var(--space-lg);
  font-size: var(--font-size-sm);
}

.modal-confirm {
  gap: var(--space-xs);
  background: #dc3545;
  box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.modal-confirm:hover {
  box-shadow: 0 6px 25px rgba(220, 53, 69, 0.4);
}

/* Modal Transitions */
.modal-enter-active {
  transition: all 0.3s ease;
}

.modal-leave-active {
  transition: all 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-card,
.modal-leave-to .modal-card {
  transform: scale(0.9);
}

/* Responsive */
@media (max-width: 900px) {
  .cart-content {
    grid-template-columns: 1fr;
  }

  .cart-summary {
    position: static;
  }
}

@media (max-width: 768px) {
  .cart-page {
    padding-bottom: 90px;
  }

  .cart-item {
    gap: var(--space-md);
    padding: var(--space-md);
  }

  .item-image-link {
    width: 100px;
    height: 100px;
  }

  .item-name {
    font-size: var(--font-size-sm);
  }

  .item-bottom {
    flex-direction: column;
    align-items: flex-start;
    gap: var(--space-md);
  }

  .item-price-group {
    align-items: flex-start;
    flex-direction: row;
    gap: var(--space-sm);
  }

  .page-title {
    font-size: var(--font-size-xl);
  }
}

@media (max-width: 480px) {
  .item-image-link {
    width: 85px;
    height: 85px;
  }

  .trust-badges {
    flex-direction: column;
    gap: var(--space-sm);
  }

  .trust-item {
    flex-direction: row;
    gap: var(--space-sm);
  }
}
</style>
