<script setup lang="ts">
import { ArrowRight, Save, Loader2, Tag, PackagePlus } from 'lucide-vue-next'
import type { ImageResource } from '~/types/category'

definePageMeta({
  title: 'إدارة التصنيف',
  description: 'إضافة أو تعديل تصنيف في المتجر.',
})

const router = useRouter()
const route = useRoute()
const categoriesStore = useCategoriesStore()
const dialog = useDialog()

const isEditing = computed(() => !!route.query.id)
const categoryId = computed(() => route.query.id as string)

const existingImages = ref<any[]>([])

// ─── Form State ───
const form = reactive({
  name: '',
  is_active: true,
  images: [] as File[],
})

const localErrors = ref<Record<string, string>>({})

// Merge local validation + store server errors
const errors = computed(() => ({
  ...localErrors.value,
  ...categoriesStore.validationErrors,
}))

// ─── Load Data if Editing ───
onMounted(async () => {
  if (isEditing.value) {
    const category = await categoriesStore.fetchCategory(categoryId.value)
    if (category) {
      form.name = category.name
      form.is_active = category.is_active
      if (category.image) {
        existingImages.value = [category.image]
      }
    }
  }
})

// ─── Validation ───
function validate(): boolean {
  const e: Record<string, string> = {}
  if (!form.name.trim()) e.name = 'اسم التصنيف مطلوب'
  
  localErrors.value = e
  return Object.keys(e).length === 0
}

// ─── Build FormData ───
function buildFormData(): FormData {
  const formData = new FormData()
  formData.append('name', form.name)
  formData.append('is_active', form.is_active ? '1' : '0')

  // Categories only support one image typically, but Uploader returns an array
  if (form.images && form.images.length > 0) {
    const firstImage = form.images[0]
    if (firstImage) {
      formData.append('image', firstImage)
    }
  } else if (isEditing.value && existingImages.value.length === 0) {
    // If the user deleted the existing image and didn't upload a new one
    // Depends on backend design, sometimes passing empty or sending specific flag is needed
    // Assuming here we might not need to do anything or send an empty field
  }

  return formData
}

// ─── Submit ───
async function submitForm() {
  if (!validate()) return

  localErrors.value = {}
  categoriesStore.validationErrors = {}

  const formData = buildFormData()
  
  let success = false
  if (isEditing.value) {
    success = await categoriesStore.updateCategory(categoryId.value, formData)
  } else {
    success = await categoriesStore.addCategory(formData)
  }

  if (success) {
    dialog.success({
      title: isEditing.value ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح',
      message: isEditing.value ? 'تم حفظ التعديلات بنجاح.' : 'تم إضافة التصنيف إلى المتجر بنجاح.',
      onConfirm: () => router.push('/categories'),
    })
  } else {
    dialog.error({
      title: isEditing.value ? 'فشل في التعديل' : 'فشل في إضافة التصنيف',
      message: errors.value._general || 'تحقق من البيانات المدخلة وحاول مرة أخرى.',
    })
  }
}
</script>

<template>
  <div class="add-product-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header-copy">
        <span class="section-eyebrow">{{ isEditing ? 'Edit Category' : 'New Category' }}</span>
        <h1>{{ isEditing ? 'تعديل التصنيف' : 'إضافة تصنيف جديد' }}</h1>
        <p class="header-desc">{{ isEditing ? 'تعديل بيانات التصنيف الحالي' : 'املأ البيانات لإنشاء قسم جديد في المتجر' }}</p>
      </div>
      <NuxtLink to="/categories" class="button-secondary back-btn">
        <ArrowRight :size="17" :stroke-width="2.2" />
        العودة للتصنيفات
      </NuxtLink>
    </div>

    <!-- General Error -->
    <Transition name="fade">
      <div v-if="errors._general" class="general-error surface-card">
        <span>{{ errors._general }}</span>
      </div>
    </Transition>

    <div class="page-content">
      <div class="form-layout">
        
        <!-- ═══════ Main Form Area ═══════ -->
        <div class="form-main">
          <!-- Basic Info Section -->
          <section class="form-section surface-card">
            <div class="section-title">
              <Tag :size="20" :stroke-width="2" />
              <h2>البيانات الأساسية</h2>
            </div>
            
            <div class="section-body">
              <CustomTextfield
                id="category-name"
                label="الاسم *"
                v-model="form.name"
                placeholder="مثال: مجوهرات"
                :error="errors.name"
              />
            </div>
          </section>

          <!-- Images Section -->
          <section class="form-section surface-card">
            <ProductImageUploader
              v-model="form.images"
              :existing-images="existingImages"
              @update:existing-images="existingImages = $event"
              :error="errors.image"
              :max-images="1"
              label="صورة التصنيف"
            />
          </section>
        </div>

        <!-- ═══════ Sidebar ═══════ -->
        <div class="form-sidebar">
          <!-- Status Section -->
          <section class="form-section surface-card">
            <div class="section-title">
              <PackagePlus :size="20" :stroke-width="2" />
              <h2>الحالة</h2>
            </div>

            <div class="section-body">
              <label class="toggle-label" for="is-active-toggle">
                <span class="toggle-text">{{ form.is_active ? 'التصنيف نشط وسيظهر في المتجر' : 'التصنيف معطّل ولن يظهر' }}</span>
                <div class="toggle-switch" :class="{ 'toggle-on': form.is_active }">
                  <input
                    type="checkbox"
                    id="is-active-toggle"
                    v-model="form.is_active"
                    class="toggle-input"
                  />
                  <span class="toggle-track">
                    <span class="toggle-thumb" />
                  </span>
                </div>
              </label>
            </div>
          </section>
        </div>
      </div>
    </div>
    
    <!-- Sticky Submit Bar -->
    <div class="sticky-submit-bar">
      <div class="submit-bar-inner">
        <p class="submit-hint">
          {{ isEditing ? 'هل انتهيت من التعديلات؟' : 'تأكد من إدخال جميع البيانات المطلوبة.' }}
        </p>

        <button
          class="button-primary submit-btn"
          @click="submitForm"
          :disabled="categoriesStore.submitting"
        >
          <template v-if="categoriesStore.submitting">
            <Loader2 :size="18" :stroke-width="2.5" class="spinner" />
            جاري الحفظ...
          </template>
          <template v-else>
            <Save :size="18" :stroke-width="2.5" />
            حفظ التصنيف
          </template>
        </button>
      </div>
    </div>

  </div>
