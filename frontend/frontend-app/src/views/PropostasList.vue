<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-semibold text-gray-800 flex items-center gap-2">
        Propostas de Crédito
      </h1>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-6 flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-sm font-medium mb-1 text-gray-700">Buscar</label>
        <input v-model="filtrosTemp.search" placeholder="Nome ou CPF" class="input" />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1 text-gray-700">Status</label>
        <select v-model="filtrosTemp.status" class="input">
          <option value="">Todos</option>
          <option v-for="st in statusList" :key="st" :value="st">{{ st }}</option>
        </select>
      </div>

      <button @click="buscar" class="btn">Filtrar</button>
      <router-link
        to="/propostas/nova"
        class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition"
      >
        Nova Proposta
      </router-link>
    </div>

    <!-- Estado de carregamento -->
    <div v-if="carregando" class="text-gray-500 italic text-center py-8">
       Carregando propostas...
    </div>

    <!-- Tabela -->
    <div v-if="!carregando && propostas.length" class="overflow-x-auto bg-white rounded-xl shadow-md">
      <table class="min-w-full text-sm text-gray-800">
        <thead class="bg-gray-100 border-b border-gray-200 text-left text-gray-700 uppercase text-xs">
        <tr>
          <th class="py-3 px-4">Cliente</th>
          <th class="py-3 px-4">CPF</th>
          <th class="py-3 px-4">Valor Solicitado</th>
          <th class="py-3 px-4">Prazo</th>
          <th class="py-3 px-4">Valor Parcela</th>
          <th class="py-3 px-4">Status</th>
          <th class="py-3 px-4 text-right">Ações</th>
        </tr>
        </thead>
        <tbody>
        <tr
          v-for="p in propostas"
          :key="p.id"
          class="border-b last:border-0 hover:bg-gray-50 transition"
        >
          <td class="py-3 px-4 font-medium">{{ p.cliente_nome }}</td>
          <td class="py-3 px-4">{{ mascararCPF(p.cliente_cpf) }}</td>
          <td class="py-3 px-4">R$ {{ formatar(p.valor_solicitado) }}</td>
          <td class="py-3 px-4">{{ p.prazo_meses }} meses</td>
          <td class="py-3 px-4">R$ {{ formatar(p.valor_parcela) }}</td>
          <td class="py-3 px-4">
            <StatusBadge :status="p.status" />
          </td>
          <td class="py-2 px-3 text-right space-x-2">
            <button class="icon-btn view" @click="verDetalhes(p.id)" title="Ver detalhes">
              <i class="fa-solid fa-eye"></i>
            </button>

            <button
              v-if="['rascunho', 'em_analise'].includes(p.status)"
              class="icon-btn edit"
              @click="editarProposta(p.id)"
              title="Editar"
            >
              <i class="fa-solid fa-pen-to-square"></i>
            </button>

            <button
              v-if="!['cancelada'].includes(p.status)"
              class="icon-btn delete"
              @click="excluirProposta(p.id)"
              title="Excluir"
            >
              <i class="fa-solid fa-trash"></i>
            </button>
          </td>

        </tr>
        </tbody>
      </table>

      <PropostaModal
        :propostaId="propostaSelecionada"
        :visible="mostrarModal"
        @close="mostrarModal = false"
        @updated="carregar"
      />
    </div>

    <div v-if="!carregando && !propostas.length" class="text-gray-500 text-center mt-6">
      Nenhuma proposta encontrada.
    </div>

    <Pagination
      :page="pagina"
      :total="totalPaginas"
      @change="irParaPagina"
      class="mt-6"
    />
  </div>
  <ConfirmModal
    v-if="mostrarConfirmacao"
    :visible="mostrarConfirmacao"
    title="Confirmar Ação"
    :message="mensagemConfirmacao"
    @cancel="mostrarConfirmacao = false"
    @confirm="executarAcao"
  />

</template>

<script setup>
import PropostaModal from '@/components/PropostaModal.vue'
import { ref, onMounted } from 'vue'
import propostasApi from '@/api/propostas'
import StatusBadge from '@/components/StatusBadge.vue'
import Pagination from '@/components/Pagination.vue'

