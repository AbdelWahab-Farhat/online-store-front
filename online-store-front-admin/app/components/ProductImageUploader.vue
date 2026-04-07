<script setup lang="ts">
import { ImagePlus, X, GripVertical } from 'lucide-vue-next'
import type { ProductImage } from '~/types/product'

const props = defineProps<{
  modelValue: File[]
  existingImages?: ProductImage[]
  error?: string
  maxImages?: number
  label?: string
}>()

const emit = defineEmits(['update:modelValue', 'update:existingImages'])

// New file previews
const previews = ref<{ file: File; url: string }[]>([])
const isDraggingOver = ref(false)
const fileInput = ref<HTMLInputElement | null>(null)
const dragIndex = ref<number | null>(null)

// Local copy of existing images (from server)
const localExisting = ref<ProductImage[]>([])

watch(
  () => props.existingImages,
  (val) => {
    if (val && localExisting.value.length === 0) {
      localExisting.value = [...val]
    }
  },
  { immediate: true }
)

const ACCEPTED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']
const MAX_SIZE = 2 * 1024 * 1024 // 2MB

function openFilePicker() {
  fileInput.value?.click()
}

function onFilesSelected(event: Event) {
  const input = event.target as HTMLInputElement
  if (!input.files) return
  addFiles(Array.from(input.files))
  input.value = ''
}

function addFiles(files: File[]) {
  const valid = files.filter(
    (f) => ACCEPTED_TYPES.includes(f.type) && f.size <= MAX_SIZE
  )

  if (props.maxImages === 1) {
    previews.value.forEach((item) => URL.revokeObjectURL(item.url))
    previews.value = []
    localExisting.value = []
    emit('update:existingImages', [])

    const firstFile = valid[0]
    if (firstFile) {
      const url = URL.createObjectURL(firstFile)
      previews.value.push({ file: firstFile, url })
    }
  } else {
    for (const file of valid) {
      if (props.maxImages && previews.value.length + localExisting.value.length >= props.maxImages) break
      const url = URL.createObjectURL(file)
      previews.value.push({ file, url })
    }
  }

  emitUpdate()
}

function removeExistingImage(index: number) {
  localExisting.value.splice(index, 1)
  emit('update:existingImages', [...localExisting.value])
}

function removeNewImage(index: number) {
  const item = previews.value[index]
  if (item) URL.revokeObjectURL(item.url)
  previews.value.splice(index, 1)
  emitUpdate()
}

function emitUpdate() {
  emit(
    'update:modelValue',
    previews.value.map((p) => p.file)
  )
}

// Drag & drop zone
function onDragOver(e: DragEvent) {
  e.preventDefault()
  isDraggingOver.value = true
}

function onDragLeave() {
  isDraggingOver.value = false
}

function onDrop(e: DragEvent) {
  e.preventDefault()
  isDraggingOver.value = false
  if (e.dataTransfer?.files) {
    addFiles(Array.from(e.dataTransfer.files))
  }
}

// Reorder drag (new files only)
function onReorderStart(index: number) {
  dragIndex.value = index
}

function onReorderOver(e: DragEvent, index: number) {
  e.preventDefault()
  if (dragIndex.value === null || dragIndex.value === index) return
  const [item] = previews.value.splice(dragIndex.value, 1)
  if (!item) return
  previews.value.splice(index, 0, item)
  dragIndex.value = index
  emitUpdate()
}

function onReorderEnd() {
  dragIndex.value = null
}

onUnmounted(() => {
  previews.value.forEach((p) => URL.revokeObjectURL(p.url))
})
</script>

