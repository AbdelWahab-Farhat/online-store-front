<script setup lang="ts">
import { Check, Loader2, Tag } from 'lucide-vue-next'
import type { Category } from '~/types/category'

const props = defineProps<{
  modelValue: number[]
  error?: string
}>()

const emit = defineEmits(['update:modelValue'])

const categoriesStore = useCategoriesStore()
const categoryImageErrors = reactive<Record<number, boolean>>({})

onMounted(() => {
  if (!categoriesStore.categories.length) {
    categoriesStore.fetchCategories()
  }
})

function isSelected(id: number) {
  return props.modelValue.includes(id)
}

function toggle(id: number) {
  const current = [...props.modelValue]
  const idx = current.indexOf(id)
  if (idx >= 0) {
    current.splice(idx, 1)
  } else {
    current.push(id)
  }
  emit('update:modelValue', current)
}

function hasCategoryImage(category: Category) {
  return Boolean(category.image?.url) && !categoryImageErrors[category.id]
}
</script>

<template>
  <div class="category-picker">
    <label class="field-label">التصنيفات <span class="required-star">*</span></label>

    <!-- Loading -->
    <div v-if="categoriesStore.loading" class="picker-loading">
      <Loader2 :size="20" :stroke-width="2" class="spinner" />
      <span>جاري تحميل التصنيفات...</span>
    </div>

    <!-- Categories Grid -->
    <div v-else class="categories-chips">
      <button
        v-for="cat in categoriesStore.categories"
        :key="cat.id"
        type="button"
        class="category-chip"
        :class="{ 'category-chip--selected': isSelected(cat.id) }"
        @click="toggle(cat.id)"
      >
        <span class="chip-check">
          <Transition name="check-pop">
            <Check v-if="isSelected(cat.id)" :size="14" :stroke-width="3" />
          </Transition>
        </span>
        <img
          v-if="hasCategoryImage(cat)"
          :src="cat.image.url"
          :alt="cat.name"
          class="chip-img"
          @error="categoryImageErrors[cat.id] = true"
        />
        <Tag v-else :size="16" :stroke-width="2" class="chip-icon-placeholder" />
        <span class="chip-label">{{ cat.name }}</span>
      </button>
    </div>

    <!-- Selected Count -->
    <span v-if="modelValue.length" class="selected-count">
      {{ modelValue.length }} تصنيف مختار
    </span>

    <!-- Error -->
    <div class="error-slot">
      <Transition name="field-error">
        <span v-if="error" class="form-error">{{ error }}</span>
      </Transition>
    </div>
  </div>
</template>

<style scoped>
.category-picker {
  display: grid;
  gap: 10px;
}

.required-star {
  color: var(--color-red-600);
}

/* ─── Loading ─── */
.picker-loading {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 18px;
  color: var(--color-slate-700);
  font-size: 0.88rem;
}

.spinner {
  color: var(--color-amber-700);
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* ─── Chips ─── */
.categories-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.category-chip {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px 8px 12px;
  border: 2px solid rgba(213, 222, 234, 0.6);
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.7);
  color: var(--color-slate-800);
  font-size: 0.88rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.22s ease;
  user-select: none;
}

.category-chip:hover {
  border-color: rgba(196, 107, 23, 0.3);
  background: rgba(236, 159, 67, 0.04);
  transform: translateY(-1px);
}

.category-chip--selected {
  border-color: var(--color-amber-500);
  background: rgba(236, 159, 67, 0.08);
  color: var(--color-amber-700);
  box-shadow: 0 4px 14px rgba(196, 107, 23, 0.12);
}

.category-chip--selected:hover {
  background: rgba(236, 159, 67, 0.12);
}

/* Check */
.chip-check {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  border-radius: 7px;
  border: 2px solid rgba(139, 152, 171, 0.3);
  background: transparent;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.category-chip--selected .chip-check {
  border-color: var(--color-amber-700);
  background: linear-gradient(135deg, var(--color-amber-700), var(--color-amber-500));
  color: white;
}

.check-pop-enter-active {
  transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.check-pop-leave-active {
  transition: all 0.15s ease;
}
.check-pop-enter-from {
  opacity: 0;
  transform: scale(0.3);
}
.check-pop-leave-to {
  opacity: 0;
  transform: scale(0.3);
}

/* Chip Image */
.chip-img {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  object-fit: cover;
  flex-shrink: 0;
}

.chip-icon-placeholder {
  color: var(--color-slate-500);
  flex-shrink: 0;
}

.chip-label {
  white-space: nowrap;
}

/* Selected count */
.selected-count {
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--color-amber-700);
}

/* ─── Error ─── */
.error-slot {
  min-height: 1.5em;
  display: grid;
  align-items: start;
}

.field-error-enter-active,
.field-error-leave-active {
  transition: opacity 0.18s ease, transform 0.18s ease;
}

.field-error-enter-from,
.field-error-leave-to {
  opacity: 0;
  transform: translateY(-2px);
}
</style>
