import { defineStore } from 'pinia'
import type { Category } from './categories'

export interface ProductImage {
  id: number
  url: string
  sort_order: number
}

export interface Product {
  id: number
  name: string
  slug: string
  description: string
  price: number
  compare_price: number | null
  has_discount: boolean
  discount_percentage: number | null
  quantity: number
  in_stock: boolean
  sku: string | null
  is_active: boolean
  categories: Category[]
  images: ProductImage[]
  created_at: string
  updated_at: string
}

export interface ProductPagination {
  currentPage: number
  lastPage: number
  total: number
  perPage: number
}

export interface ProductFilters {
  search?: string
  category_slug?: string
  sort_by?: string
  sort_dir?: string
  per_page?: number
  page?: number
}

export const useProductsStore = defineStore('products', () => {
  const { $axios } = useNuxtApp()

  const products = ref<Product[]>([])
  const loading = ref(false)
  const loadingMore = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref<ProductPagination>({
    currentPage: 1,
    lastPage: 1,
    total: 0,
    perPage: 15,
  })
  const currentFilters = ref<ProductFilters>({})

  const hasMore = computed(() => pagination.value.currentPage < pagination.value.lastPage)

  async function fetchProducts(filters: ProductFilters = {}, append = false) {
    if (append) {
      loadingMore.value = true
    } else {
      loading.value = true
      products.value = []
    }
    error.value = null
    currentFilters.value = { ...filters }

    try {
      const params: Record<string, any> = {}
      if (filters.search) params.search = filters.search
      if (filters.category_slug) params.category_slug = filters.category_slug
      if (filters.sort_by) params.sort_by = filters.sort_by
      if (filters.sort_dir) params.sort_dir = filters.sort_dir
      if (filters.per_page) params.per_page = filters.per_page
      if (filters.page) params.page = filters.page

      const { data } = await $axios.get('/products', { params })

      if (append) {
        products.value = [...products.value, ...data.data]
      } else {
        products.value = data.data
      }

      pagination.value = {
        currentPage: data.meta.current_page,
        lastPage: data.meta.last_page,
        total: data.meta.total,
        perPage: data.meta.per_page,
      }
    } catch (e: any) {
      error.value = e.message || 'فشل تحميل المنتجات'
      console.error('Failed to fetch products:', e)
    } finally {
      loading.value = false
      loadingMore.value = false
    }
  }

  async function loadMore() {
    if (!hasMore.value || loadingMore.value) return
    await fetchProducts(
      {
        ...currentFilters.value,
        page: pagination.value.currentPage + 1,
      },
      true
    )
  }

  function reset() {
    products.value = []
    pagination.value = { currentPage: 1, lastPage: 1, total: 0, perPage: 15 }
    currentFilters.value = {}
    error.value = null
  }

  return {
    products,
    loading,
    loadingMore,
    error,
    pagination,
    hasMore,
    currentFilters,
    fetchProducts,
    loadMore,
    reset,
  }
})
