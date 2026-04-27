<template>
  <div class="toast-container">
    <TransitionGroup name="toast-list">
      <div 
        v-for="toast in toasts" 
        :key="toast.id" 
        class="toast"
        :class="`toast-${toast.type}`"
      >
        <Icon v-if="toast.type === 'success'" name="mdi:check-circle" class="toast-icon" />
        <Icon v-if="toast.type === 'error'" name="mdi:alert-circle" class="toast-icon" />
        <Icon v-if="toast.type === 'info'" name="mdi:information" class="toast-icon" />
        <span class="toast-message">{{ toast.message }}</span>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup lang="ts">
import { useToast } from '~/composables/useToast'
const { toasts } = useToast()
</script>

<style scoped>
.toast-container {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 12px;
  pointer-events: none;
}

.toast {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #ffffff;
  border-radius: 8px;
  padding: 12px 20px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  font-family: var(--font-family);
  font-size: 0.95rem;
  font-weight: 600;
  color: #333;
  pointer-events: auto;
  border-right: 4px solid #333;
  min-width: 250px;
}

.toast-success {
  border-right-color: #2e7d32;
}

.toast-success .toast-icon {
  color: #2e7d32;
}

.toast-error {
  border-right-color: #c62828;
}

.toast-error .toast-icon {
  color: #c62828;
}

.toast-info {
  border-right-color: #1565c0;
}

.toast-info .toast-icon {
  color: #1565c0;
}

.toast-icon {
  font-size: 1.25rem;
}

.toast-list-enter-active,
.toast-list-leave-active {
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-list-enter-from,
.toast-list-leave-to {
  opacity: 0;
  transform: translateX(30px) scale(0.95);
}
</style>
