<script setup lang="ts">
import type { ProductImage } from '~/types/product'
import {
  ArrowRight,
  Save,
  Loader2,
  PackagePlus,
  FileText,
  DollarSign,
  Boxes,
} from 'lucide-vue-next'

definePageMeta({
  title: 'إدارة المنتج',
  description: 'إضافة أو تعديل منتج في المتجر.',
})

const router = useRouter()
const route = useRoute()
const productsStore = useProductsStore()
const dialog = useDialog()

const isEditing = computed(() => !!route.query.id)
const productId = computed(() => route.query.id as string)

const existingImages = ref<ProductImage[]>([])

onMounted(async () => {
  if (isEditing.value) {
    const product = await productsStore.fetchProduct(productId.value)
    if (product) {
      form.name = product.name
      form.description = product.description || ''
      form.price = product.price
      form.compare_price = product.compare_price || ''
      form.quantity = product.quantity
      form.sku = product.sku || ''
      form.is_active = product.is_active
      form.category_ids = product.categories?.map(c => c.id) || []
      existingImages.value = product.images || []
    }
  }
})

// ─── Form State ───
const form = reactive({
  name: '',
  description: '',
  price: '' as string | number,
  compare_price: '' as string | number,
  quantity: '' as string | number,
  sku: '',
  is_active: true,
  category_ids: [] as number[],
  images: [] as File[],
})

const localErrors = ref<Record<string, string>>({})

// Merge local validation + store server errors
const errors = computed(() => ({
  ...localErrors.value,
  ...productsStore.validationErrors,
}))

// ─── Validation ───
function validate(): boolean {
  const e: Record<string, string> = {}

  if (!form.name.trim()) e.name = 'اسم المنتج مطلوب'
  if (!form.price || Number(form.price) < 0) e.price = 'السعر مطلوب ويجب أن يكون رقم موجب'
  if (form.compare_price && Number(form.compare_price) < 0) e.compare_price = 'يجب أن يكون رقم موجب'
  if (form.quantity === '' || Number(form.quantity) < 0) e.quantity = 'الكمية مطلوبة'
  if (!form.category_ids.length) e.category_ids = 'يجب اختيار تصنيف واحد على الأقل'

  localErrors.value = e
  return Object.keys(e).length === 0
}

// ─── Build FormData ───
function buildFormData(): FormData {
  const formData = new FormData()
  formData.append('name', form.name)

  if (form.description) formData.append('description', form.description)
  formData.append('price', String(form.price))
  if (form.compare_price) formData.append('compare_price', String(form.compare_price))
  formData.append('quantity', String(form.quantity))
  if (form.sku) formData.append('sku', form.sku)
  formData.append('is_active', form.is_active ? '1' : '0')

  form.category_ids.forEach((id) => {
    formData.append('category_ids[]', String(id))
  })

  // Send IDs of existing images the user wants to keep
  if (isEditing.value) {
    existingImages.value.forEach((img) => {
      formData.append('existing_images[]', String(img.id))
    })
  }

  form.images.forEach((file) => {
    formData.append('images[]', file)
  })

  return formData
}

