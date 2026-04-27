<script setup lang="ts">
import type { SettingItem } from '~/stores/settings'
import { 
  Settings as SettingsIcon, 
  Save, 
  Loader2, 
  AlertCircle 
} from 'lucide-vue-next'
import { useSettingsStore } from '~/stores/settings'

definePageMeta({
  title: 'الإعدادات',
  description: 'إدارة الإعدادات العامة والتفضيلات الخاصة بمتجرك',
})

const settingsStore = useSettingsStore()
const dialog = useDialog()

// Form state
const form = ref<Record<string, string>>({})
const isSubmitting = ref(false)

// Labels map for dynamic fields
const labelsMap: Record<string, string> = {
  whatsapp_number: 'رقم الواتساب',
  // Future settings can be mapped here
}

function normalizeFormValues(settings: Record<string, string | undefined>) {
  return Object.entries(settings).reduce<Record<string, string>>((mappedValues, [key, value]) => {
    mappedValues[key] = value ?? ''
    return mappedValues
  }, {})
}

const displayedSettings = computed<SettingItem[]>(() => {
  if (settingsStore.settingItems.length > 0) {
    return settingsStore.settingItems
  }

  return Object.entries(form.value).map(([key, value]) => ({
    id: key,
    key,
    value,
  }))
})

