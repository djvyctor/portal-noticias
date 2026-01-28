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
            <h1 class="text-xl font-bold text-gray-900">Solicitar Promoção para Editor</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Verifica se já existe solicitação pendente -->
      <div v-if="hasPendingRequest" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <div class="flex items-start gap-3">
          <svg class="h-6 w-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <div>
            <h3 class="text-lg font-semibold text-yellow-900 mb-2">Solicitação Pendente</h3>
            <p class="text-sm text-yellow-800 mb-4">
              Você já possui uma solicitação pendente de promoção. Aguarde a resposta dos editores antes de criar uma nova solicitação.
            </p>
            <router-link
              to="/painel/minhas-solicitacoes"
              class="text-sm font-semibold text-yellow-700 hover:text-yellow-800 underline"
            >
              Ver minhas solicitações →
            </router-link>
          </div>
        </div>
      </div>

      <!-- Formulário de solicitação -->
      <div v-else class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-2">Por que você quer ser Editor?</h2>
          <p class="text-sm text-gray-600">
            Escreva uma mensagem explicando por que você deseja ser promovido a Editor. 
            Esta solicitação será revisada pelos editores e administradores do sistema.
          </p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
              Mensagem *
            </label>
            <textarea
              id="message"
              v-model="form.message"
              required
              rows="8"
              minlength="10"
              maxlength="1000"
              placeholder="Explique por que você deseja ser promovido a Editor. Mínimo de 10 caracteres."
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"
            ></textarea>
            <p class="text-xs text-gray-500 mt-2">
              {{ form.message.length }}/1000 caracteres (mínimo: 10)
            </p>
          </div>

          <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-red-600">{{ error }}</p>
          </div>

          <div class="flex gap-4">
            <router-link
              to="/painel"
              class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 text-center transition-colors"
            >
              Cancelar
            </router-link>
            
            <button
              type="submit"
              :disabled="saving || form.message.length < 10"
              class="flex-1 px-6 py-3 bg-yellow-600 text-white rounded-lg font-semibold hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {{ saving ? 'Enviando...' : 'Enviar Solicitação' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

/**
 * View SolicitarPromocaoView - Formulário para jornalista solicitar promoção
 * 
 * Esta view permite que jornalistas solicitem promoção para Editor.
 * 
 * Funcionalidades:
 * - Formulário para escrever mensagem de solicitação
 * - Validação de mensagem (mínimo 10 caracteres, máximo 1000)
 * - Verificação se já existe solicitação pendente
 * - Envio da solicitação para revisão de Editor/Admin
 * 
 * Permissões:
 * - Apenas jornalistas podem acessar
 * - Não pode criar nova solicitação se já tiver uma pendente
 */

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { promotionRequestAPI } from '../../services/api'

const router = useRouter()

// Estado do componente
const saving = ref(false) // Estado de salvamento
const error = ref(null) // Mensagem de erro
const hasPendingRequest = ref(false) // Se já existe solicitação pendente

// Dados do formulário
const form = reactive({
  message: ''
})

/**
 * Verifica se o usuário já possui uma solicitação pendente
 */
const checkPendingRequest = async () => {
  try {
    const response = await promotionRequestAPI.myRequests()
    const requests = response.data || []
    hasPendingRequest.value = requests.some(req => req.status === 'pending')
  } catch (err) {
    console.error('Erro ao verificar solicitações:', err)
  }
}

/**
 * Processa o envio do formulário de solicitação
 */
const handleSubmit = async () => {
  saving.value = true
  error.value = null

  try {
    if (form.message.length < 10) {
      error.value = 'A mensagem deve ter pelo menos 10 caracteres.'
      saving.value = false
      return
    }

    await promotionRequestAPI.create({ message: form.message })
    
    // Redireciona para a lista de solicitações após sucesso
    router.push('/painel/minhas-solicitacoes')
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao enviar solicitação. Tente novamente.'
    console.error('Erro ao enviar solicitação:', err)
  } finally {
    saving.value = false
  }
}

// Carrega dados ao montar o componente
onMounted(() => {
  checkPendingRequest()
})
</script>
