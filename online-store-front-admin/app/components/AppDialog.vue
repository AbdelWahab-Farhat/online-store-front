<script setup lang="ts">
import {
  CheckCircle2,
  XCircle,
  AlertTriangle,
  Info,
  HelpCircle,
  X,
} from 'lucide-vue-next'

export type DialogVariant = 'confirm' | 'success' | 'error' | 'info' | 'warning'

export interface DialogOptions {
  variant: DialogVariant
  title: string
  message: string
  confirmText?: string
  cancelText?: string
  /** Hide the cancel button (useful for success/error/info alerts) */
  hideCancel?: boolean
}

const props = withDefaults(defineProps<{
  open: boolean
  variant?: DialogVariant
  title?: string
  message?: string
  confirmText?: string
  cancelText?: string
  hideCancel?: boolean
}>(), {
  variant: 'info',
  title: '',
  message: '',
  confirmText: '',
  cancelText: '',
  hideCancel: false,
})

const emit = defineEmits<{
  (e: 'confirm'): void
  (e: 'cancel'): void
  (e: 'update:open', val: boolean): void
}>()

// ─── Variant Config ───
const variantConfig = computed(() => {
  const configs: Record<DialogVariant, {
    icon: typeof CheckCircle2
    accentClass: string
    defaultConfirm: string
    defaultCancel: string
    defaultHideCancel: boolean
  }> = {
    confirm: {
      icon: HelpCircle,
      accentClass: 'variant-confirm',
      defaultConfirm: 'تأكيد',
      defaultCancel: 'إلغاء',
      defaultHideCancel: false,
    },
    success: {
      icon: CheckCircle2,
      accentClass: 'variant-success',
      defaultConfirm: 'حسناً',
      defaultCancel: 'إغلاق',
      defaultHideCancel: true,
    },
    error: {
      icon: XCircle,
      accentClass: 'variant-error',
      defaultConfirm: 'حسناً',
      defaultCancel: 'إغلاق',
      defaultHideCancel: true,
    },
    info: {
      icon: Info,
      accentClass: 'variant-info',
      defaultConfirm: 'فهمت',
      defaultCancel: 'إغلاق',
      defaultHideCancel: true,
    },
    warning: {
      icon: AlertTriangle,
      accentClass: 'variant-warning',
      defaultConfirm: 'متابعة',
      defaultCancel: 'إلغاء',
      defaultHideCancel: false,
    },
  }
  return configs[props.variant]
})

const resolvedConfirmText = computed(() => props.confirmText || variantConfig.value.defaultConfirm)
const resolvedCancelText = computed(() => props.cancelText || variantConfig.value.defaultCancel)
const resolvedHideCancel = computed(() => props.hideCancel || variantConfig.value.defaultHideCancel)

function handleConfirm() {
  emit('confirm')
  emit('update:open', false)
}

function handleCancel() {
  emit('cancel')
  emit('update:open', false)
}

function handleBackdrop() {
  handleCancel()
}
</script>

