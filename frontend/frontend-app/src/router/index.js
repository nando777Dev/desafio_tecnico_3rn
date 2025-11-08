import { createRouter, createWebHistory } from 'vue-router'
import PropostasList from '@/views/PropostasList.vue'
import PropostasForm from '@/views/PropostasForm.vue'
import PropostasEdit from '@/views/PropostasEdit.vue'

const routes = [
  { path: '/', redirect: '/propostas' },
  { path: '/propostas', name: 'propostas.index', component: PropostasList },
  { path: '/propostas/nova', name: 'propostas.nova', component: PropostasForm },
  { path: '/propostas/:id/editar', name: 'propostas.editar', component: PropostasEdit },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router

