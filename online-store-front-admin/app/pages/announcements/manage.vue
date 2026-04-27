<script setup lang="ts">
import { ArrowRight, Save, Loader2, Megaphone, PackagePlus, Link2, SortAsc } from 'lucide-vue-next'

definePageMeta({
  title: 'إدارة اللوحة الإعلانية',
  description: 'إضافة أو تعديل لوحة إعلانية في المتجر.',
})

const router = useRouter()
const route = useRoute()
const announcementsStore = useAnnouncementsStore()
const dialog = useDialog()

const isEditing = computed(() => !!route.query.id)
const announcementId = computed(() => route.query.id as string)

const existingImages = ref<any[]>([])

// ─── Form State ───
const form = reactive({
  title: '',
  description: '',
  is_active: true,
  sort_order: 0,
  link: '',
  link_text: '',
  images: [] as File[],
})

const localErrors = ref<Record<string, string>>({})

// Merge local validation + store server errors
const errors = computed(() => ({
  ...localErrors.value,
  ...announcementsStore.validationErrors,
}))

// ─── Load Data if Editing ───
onMounted(async () => {
  if (isEditing.value) {
    const announcement = await announcementsStore.fetchAnnouncement(announcementId.value)
    if (announcement) {
      form.title = announcement.title
      form.description = announcement.description || ''
      form.is_active = announcement.is_active
      form.sort_order = announcement.sort_order ?? 0
      form.link = announcement.link || ''
      form.link_text = announcement.link_text || ''
      if (announcement.image) {
        existingImages.value = [announcement.image]
      }
    }
  }
})

// ─── Validation ───
function validate(): boolean {
  const e: Record<string, string> = {}
  if (!form.title.trim()) e.title = 'عنوان اللوحة الإعلانية مطلوب'
  if (!isEditing.value && form.images.length === 0 && existingImages.value.length === 0) {
    e.image = 'صورة اللوحة الإعلانية مطلوبة'
  }

  localErrors.value = e
  return Object.keys(e).length === 0
}

// ─── Build FormData ───
function buildFormData(): FormData {
  const formData = new FormData()
  formData.append('title', form.title)
  formData.append('is_active', form.is_active ? '1' : '0')
  formData.append('sort_order', String(form.sort_order))

  if (form.description.trim()) {
    formData.append('description', form.description)
  }
  if (form.link.trim()) {
    formData.append('link', form.link)
  }
  if (form.link_text.trim()) {
    formData.append('link_text', form.link_text)
  }

  if (form.images && form.images.length > 0) {
    const firstImage = form.images[0]
    if (firstImage) {
      formData.append('image', firstImage)
    }
  }

  return formData
}

// ─── Submit ───
async function submitForm() {
  if (!validate()) return

  localErrors.value = {}
  announcementsStore.validationErrors = {}

  const formData = buildFormData()

  let success = false
  if (isEditing.value) {
    success = await announcementsStore.updateAnnouncement(announcementId.value, formData)
  } else {
    success = await announcementsStore.addAnnouncement(formData)
  }

  if (success) {
    dialog.success({
      title: isEditing.value ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح',
      message: isEditing.value ? 'تم حفظ التعديلات بنجاح.' : 'تم إضافة اللوحة الإعلانية بنجاح.',
      onConfirm: () => router.push('/announcements'),
    })
  } else {
    dialog.error({
      title: isEditing.value ? 'فشل في التعديل' : 'فشل في الإضافة',
      message: errors.value._general || errors.value.limit || 'تحقق من البيانات المدخلة وحاول مرة أخرى.',
    })
  }
}
</script>

<template>
  <div class="add-product-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header-copy">
        <span class="section-eyebrow">{{ isEditing ? 'Edit Announcement' : 'New Announcement' }}</span>
        <h1>{{ isEditing ? 'تعديل اللوحة الإعلانية' : 'إضافة لوحة إعلانية جديدة' }}</h1>
        <p class="header-desc">{{ isEditing ? 'تعديل بيانات اللوحة الإعلانية الحالية' : 'املأ البيانات لإنشاء لوحة إعلانية جديدة في المتجر' }}</p>
      </div>
      <NuxtLink to="/announcements" class="button-secondary back-btn">
        <ArrowRight :size="17" :stroke-width="2.2" />
        العودة للوحات الإعلانية
      </NuxtLink>
    </div>

    <!-- General Error -->
    <Transition name="fade">
      <div v-if="errors._general || errors.limit" class="general-error surface-card">
        <span>{{ errors._general || errors.limit }}</span>
      </div>
    </Transition>

    <div class="page-content">
      <div class="form-layout">

        <!-- ═══════ Main Form Area ═══════ -->
        <div class="form-main">
          <!-- Basic Info Section -->
          <section class="form-section surface-card">
            <div class="section-title">
              <Megaphone :size="20" :stroke-width="2" />
              <h2>البيانات الأساسية</h2>
            </div>

            <div class="section-body">
              <CustomTextfield
                id="announcement-title"
                label="العنوان *"
                v-model="form.title"
                placeholder="مثال: عروض الصيف"
                :error="errors.title"
              />

              <div class="field-group">
                <label for="announcement-description" class="field-label">الوصف</label>
                <textarea
                  id="announcement-description"
                  v-model="form.description"
                  class="text-field textarea-field"
                  placeholder="وصف مختصر للوحة الإعلانية (اختياري)"
                  rows="3"
                ></textarea>
                <div class="error-slot">
                  <Transition name="field-error">
                    <span v-if="errors.description" class="form-error">{{ errors.description }}</span>
                  </Transition>
                </div>
              </div>
            </div>
          </section>

          <!-- Link Section -->
          <section class="form-section surface-card">
            <div class="section-title">
              <Link2 :size="20" :stroke-width="2" />
              <h2>الرابط (اختياري)</h2>
            </div>

            <div class="section-body">
              <CustomTextfield
                id="announcement-link"
                label="رابط الإعلان"
                v-model="form.link"
                placeholder="https://example.com/offer"
                :error="errors.link"
                dir="ltr"
              />

              <CustomTextfield
                id="announcement-link-text"
                label="نص الرابط"
                v-model="form.link_text"
                placeholder="مثال: تسوق الآن"
                :error="errors.link_text"
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
              label="صورة اللوحة الإعلانية"
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
                <span class="toggle-text">{{ form.is_active ? 'اللوحة نشطة وستظهر للزوار' : 'اللوحة معطّلة ولن تظهر' }}</span>
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

          <!-- Sort Order Section -->
          <section class="form-section surface-card">
            <div class="section-title">
              <SortAsc :size="20" :stroke-width="2" />
              <h2>ترتيب العرض</h2>
            </div>

            <div class="section-body">
              <CustomTextfield
                id="announcement-sort-order"
                label="رقم الترتيب"
                v-model="form.sort_order"
                type="number"
                placeholder="0"
                :error="errors.sort_order"
              />
              <span class="input-hint">الأرقام الأصغر تظهر أولاً</span>
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
          :disabled="announcementsStore.submitting"
        >
          <template v-if="announcementsStore.submitting">
            <Loader2 :size="18" :stroke-width="2.5" class="spinner" />
            جاري الحفظ...
          </template>
          <template v-else>
            <Save :size="18" :stroke-width="2.5" />
            حفظ اللوحة الإعلانية
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

/* ─── Textarea ─── */
.textarea-field {
  resize: vertical;
  padding: 14px 16px;
  min-height: 90px;
  line-height: 1.7;
}

/* ─── Error slot ─── */
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

  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
