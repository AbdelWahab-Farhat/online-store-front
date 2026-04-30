import { defineStore } from 'pinia'
import type { Product, ProductsResponse } from '~/types/product'

export const useProductsStore = defineStore('products', () => {
  const products = ref<Product[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)
  const currentPage = ref(1)
  const lastPage = ref(1)
  const perPage = ref(15)
  const total = ref(0)
  const from = ref<number | null>(null)
  const to = ref<number | null>(null)

  const searchQuery = ref('')
  const selectedCategoryId = ref<number | null>(null)

  // ─── Add Product State ───
  const submitting = ref(false)
  const validationErrors = ref<Record<string, string>>({})
  const deletingIds = ref<string[]>([])

  function isDeleting(id: number | string) {
    return deletingIds.value.includes(String(id))
  }

  async function fetchProducts(page = 1) {
    const api = useApi()

    loading.value = true
    error.value = null

    try {
      const { data } = await api.get<ProductsResponse>('/products', {
        params: {
          page,
          per_page: perPage.value,
          search: searchQuery.value || undefined,
          category_id: selectedCategoryId.value || undefined
        },
      })
      products.value = data.data
      currentPage.value = data.meta.current_page
      lastPage.value = data.meta.last_page
      perPage.value = data.meta.per_page
      total.value = data.meta.total
      from.value = data.meta.from
      to.value = data.meta.to
    } catch (err: any) {
      const message = err?.response?.data?.message || err?.message || 'حدث خطأ أثناء تحميل المنتجات.'
      error.value = message
    } finally {
      loading.value = false
    }
  }

  async function addProduct(formData: FormData): Promise<boolean> {
    const api = useApi()

    submitting.value = true
    validationErrors.value = {}

    try {
      await api.post('/admin/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })

      // Refresh list after successful add
      await fetchProducts()
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
          err?.response?.data?.message || 'حدث خطأ غير متوقع أثناء إضافة المنتج.'
      }
      return false
    } finally {
      submitting.value = false
    }
  }

  async function fetchProduct(id: number | string): Promise<Product | null> {
    const localProduct = products.value.find(p => String(p.id) === String(id))
    if (localProduct) {
      return localProduct
    }
    
    const api = useApi()
    try {
      const { data } = await api.get(`/products/${id}`)
      return data.data || data
    } catch (err) {
      console.error('Failed to fetch product:', err)
      return null
    }
  }

  async function updateProduct(id: number | string, formData: FormData): Promise<boolean> {
    const api = useApi()

    submitting.value = true
    validationErrors.value = {}

    // Laravel handles typical PUT requests for multipart by checking the _method field
    formData.append('_method', 'PUT')

    try {
      await api.post(`/admin/products/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })

      await fetchProducts(currentPage.value)
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
          err?.response?.data?.message || 'حدث خطأ غير متوقع أثناء تعديل المنتج.'
      }
      return false
    } finally {
      submitting.value = false
    }
  }

  async function deleteProduct(id: number | string): Promise<{ success: boolean; message: string }> {
    const api = useApi()
    const normalizedId = String(id)

    deletingIds.value = [...deletingIds.value, normalizedId]

    try {
      const { data } = await api.delete(`/admin/products/${id}`)
      const nextPage = products.value.length === 1 && currentPage.value > 1
        ? currentPage.value - 1
        : currentPage.value

      await fetchProducts(nextPage)

      return {
        success: true,
        message: data?.message || 'تم حذف المنتج بنجاح.',
      }
    } catch (err: any) {
      return {
        success: false,
        message: err?.response?.data?.message || 'حدث خطأ أثناء حذف المنتج.',
      }
    } finally {
      deletingIds.value = deletingIds.value.filter(currentId => currentId !== normalizedId)
    }
  }

  return {
    products,
    loading,
    error,
    currentPage,
    lastPage,
    perPage,
    total,
    from,
    to,
    searchQuery,
    selectedCategoryId,
    submitting,
    validationErrors,
    deletingIds,
    isDeleting,
    fetchProducts,
    fetchProduct,
    addProduct,
    updateProduct,
    deleteProduct,
  }
})