function getLabel(setting: Pick<SettingItem, 'key' | 'label'>) {
  if (setting.label) return setting.label
  if (labelsMap[setting.key]) return labelsMap[setting.key]
  // Fallback label generation
  return setting.key
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

function getPlaceholder(key: string) {
  if (key === 'whatsapp_number') return 'مثال: 218912345678'
  return `أدخل ${getLabel({ key })}`
}

function formatUpdatedAt(updatedAt?: string) {
  if (!updatedAt) return null

  const date = new Date(updatedAt)

  if (Number.isNaN(date.getTime())) {
    return null
  }

  return new Intl.DateTimeFormat('ar', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(date)
}

// English-like keys standard LTR (e.g. phone numbers, API keys)
function isEnglishLike(key: string) {
  return key.includes('number') || key.includes('url') || key.includes('key') || key.includes('token')
}

const savedSettings = computed(() => {
  return normalizeFormValues(settingsStore.settings as Record<string, string | undefined>)
})

// Track if form has changes
const hasChanges = computed(() => {
  return JSON.stringify(form.value) !== JSON.stringify(savedSettings.value)
})

onMounted(async () => {
  await settingsStore.fetchSettings()
  form.value = { ...savedSettings.value }
})

async function submitForm() {
  if (!hasChanges.value) return
  
  isSubmitting.value = true
  try {
    await settingsStore.updateSettings(form.value)
    form.value = { ...savedSettings.value }
    dialog.success({
      title: 'تم التعديل بنجاح',
      message: 'تم حفظ إعدادات المتجر بنجاح.',
    })
  } catch (error: any) {
    dialog.error({
      title: 'خطأ في الحفظ',
      message: error.response?.data?.message || settingsStore.error || 'فشل في حفظ الإعدادات، يرجى المحاولة مرة أخرى.',
    })
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="settings-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header-copy">
        <span class="section-eyebrow">Settings Management</span>
        <h1>الإعدادات العامة</h1>
        <p class="header-desc">إدارة الإعدادات العامة والتفضيلات الخاصة بمتجرك</p>
      </div>
    </div>

    <!-- General Error -->
    <Transition name="fade">
      <div v-if="settingsStore.error && !isSubmitting" class="general-error surface-card">
        <span>{{ settingsStore.error }}</span>
      </div>
    </Transition>

    <div class="page-content">
      <div class="form-layout">
        
        <!-- ═══════ Main Form Area ═══════ -->
        <div class="form-main">

          <LoadingState v-if="settingsStore.loading" message="جاري تحميل الإعدادات..." />

          <section v-else class="form-section surface-card">
            <div class="section-title">
              <SettingsIcon :size="20" :stroke-width="2" />
              <h2>البيانات الأساسية</h2>
            </div>
            
            <div class="section-body">
              <div v-if="displayedSettings.length === 0" class="no-settings">
                <AlertCircle :size="32" class="text-gray-400 mb-2" />
                <p>لا توجد إعدادات متاحة حالياً للتعديل.</p>
              </div>

              <!-- Dynamic Settings Form Grid -->
              <div v-else class="fields-row">
                <div
                  v-for="setting in displayedSettings"
                  :key="String(setting.id)"
                  class="field-card"
                >
                  <CustomTextfield
                    :id="setting.key"
                    v-model="form[setting.key]"
                    :label="getLabel(setting)"
                    :dir="isEnglishLike(setting.key) ? 'ltr' : 'auto'"
                    :placeholder="getPlaceholder(setting.key)"
                  />

                  <div class="field-meta">
                    <span class="field-key">{{ setting.key }}</span>
                    <span v-if="formatUpdatedAt(setting.updated_at)" class="field-updated">
                      آخر تحديث: {{ formatUpdatedAt(setting.updated_at) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- ═══════ Sidebar ═══════ -->
        <div class="form-sidebar" v-if="!settingsStore.loading">
          
          <!-- Actions Section -->
          <section class="form-section surface-card">
            <div class="section-title">
              <Save :size="20" :stroke-width="2" />
              <h2>حفظ الإعدادات</h2>
            </div>
            <div class="section-body">
              <p class="text-sm text-gray-500 mb-4">
                تأكد من صحة البيانات قبل حفظها لتطبيقها فوراً في المتجر.
              </p>
              <button
                class="button-primary submit-btn w-full"
                @click="submitForm"
                :disabled="isSubmitting || !hasChanges"
              >
                <template v-if="isSubmitting">
                  <Loader2 :size="18" :stroke-width="2.5" class="spinner" />
                  جاري الحفظ...
                </template>
                <template v-else>
                  <Save :size="18" :stroke-width="2.5" />
                  حفظ التغييرات
                </template>
              </button>
            </div>
          </section>

          <!-- Notes Section -->
          <section class="form-section surface-card mt-5">
            <div class="section-title">
              <AlertCircle :size="20" :stroke-width="2" />
              <h2>ملاحظات</h2>
            </div>
            <div class="section-body info-box">
              <p>تُطبق التغييرات في لوحة الإعدادات وتظهر فوراً على واجهة المستخدم في متجرك.</p>
              <br>
              <p>سيتم جلب جميع الإعدادات الجديدة مستقبلاً بشكل تلقائي وعرضها في هذه الشاشة.</p>
            </div>
          </section>

        </div>

      </div>
    </div>
  </div>
</template>

<style scoped>
.settings-page {
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

/* ─── General Error ─── */
.general-error {
  padding: 16px 24px;
  border-color: rgba(202, 61, 84, 0.3);
  background: rgba(202, 61, 84, 0.06);
  color: var(--color-red-600);
  font-weight: 700;
  font-size: 0.92rem;
}

/* Loading state is now handled by reusable LoadingState component */

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
  gap: 16px;
}

.info-box p {
  margin: 0;
  font-size: 0.9rem;
  color: var(--color-slate-600);
  line-height: 1.6;
}

.no-settings {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 0;
  color: var(--color-slate-500);
}

/* ─── Fields Row ─── */
.fields-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

.field-card {
  display: grid;
  gap: 8px;
}

.field-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
}

.field-key,
.field-updated {
  font-size: 0.82rem;
  color: var(--color-slate-500);
}

.field-key {
  padding: 4px 10px;
  border-radius: 999px;
  background: rgba(148, 163, 184, 0.12);
  color: var(--color-slate-700);
  direction: ltr;
}

.submit-btn {
  min-height: 48px;
  min-width: 180px;
  font-size: 0.95rem;
  border-radius: 14px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none !important;
}

.w-full {
  width: 100%;
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
