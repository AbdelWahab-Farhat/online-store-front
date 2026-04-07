<script setup lang="ts">
import {
  Ticket,
  AlertCircle,
  RefreshCw,
  Eye,
  Pencil,
  Loader2,
  CalendarDays,
  Users
} from 'lucide-vue-next'

definePageMeta({
  title: 'الكوبونات',
  description: 'إدارة كوبونات الخصم والعروض الترويجية.',
})

const couponsStore = useCouponsStore()

onMounted(() => {
  couponsStore.fetchCoupons()
})

function formatDiscount(coupon: any) {
  if (coupon.discount_type === 'percent') {
    return `${coupon.discount_value}%`
  }
  
  // Default to fixed currency
  return new Intl.NumberFormat('ar-LY', {
    style: 'currency',
    currency: 'LYD',
    minimumFractionDigits: 0,
  }).format(coupon.discount_value)
}

function formatDate(dateStr: string) {
  return new Intl.DateTimeFormat('ar-EG-u-nu-latn', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(new Date(dateStr))
}
</script>

<template>
  <div class="coupons-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header-copy">
        <span class="section-eyebrow">Coupons</span>
        <h1>إدارة الكوبونات</h1>
        <p class="header-desc">عرض وإدارة قسائم التخفيض وخصومات المتجر</p>
      </div>
      <div class="header-actions" v-if="!couponsStore.loading && !couponsStore.error && couponsStore.coupons.length">
        <span class="count-badge">
          <Ticket :size="15" :stroke-width="2.2" />
          {{ couponsStore.coupons.length }} كوبون
        </span>
      </div>
    </div>

    <!-- ═══════ Loading State ═══════ -->
    <Transition name="fade" mode="out-in">
      <div v-if="couponsStore.loading" class="state-card surface-card" key="loading">
        <div class="loader-wrap">
          <Loader2 :size="44" :stroke-width="2" class="spinner" />
        </div>
        <h2>جاري تحميل الكوبونات...</h2>
        <p>يرجى الانتظار لحظة</p>
      </div>

      <!-- ═══════ Error State ═══════ -->
      <div v-else-if="couponsStore.error" class="state-card error-state surface-card" key="error">
        <div class="state-icon error-icon">
          <AlertCircle :size="40" :stroke-width="1.5" />
        </div>
        <h2>تعذر تحميل الكوبونات</h2>
        <p>{{ couponsStore.error }}</p>
        <button class="button-primary retry-btn" @click="couponsStore.fetchCoupons()">
          <RefreshCw :size="17" :stroke-width="2.2" />
          إعادة المحاولة
        </button>
      </div>

      <!-- ═══════ Success State (Coupons Grid) ═══════ -->
      <div v-else class="coupons-grid" key="success">
        <article
          v-for="coupon in couponsStore.coupons"
          :key="coupon.id"
          class="coupon-card surface-card"
        >
          <!-- Card Header & Code -->
          <div class="card-header">
            <div class="coupon-code-wrap">
              <Ticket :size="20" :stroke-width="2.5" class="code-icon" />
              <span class="coupon-code">{{ coupon.code }}</span>
            </div>
            
            <span
              class="status-chip"
              :class="coupon.is_valid ? 'status-valid' : 'status-invalid'"
            >
              {{ coupon.is_valid ? 'صالح للاستخدام' : 'منتهي أو متوقف' }}
            </span>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <div class="discount-value">
              <span class="discount-prefix">خصم</span>
              <span class="discount-amount">{{ formatDiscount(coupon) }}</span>
            </div>

            <div class="meta-list">
              <div class="meta-item">
                <Users :size="16" :stroke-width="2" class="meta-icon" />
                <span>
                  مستخدم: <strong>{{ coupon.used_count }}</strong>
                  <template v-if="coupon.usage_limit">
                     من <strong>{{ coupon.usage_limit }}</strong>
                  </template>
                </span>
              </div>
              
              <div class="meta-item">
                <CalendarDays :size="16" :stroke-width="2" class="meta-icon" />
                <span v-if="coupon.expires_at">
                  ينتهي في: {{ formatDate(coupon.expires_at) }}
                </span>
                <span v-else class="text-unlimited">مفتوح (ليس له تاريخ انتهاء)</span>
              </div>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="card-footer">
            <div class="card-actions">
              <button class="action-btn" title="عرض التفاصيل">
                <Eye :size="16" :stroke-width="2.2" />
              </button>
              <button class="action-btn" title="تعديل">
                <Pencil :size="16" :stroke-width="2.2" />
              </button>
            </div>
          </div>
        </article>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.coupons-page {
  display: grid;
  gap: 24px;
}

/* ─── Header ─── */
.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.page-header h1 {
  margin: 8px 0 0;
  font-size: 1.5rem;
  color: var(--color-slate-950);
}

.header-desc {
  margin: 4px 0 0;
  color: var(--color-slate-700);
  font-size: 0.92rem;
}

.count-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 999px;
  background: rgba(20, 32, 51, 0.06);
  font-size: 0.88rem;
  font-weight: 800;
  color: var(--color-slate-800);
}

