import { ref } from 'vue'

export function useForm(initialData = {}) {
  const form = ref({ ...initialData })
  const errors = ref({})
  const success = ref(false)

  const reset = () => {
    Object.keys(initialData).forEach(k => form.value[k] = '')
    errors.value = {}
    success.value = false
  }

  const validate = (rules) => {
    errors.value = {}
    for (const [field, rule] of Object.entries(rules)) {
      const result = rule(form.value[field])
      if (result !== true) errors.value[field] = result
    }
    return Object.keys(errors.value).length === 0
  }

  return { form, errors, success, reset, validate }
}
