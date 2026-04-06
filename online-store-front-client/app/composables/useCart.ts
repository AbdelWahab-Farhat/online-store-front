import type { Product } from '~/data/store'

export interface CartItem {
  product: Product
  quantity: number
}

export function useCart() {
  const cartItems = useState<CartItem[]>('cart', () => [])

  const cartCount = computed(() =>
    cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
  )

  const cartTotal = computed(() =>
    cartItems.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0)
  )

  const cartSavings = computed(() =>
    cartItems.value.reduce((sum, item) => {
      if (item.product.oldPrice) {
        return sum + (item.product.oldPrice - item.product.price) * item.quantity
      }
      return sum
    }, 0)
  )

  function addToCart(product: Product, quantity = 1) {
    const existing = cartItems.value.find((item) => item.product.id === product.id)
    if (existing) {
      existing.quantity += quantity
    } else {
      cartItems.value.push({ product, quantity })
    }
  }

  function removeFromCart(productId: number) {
    cartItems.value = cartItems.value.filter((item) => item.product.id !== productId)
  }

  function updateQuantity(productId: number, quantity: number) {
    const item = cartItems.value.find((item) => item.product.id === productId)
    if (item) {
      if (quantity <= 0) {
        removeFromCart(productId)
      } else {
        item.quantity = quantity
      }
    }
  }

  function clearCart() {
    cartItems.value = []
  }

  function isInCart(productId: number) {
    return cartItems.value.some((item) => item.product.id === productId)
  }

  return {
    cartItems,
    cartCount,
    cartTotal,
    cartSavings,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    isInCart,
  }
}
