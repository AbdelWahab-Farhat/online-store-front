<script setup lang="ts">
defineProps<{
  id: string
  label: string
  modelValue: string | number
  type?: string
  placeholder?: string
  error?: string
  dir?: string
  autocomplete?: string
  inputClass?: string
}>()

defineEmits(['update:modelValue'])
</script>

<template>
  <div class="field-group">
    <label :for="id" class="field-label">{{ label }}</label>

    <div class="input-wrap">
      <input
        :id="id"
        :type="type || 'text'"
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        class="text-field"
        :class="[inputClass, { 'has-error': !!error }]"
        :dir="dir"
        :autocomplete="autocomplete"
        :placeholder="placeholder"
      />
      <slot name="suffix" />
    </div>

    <div class="error-slot">
      <Transition name="field-error">
        <span v-if="error" class="form-error">{{ error }}</span>
      </Transition>
    </div>
  </div>
</template>

<style scoped>
.input-wrap {
  position: relative;
}
.has-error {
  border-color: rgba(202, 61, 84, 0.8) !important;
}

/* reserve exactly 1 line of error-text height so the layout never shifts */
.error-slot {
  min-height: 1.5em;
  display: grid;         /* keeps the child aligned without extra margin */
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
