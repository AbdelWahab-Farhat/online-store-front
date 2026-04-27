export function useFavorites() {
  const favoriteIds = useState<number[]>('favorite_products', () => [])
  const { addToast } = useToast()

  onMounted(() => {
    const saved = localStorage.getItem('favorite_products')
    if (saved) {
      try {
        favoriteIds.value = JSON.parse(saved)
      } catch (e) {}
    }
  })

  watch(favoriteIds, (newVal) => {
    localStorage.setItem('favorite_products', JSON.stringify(newVal))
  }, { deep: true })

  const favoritesCount = computed(() => favoriteIds.value.length)

  function isFavorite(productId: number): boolean {
    return favoriteIds.value.includes(productId)
  }

  function toggleFavorite(productId: number) {
    if (isFavorite(productId)) {
      favoriteIds.value = favoriteIds.value.filter(id => id !== productId)
      addToast('تمت الإزالة من المفضلة', 'info')
    } else {
      favoriteIds.value = [...favoriteIds.value, productId]
      addToast('تمت الإضافة إلى المفضلة', 'success')
    }
  }

  function addFavorite(productId: number) {
    if (!isFavorite(productId)) {
      favoriteIds.value = [...favoriteIds.value, productId]
      addToast('تمت الإضافة إلى المفضلة', 'success')
    }
  }

  function removeFavorite(productId: number) {
    favoriteIds.value = favoriteIds.value.filter(id => id !== productId)
    addToast('تمت الإزالة من المفضلة', 'info')
  }

  function clearFavorites() {
    favoriteIds.value = []
  }

  return {
    favoriteIds,
    favoritesCount,
    isFavorite,
    toggleFavorite,
    addFavorite,
    removeFavorite,
    clearFavorites,
  }
}
