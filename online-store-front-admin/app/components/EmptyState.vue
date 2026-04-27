<script setup lang="ts">
import { Plus } from 'lucide-vue-next'

interface Props {
  title: string
  description: string
  actionLabel?: string
  actionTo?: string
}

defineProps<Props>()
</script>

<template>
  <div class="state-card surface-card">
    <div class="state-icon empty-icon">
      <slot name="icon" />
    </div>
    <h2>{{ title }}</h2>
    <p>{{ description }}</p>
    <NuxtLink v-if="actionTo && actionLabel" :to="actionTo" class="button-primary retry-btn">
      <Plus :size="17" :stroke-width="2.5" />
      {{ actionLabel }}
    </NuxtLink>
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

.empty-icon {
  background: rgba(236, 159, 67, 0.1);
  color: var(--color-amber-700);
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
