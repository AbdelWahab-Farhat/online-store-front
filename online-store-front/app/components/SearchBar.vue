<template>
  <div class="search-bar" :class="{ 'is-focused': isFocused }">
    <Icon name="mdi:magnify" class="search-icon" />
    <input
      v-model="query"
      type="text"
      :placeholder="placeholder"
      class="search-input"
      @focus="isFocused = true"
      @blur="isFocused = false"
      @input="$emit('search', query)"
    />
    <button v-if="query" class="search-clear" @click="clear">
      <Icon name="mdi:close" />
    </button>
  </div>
</template>

<script setup lang="ts">
defineProps({
  placeholder: {
    type: String,
    default: 'ابحثي عن منتجات...',
  },
})

const emit = defineEmits(['search'])
const query = ref('')
const isFocused = ref(false)

function clear() {
  query.value = ''
  emit('search', '')
}
</script>

<style scoped>
.search-bar {
  display: flex;
  align-items: center;
  background: var(--color-bg-card);
  border: 2px solid var(--color-border);
  border-radius: var(--radius-full);
  padding: var(--space-sm) var(--space-md);
  transition: all var(--transition-base);
  width: 100%;
  max-width: 400px;
}

.search-bar.is-focused {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(194, 24, 91, 0.1);
}

.search-icon {
  font-size: 1.2rem;
  color: var(--color-text-light);
  margin-left: var(--space-sm);
  flex-shrink: 0;
}

.search-input {
  border: none;
  outline: none;
  background: transparent;
  font-family: inherit;
  font-size: var(--font-size-sm);
  color: var(--color-text);
  width: 100%;
  direction: rtl;
}

.search-input::placeholder {
  color: var(--color-text-light);
}

.search-clear {
  background: none;
  color: var(--color-text-light);
  font-size: 1.1rem;
  padding: 2px;
  display: flex;
  transition: color var(--transition-fast);
}

.search-clear:hover {
  color: var(--color-primary);
}
</style>
