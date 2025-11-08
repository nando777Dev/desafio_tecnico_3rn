import axios from 'axios'
import { ref } from 'vue'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  timeout: 8000,
})

export function useApi() {
  const loading = ref(false)
  const error = ref(null)

  const request = async (method, url, data = {}, params = {}) => {
    loading.value = true
    error.value = null
    try {
      const res = await api({ method, url, data, params })
      return res.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro de comunicação com o servidor'
      throw err
    } finally {
      loading.value = false
    }
  }

  return { request, loading, error }
}
