<template>
  <div class="p-6 max-w-3xl mx-auto bg-white rounded-xl shadow-md">
    <h1 class="text-2xl font-semibold mb-6 text-gray-800">
       Editar Proposta
    </h1>

    <form @submit.prevent="salvar">

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Nome completo</label>
        <input v-model="form.cliente_nome" type="text" class="input" required />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">CPF</label>
        <input
          v-model="form.cliente_cpf"
          type="text"
          class="input"
          maxlength="14"
          @input="form.cliente_cpf = formatarCPF(form.cliente_cpf)"
          required
        />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Salário</label>
        <input
          v-model="form.cliente_salario"
          type="text"
          class="input"
          @input="form.cliente_salario = formatarMoeda(form.cliente_salario)"
          required
        />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Valor solicitado</label>
        <input
          v-model="form.valor_solicitado"
          type="text"
          class="input"
          @input="form.valor_solicitado = formatarMoeda(form.valor_solicitado)"
          required
        />
      </div>


      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Prazo (meses)</label>
        <select v-model="form.prazo_meses" class="input" required>
          <option disabled value="">Selecione...</option>
          <option v-for="p in prazos" :key="p" :value="p">{{ p }} meses</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Observações</label>
        <textarea
          v-model="form.observacoes"
          rows="3"
          class="input"
          placeholder="Comentários opcionais..."
        ></textarea>
      </div>

      <!-- Cálculos -->
      <div class="bg-gray-50 p-4 rounded-md text-sm text-gray-700 space-y-1 mb-6">
        <p><strong>Margem disponível:</strong> {{ margemFormatada }}</p>
        <p><strong>Valor da parcela:</strong> {{ parcelaFormatada }}</p>
        <p><strong>Valor total a pagar:</strong> {{ totalFormatado }}</p>
      </div>

      <!-- Botões -->
      <div class="flex justify-end gap-3">
        <router-link to="/propostas" class="btn-secondary">Cancelar</router-link>
        <button type="submit" class="btn-primary" :disabled="salvando">
          <span v-if="salvando"> Salvando...</span>
          <span v-else> Salvar Alterações</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import propostasApi from '@/api/propostas'
import useMask from '@/composables/useMasks'

const { formatarCPF, formatarMoeda, limparMoeda } = useMask()

const route = useRoute()
const router = useRouter()

const form = ref({})
const salvando = ref(false)
const prazos = [6, 12, 18, 24, 36, 48, 60]

const carregar = async () => {
  try {
    const res = await propostasApi.get(route.params.id)
    form.value = res.data.data

    form.value.cliente_cpf = formatarCPF(form.value.cliente_cpf || '')
    form.value.cliente_salario = formatarMoeda(String(form.value.cliente_salario || ''))
    form.value.valor_solicitado = formatarMoeda(String(form.value.valor_solicitado || ''))
  } catch (e) {
    console.error('Erro ao carregar proposta:', e)
    alert('Erro ao carregar a proposta!')
  }
}

onMounted(carregar)

const salarioNumerico = computed(() => limparMoeda(form.value.cliente_salario))
const valorSolicitadoNumerico = computed(() => limparMoeda(form.value.valor_solicitado))
const margemDisponivel = computed(() => salarioNumerico.value * 0.3)

const valorParcela = computed(() => {
  const taxa = 0.025
  const n = Number(form.value.prazo_meses)
  const pv = valorSolicitadoNumerico.value
  if (!n || pv === 0) return 0
  return pv * (taxa * Math.pow(1 + taxa, n)) / (Math.pow(1 + taxa, n) - 1)
})

const valorTotal = computed(() => valorParcela.value * Number(form.value.prazo_meses || 0))

const formatarBRL = (v) =>
  new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)

const margemFormatada = computed(() => formatarBRL(margemDisponivel.value))
const parcelaFormatada = computed(() => formatarBRL(valorParcela.value))
const totalFormatado = computed(() => formatarBRL(valorTotal.value))

const salvar = async () => {
  salvando.value = true
  try {
    const payload = {
      ...form.value,
      cliente_salario: limparMoeda(form.value.cliente_salario),
      valor_solicitado: limparMoeda(form.value.valor_solicitado),
      prazo_meses: Number(form.value.prazo_meses),
      valor_parcela: valorParcela.value,
      valor_total: valorTotal.value,
      margem_disponivel: margemDisponivel.value,
    }

    await propostasApi.update(route.params.id, payload)
    alert('Proposta atualizada com sucesso!')
    router.push('/propostas')
  } catch (e) {
    console.error('Erro ao atualizar proposta:', e)
    alert('Erro ao atualizar a proposta!')
  } finally {
    salvando.value = false
  }
}
</script>

<style scoped>
.input {
  @apply border rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.btn-primary {
  @apply bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition;
}

.btn-secondary {
  @apply bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 transition;
}
</style>
