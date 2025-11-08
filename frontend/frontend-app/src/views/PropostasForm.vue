<template>
  <div class="bg-gray-100 min-h-screen p-6 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-2xl p-8">
      <i class="fa-solid fa-pen-to-square text-yellow-500"></i>
      <h1 class="text-2xl font-bold mb-6 text-gray-800 text-center"> Nova Proposta de Crédito</h1>

      <form @submit.prevent="abrirConfirmacao" class="space-y-5">

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">Nome completo</label>
          <input v-model="form.cliente_nome" type="text" class="input" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">CPF</label>
          <input v-model="form.cliente_cpf" @input="formatarCPF" maxlength="14" class="input" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">Salário</label>
          <input v-model="form.cliente_salario" @input="formatarMoeda('cliente_salario')" class="input" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">Valor solicitado</label>
          <input v-model="form.valor_solicitado" @input="formatarMoeda('valor_solicitado')" class="input" required />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">Prazo (meses)</label>
          <select v-model="form.prazo_meses" class="input" required>
            <option value="">Selecione</option>
            <option v-for="prazo in prazos" :key="prazo" :value="prazo">{{ prazo }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">Observações</label>
          <textarea v-model="form.observacoes" rows="3" class="input"></textarea>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border mt-4">
          <p><strong>Margem disponível:</strong> R$ {{ formatarNumero(margemDisponivel) }}</p>
          <p><strong>Valor da parcela:</strong> R$ {{ formatarNumero(valorParcela) }}</p>
          <p><strong>Valor total a pagar:</strong> R$ {{ formatarNumero(valorTotal) }}</p>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <router-link to="/propostas" class="btn-secondary">Voltar</router-link>
          <button type="submit" class="btn-primary" :disabled="salvando">
            <span v-if="salvando">Salvando...</span>
            <span v-else>Salvar Proposta</span>
          </button>
        </div>
      </form>

      <div v-if="mostrarConfirmacao" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 shadow-lg w-full max-w-sm text-center animate-fadeIn">
          <h2 class="text-lg font-semibold text-gray-800 mb-2">Confirmar ação</h2>
          <p class="text-gray-600 mb-4">Deseja realmente salvar esta proposta?</p>

          <div class="flex justify-center gap-3">
            <button @click="mostrarConfirmacao = false" class="btn-secondary w-24">Cancelar</button>
            <button @click="confirmarSalvar" class="btn-primary w-24">Salvar</button>
          </div>
        </div>
      </div>

      <p
        v-if="mensagem"
        :class="[
          'mt-4 text-center font-semibold',
          mensagemTipo === 'erro' ? 'text-red-500' : 'text-green-600'
        ]"
      >
        {{ mensagem }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import propostasApi from '@/api/propostas'

const router = useRouter()

const form = ref({
  cliente_nome: '',
  cliente_cpf: '',
  cliente_salario: '',
  valor_solicitado: '',
  prazo_meses: '',
  observacoes: '',
})

const mensagem = ref('')
const mensagemTipo = ref('')
const salvando = ref(false)
const mostrarConfirmacao = ref(false)
const prazos = [6, 12, 18, 24, 36, 48, 60]


const formatarCPF = () => {
  let v = form.value.cliente_cpf.replace(/\D/g, '')
  v = v.replace(/(\d{3})(\d)/, '$1.$2')
  v = v.replace(/(\d{3})(\d)/, '$1.$2')
  v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2')
  form.value.cliente_cpf = v
}

const formatarMoeda = (campo) => {
  let v = form.value[campo].replace(/\D/g, '')
  v = (v / 100).toFixed(2) + ''
  v = v.replace('.', ',')
  v = v.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
  form.value[campo] = 'R$ ' + v
}


const salarioNumerico = computed(() => parseFloat(form.value.cliente_salario.replace(/[^\d,]/g, '').replace(',', '.')) || 0)
const valorSolicitadoNumerico = computed(() => parseFloat(form.value.valor_solicitado.replace(/[^\d,]/g, '').replace(',', '.')) || 0)
const margemDisponivel = computed(() => salarioNumerico.value * 0.3)
const valorParcela = computed(() => {
  const taxa = 0.025
  const n = Number(form.value.prazo_meses)
  if (!n || valorSolicitadoNumerico.value === 0) return 0
  return valorSolicitadoNumerico.value * (taxa * Math.pow(1 + taxa, n)) / (Math.pow(1 + taxa, n) - 1)
})
const valorTotal = computed(() => valorParcela.value * Number(form.value.prazo_meses || 0))
const formatarNumero = (v) => v.toLocaleString('pt-BR', { minimumFractionDigits: 2 })


const abrirConfirmacao = () => {
  mostrarConfirmacao.value = true
}

const confirmarSalvar = async () => {
  mostrarConfirmacao.value = false
  await salvarProposta()
}

/* === SALVAR === */
const salvarProposta = async () => {
  mensagem.value = ''
  mensagemTipo.value = ''
  salvando.value = true

  try {
    const payload = {
      cliente_nome: form.value.cliente_nome,
      cliente_cpf: form.value.cliente_cpf.replace(/\D/g, ''),
      cliente_salario: salarioNumerico.value,
      valor_solicitado: valorSolicitadoNumerico.value,
      prazo_meses: form.value.prazo_meses,
      observacoes: form.value.observacoes,
    }

    const res = await propostasApi.create(payload)
    if (res.data.success) {
      mensagem.value = '✅ Proposta criada com sucesso!'
      mensagemTipo.value = 'sucesso'
      setTimeout(() => router.push('/propostas'), 1000)
    } else {
      mensagem.value = res.data.message || 'Erro ao salvar proposta.'
      mensagemTipo.value = 'erro'
    }
  } catch (err) {
    mensagem.value = err.response?.data?.message || 'Erro ao salvar proposta.'
    mensagemTipo.value = 'erro'
  } finally {
    salvando.value = false
  }
}
</script>

<style scoped>
.input {
  @apply border rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400 transition;
}
.btn-primary {
  @apply bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition;
}
.btn-secondary {
  @apply bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 transition;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s ease-out;
}
</style>
