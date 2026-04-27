import { defineStore } from 'pinia'

export interface SettingsMap {
  whatsapp_number?: string
  [key: string]: string | undefined
}

export interface SettingItem {
  id: number | string
  key: string
  value: string
  label?: string
  created_at?: string
  updated_at?: string
}

type RawSettingItem = Partial<SettingItem> & {
  value?: unknown
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return !!value && typeof value === 'object' && !Array.isArray(value)
}

function toSettingValue(value: unknown) {
  if (value === null || value === undefined) return ''
  return String(value)
}

function normalizeSettingItem(item: RawSettingItem): SettingItem | null {
  if (!item.key || typeof item.key !== 'string') return null

  return {
    id: typeof item.id === 'number' || typeof item.id === 'string' ? item.id : item.key,
    key: item.key,
    value: toSettingValue(item.value),
    label: typeof item.label === 'string' && item.label.trim() ? item.label : undefined,
    created_at: typeof item.created_at === 'string' ? item.created_at : undefined,
    updated_at: typeof item.updated_at === 'string' ? item.updated_at : undefined,
  }
}

function mapItemsToSettings(items: SettingItem[]) {
  return items.reduce<SettingsMap>((mappedSettings, item) => {
    mappedSettings[item.key] = item.value
    return mappedSettings
  }, {})
}

function buildItemsFromSettingsMap(
  mappedSettings: SettingsMap,
  existingItems: SettingItem[] = [],
) {
  const existingItemsByKey = new Map(existingItems.map(item => [item.key, item]))

  return Object.entries(mappedSettings).map(([key, value]) => {
    const existingItem = existingItemsByKey.get(key)

    return {
      id: existingItem?.id ?? key,
      key,
      value: value ?? '',
      label: existingItem?.label,
      created_at: existingItem?.created_at,
      updated_at: existingItem?.updated_at,
    }
  })
}

export const useSettingsStore = defineStore('settings', () => {
  const { $axios } = useNuxtApp()

  const settings = ref<SettingsMap>({})
  const settingItems = ref<SettingItem[]>([])
  const loading = ref(false)
  const saving = ref(false)
  const error = ref<string | null>(null)

  function syncSettingsState(payload: unknown) {
    const responseData = isRecord(payload) && 'data' in payload ? payload.data : payload

    if (Array.isArray(responseData)) {
      const normalizedItems = responseData
        .map(item => normalizeSettingItem(item as RawSettingItem))
        .filter((item): item is SettingItem => item !== null)

      settingItems.value = normalizedItems
      settings.value = mapItemsToSettings(normalizedItems)
      return
    }

    if (isRecord(responseData)) {
      const mappedSettings = Object.entries(responseData).reduce<SettingsMap>((acc, [key, value]) => {
        acc[key] = toSettingValue(value)
        return acc
      }, {})

      settings.value = mappedSettings
      settingItems.value = buildItemsFromSettingsMap(mappedSettings, settingItems.value)
      return
    }

    settings.value = {}
    settingItems.value = []
  }

  async function fetchSettings() {
    loading.value = true
    error.value = null
    try {
      const { data } = await $axios.get('/admin/settings')
      syncSettingsState(data)
    } catch (e: any) {
      error.value = e.message || 'فشل تحميل الإعدادات'
      console.error('Failed to fetch settings:', e)
    } finally {
      loading.value = false
    }
  }

  async function updateSettings(payload: SettingsMap) {
    saving.value = true
    error.value = null
    try {
      const { data } = await $axios.put('/admin/settings', payload)
      syncSettingsState(data)
      return true
    } catch (e: any) {
      error.value = e.response?.data?.message || e.message || 'فشل تحديث الإعدادات'
      throw e
    } finally {
      saving.value = false
    }
  }

  return {
    settings,
    settingItems,
    loading,
    saving,
    error,
    fetchSettings,
    updateSettings,
  }
})
