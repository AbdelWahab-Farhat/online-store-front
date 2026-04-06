import { ref } from 'vue'
import { z } from 'zod'

export function useValidator<T extends z.ZodRawShape>(schema: z.ZodObject<T>) {
  const errors = ref<Record<string, string>>({})

  const validate = (data: Record<string, any>) => {
    errors.value = {}
    const result = schema.safeParse(data)

    if (!result.success) {
      const fieldErrors = result.error.flatten().fieldErrors as Record<string, string[]>
      for (const [key, messages] of Object.entries(fieldErrors)) {
        if (messages && messages.length > 0) {
          errors.value[key] = messages[0] as string
        }
      }
      return false
    }

    return true
  }

  const validateField = (field: keyof T, value: any) => {
    // Optional: Validate a single field
    const fieldSchema = schema.shape[field as string] as z.ZodTypeAny
    if (fieldSchema) {
      const result = fieldSchema.safeParse(value)
      if (!result.success) {
        errors.value[field as string] = (result as any).error.errors[0].message
        return false
      } else {
        delete errors.value[field as string]
      }
    }
    return true
  }

  const clearErrors = () => {
    errors.value = {}
  }

  return {
    errors,
    validate,
    validateField,
    clearErrors,
  }
}
