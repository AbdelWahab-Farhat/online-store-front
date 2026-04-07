import { defineStore } from 'pinia'
import type { Coupon, CouponsResponse } from '~/types/coupon'

export const useCouponsStore = defineStore('coupons', () => {
  const coupons = ref<Coupon[]>([])
  const loading = ref(true)
  const error = ref<string | null>(null)

  async function fetchCoupons() {
    const api = useApi()

    loading.value = true
    error.value = null

    try {
      const { data } = await api.get<CouponsResponse>('/admin/coupons')
      coupons.value = data.data
    } catch (err: any) {
      const message = err?.response?.data?.message || err?.message || 'حدث خطأ أثناء تحميل الكوبونات.'
      error.value = message
    } finally {
      loading.value = false
    }
  }

  return {
    coupons,
    loading,
    error,
    fetchCoupons,
  }
})
