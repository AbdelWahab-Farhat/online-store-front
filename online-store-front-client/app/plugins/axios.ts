import axios from 'axios'

function replaceInternalUrls(data: any, internalHost: string, publicHost: string): any {
  if (typeof data === 'string') {
    if (data.includes(internalHost)) {
      return data.replace(new RegExp(internalHost, 'g'), publicHost)
    }
    return data
  }
  
  if (Array.isArray(data)) {
    return data.map(item => replaceInternalUrls(item, internalHost, publicHost))
  }
  
  if (data !== null && typeof data === 'object') {
    const newData: any = {}
    for (const key in data) {
      newData[key] = replaceInternalUrls(data[key], internalHost, publicHost)
    }
    return newData
  }
  
  return data
}

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()

  const internalBase = process.env.NUXT_API_INTERNAL_BASE as string | undefined
  const publicBase = config.public.apiBase as string

  // In SSR (server-side), use internal Docker network URL if available
  // In browser (client-side), always use the public URL
  const baseURL = import.meta.server
    ? (internalBase || publicBase)
    : publicBase

  const api = axios.create({
    baseURL,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  })
  
  // Replace internal docker image URLs with public URLs during SSR fetch
  if (import.meta.server && internalBase && publicBase && internalBase !== publicBase) {
    try {
      const internalHost = new URL(internalBase).origin
      const publicHost = new URL(publicBase).origin
      
      api.interceptors.response.use((response) => {
        if (response && response.data) {
          response.data = replaceInternalUrls(response.data, internalHost, publicHost)
        }
        return response
      })
    } catch (e) {
      console.error('Failed to parse API base URLs for replacement', e)
    }
  }

  return {
    provide: {
      axios: api,
    },
  }
})
