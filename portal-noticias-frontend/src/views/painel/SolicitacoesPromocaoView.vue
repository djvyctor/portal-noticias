<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Solicitações de Promoção</h1>
      <div class="flex gap-2">
        <button
          @click="filterStatus = 'all'"
          :class="filterStatus === 'all' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700'"
          class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors"
        >
          Todas ({{ allRequests.length }})
        </button>
        <button
          @click="filterStatus = 'pending'"
          :class="filterStatus === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700'"
          class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors"
        >
          Pendentes ({{ pendingCount }})
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-500">Carregando solicitações...</p>
    </div>

    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
      {{ error }}
    </div>

    <template v-else>
      <div v-if="filteredRequests.length === 0" class="text-center py-12">
        <p class="text-gray-500">Nenhuma solicitação encontrada.</p>
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="request in filteredRequests"
          :key="request.id"
          :class="getStatusBorderClass(request.status)"
          class="bg-white shadow-md rounded-lg overflow-hidden border-l-4"
        >
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-800 mb-2">{{ request.user?.name }}</h2>
                <div class="text-sm text-gray-600 space-y-1">
                  <p><strong>Email:</strong> {{ request.user?.email }}</p>
                  <p><strong>Solicitado em:</strong> {{ formatDate(request.created_at) }}</p>
                  <p v-if="request.reviewed_at"><strong>Revisado em:</strong> {{ formatDate(request.reviewed_at) }}</p>
                  <p v-if="request.reviewer"><strong>Revisado por:</strong> {{ request.reviewer?.name }}</p>
                </div>
              </div>
              <span :class="getStatusBadgeClass(request.status)" class="px-2.5 py-0.5 rounded text-xs font-semibold">
                {{ getStatusLabel(request.status) }}
              </span>
            </div>

            <div class="mb-4 bg-gray-50 rounded-lg p-4">
              <h3 class="text-sm font-semibold text-gray-700 mb-2">Mensagem:</h3>
              <p class="text-gray-700 whitespace-pre-wrap">{{ request.message }}</p>
            </div>

            <div v-if="request.rejection_reason" class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
              <h3 class="text-sm font-semibold text-red-700 mb-2">Motivo da Rejeição:</h3>
              <p class="text-red-700">{{ request.rejection_reason }}</p>
            </div>

            <div v-if="request.status === 'pending'" class="flex gap-2">
              <button
                @click="approveRequest(request.id)"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition duration-200"
              >
                Aprovar
              </button>
              <button
                @click="openRejectModal(request)"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition duration-200"
              >
                Rejeitar
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Modal de Rejeição -->
    <div v-if="showRejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showRejectModal = false">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Rejeitar Solicitação</h3>
          <p class="text-gray-700 mb-2"><strong>Usuário:</strong> {{ requestToReject?.user?.name }}</p>
          <form @submit.prevent="rejectRequest">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Motivo da Rejeição (opcional)</label>
              <textarea
                v-model="rejectionReason"
                rows="4"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Descreva o motivo da rejeição (opcional)..."
              ></textarea>
            </div>
            <div v-if="rejectError" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
              {{ rejectError }}
            </div>
            <div class="flex justify-end gap-2">
              <button
                type="button"
                @click="showRejectModal = false"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
              >
                Rejeitar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

/**
 * View SolicitacoesPromocaoView - Gerenciamento de solicitações de promoção (Editor/Admin)
 * 
 * Esta view permite que Editores e Admins visualizem, aprovem ou rejeitem
 * solicitações de promoção de Jornalista para Editor.
 * 
 * Funcionalidades:
 * - Lista todas as solicitações de promoção
 * - Filtros por status (Todas, Pendentes)
 * - Aprovação de solicitações (promove automaticamente para Editor)
 * - Rejeição de solicitações com motivo opcional
 * 
 * Permissões:
 * - Apenas Editor e Admin podem acessar
 * - Ambos podem aprovar e rejeitar solicitações
 */

<script setup>
import { ref, computed, onMounted } from 'vue'
import { promotionRequestAPI } from '../../services/api'
import { formatDate } from '../../utils/date'

// Estado do componente
const loading = ref(false) // Estado de carregamento
const error = ref(null) // Mensagem de erro geral
const allRequests = ref([]) // Lista de todas as solicitações
const filterStatus = ref('pending') // Filtro atual (all, pending)
const showRejectModal = ref(false) // Controla exibição do modal de rejeição
const requestToReject = ref(null) // Solicitação que será rejeitada
const rejectionReason = ref('') // Motivo da rejeição
const rejectError = ref(null) // Mensagem de erro ao rejeitar

// Computed properties para contadores
const pendingCount = computed(() => allRequests.value.filter(r => r.status === 'pending').length)

/**
 * Filtra as solicitações baseado no status selecionado
 */
const filteredRequests = computed(() => {
  if (filterStatus.value === 'all') return allRequests.value
  return allRequests.value.filter(r => r.status === filterStatus.value)
})

/**
 * Carrega todas as solicitações da API
 */
const loadRequests = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await promotionRequestAPI.index()
    const res = response.data
    allRequests.value = res.data ?? res ?? []
  } catch (err) {
    console.error('Erro ao carregar solicitações:', err)
    error.value = 'Erro ao carregar solicitações: ' + (err.response?.data?.message || err.message)
  } finally {
    loading.value = false
  }
}

/**
 * Aprova uma solicitação de promoção
 * 
 * @param {number} id - ID da solicitação
 */
const approveRequest = async (id) => {
  if (!confirm('Deseja aprovar esta solicitação? O usuário será promovido a Editor automaticamente.')) return
  
  try {
    await promotionRequestAPI.approve(id)
    alert('Solicitação aprovada com sucesso! O usuário foi promovido a Editor.')
    await loadRequests() // Recarrega a lista
  } catch (err) {
    alert('Erro ao aprovar solicitação: ' + (err.response?.data?.message || err.message))
    console.error('Erro ao aprovar solicitação:', err)
  }
}

/**
 * Abre o modal de rejeição para uma solicitação
 * 
 * @param {Object} request - Objeto da solicitação a ser rejeitada
 */
const openRejectModal = (request) => {
  requestToReject.value = request
  rejectionReason.value = ''
  rejectError.value = null
  showRejectModal.value = true
}

/**
 * Rejeita uma solicitação com o motivo fornecido
 */
const rejectRequest = async () => {
  rejectError.value = null
  try {
    await promotionRequestAPI.reject(requestToReject.value.id, {
      rejection_reason: rejectionReason.value || null
    })
    showRejectModal.value = false
    await loadRequests() // Recarrega a lista
  } catch (err) {
    rejectError.value = 'Erro ao rejeitar solicitação: ' + (err.response?.data?.message || err.message)
    console.error('Erro ao rejeitar solicitação:', err)
  }
}

/**
 * Retorna a classe CSS para o badge de status
 * 
 * @param {string} status - Status da solicitação
 * @returns {string} Classe CSS
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
 * 
 * @param {string} status - Status da solicitação
 * @returns {string} Classe CSS
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
 * 
 * @param {string} status - Status da solicitação
 * @returns {string} Label do status
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
