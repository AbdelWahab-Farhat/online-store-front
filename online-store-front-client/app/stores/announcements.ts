import { defineStore } from 'pinia'

export interface AnnouncementImage {
  id: number
  url: string
  sort_order: number
}

export interface Announcement {
  id: number
  title: string
  description: string | null
  is_active: boolean
  sort_order: number
  link: string | null
  link_text: string | null
  image: AnnouncementImage | null
}

export const useAnnouncementsStore = defineStore('announcements', () => {
  const { $axios } = useNuxtApp()

  const announcements = ref<Announcement[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchAnnouncements() {
    if (announcements.value.length > 0) return
    loading.value = true
    error.value = null
    try {
      const { data } = await $axios.get('/announcements')
      announcements.value = data.data
    } catch (e: any) {
      error.value = e.message || 'فشل تحميل الإعلانات'
      console.error('Failed to fetch announcements:', e)
    } finally {
      loading.value = false
    }
  }

  return {
    announcements,
    loading,
    error,
    fetchAnnouncements,
  }
})
