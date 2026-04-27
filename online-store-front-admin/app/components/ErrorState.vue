<script setup lang="ts">
import { AlertCircle, RefreshCw } from 'lucide-vue-next'

interface Props {
  title?: string
  message: string
}

withDefaults(defineProps<Props>(), {
  title: 'حدث خطأ',
})

const emit = defineEmits<{
  retry: []
}>()
</script>

<template>
  <div class="state-card error-state surface-card">
    <div class="state-icon error-icon">
      <AlertCircle :size="40" :stroke-width="1.5" />
    </div>
    <h2>{{ title }}</h2>
    <p>{{ message }}</p>
    <button class="button-primary retry-btn" @click="emit('retry')">
      <RefreshCw :size="17" :stroke-width="2.2" />
      إعادة المحاولة
    </button>
  </div>
</template>

<style scoped>
.state-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 60px 32px;
  gap: 12px;
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
</style>