// ─── Submit ───
async function submitForm() {
  if (!validate()) return

  localErrors.value = {}
  productsStore.validationErrors = {}

  const formData = buildFormData()
  
  let success = false
  if (isEditing.value) {
    success = await productsStore.updateProduct(productId.value, formData)
  } else {
    success = await productsStore.addProduct(formData)
  }

  if (success) {
    dialog.success({
      title: isEditing.value ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح',
      message: isEditing.value ? 'تم حفظ التعديلات بنجاح.' : 'تم إضافة المنتج إلى المتجر بنجاح.',
      onConfirm: () => router.push('/products'),
    })
  } else {
    dialog.error({
      title: isEditing.value ? 'فشل في التعديل' : 'فشل في إضافة المنتج',
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
        <span class="section-eyebrow">{{ isEditing ? 'Edit Product' : 'New Product' }}</span>
        <h1>{{ isEditing ? 'تعديل المنتج' : 'إضافة منتج جديد' }}</h1>
        <p class="header-desc">{{ isEditing ? 'قم بتحديث بيانات المنتج والتفاصيل الأخرى' : 'أضف منتجاً جديداً للمتجر مع الصور والتصنيفات' }}</p>
      </div>
      <NuxtLink to="/products" class="button-secondary back-btn">
        <ArrowRight :size="17" :stroke-width="2.2" />
        العودة للمنتجات
      </NuxtLink>
    </div>

    <!-- General Error -->
    <Transition name="fade">
      <div v-if="errors._general" class="general-error surface-card">
        <span>{{ errors._general }}</span>
      </div>
    </Transition>

    <!-- Form -->
    <form @submit.prevent="submitForm" class="product-form">
      <div class="form-layout">
        <!-- ═══════ Main Column ═══════ -->
        <div class="form-main">

          <!-- Basic Info Section -->
          <section class="form-section surface-card">
            <div class="section-title">
              <FileText :size="20" :stroke-width="2" />
              <h2>المعلومات الأساسية</h2>
            </div>

            <div class="section-body">
              <CustomTextfield
                id="product-name"
                label="اسم المنتج *"
                v-model="form.name"
                placeholder="مثال: قميص رجالي قطني"
                :error="errors.name"
              />



              <div class="field-group">
                <label for="product-desc" class="field-label">الوصف</label>
                <textarea
                  id="product-desc"
                  v-model="form.description"
                  class="text-field textarea-field"
                  placeholder="أدخل وصفاً تفصيلياً للمنتج..."
                  rows="4"
                ></textarea>
                <div class="error-slot">
                  <Transition name="field-error">
                    <span v-if="errors.description" class="form-error">{{ errors.description }}</span>
                  </Transition>
                </div>
              </div>
            </div>
          </section>

          <!-- Pricing Section -->
          <section class="form-section surface-card">
            <div class="section-title">
              <DollarSign :size="20" :stroke-width="2" />
              <h2>التسعير والمخزون</h2>
            </div>

            <div class="section-body">
              <div class="fields-row">
                <CustomTextfield
                  id="product-price"
                  label="السعر *"
                  v-model="form.price"
                  type="number"
                  placeholder="0.00"
                  dir="ltr"
                  :error="errors.price"
                />

                <CustomTextfield
                  id="product-compare-price"
                  label="السعر قبل الخصم"
                  v-model="form.compare_price"
                  type="number"
                  placeholder="0.00"
                  dir="ltr"
                  :error="errors.compare_price"
                />
              </div>

              <div class="fields-row">
                <CustomTextfield
                  id="product-quantity"
                  label="الكمية *"
                  v-model="form.quantity"
                  type="number"
                  placeholder="0"
                  dir="ltr"
                  :error="errors.quantity"
                />

                <CustomTextfield
                  id="product-sku"
                  label="رمز المنتج (SKU)"
                  v-model="form.sku"
                  placeholder="مثال: SHIRT-001"
                  dir="ltr"
                  :error="errors.sku"
                />
              </div>
            </div>
          </section>

          <!-- Images Section -->
          <section class="form-section surface-card">
            <ProductImageUploader
              v-model="form.images"
              :existing-images="existingImages"
              @update:existing-images="existingImages = $event"
              :error="errors.images"
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
                <span class="toggle-text">{{ form.is_active ? 'المنتج نشط وسيظهر في المتجر' : 'المنتج معطّل ولن يظهر' }}</span>
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

          <!-- Categories Section -->
          <section class="form-section surface-card">
            <CategoryPicker v-model="form.category_ids" :error="errors.category_ids" />
          </section>
        </div>
      </div>

      <!-- ═══════ Sticky Submit Bar ═══════ -->
      <div class="sticky-submit-bar">
        <div class="submit-bar-inner">
          <p class="submit-hint">تأكد من مراجعة جميع البيانات قبل الحفظ</p>
          <button
            type="submit"
            class="button-primary submit-btn"
            :disabled="productsStore.submitting"
          >
            <Loader2 v-if="productsStore.submitting" :size="20" :stroke-width="2.5" class="spinner" />
            <Save v-else :size="20" :stroke-width="2.2" />
            {{ productsStore.submitting ? 'جاري الحفظ...' : (isEditing ? 'حفظ التعديلات' : 'حفظ المنتج') }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<style scoped>
.add-product-page {
  display: grid;
  gap: 24px;
}

/* ─── Header ─── */
.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.page-header h1 {
  margin: 8px 0 0;
  font-size: 1.5rem;
  color: var(--color-slate-950);
}

.header-desc {
  margin: 4px 0 0;
  color: var(--color-slate-700);
  font-size: 0.92rem;
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

/* ─── Textarea ─── */
.textarea-field {
  resize: vertical;
  min-height: 100px;
  padding: 14px 16px;
  line-height: 1.7;
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