<template>
  <div class="image-uploader">
    <label class="field-label">{{ label || 'صور المنتج' }}</label>

    <!-- Drop Zone -->
    <div
      class="drop-zone"
      :class="{ 'drop-zone--active': isDraggingOver }"
      @dragover="onDragOver"
      @dragleave="onDragLeave"
      @drop="onDrop"
      @click="openFilePicker"
    >
      <ImagePlus :size="36" :stroke-width="1.4" class="drop-icon" />
      <span class="drop-text" v-if="maxImages === 1">اسحب الصورة هنا أو <strong>اختر من جهازك</strong></span>
      <span class="drop-text" v-else>اسحب الصور هنا أو <strong>اختر من جهازك</strong></span>
      <span class="drop-hint">JPG, PNG, WEBP — الحد الأقصى 2MB</span>

      <input
        ref="fileInput"
        type="file"
        accept=".jpg,.jpeg,.png,.webp"
        :multiple="!maxImages || maxImages > 1"
        class="hidden-input"
        @change="onFilesSelected"
      />
    </div>

    <!-- Existing Images (from server) -->
    <div v-if="localExisting.length" class="existing-section">
      <span class="existing-label">الصور الحالية</span>
      <TransitionGroup name="preview-list" tag="div" class="previews-grid">
        <div
          v-for="(img, i) in localExisting"
          :key="'existing-' + img.id"
          class="preview-card existing-card"
        >
          <img :src="img.url" :alt="`Image ${i + 1}`" class="preview-img" />
          <span v-if="i === 0 && previews.length === 0" class="primary-badge">رئيسية</span>
          <button type="button" class="remove-btn" @click.stop="removeExistingImage(i)">
            <X :size="14" :stroke-width="2.5" />
          </button>
        </div>
      </TransitionGroup>
    </div>

    <!-- New File Previews -->
    <TransitionGroup name="preview-list" tag="div" class="previews-grid" v-if="previews.length">
      <div
        v-for="(item, i) in previews"
        :key="item.url"
        class="preview-card"
        draggable="true"
        @dragstart="onReorderStart(i)"
        @dragover="onReorderOver($event, i)"
        @dragend="onReorderEnd"
      >
        <img :src="item.url" :alt="`New Image ${i + 1}`" class="preview-img" />
        <span v-if="i === 0 && localExisting.length === 0" class="primary-badge">رئيسية</span>
        <span class="new-badge">جديدة</span>
        <button type="button" class="remove-btn" @click.stop="removeNewImage(i)">
          <X :size="14" :stroke-width="2.5" />
        </button>
        <div class="drag-handle">
          <GripVertical :size="14" :stroke-width="2" />
        </div>
      </div>
    </TransitionGroup>

    <!-- Error -->
    <div class="error-slot">
      <Transition name="field-error">
        <span v-if="error" class="form-error">{{ error }}</span>
      </Transition>
    </div>
  </div>
</template>

<style scoped>
.image-uploader {
  display: grid;
  gap: 12px;
}

/* ─── Drop Zone ─── */
.drop-zone {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 36px 24px;
  border: 2px dashed rgba(139, 152, 171, 0.36);
  border-radius: var(--radius-md);
  background: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  transition: all 0.28s ease;
}

.drop-zone:hover,
.drop-zone--active {
  border-color: var(--color-amber-500);
  background: rgba(236, 159, 67, 0.04);
}

.drop-zone--active {
  transform: scale(1.01);
  box-shadow: 0 0 0 4px rgba(236, 159, 67, 0.12);
}

.drop-icon {
  color: var(--color-slate-500);
  transition: color 0.2s;
}

.drop-zone:hover .drop-icon,
.drop-zone--active .drop-icon {
  color: var(--color-amber-700);
}

.drop-text {
  font-size: 0.92rem;
  color: var(--color-slate-700);
}

.drop-text strong {
  color: var(--color-amber-700);
  font-weight: 800;
}

.drop-hint {
  font-size: 0.8rem;
  color: var(--color-slate-500);
}

.hidden-input {
  display: none;
}

/* ─── Existing Section ─── */
.existing-section {
  display: grid;
  gap: 8px;
}

.existing-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--color-slate-600);
}

.existing-card {
  border-color: rgba(236, 159, 67, 0.3);
}

/* ─── Previews Grid ─── */
.previews-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
  gap: 12px;
}

.preview-card {
  position: relative;
  aspect-ratio: 1;
  border-radius: var(--radius-sm);
  overflow: hidden;
  border: 2px solid rgba(213, 222, 234, 0.5);
  cursor: grab;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.preview-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(20, 32, 51, 0.1);
}

.preview-card:active {
  cursor: grabbing;
}

.preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.primary-badge {
  position: absolute;
  bottom: 6px;
  right: 6px;
  padding: 2px 8px;
  border-radius: 6px;
  background: linear-gradient(135deg, var(--color-amber-700), var(--color-amber-500));
  color: var(--color-white);
  font-size: 0.68rem;
  font-weight: 800;
}

.new-badge {
  position: absolute;
  bottom: 6px;
  left: 6px;
  padding: 2px 8px;
  border-radius: 6px;
  background: linear-gradient(135deg, #2563eb, #3b82f6);
  color: white;
  font-size: 0.68rem;
  font-weight: 800;
}

.remove-btn {
  position: absolute;
  top: 6px;
  left: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 8px;
  background: rgba(202, 61, 84, 0.9);
  color: white;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.18s;
}

.preview-card:hover .remove-btn {
  opacity: 1;
}

.drag-handle {
  position: absolute;
  top: 6px;
  right: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 6px;
  background: rgba(255, 255, 255, 0.8);
  color: var(--color-slate-700);
  opacity: 0;
  transition: opacity 0.18s;
}

.preview-card:hover .drag-handle {
  opacity: 1;
}

/* ─── Transitions ─── */
.preview-list-enter-active {
  transition: all 0.3s ease;
}
.preview-list-leave-active {
  transition: all 0.2s ease;
}
.preview-list-enter-from {
  opacity: 0;
  transform: scale(0.85);
}
.preview-list-leave-to {
  opacity: 0;
  transform: scale(0.85);
}
.preview-list-move {
  transition: transform 0.25s ease;
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
