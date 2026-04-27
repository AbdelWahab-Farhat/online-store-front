import type { ImageResource } from './category'

export interface Announcement {
  id: number
  title: string
  description: string | null
  is_active: boolean
  sort_order: number
  link: string | null
  link_text: string | null
  image: ImageResource | null
  created_at: string
  updated_at: string
}

export interface AnnouncementsResponse {
  data: Announcement[]
}
