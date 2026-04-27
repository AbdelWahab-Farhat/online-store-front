export interface ToastMessage {
  id: number
  message: string
  type: 'success' | 'error' | 'info'
}

export function useToast() {
  const toasts = useState<ToastMessage[]>('toasts', () => [])

  function addToast(message: string, type: 'success' | 'error' | 'info' = 'success', duration = 3000) {
    const id = Date.now()
    toasts.value.push({ id, message, type })
    setTimeout(() => {
      removeToast(id)
    }, duration)
  }

  function removeToast(id: number) {
    toasts.value = toasts.value.filter(t => t.id !== id)
  }

  return {
    toasts,
    addToast,
    removeToast
  }
}
