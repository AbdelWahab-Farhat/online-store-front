export interface ProductImage {
  id: number
  url: string
  sort_order: number
}

export interface ProductCategory {
  id: number
  name: string
  slug: string
}

export interface Product {
  id: number
  name: string
  slug: string
  description: string | null
  price: number
  compare_price: number | null
  has_discount: boolean
  discount_percentage: number | null
  quantity: number
  in_stock: boolean
  sku: string | null
  is_active: boolean
  categories: ProductCategory[]
  images: ProductImage[]
  created_at: string
  updated_at: string
}

export interface ProductsResponse {
  data: Product[]
  links: {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number | null
    last_page: number
    path: string
    per_page: number
    to: number | null
    total: number
  }
}
