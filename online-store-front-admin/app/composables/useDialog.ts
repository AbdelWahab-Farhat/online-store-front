import type { DialogVariant } from '~/components/AppDialog.vue'

interface DialogState {
  open: boolean
  variant: DialogVariant
  title: string
  message: string
  confirmText: string
  cancelText: string
  hideCancel: boolean
  onConfirm: (() => void) | null
  onCancel: (() => void) | null
}

const dialogState = reactive<DialogState>({
  open: false,
  variant: 'info',
  title: '',
  message: '',
  confirmText: '',
  cancelText: '',
  hideCancel: false,
  onConfirm: null,
  onCancel: null,
})

interface DialogOpenOptions {
  variant?: DialogVariant
  title: string
  message: string
  confirmText?: string
  cancelText?: string
  hideCancel?: boolean
  onConfirm?: () => void
  onCancel?: () => void
}

export function useDialog() {
  function open(options: DialogOpenOptions) {
    dialogState.variant = options.variant ?? 'info'
    dialogState.title = options.title
    dialogState.message = options.message
    dialogState.confirmText = options.confirmText ?? ''
    dialogState.cancelText = options.cancelText ?? ''
    dialogState.hideCancel = options.hideCancel ?? false
    dialogState.onConfirm = options.onConfirm ?? null
    dialogState.onCancel = options.onCancel ?? null
    dialogState.open = true
  }

  function confirm(options: Omit<DialogOpenOptions, 'variant'>) {
    open({ ...options, variant: 'confirm' })
  }

  function success(options: Omit<DialogOpenOptions, 'variant'>) {
    open({ ...options, variant: 'success' })
  }

  function error(options: Omit<DialogOpenOptions, 'variant'>) {
    open({ ...options, variant: 'error' })
  }

  function info(options: Omit<DialogOpenOptions, 'variant'>) {
    open({ ...options, variant: 'info' })
  }

  function warning(options: Omit<DialogOpenOptions, 'variant'>) {
    open({ ...options, variant: 'warning' })
  }

  function close() {
    dialogState.open = false
  }

  function handleConfirm() {
    dialogState.onConfirm?.()
    close()
  }

  function handleCancel() {
    dialogState.onCancel?.()
    close()
  }

  return {
    state: dialogState,
    open,
    confirm,
    success,
    error,
    info,
    warning,
    close,
    handleConfirm,
    handleCancel,
  }
}
