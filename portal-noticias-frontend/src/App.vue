<script setup>
import { onMounted, ref } from 'vue'
import api from './services/api.js'

const apiMessage = ref('')
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  try {
    const response = await api.get('/hello')
    console.log('Resposta da API:', response.data)
    apiMessage.value = response.data.message
    loading.value = false
  } catch (err) {
    console.error('Erro ao buscar dados:', err)
    error.value = 'Erro ao conectar com a API Laravel'
    loading.value = false
  }
})
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
      <h1 class="text-3xl font-bold text-gray-800 mb-4">Teste de Integração</h1>
      
      <div v-if="loading" class="text-gray-600">
        Carregando...
      </div>
      
      <div v-else-if="error" class="text-red-600 font-semibold">
        {{ error }}
      </div>
      
      <div v-else class="text-green-600 font-semibold">
        ✅ {{ apiMessage }}
      </div>
      
      <p class="text-sm text-gray-500 mt-4">
        Abra o console do navegador (F12) para ver os detalhes.
      </p>
    </div>
  </div>
</template>

<style scoped></style>
