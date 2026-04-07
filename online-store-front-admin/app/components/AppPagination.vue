<script setup lang="ts">
import { ChevronRight, ChevronLeft, ChevronsRight, ChevronsLeft } from 'lucide-vue-next'

const props = withDefaults(defineProps<{
  currentPage: number
  lastPage: number
  total?: number
  from?: number | null
  to?: number | null
  loading?: boolean
  maxVisiblePages?: number
}>(), {
  total: 0,
  from: null,
  to: null,
  loading: false,
  maxVisiblePages: 5,
})

const emit = defineEmits<{
  change: [page: number]
}>()

const pageNumbers = computed(() => {
  if (props.lastPage <= 1) {
    return []
  }

  const visibleCount = Math.max(3, props.maxVisiblePages)
  const half = Math.floor(visibleCount / 2)

  let start = Math.max(1, props.currentPage - half)
  let end = Math.min(props.lastPage, start + visibleCount - 1)

  if (end - start + 1 < visibleCount) {
    start = Math.max(1, end - visibleCount + 1)
  }

  return Array.from({ length: end - start + 1 }, (_, index) => start + index)
})

function goTo(page: number) {
  if (
    props.loading
    || page < 1
    || page > props.lastPage
    || page === props.currentPage
  ) {
    return
  }

  emit('change', page)
}
</script>

<template>
  <div v-if="lastPage > 1" class="pagination-wrap surface-card">
    <div class="pagination-summary">
      عرض {{ from || 0 }} - {{ to || 0 }} من {{ total }}
    </div>

    <div class="pagination-actions">
      <button
        type="button"
        class="pagination-btn"
        :disabled="currentPage <= 1 || loading"
        @click="goTo(1)"
      >
        <ChevronsRight :size="16" :stroke-width="2.4" />
        الأول
      </button>

      <button
        type="button"
        class="pagination-btn"
        :disabled="currentPage <= 1 || loading"
        @click="goTo(currentPage - 1)"
      >
        <ChevronRight :size="16" :stroke-width="2.4" />
        السابق
      </button>

      <div class="pagination-pages">
        <button
          v-for="page in pageNumbers"
          :key="page"
          type="button"
          class="page-number-btn"
          :class="{ 'page-number-btn--active': page === currentPage }"
          :disabled="loading"
          @click="goTo(page)"
        >
          {{ page }}
        </button>
      </div>

      <button
        type="button"
        class="pagination-btn"
        :disabled="currentPage >= lastPage || loading"
        @click="goTo(currentPage + 1)"
      >
        التالي
        <ChevronLeft :size="16" :stroke-width="2.4" />
      </button>

      <button
        type="button"
        class="pagination-btn"
        :disabled="currentPage >= lastPage || loading"
        @click="goTo(lastPage)"
      >
        الأخير
        <ChevronsLeft :size="16" :stroke-width="2.4" />
      </button>
    </div>
  </div>
</template>

<style scoped>
.pagination-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  padding: 18px 20px;
  border-radius: var(--radius-lg);
}

.pagination-summary {
  font-size: 0.92rem;
  color: var(--color-slate-700);
  font-weight: 600;
}

.pagination-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.pagination-pages {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.pagination-btn,
.page-number-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-height: 42px;
  padding: 0 16px;
  border: 1px solid rgba(20, 32, 51, 0.12);
  border-radius: 999px;
  background: #fff;
  color: var(--color-slate-900);
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.page-number-btn {
  min-width: 42px;
  padding: 0 12px;
}

.pagination-btn:hover:not(:disabled),
.page-number-btn:hover:not(:disabled) {
  border-color: rgba(20, 32, 51, 0.22);
  transform: translateY(-1px);
}

.page-number-btn--active {
  background: var(--color-amber-700);
  border-color: var(--color-amber-700);
  color: var(--color-white);
}

.pagination-btn:disabled,
.page-number-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

@media (max-width: 640px) {
  .pagination-wrap,
  .pagination-actions,
  .pagination-pages {
    width: 100%;
  }

  .pagination-actions {
    justify-content: center;
  }
}
</style>
