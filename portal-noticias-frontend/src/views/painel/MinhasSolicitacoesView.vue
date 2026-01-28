<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center gap-4">
            <router-link to="/painel" class="text-gray-600 hover:text-gray-900">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </router-link>
            <h1 class="text-xl font-bold text-gray-900">Minhas Solicitações de Promoção</h1>
          </div>
          <router-link
            v-if="!hasPendingRequest"
            to="/painel/solicitar-promocao"
            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold text-sm"
          >
            Nova Solicitação
          </router-link>
        </div>
      </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-500">Carregando...</p>
      </div>

      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-red-600">{{ error }}</p>
      </div>

      <div v-else-if="requests.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma solicitação encontrada</h3>
        <p class="text-gray-500 mb-4">Você ainda não criou nenhuma solicitação de promoção.</p>
        <router-link
          to="/painel/solicitar-promocao"
          class="inline-block px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold"
        >
          Criar Solicitação
        </router-link>
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="request in requests"
          :key="request.id"
          :class="getStatusBorderClass(request.status)"
          class="bg-white rounded-lg shadow border-l-4 p-6"
        >
          <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <h3 class="text-lg font-semibold text-gray-900">Solicitação #{{ request.id }}</h3>
                <span :class="getStatusBadgeClass(request.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ getStatusLabel(request.status) }}
                </span>
              </div>
              <div class="text-sm text-gray-600 space-y-1">
                <p><strong>Criada em:</strong> {{ formatDate(request.created_at) }}</p>
                <p v-if="request.reviewed_at"><strong>Revisada em:</strong> {{ formatDate(request.reviewed_at) }}</p>
                <p v-if="request.reviewer"><strong>Revisada por:</strong> {{ request.reviewer?.name }}</p>
              </div>
            </div>
          </div>

          <div class="mb-4 bg-gray-50 rounded-lg p-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Sua mensagem:</h4>
            <p class="text-gray-700 whitespace-pre-wrap">{{ request.message }}</p>
          </div>

          <div v-if="request.rejection_reason" class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <h4 class="text-sm font-semibold text-red-700 mb-2">Motivo da Rejeição:</h4>
            <p class="text-red-700">{{ request.rejection_reason }}</p>
          </div>

          <div v-if="request.status === 'approved'" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center gap-2">
              <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-sm font-semibold text-green-800">
                Parabéns! Sua solicitação foi aprovada. Você agora é um Editor!
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

/**
 * View MinhasSolicitacoesView - Lista de solicitações do jornalista
 * 
 * Esta view permite que jornalistas visualizem suas próprias solicitações
 * de promoção e seus status.
 * 
 * Funcionalidades:
 * - Lista todas as solicitações do usuário logado
 * - Exibe status de cada solicitação (Pendente, Aprovada, Rejeitada)
 * - Mostra mensagem de aprovação quando aprovada
 * - Permite criar nova solicitação se não houver pendente
 */

<script setup>
import { ref, computed, onMounted } from 'vue'
import { promotionRequestAPI } from '../../services/api'
import { formatDate } from '../../utils/date'

// Estado do componente
const loading = ref(false) // Estado de carregamento
const error = ref(null) // Mensagem de erro
const requests = ref([]) // Lista de solicitações

// Verifica se existe solicitação pendente
const hasPendingRequest = computed(() => {
  return requests.value.some(req => req.status === 'pending')
})

/**
 * Carrega as solicitações do usuário logado
 */
const loadRequests = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await promotionRequestAPI.myRequests()
    requests.value = response.data || []
  } catch (err) {
    console.error('Erro ao carregar solicitações:', err)
    error.value = 'Erro ao carregar solicitações: ' + (err.response?.data?.message || err.message)
  } finally {
    loading.value = false
  }
}

/**
 * Retorna a classe CSS para o badge de status
 */
const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

/**
 * Retorna a classe CSS para a borda lateral baseada no status
 */
const getStatusBorderClass = (status) => {
  const classes = {
    pending: 'border-yellow-500',
    approved: 'border-green-500',
    rejected: 'border-red-500'
  }
  return classes[status] || 'border-gray-500'
}

/**
 * Retorna o label do status
 */
const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pendente',
    approved: 'Aprovada',
    rejected: 'Rejeitada'
  }
  return labels[status] || status
}

// Carrega solicitações ao montar o componente
onMounted(() => {
  loadRequests()
})
</script>
