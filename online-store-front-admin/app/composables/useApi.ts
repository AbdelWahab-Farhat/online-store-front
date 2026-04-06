import type { AxiosInstance } from 'axios'

export function useApi() {
  return (useNuxtApp() as ReturnType<typeof useNuxtApp> & { $axios: AxiosInstance }).$axios
}
