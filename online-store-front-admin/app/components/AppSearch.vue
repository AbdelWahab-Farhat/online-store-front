<script setup lang="ts">
import { Search, Filter, X } from 'lucide-vue-next'
import { ref, watch, onMounted } from 'vue'

const props = defineProps<{
  modelValue: string
  categoryId?: number | null
  placeholder?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'update:categoryId': [value: number | null]
  'search': []
}>()

const categoriesStore = useCategoriesStore()

onMounted(() => {
  if (!categoriesStore.categories.length) {
    categoriesStore.fetchCategories()
  }
})

const internalSearch = ref(props.modelValue)
const internalCategory = ref(props.categoryId)

watch(internalSearch, (val) => {
  emit('update:modelValue', val)
})

watch(() => props.modelValue, (val) => {
  internalSearch.value = val
})

watch(internalCategory, (val) => {
  emit('update:categoryId', val ?? null)
  emit('search')
})

watch(() => props.categoryId, (val) => {
  internalCategory.value = val
})

let searchTimeout: any
function onInput() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    emit('search')
  }, 500) // Debounce search
}

function clearSearch() {
  internalSearch.value = ''
  emit('update:modelValue', '')
  emit('search')
}
</script>

<template>
  <div class="search-component">
    <div class="search-input-wrap">
      <Search class="search-icon" :size="18" :stroke-width="2.5" />
      <input
        v-model="internalSearch"
        type="text"
        :placeholder="placeholder || 'ابحث بالاسم...'"
        class="search-input"
        @input="onInput"
        @keyup.enter="emit('search')"
      />
      <button v-if="internalSearch" type="button" class="clear-btn" @click="clearSearch">
        <X :size="14" :stroke-width="3" />
      </button>
    </div>

    <div class="category-filter">
      <div class="filter-icon-wrap">
        <Filter :size="16" :stroke-width="2.5" />
      </div>
      <select 
        v-model="internalCategory"
        class="category-select" 
      >
        <option :value="null">جميع التصنيفات</option>
        <option v-for="cat in categoriesStore.categories" :key="cat.id" :value="cat.id">
          {{ cat.name }}
        </option>
      </select>
    </div>
  </div>
</template>

<style scoped>
.search-component {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  flex: 1;
}

.search-input-wrap {
  position: relative;
  display: flex;
  align-items: center;
  flex: 1;
  min-width: 200px;
}

.search-icon {
  position: absolute;
  right: 14px;
  color: var(--color-slate-400);
  pointer-events: none;
}

.search-input {
  width: 100%;
  height: 44px;
  padding: 0 42px 0 42px;
  border: 1.5px solid rgba(20, 32, 51, 0.1);
  border-radius: var(--radius-md);
  background: #fff;
  font-family: inherit;
  font-size: 0.92rem;
  color: var(--color-slate-900);
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(20, 32, 51, 0.02);
}

.search-input:focus {
  outline: none;
  border-color: var(--color-amber-500);
  box-shadow: 0 0 0 3px rgba(236, 159, 67, 0.15);
}

.search-input::placeholder {
  color: var(--color-slate-400);
}

.clear-btn {
  position: absolute;
  left: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--color-slate-100);
  color: var(--color-slate-500);
  cursor: pointer;
  transition: all 0.2s ease;
  padding: 0;
  border: none;
}

.clear-btn:hover {
  background: var(--color-red-100);
  color: var(--color-red-600);
}

.category-filter {
  position: relative;
  display: flex;
  align-items: center;
}

.filter-icon-wrap {
  position: absolute;
  right: 14px;
  color: var(--color-slate-500);
  pointer-events: none;
  z-index: 1;
}

.category-select {
  appearance: none;
  height: 44px;
  padding: 0 36px 0 16px;
  border: 1.5px solid rgba(20, 32, 51, 0.1);
  border-radius: var(--radius-md);
  background: #fff;
  font-family: inherit;
  font-size: 0.92rem;
  font-weight: 600;
  color: var(--color-slate-800);
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(20, 32, 51, 0.02);
  min-width: 160px;
}

.category-select:focus {
  outline: none;
  border-color: var(--color-amber-500);
  box-shadow: 0 0 0 3px rgba(236, 159, 67, 0.15);
}

.category-select option {
  font-weight: 600;
}
</style>