<template>
  <Teleport to="body">
    <Transition name="dialog">
      <div v-if="open" class="dialog-backdrop" @click.self="handleBackdrop">
        <div class="dialog-panel" :class="variantConfig.accentClass" role="alertdialog" aria-modal="true">
          
          <!-- Close button -->
          <button class="dialog-close" @click="handleCancel" aria-label="إغلاق">
            <X :size="18" :stroke-width="2.5" />
          </button>

          <!-- Icon -->
          <div class="dialog-icon-wrap">
            <div class="icon-ring">
              <component :is="variantConfig.icon" :size="32" :stroke-width="1.8" />
            </div>
          </div>

          <!-- Content -->
          <div class="dialog-content">
            <h3 class="dialog-title">{{ title }}</h3>
            <p class="dialog-message">{{ message }}</p>
          </div>

          <!-- Actions -->
          <div class="dialog-actions">
            <button
              class="dialog-btn btn-confirm"
              @click="handleConfirm"
            >
              {{ resolvedConfirmText }}
            </button>
            <button
              v-if="!resolvedHideCancel"
              class="dialog-btn btn-cancel"
              @click="handleCancel"
            >
              {{ resolvedCancelText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
/* ─── Backdrop ─── */
.dialog-backdrop {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: rgba(20, 32, 51, 0.45);
  backdrop-filter: blur(6px);
}

/* ─── Panel ─── */
.dialog-panel {
  position: relative;
  width: 100%;
  max-width: 420px;
  padding: 36px 32px 28px;
  border-radius: 24px;
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(213, 222, 234, 0.7);
  box-shadow:
    0 24px 64px rgba(20, 32, 51, 0.18),
    0 8px 20px rgba(20, 32, 51, 0.08);
  backdrop-filter: blur(16px);
  text-align: center;
  direction: rtl;
}

/* ─── Close Button ─── */
.dialog-close {
  position: absolute;
  top: 14px;
  left: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: rgba(20, 32, 51, 0.05);
  color: var(--color-slate-600, #55657f);
  cursor: pointer;
  transition: all 0.2s ease;
}

.dialog-close:hover {
  background: rgba(20, 32, 51, 0.1);
  color: var(--color-slate-950, #142033);
  transform: scale(1.06);
}

/* ─── Icon ─── */
.dialog-icon-wrap {
  display: flex;
  justify-content: center;
  margin-bottom: 18px;
}

.icon-ring {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 72px;
  height: 72px;
  border-radius: 50%;
  transition: all 0.3s ease;
}

/* Variant icon colors */
.variant-confirm .icon-ring {
  background: rgba(196, 107, 23, 0.1);
  color: #c46b17;
  box-shadow: 0 0 0 8px rgba(236, 159, 67, 0.08);
}

.variant-success .icon-ring {
  background: rgba(32, 128, 94, 0.1);
  color: #20805e;
  box-shadow: 0 0 0 8px rgba(32, 128, 94, 0.06);
}

.variant-error .icon-ring {
  background: rgba(202, 61, 84, 0.1);
  color: #ca3d54;
  box-shadow: 0 0 0 8px rgba(202, 61, 84, 0.06);
}

.variant-info .icon-ring {
  background: rgba(59, 130, 206, 0.1);
  color: #3b82ce;
  box-shadow: 0 0 0 8px rgba(59, 130, 206, 0.06);
}

.variant-warning .icon-ring {
  background: rgba(217, 145, 33, 0.12);
  color: #d99121;
  box-shadow: 0 0 0 8px rgba(217, 145, 33, 0.06);
}

/* ─── Content ─── */
.dialog-content {
  margin-bottom: 24px;
}

.dialog-title {
  margin: 0 0 8px;
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--color-slate-950, #142033);
  line-height: 1.45;
}

.dialog-message {
  margin: 0;
  font-size: 0.92rem;
  color: var(--color-slate-700, #55657f);
  line-height: 1.75;
  max-width: 340px;
  margin-inline: auto;
}

/* ─── Actions ─── */
.dialog-actions {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.dialog-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 44px;
  min-width: 110px;
  padding: 0 22px;
  border-radius: 14px;
  font-weight: 800;
  font-size: 0.92rem;
  cursor: pointer;
  transition: all 0.22s ease;
}

.btn-confirm {
  color: #fff;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.btn-confirm:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.16);
}

.btn-cancel {
  background: rgba(20, 32, 51, 0.06);
  color: var(--color-slate-800, #32425a);
}

.btn-cancel:hover {
  background: rgba(20, 32, 51, 0.1);
  transform: translateY(-1px);
}

/* Variant confirm button colors */
.variant-confirm .btn-confirm {
  background: linear-gradient(135deg, #c46b17, #ec9f43);
}

.variant-success .btn-confirm {
  background: linear-gradient(135deg, #1a7a56, #34c88e);
}

.variant-error .btn-confirm {
  background: linear-gradient(135deg, #b83449, #e05d72);
}

.variant-info .btn-confirm {
  background: linear-gradient(135deg, #2d6cb5, #5a9de0);
}

.variant-warning .btn-confirm {
  background: linear-gradient(135deg, #c47e10, #e5a832);
}

/* ─── Transitions ─── */
.dialog-enter-active {
  transition: opacity 0.28s ease;
}
.dialog-enter-active .dialog-panel {
  animation: dialog-in 0.34s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

.dialog-leave-active {
  transition: opacity 0.2s ease;
}
.dialog-leave-active .dialog-panel {
  animation: dialog-out 0.18s ease forwards;
}

.dialog-enter-from,
.dialog-leave-to {
  opacity: 0;
}

@keyframes dialog-in {
  from {
    opacity: 0;
    transform: scale(0.88) translateY(12px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes dialog-out {
  from {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
  to {
    opacity: 0;
    transform: scale(0.94) translateY(6px);
  }
}

/* ─── Responsive ─── */
@media (max-width: 480px) {
  .dialog-panel {
    padding: 28px 20px 22px;
    border-radius: 20px;
  }

  .dialog-actions {
    flex-direction: column;
  }

  .dialog-btn {
    width: 100%;
  }
}
</style>
