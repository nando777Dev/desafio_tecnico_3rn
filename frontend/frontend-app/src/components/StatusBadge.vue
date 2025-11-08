<template>
  <span :class="classe" class="px-2 py-1 text-xs font-semibold rounded-full capitalize">
    {{ status }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ status: String })
const classe = computed(() => {
  switch (props.status) {
    case 'aprovada': return 'bg-green-100 text-green-700'
    case 'reprovada': return 'bg-red-100 text-red-700'
    case 'em_analise': return 'bg-yellow-100 text-yellow-700'
    case 'rascunho': return 'bg-gray-100 text-gray-700'
    case 'cancelada': return 'bg-gray-300 text-gray-600'
    default: return 'bg-gray-100 text-gray-700'
  }
})
</script>