import ConfirmModal from '@/components/ConfirmModal.vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const mostrarConfirmacao = ref(false)
const acaoAtual = ref(null)

const propostas = ref([])
const pagina = ref(1)
const totalPaginas = ref(1)
const carregando = ref(false)

const filtros = ref({ search: '', status: '' })
const filtrosTemp = ref({ search: '', status: '' })

const statusList = ['rascunho', 'em_analise', 'aprovada', 'reprovada', 'cancelada']

const formatar = v => Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 })
const mascararCPF = cpf => cpf?.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4')

const carregar = async () => {
  carregando.value = true
  try {
    const params = {
      page: pagina.value,
      search: filtros.value.search || undefined,
      status: filtros.value.status || undefined
    }

    console.log('Parâmetros enviados:', params)

    const res = await propostasApi.getAll(params)
    propostas.value = Array.isArray(res.data.data) ? res.data.data : res.data
    totalPaginas.value = Number(
      Array.isArray(res.data.last_page)
        ? res.data.last_page[0]
        : res.data.last_page || res.data.meta?.last_page || 1
    )
  } catch (error) {
    console.error('Erro ao carregar propostas:', error)
  } finally {
    carregando.value = false
  }
}

const mostrarModal = ref(false)
const propostaSelecionada = ref(null)

const verDetalhes = id => {
  propostaSelecionada.value = id
  mostrarModal.value = true
}

const buscar = () => {
  filtros.value = { ...filtrosTemp.value }
  pagina.value = 1
  carregar()
}

const irParaPagina = p => {
  pagina.value = p
  carregar()
}

const editarStatus = id => alert(`Editar status da proposta #${id}`)
const cancelar = async id => {
  if (confirm('Deseja realmente cancelar esta proposta?')) {
    await propostasApi.delete(id)
    carregar()
  }
}

const mensagemConfirmacao = ref('')

const editarProposta = (id) => {
  propostaSelecionada.value = id
  acaoAtual.value = 'editar'
  mensagemConfirmacao.value = 'Deseja editar esta proposta?'
  mostrarConfirmacao.value = true
}

const excluirProposta = (id) => {
  propostaSelecionada.value = id
  acaoAtual.value = 'excluir'
  mensagemConfirmacao.value = 'Deseja excluir esta proposta permanentemente?'
  mostrarConfirmacao.value = true
}

const executarAcao = async () => {
  mostrarConfirmacao.value = false

  if (acaoAtual.value === 'editar') {
    router.push(`/propostas/${propostaSelecionada.value}/editar`)
  }

  if (acaoAtual.value === 'excluir') {
    try {
      await propostasApi.delete(propostaSelecionada.value)
      await carregar()
    } catch (err) {
      console.error('Erro ao excluir proposta:', err)
    }
  }
}


onMounted(carregar)
</script>

<style scoped>
.input {
  @apply border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400;
}
.btn {
  @apply bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition;
}
.icon-btn {
  @apply inline-flex items-center justify-center w-8 h-8 rounded-md transition text-base;
}
.icon-btn.info {
  @apply text-blue-600 hover:bg-blue-100;
}
.icon-btn.edit {
  @apply text-yellow-600 hover:bg-yellow-100;
}
.icon-btn.danger {
  @apply text-red-600 hover:bg-red-100;
}

.icon-btn {
  @apply inline-flex items-center justify-center w-8 h-8 rounded-md transition;
  font-size: 1rem;
}

.icon-btn {
  @apply inline-flex items-center justify-center w-8 h-8 rounded-md transition;
}
.icon-btn.view {
  @apply bg-blue-100 text-blue-600 hover:bg-blue-200;
}
.icon-btn.edit {
  @apply bg-yellow-100 text-yellow-700 hover:bg-yellow-200;
}
.icon-btn.delete {
  @apply bg-red-100 text-red-600 hover:bg-red-200;
}


.icon-btn i {
  transition: color 0.2s ease, transform 0.2s ease;
}

.icon-btn.info i {
  color: #2563eb;
}

.icon-btn.edit i {
  color: #d97706;
}

.icon-btn.danger i {
  color: #dc2626;
}

.icon-btn:hover i {
  transform: scale(1.2);
}

</style>
