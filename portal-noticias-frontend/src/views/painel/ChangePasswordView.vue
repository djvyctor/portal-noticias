<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center gap-4">
            <router-link to="/painel" class="flex items-center">
              <div class="w-8 h-8 bg-yellow-400 rounded flex items-center justify-center mr-3">
                <span class="text-white font-black text-lg">P</span>
              </div>
              <h1 class="text-xl font-bold text-gray-900">Alterar Senha</h1>
            </router-link>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Alterar minha senha</h2>

        <transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="transform -translate-y-2 opacity-0"
          enter-to-class="transform translate-y-0 opacity-100"
        >
          <div v-if="error" class="mb-4 rounded-md bg-red-50 p-4 border-l-4 border-red-500">
            <p class="text-sm text-red-700 font-medium">{{ error }}</p>
          </div>
        </transition>

        <transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="transform -translate-y-2 opacity-0"
          enter-to-class="transform translate-y-0 opacity-100"
        >
          <div v-if="success" class="mb-4 rounded-md bg-green-50 p-4 border-l-4 border-green-500">
            <p class="text-sm text-green-700 font-medium">{{ success }}</p>
          </div>
        </transition>

        <form @submit.prevent="handleChangePassword" class="space-y-6">
          <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
              Senha atual *
            </label>
            <div class="relative">
              <input
                id="current_password"
                v-model="form.current_password"
                :type="showCurrentPassword ? 'text' : 'password'"
                required
                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"
                placeholder="Digite sua senha atual"
              />
              <button
                type="button"
                @click="showCurrentPassword = !showCurrentPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <svg v-if="!showCurrentPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Nova senha *
            </label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                minlength="8"
                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"
                placeholder="Mínimo 8 caracteres"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
              Confirmar nova senha *
            </label>
            <div class="relative">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                :type="showPassword ? 'text' : 'password'"
                required
                minlength="8"
                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"
                placeholder="Digite a nova senha novamente"
              />
            </div>
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
              :disabled="loading"
              class="flex-1 px-6 py-3 bg-yellow-600 text-white rounded-lg font-semibold hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {{ loading ? 'Alterando...' : 'Alterar Senha' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()

const loading = ref(false)
const error = ref(null)
const success = ref(null)
const showPassword = ref(false)
const showCurrentPassword = ref(false)

const form = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const handleChangePassword = async () => {
  loading.value = true
  error.value = null
  success.value = null

  if (form.password !== form.password_confirmation) {
    error.value = "As senhas não coincidem."
    loading.value = false
    return
  }

  if (form.password.length < 8) {
    error.value = "A senha deve ter no mínimo 8 caracteres."
    loading.value = false
    return
  }

  try {
    await api.post("/change-password", {
      current_password: form.current_password,
      password: form.password,
      password_confirmation: form.password_confirmation
    })
    
    success.value = "Senha alterada com sucesso!"
    
    // Limpa o formulário
    form.current_password = ''
    form.password = ''
    form.password_confirmation = ''
    
    // Redireciona após 2 segundos
    setTimeout(() => {
      router.push("/painel")
    }, 2000)
  } catch (err) {
    if (err.response?.status === 400) {
      error.value = err.response.data.message || "Senha atual incorreta."
    } else if (err.response?.status === 422) {
      error.value = "Erro na validação dos dados. Verifique os campos."
    } else {
      error.value = err.response?.data?.message || "Ocorreu um erro ao alterar a senha."
    }
  } finally {
    loading.value = false
  }
}
</script>
