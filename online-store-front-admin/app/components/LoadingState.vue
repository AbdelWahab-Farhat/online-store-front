<script setup lang="ts">
import { Loader2 } from 'lucide-vue-next'

interface Props {
  message?: string
}

withDefaults(defineProps<Props>(), {
  message: 'جاري التحميل...',
})
</script>

<template>
  <div class="state-card surface-card">
    <div class="loader-wrap">
      <Loader2 :size="44" :stroke-width="2" class="spinner" />
    </div>
    <h2>{{ message }}</h2>
    <p>يرجى الانتظار لحظة</p>
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
</style>
