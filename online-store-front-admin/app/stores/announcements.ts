import { defineStore } from 'pinia'
import type { Announcement, AnnouncementsResponse } from '~/types/announcement'

export const useAnnouncementsStore = defineStore('announcements', () => {
  const announcements = ref<Announcement[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)

  const submitting = ref(false)
  const validationErrors = ref<Record<string, string>>({})

  async function fetchAnnouncements() {
    const api = useApi()

    loading.value = true
    error.value = null

    try {
      const { data } = await api.get<AnnouncementsResponse>('/announcements')
      announcements.value = data.data
    } catch (err: any) {
      const message = err?.response?.data?.message || err?.message || 'حدث خطأ أثناء تحميل اللوحات الإعلانية.'
      error.value = message
    } finally {
      loading.value = false
    }
  }

  async function fetchAnnouncement(id: number | string): Promise<Announcement | null> {
    const localAnnouncement = announcements.value.find(a => String(a.id) === String(id))
    if (localAnnouncement) {
      return localAnnouncement
    }

    const api = useApi()
    try {
      const { data } = await api.get(`/announcements/${id}`)
      return data.data || data
    } catch (err) {
      console.error('Failed to fetch announcement:', err)
      return null
    }
  }

  async function addAnnouncement(formData: FormData): Promise<boolean> {
    const api = useApi()

    submitting.value = true
    validationErrors.value = {}

    try {
      await api.post('/admin/announcements', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })

      await fetchAnnouncements()
      return true
    } catch (err: any) {
      if (err?.response?.status === 422) {
        const serverErrors = err.response.data.errors || {}
        const mapped: Record<string, string> = {}
        for (const [key, messages] of Object.entries(serverErrors)) {
          mapped[key] = Array.isArray(messages) ? String(messages[0]) : String(messages)
        }
        validationErrors.value = mapped
      } else {
        validationErrors.value._general =
          err?.response?.data?.message || 'حدث خطأ غير متوقع أثناء إضافة اللوحة الإعلانية.'
      }
      return false
    } finally {
      submitting.value = false
    }
  }

  async function updateAnnouncement(id: number | string, formData: FormData): Promise<boolean> {
    const api = useApi()

    submitting.value = true
    validationErrors.value = {}

    formData.append('_method', 'PUT')

    try {
      await api.post(`/admin/announcements/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })

      await fetchAnnouncements()
      return true
    } catch (err: any) {
      if (err?.response?.status === 422) {
        const serverErrors = err.response.data.errors || {}
        const mapped: Record<string, string> = {}
        for (const [key, messages] of Object.entries(serverErrors)) {
          mapped[key] = Array.isArray(messages) ? String(messages[0]) : String(messages)
        }
        validationErrors.value = mapped
      } else {
        validationErrors.value._general =
          err?.response?.data?.message || 'حدث خطأ غير متوقع أثناء تعديل اللوحة الإعلانية.'
      }
      return false
    } finally {
      submitting.value = false
    }
  }

  async function toggleActive(id: number | string): Promise<boolean> {
    const api = useApi()

    try {
      await api.patch(`/admin/announcements/${id}/toggle-active`)

      // Update local state
      const announcement = announcements.value.find(a => String(a.id) === String(id))
      if (announcement) {
        announcement.is_active = !announcement.is_active
      }

      return true
    } catch (err: any) {
      console.error('Failed to toggle announcement active state:', err)
      return false
    }
  }

  async function deleteAnnouncement(id: number | string): Promise<boolean> {
    const api = useApi()

    try {
      await api.delete(`/admin/announcements/${id}`)
      announcements.value = announcements.value.filter(a => String(a.id) !== String(id))
      return true
    } catch (err: any) {
      console.error('Failed to delete announcement:', err)
      return false
    }
  }

  return {
    announcements,
    loading,
    error,
    submitting,
    validationErrors,
    fetchAnnouncements,
    fetchAnnouncement,
    addAnnouncement,
    updateAnnouncement,
    toggleActive,
    deleteAnnouncement,
  }
})
