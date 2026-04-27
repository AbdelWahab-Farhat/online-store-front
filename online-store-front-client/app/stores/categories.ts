import { defineStore } from 'pinia'

export interface CategoryImage {
  id: number
  url: string
  sort_order: number
}

export interface Category {
  id: number
  name: string
  slug: string
  is_active: boolean
  image: CategoryImage | null
  products_count: number
}

export const useCategoriesStore = defineStore('categories', () => {
  const { $axios } = useNuxtApp()
  
  const categories = ref<Category[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchCategories() {
    if (categories.value.length > 0) return // already loaded
    loading.value = true
    error.value = null
    try {
      const { data } = await $axios.get('/categories')
      categories.value = data.data
    } catch (e: any) {
      error.value = e.message || 'فشل تحميل التصنيفات'
      console.error('Failed to fetch categories:', e)
    } finally {
      loading.value = false
    }
  }

  function getCategoryBySlug(slug: string): Category | undefined {
    return categories.value.find((c) => c.slug === slug)
  }

  function getCategoryName(slug: string): string {
    return getCategoryBySlug(slug)?.name || 'المنتجات'
  }

  return {
    categories,
    loading,
    error,
    fetchCategories,
    getCategoryBySlug,
    getCategoryName,
  }
})
