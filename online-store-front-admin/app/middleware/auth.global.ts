export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore()
  const isLoginPage = to.path === '/login'

  if (!authStore.isAuthenticated && !isLoginPage) {
    return navigateTo('/login')
  }

  if (authStore.isAuthenticated && isLoginPage) {
    return navigateTo('/')
  }
})
