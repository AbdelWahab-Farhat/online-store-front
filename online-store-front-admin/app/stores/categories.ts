import { defineStore } from 'pinia'
import type { Category, CategoriesResponse } from '~/types/category'

export const useCategoriesStore = defineStore('categories', () => {
  const categories = ref<Category[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)

  const submitting = ref(false)
  const validationErrors = ref<Record<string, string>>({})
  const deletingIds = ref<string[]>([])

  function isDeleting(id: number | string) {
    return deletingIds.value.includes(String(id))
  }

  async function fetchCategories() {
    const api = useApi()

    loading.value = true
    error.value = null

    try {
      const { data } = await api.get<CategoriesResponse>('/categories')
      categories.value = data.data
    } catch (err: any) {
      const message = err?.response?.data?.message || err?.message || 'حدث خطأ أثناء تحميل التصنيفات.'
      error.value = message
    } finally {
      loading.value = false
    }
  }

  async function fetchCategory(id: number | string): Promise<Category | null> {
    const localCategory = categories.value.find(c => String(c.id) === String(id))
    if (localCategory) {
      return localCategory
    }
    
    const api = useApi()
    try {
      const { data } = await api.get(`/categories/${id}`)
      return data.data || data
    } catch (err) {
      console.error('Failed to fetch category:', err)
      return null
    }
  }

  async function addCategory(formData: FormData): Promise<boolean> {
    const api = useApi()

    submitting.value = true
    validationErrors.value = {}

    try {
      await api.post('/admin/categories', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })

      await fetchCategories()
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
          err?.response?.data?.message || 'حدث خطأ غير متوقع أثناء إضافة التصنيف.'
      }
      return false
    } finally {
      submitting.value = false
    }
  }

  async function updateCategory(id: number | string, formData: FormData): Promise<boolean> {
    const api = useApi()

    submitting.value = true
    validationErrors.value = {}

    formData.append('_method', 'PUT')

    try {
      await api.post(`/admin/categories/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })

      await fetchCategories()
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
          err?.response?.data?.message || 'حدث خطأ غير متوقع أثناء تعديل التصنيف.'
      }
      return false
    } finally {
      submitting.value = false
    }
  }

  async function deleteCategory(id: number | string): Promise<{ success: boolean; message: string }> {
    const api = useApi()
    const normalizedId = String(id)

    deletingIds.value = [...deletingIds.value, normalizedId]

    try {
      const { data } = await api.delete(`/admin/categories/${id}`)
      await fetchCategories()

      return {
        success: true,
        message: data?.message || 'تم حذف التصنيف بنجاح.',
      }
    } catch (err: any) {
      return {
        success: false,
        message: err?.response?.data?.message || 'حدث خطأ أثناء حذف التصنيف.',
      }
    } finally {
      deletingIds.value = deletingIds.value.filter(currentId => currentId !== normalizedId)
    }
  }

  return {
    categories,
    loading,
    error,
    submitting,
    validationErrors,
    deletingIds,
    isDeleting,
    fetchCategories,
    fetchCategory,
    addCategory,
    updateCategory,
    deleteCategory,
  }
})
