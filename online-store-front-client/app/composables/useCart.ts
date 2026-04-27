export interface CartProduct {
  id: number
  name: string
  price: number
  image?: string | null
  images?: Array<{ url?: string | null }>
  oldPrice?: number | null
  compare_price?: number | null
  category?: string | null
  categories?: Array<{ name?: string | null; slug?: string | null }>
  badge?: string | null
  has_discount?: boolean
  slug?: string
  [key: string]: unknown
}

export interface CartItem {
  product: CartProduct
  quantity: number
}

function getProductImage(product: CartProduct) {
  return product.image || product.images?.find((image) => image?.url)?.url || '/images/placeholder.png'
}

function normalizeCartProduct(product: CartProduct): CartProduct {
  const comparePrice = product.compare_price ?? product.oldPrice ?? null

  return {
    ...product,
    image: getProductImage(product),
    compare_price: comparePrice,
    oldPrice: product.oldPrice ?? comparePrice,
    category: product.category ?? product.categories?.find((category) => category?.slug)?.slug ?? '',
    badge: product.badge ?? (product.has_discount ? 'تخفيض' : null),
  }
}

function normalizeCartItem(item: CartItem): CartItem {
  return {
    ...item,
    product: normalizeCartProduct(item.product),
    quantity: Math.max(1, Number(item.quantity) || 1),
  }
}

export function useCart() {
  const cartItems = useState<CartItem[]>('cart', () => [])
  const { addToast } = useToast()

  onMounted(() => {
    const saved = localStorage.getItem('cart_items')
    if (saved) {
      try {
        const parsedItems = JSON.parse(saved)
        if (Array.isArray(parsedItems)) {
          cartItems.value = parsedItems
            .filter((item): item is CartItem => Boolean(item?.product?.id && item?.product?.name))
            .map(normalizeCartItem)
        }
      } catch (e) {}
    }
  })

  watch(cartItems, (newVal) => {
    localStorage.setItem('cart_items', JSON.stringify(newVal))
  }, { deep: true })

  const cartCount = computed(() =>
    cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
  )

  const cartTotal = computed(() =>
    cartItems.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0)
  )

  const cartSavings = computed(() =>
    cartItems.value.reduce((sum, item) => {
      const comparePrice = item.product.compare_price ?? item.product.oldPrice
      if (comparePrice) {
        return sum + (comparePrice - item.product.price) * item.quantity
      }
      return sum
    }, 0)
  )

  function addToCart(product: CartProduct, quantity = 1) {
    const normalizedProduct = normalizeCartProduct(product)
    const normalizedQuantity = Math.max(1, quantity)
    const existing = cartItems.value.find((item) => item.product.id === normalizedProduct.id)
    if (existing) {
      existing.quantity += normalizedQuantity
      existing.product = normalizeCartProduct(existing.product)
    } else {
      cartItems.value.push({ product: normalizedProduct, quantity: normalizedQuantity })
    }
    addToast('تم إضافة المنتج لعربة التسوق بنجاح', 'success')
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
