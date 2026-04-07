export interface Coupon {
  id: number
  code: string
  discount_type: 'percent' | 'fixed' | string
  discount_value: number
  min_order_amount: number | null
  max_discount: number | null
  usage_limit: number | null
  used_count: number
  starts_at: string | null
  expires_at: string | null
  is_active: boolean
  is_valid: boolean
  created_at: string
  updated_at: string
}

export interface CouponsResponse {
  data: Coupon[]
}
