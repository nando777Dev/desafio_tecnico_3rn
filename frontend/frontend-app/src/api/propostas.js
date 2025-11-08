import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
})

export default {
  async getAll(params = {}) {
    return api.get('/propostas', { params })
  },

  async create(data) {
    return api.post('/propostas/create', data)
  },
  async get(id) {
    return api.get(`/propostas/${id}`)
  },

  async update(id, data) {
    return api.patch(`/propostas/${id}/update`, data)
  },

  async updateStatus(id, status) {
    return api.patch(`/propostas/${id}/status`, { status })
  },

  async delete(id) {
    return api.delete(`/propostas/${id}`)
  },

}
