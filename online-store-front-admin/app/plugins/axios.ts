import axios from 'axios'
import { AUTH_TOKEN_COOKIE } from '~/constants/auth'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const router = useRouter()
  const authStore = useAuthStore()
  const tokenCookie = useCookie<string | null>(AUTH_TOKEN_COOKIE, {
    default: () => null,
    sameSite: 'lax',
    secure: false,
  })

  const api = axios.create({
    baseURL: config.public.apiBaseUrl,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  })

  api.interceptors.request.use((request) => {
    const token = authStore.token || tokenCookie.value

    if (token) {
      request.headers.Authorization = `Bearer ${token}`
    }

    return request
  })

  api.interceptors.response.use(
    (response) => response,
    async (error) => {
      if (error.response?.status === 401) {
        authStore.clearAuth()

        if (import.meta.client && router.currentRoute.value.path !== '/login') {
          await navigateTo('/login')
        }
      }

      return Promise.reject(error)
    },
  )

  return {
    provide: {
      axios: api,
    },
  }
})
