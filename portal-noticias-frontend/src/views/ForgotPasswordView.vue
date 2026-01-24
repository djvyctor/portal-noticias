<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100">
      
      <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-yellow-400 rounded flex items-center justify-center mb-4">
          <span class="text-white font-black text-2xl">P</span>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
          Recuperar Senha
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Digite seu e-mail para receber o link de recuperação
        </p>
      </div>

      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="error" class="rounded-md bg-red-50 p-4 border-l-4 border-red-500">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-700 font-medium">{{ error }}</p>
            </div>
          </div>
        </div>
      </transition>

      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="success" class="rounded-md bg-green-50 p-4 border-l-4 border-green-500">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-green-700 font-medium mb-2">{{ success }}</p>
              <div v-if="resetUrl" class="mt-3 p-3 bg-white rounded border border-green-200">
                <p class="text-xs text-gray-600 mb-2">Link de recuperação (desenvolvimento):</p>
                <a 
                  :href="resetUrl" 
                  class="text-xs text-blue-600 break-all hover:underline block mb-2"
                  target="_self"
                >
                  {{ resetUrl }}
                </a>
                <button
                  @click="goToReset"
                  class="text-xs px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                >
                  Ir para página de reset
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <form class="mt-8 space-y-6" @submit.prevent="handleForgotPassword" v-if="!success">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Endereço de e-mail</label>
          <div class="relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
              </svg>
            </div>
            <input
              id="email"
              v-model="email"
              type="email"
              required
              @blur="emailError = email && !validateEmail(email) ? 'Por favor, informe um e-mail válido' : null"
              :class="[
                'block w-full pl-10 pr-3 py-3 border rounded-lg focus:ring-yellow-400 focus:border-yellow-400 sm:text-sm transition-shadow placeholder-gray-400',
                emailError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300'
              ]"
              placeholder="nome@exemplo.com"
            />
          </div>
          <p v-if="emailError" class="mt-1 text-sm text-red-600">{{ emailError }}</p>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-black bg-yellow-400 hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg v-if="!loading" class="h-5 w-5 text-yellow-700 group-hover:text-yellow-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <svg v-else class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ loading ? 'Enviando...' : 'Enviar Link de Recuperação' }}
          </button>
        </div>
      </form>

      <div class="mt-6 text-center">
        <router-link to="/login" class="text-sm font-medium text-yellow-600 hover:text-yellow-500">
          ← Voltar para o login
        </router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from "@/services/api"

const router = useRouter()

const loading = ref(false)
const error = ref(null)
const success = ref(null)
const resetUrl = ref(null)
const email = ref('')
const emailError = ref(null)

// Função para validar formato de email
const validateEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

const handleForgotPassword = async () => {
  loading.value = true
  error.value = null
  success.value = null
  resetUrl.value = null
  emailError.value = null

  // Validação de email
  if (!email.value) {
    error.value = "Por favor, informe seu e-mail."
    loading.value = false
    return
  }

  if (!validateEmail(email.value)) {
    emailError.value = "Por favor, informe um e-mail válido (ex: nome@exemplo.com)"
    error.value = "Por favor, informe um e-mail válido."
    loading.value = false
    return
  }

  try {
    const response = await api.post("/forgot-password", {
      email: email.value
    })
    
    success.value = "Link de recuperação gerado! Em produção, será enviado por e-mail."
    resetUrl.value = response.data.reset_url
  } catch (err) {
    if (err.response?.status === 422) {
      error.value = "E-mail não encontrado em nosso sistema."
    } else {
      error.value = err.response?.data?.message || "Ocorreu um erro ao processar sua solicitação."
    }
  } finally {
    loading.value = false
  }
}

const goToReset = () => {
  if (resetUrl.value) {
    // Extrai token e email da URL
    const url = new URL(resetUrl.value)
    const token = url.searchParams.get('token')
    const email = url.searchParams.get('email')
    
    // Redireciona usando o router
    router.push({
      path: '/resetar-senha',
      query: { token, email }
    })
  }
}
</script>
