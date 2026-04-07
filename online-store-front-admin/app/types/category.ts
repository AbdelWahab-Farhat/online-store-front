export interface ImageResource {
  id: number
  url: string
}

export interface Category {
  id: number
  name: string
  slug: string
  is_active: boolean
  image: ImageResource | null
  products_count?: number
  created_at: string
  updated_at: string
}

export interface CategoriesResponse {
  data: Category[]
}
