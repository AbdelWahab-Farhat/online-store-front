import { defineStore } from 'pinia'
import { AxiosError } from 'axios'
import { AUTH_TOKEN_COOKIE, AUTH_USER_COOKIE, MANAGEMENT_ROLES } from '~/constants/auth'
import type { AuthUser, LoginPayload, LoginResponse, ProfileResponse } from '~/types/auth'

function extractApiMessage(error: unknown, fallback: string) {
  if (error instanceof AxiosError) {
    return (error.response?.data as { message?: string } | undefined)?.message || fallback
  }

  if (error instanceof Error && error.message) {
    return error.message
  }

  return fallback
}

export const useAuthStore = defineStore('auth', () => {
  const tokenCookie = useCookie<string | null>(AUTH_TOKEN_COOKIE, {
    default: () => null,
    sameSite: 'lax',
    secure: false,
    maxAge: 60 * 60 * 24 * 7,
  })

  const userCookie = useCookie<AuthUser | null>(AUTH_USER_COOKIE, {
    default: () => null,
    sameSite: 'lax',
    secure: false,
    maxAge: 60 * 60 * 24 * 7,
  })

  const token = ref<string | null>(tokenCookie.value)
  const user = ref<AuthUser | null>(userCookie.value)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const isAuthenticated = computed(() => Boolean(token.value))
  const hasManagementAccess = computed(() =>
    user.value?.role ? MANAGEMENT_ROLES.includes(user.value.role as (typeof MANAGEMENT_ROLES)[number]) : false,
  )

  watch(token, (value) => {
    tokenCookie.value = value
  }, { immediate: true })

  watch(user, (value) => {
    userCookie.value = value
  }, { immediate: true, deep: true })

  function setAuth(payload: { token: string; user: AuthUser }) {
    token.value = payload.token
    user.value = payload.user
  }

  function clearAuth() {
    token.value = null
    user.value = null
  }

  async function login(payload: LoginPayload) {
    const api = useApi()

    loading.value = true
    error.value = null

    try {
      const { data } = await api.post<LoginResponse>('/auth/login', payload)

      if (!data.user?.role || !MANAGEMENT_ROLES.includes(data.user.role as (typeof MANAGEMENT_ROLES)[number])) {
        clearAuth()
        throw new Error('هذا الحساب لا يملك صلاحية الدخول إلى لوحة الإدارة.')
      }

      setAuth({
        token: data.token,
        user: data.user,
      })

      return data
    } catch (err) {
      error.value = extractApiMessage(err, 'تعذر تسجيل الدخول حاليًا.')
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchProfile() {
    if (!token.value) {
      return null
    }

    const api = useApi()

    try {
      const { data } = await api.get<ProfileResponse>('/auth/profile')
      user.value = data.user
      return data.user
    } catch (error) {
      clearAuth()
      throw new Error(extractApiMessage(error, 'تعذر تحميل بيانات المستخدم.'))
    }
  }

  async function logout() {
    const api = useApi()

    try {
      if (token.value) {
        await api.post('/auth/logout')
      }
    } catch {
      // نحافظ على تجربة خروج مرنة حتى إذا انتهت صلاحية التوكن على الخادم.
    } finally {
      clearAuth()
    }
  }

  return {
    token,
    user,
    loading,
    error,
    isAuthenticated,
    hasManagementAccess,
    setAuth,
    clearAuth,
    login,
    logout,
    fetchProfile,
  }
})