/* ─── Fade Transition ─── */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* ─── Coupons Grid ─── */
.coupons-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
  gap: 18px;
}

/* ─── Coupon Card ─── */
.coupon-card {
  display: flex;
  flex-direction: column;
  border-radius: var(--radius-lg);
  overflow: hidden;
  position: relative;
  transition: transform 0.28s ease, box-shadow 0.28s ease;
}

/* Add a dotted cutout aesthetic to look like a coupon/ticket */
.coupon-card::before,
.coupon-card::after {
  content: "";
  position: absolute;
  top: 60px;
  width: 16px;
  height: 16px;
  background: #F3F4F6; /* Matches app background ideally */
  border-radius: 50%;
  z-index: 10;
  box-shadow: inset 0 0 4px rgba(0,0,0,0.06);
}
.coupon-card::before {
  right: -8px;
}
.coupon-card::after {
  left: -8px;
}

.coupon-card:hover {
  transform: translateY(-4px);
  box-shadow:
    0 20px 50px rgba(20, 32, 51, 0.12),
    0 8px 16px rgba(20, 32, 51, 0.06);
}

/* Header */
.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 24px;
  background: linear-gradient(135deg, rgba(20, 32, 51, 0.02), rgba(20, 32, 51, 0.04));
  border-bottom: 2px dashed rgba(213, 222, 234, 0.8);
}

.coupon-code-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
}

.code-icon {
  color: var(--color-amber-700);
}

.coupon-code {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 1.25rem;
  font-weight: 900;
  letter-spacing: 1.5px;
  color: var(--color-slate-950);
}

.status-chip {
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.72rem;
  font-weight: 800;
}

.status-valid {
  background: rgba(32, 128, 94, 0.12);
  color: var(--color-green-700);
}

.status-invalid {
  background: rgba(202, 61, 84, 0.12);
  color: var(--color-red-600);
}

/* Body */
.card-body {
  display: flex;
  flex-direction: column;
  padding: 24px;
  gap: 20px;
  flex: 1;
}

.discount-value {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.discount-prefix {
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--color-slate-600);
}

.discount-amount {
  font-size: 1.6rem;
  font-weight: 900;
  color: var(--color-amber-700);
  background: linear-gradient(135deg, var(--color-amber-700), var(--color-amber-500));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.meta-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 10px;
  color: var(--color-slate-700);
  font-size: 0.88rem;
}

.meta-icon {
  color: var(--color-slate-500);
}

.meta-item strong {
  color: var(--color-slate-950);
  font-weight: 700;
}

.text-unlimited {
  color: var(--color-green-600);
  font-weight: 600;
}

/* Footer */
.card-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 16px 24px;
  background: rgba(20, 32, 51, 0.01);
  border-top: 1px solid rgba(213, 222, 234, 0.5);
}

.card-actions {
  display: flex;
  gap: 6px;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(20, 32, 51, 0.05);
  color: var(--color-slate-700);
  cursor: pointer;
  transition: all var(--transition-base);
}

.action-btn:hover {
  background: rgba(20, 32, 51, 0.1);
  color: var(--color-slate-950);
  transform: scale(1.08);
}

/* ─── States (Loading / Error) ─── */
.state-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 60px 32px;
  gap: 12px;
}

.loader-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 88px;
  height: 88px;
  border-radius: 50%;
  background: rgba(236, 159, 67, 0.08);
  margin-bottom: 8px;
}

.spinner {
  color: var(--color-amber-700);
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.state-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 88px;
  height: 88px;
  border-radius: 50%;
  margin-bottom: 8px;
}

.error-icon {
  background: rgba(202, 61, 84, 0.1);
  color: var(--color-red-600);
}

.state-card h2 {
  margin: 0;
  font-size: 1.18rem;
  color: var(--color-slate-950);
}

.state-card p {
  margin: 0;
  color: var(--color-slate-700);
  font-size: 0.94rem;
  max-width: 360px;
  line-height: 1.7;
}

.retry-btn {
  margin-top: 8px;
  min-height: 42px;
  padding: 0 24px;
  font-size: 0.92rem;
}

/* ─── Responsive ─── */
@media (max-width: 640px) {
  .coupons-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
