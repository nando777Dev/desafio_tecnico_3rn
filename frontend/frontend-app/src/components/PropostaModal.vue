<template>
  <transition name="fade">
    <div
      v-if="visible"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 relative transform transition-all">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4 border-b pb-2">
          <h2 class="text-xl font-semibold text-gray-800">
            Proposta #{{ proposta?.id }}
          </h2>
          <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 text-lg">
            ✖
          </button>
        </div>

        <!-- Corpo -->
        <div v-if="carregando" class="text-gray-500 italic text-center py-6">
          Carregando detalhes...
        </div>

        <div v-else-if="proposta" class="space-y-3">
          <p><strong>Cliente:</strong> {{ proposta.cliente_nome }}</p>
          <p><strong>CPF:</strong> {{ mascararCPF(proposta.cliente_cpf) }}</p>
          <p><strong>Salário:</strong> R$ {{ formatar(proposta.cliente_salario) }}</p>
          <p><strong>Valor Solicitado:</strong> R$ {{ formatar(proposta.valor_solicitado) }}</p>
          <p><strong>Prazo:</strong> {{ proposta.prazo_meses }} meses</p>
          <p><strong>Valor da Parcela:</strong> R$ {{ formatar(proposta.valor_parcela) }}</p>
          <p><strong>Valor Total:</strong> R$ {{ formatar(proposta.valor_total) }}</p>
          <p>
            <strong>Status:</strong>
            <span class="font-semibold capitalize"
                  :class="{
                'text-blue-600': proposta.status === 'em_analise',
                'text-yellow-600': proposta.status === 'rascunho',
                'text-green-600': proposta.status === 'aprovada',
                'text-red-600': proposta.status === 'reprovada' || proposta.status === 'cancelada'
              }"
            >
              {{ proposta.status }}
            </span>
          </p>
          <p v-if="proposta.observacoes">
            <strong>Observações:</strong> {{ proposta.observacoes }}
          </p>
        </div>

        <div v-if="mensagem" :class="['mt-3 p-3 rounded text-sm',
          mensagemTipo === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
          {{ mensagem }}
        </div>

        <div class="flex justify-end gap-2 mt-6 border-t pt-4">
          <button
            v-if="proposta?.status === 'rascunho'"
            @click="confirmarAcao('enviar')"
            class="btn btn-blue"
          >Enviar para Análise</button>

          <button
            v-if="proposta?.status === 'em_analise'"
            @click="confirmarAcao('aprovar')"
            class="btn btn-green"
          >Aprovar</button>

          <button
            v-if="proposta?.status === 'em_analise'"
            @click="confirmarAcao('reprovar')"
            class="btn btn-yellow"
          >Reprovar</button>

          <button
            v-if="!['cancelada'].includes(proposta?.status)"
            @click="confirmarAcao('cancelar')"
            class="btn btn-red"
          >Cancelar</button>

          <button @click="$emit('close')" class="btn btn-gray">Fechar</button>
        </div>
      </div>
      <transition name="fade">
        <div
          v-if="mostrarConfirmacao"
          class="fixed inset-0 flex items-center justify-center bg-black/40 z-50"
        >
          <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md text-center">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Confirmar ação</h3>
            <p class="text-gray-600 mb-4">
              Tem certeza que deseja <strong>{{ textoAcao }}</strong> esta proposta?
            </p>
            <div class="flex justify-center gap-3">
              <button @click="executarAcao" class="btn btn-green">Sim</button>
              <button @click="mostrarConfirmacao = false" class="btn btn-gray">Não</button>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import propostasApi from '@/api/propostas'

const props = defineProps({
  propostaId: Number,
  visible: Boolean,
})
const emit = defineEmits(['close', 'updated'])

const proposta = ref(null)
const carregando = ref(false)
const mensagem = ref('')
const mensagemTipo = ref('')

const mostrarConfirmacao = ref(false)
const acaoAtual = ref(null)
const textoAcao = ref('')

const formatar = v => Number(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 })
const mascararCPF = cpf => cpf?.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4')

const carregar = async () => {
  if (!props.propostaId) return
  carregando.value = true
  try {
    const res = await propostasApi.get(props.propostaId)
    proposta.value = res.data.data || res.data
  } catch (err) {
    console.error('Erro ao carregar proposta:', err)
  } finally {
    carregando.value = false
  }
}

const confirmarAcao = (acao) => {
  acaoAtual.value = acao
  textoAcao.value =
    acao === 'enviar' ? 'enviar para análise' :
      acao === 'aprovar' ? 'aprovar' :
        acao === 'reprovar' ? 'reprovar' :
          'cancelar'
  mostrarConfirmacao.value = true
}

const executarAcao = async () => {
  mostrarConfirmacao.value = false
  const statusMap = {
    enviar: 'em_analise',
    aprovar: 'aprovada',
    reprovar: 'reprovada',
    cancelar: 'cancelada',
  }

  const novoStatus = statusMap[acaoAtual.value]
  if (!novoStatus || !proposta.value) return

  try {
    console.log('Atualizando status para:', novoStatus)
    const res = await propostasApi.updateStatus(proposta.value.id, novoStatus)

    proposta.value.status = res.data.data?.status || novoStatus
    mensagem.value = `Status atualizado para "${novoStatus}".`
    mensagemTipo.value = 'success'


    setTimeout(() => {
      emit('updated')
      emit('close')
    }, 400)
  } catch (err) {
    console.error(err)
    mensagem.value = err.response?.data?.message || 'Erro ao atualizar status.'
    mensagemTipo.value = 'error'
  }
}

watch(() => props.propostaId, (v) => v && props.visible && carregar())
watch(() => props.visible, (v) => v && props.propostaId && carregar())
onMounted(() => props.visible && props.propostaId && carregar())
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.btn {
  @apply px-3 py-2 rounded-md text-sm font-medium text-white transition;
}
.btn-blue {
  @apply bg-blue-600 hover:bg-blue-700;
}
.btn-green {
  @apply bg-green-600 hover:bg-green-700;
}
.btn-yellow {
  @apply bg-yellow-500 hover:bg-yellow-600 text-white;
}
.btn-red {
  @apply bg-red-600 hover:bg-red-700;
}
.btn-gray {
  @apply bg-gray-400 hover:bg-gray-500 text-white;
}
</style>
