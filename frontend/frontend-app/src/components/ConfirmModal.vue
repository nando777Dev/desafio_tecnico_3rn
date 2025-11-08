<template>
  <div
    v-if="visible"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
  >
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 text-center animate-fadeIn">
      <h3 class="text-lg font-semibold text-gray-800 mb-3">
        {{ title || 'Confirmação' }}
      </h3>
      <p class="text-gray-600 mb-5">
        {{ message || 'Tem certeza que deseja continuar?' }}
      </p>
      <div class="flex justify-center gap-3">
        <button @click="$emit('cancel')" class="btn-secondary">Cancelar</button>
        <button @click="$emit('confirm')" class="btn-blue">Confirmar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  visible: Boolean,
  title: String,
  message: String
})
defineEmits(['confirm', 'cancel'])
</script>

<style scoped>
.btn-blue {
  background-color: #2563eb;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  transition: 0.2s;
}
.btn-blue:hover {
  background-color: #1d4ed8;
}
.btn-secondary {
  background-color: #e5e7eb;
  color: #111827;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  transition: 0.2s;
}
.btn-secondary:hover {
  background-color: #d1d5db;
}
.animate-fadeIn {
  animation: fadeIn 0.2s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
