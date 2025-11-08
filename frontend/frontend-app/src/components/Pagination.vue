<template>
  <div class="flex justify-center items-center gap-2 mt-4">
    <button
      class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
      :disabled="page <= 1"
      @click="$emit('change', page - 1)"
    >
      ‹
    </button>

    <span class="font-medium">Página {{ page }} / {{ total }}</span>

    <button
      class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
      :disabled="page >= total"
      @click="$emit('change', page + 1)"
    >
      ›
    </button>
  </div>
</template>

<script setup>
defineProps({
  page: {
    type: Number,
    required: true
  },
  total: {
    type: Number,
    required: true
  }
})
</script>

<style scoped>
button {
  transition: all 0.2s;
}
</style>