</template>

<style scoped>
.add-product-page {
  display: flex;
  flex-direction: column;
  gap: 24px;
  position: relative;
  min-height: calc(100vh - 120px);
}

/* ─── Page Header ─── */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.page-header-copy {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 6px;
}

.section-eyebrow {
  font-size: 0.8rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--color-amber-600);
}

.page-header h1 {
  margin: 0;
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--color-slate-950);
}

.header-desc {
  margin: 0;
  font-size: 0.95rem;
  color: var(--color-slate-500);
}

.back-btn {
  gap: 6px;
  font-size: 0.88rem;
}

/* ─── General Error ─── */
.general-error {
  padding: 16px 24px;
  border-color: rgba(202, 61, 84, 0.3);
  background: rgba(202, 61, 84, 0.06);
  color: var(--color-red-600);
  font-weight: 700;
  font-size: 0.92rem;
}

/* ─── Form Layout ─── */
.form-layout {
  display: grid;
  grid-template-columns: 1.4fr 0.6fr;
  gap: 20px;
  align-items: start;
}

.form-main,
.form-sidebar {
  display: grid;
  gap: 20px;
}

/* ─── Form Section ─── */
.form-section {
  padding: 24px;
}

.section-title {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  color: var(--color-slate-950);
}

.section-title h2 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 800;
}

.section-body {
  display: grid;
  gap: 4px;
}

/* ─── Fields Row ─── */
.fields-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

/* ─── Toggle ─── */
.toggle-label {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  cursor: pointer;
}

.toggle-text {
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--color-slate-800);
  line-height: 1.6;
}

.toggle-switch {
  position: relative;
  flex-shrink: 0;
}

.toggle-input {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-track {
  display: block;
  width: 48px;
  height: 28px;
  border-radius: 999px;
  background: rgba(139, 152, 171, 0.3);
  transition: background 0.24s ease;
  position: relative;
}

.toggle-on .toggle-track {
  background: linear-gradient(135deg, var(--color-amber-700), var(--color-amber-500));
}

.toggle-thumb {
  position: absolute;
  top: 3px;
  right: 3px;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: white;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  transition: transform 0.24s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.toggle-on .toggle-thumb {
  transform: translateX(-20px);
}

/* ─── Sticky Submit Bar ─── */
.sticky-submit-bar {
  position: sticky;
  bottom: 0;
  z-index: 50;
  margin: 0 -24px -32px;
  padding: 0 24px;
}

.submit-bar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  max-width: 100%;
  padding: 16px 28px;
  border-radius: 20px 20px 0 0;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(213, 222, 234, 0.7);
  border-bottom: none;
  box-shadow:
    0 -12px 40px rgba(20, 32, 51, 0.08),
    0 -4px 12px rgba(20, 32, 51, 0.04);
}

.submit-hint {
  margin: 0;
  font-size: 0.88rem;
  color: var(--color-slate-600);
  font-weight: 600;
}

.submit-btn {
  min-height: 48px;
  min-width: 180px;
  font-size: 0.95rem;
  border-radius: 14px;
  flex-shrink: 0;
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none !important;
}

.spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* ─── Transitions ─── */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* ─── Responsive ─── */
@media (max-width: 900px) {
  .form-layout {
    grid-template-columns: 1fr;
  }

  .fields-row {
    grid-template-columns: 1fr;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
