<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100">
      
      <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-yellow-400 rounded flex items-center justify-center mb-4">
          <span class="text-white font-black text-2xl">P</span>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
          Redefinir Senha
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Digite sua nova senha
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
              <p class="text-sm text-green-700 font-medium">{{ success }}</p>
            </div>
          </div>
        </div>
      </transition>

      <form class="mt-8 space-y-6" @submit.prevent="handleResetPassword" v-if="!success">
        <div class="space-y-5">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              @blur="emailError = form.email && !validateEmail(form.email) ? 'Por favor, informe um e-mail válido' : null"
              :class="[
                'block w-full px-3 py-3 border rounded-lg focus:ring-yellow-400 focus:border-yellow-400 sm:text-sm',
                emailError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300'
              ]"
              placeholder="nome@exemplo.com"
            />
            <p v-if="emailError" class="mt-1 text-sm text-red-600">{{ emailError }}</p>
          </div>

          <div>
            <label for="token" class="block text-sm font-medium text-gray-700 mb-1">Token de recuperação</label>
            <input
              id="token"
              v-model="form.token"
              type="text"
              required
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400 sm:text-sm"
              placeholder="Token recebido por e-mail"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              minlength="8"
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400 sm:text-sm"
              placeholder="Mínimo 8 caracteres"
            />
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar nova senha</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              :type="showPassword ? 'text' : 'password'"
              required
              minlength="8"
              class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400 sm:text-sm"
              placeholder="Digite a senha novamente"
            />
          </div>
        </div>

        <div class="flex items-center">
          <input
            id="show-password"
            v-model="showPassword"
            type="checkbox"
            class="h-4 w-4 text-yellow-500 focus:ring-yellow-400 border-gray-300 rounded"
          >
          <label for="show-password" class="ml-2 block text-sm text-gray-900">
            Mostrar senhas
          </label>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-black bg-yellow-400 hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Redefinindo...' : 'Redefinir Senha' }}
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
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from "@/services/api"

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const error = ref(null)
const success = ref(null)
const showPassword = ref(false)
const emailError = ref(null)

const form = reactive({
  email: route.query.email || '',
  token: route.query.token || '',
  password: '',
  password_confirmation: ''
})

// Função para validar formato de email
const validateEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

const handleResetPassword = async () => {
  loading.value = true
  error.value = null
  success.value = null
  emailError.value = null

  // Validação de email
  if (!form.email) {
    error.value = "Por favor, informe seu e-mail."
    loading.value = false
    return
  }

  if (!validateEmail(form.email)) {
    emailError.value = "Por favor, informe um e-mail válido (ex: nome@exemplo.com)"
    error.value = "Por favor, informe um e-mail válido."
    loading.value = false
    return
  }

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
    await api.post("/reset-password", form)
    
    success.value = "Senha redefinida com sucesso! Redirecionando para o login..."
    
    setTimeout(() => {
      router.push("/login")
    }, 2000)
  } catch (err) {
    if (err.response?.status === 400) {
      error.value = err.response.data.message || "Token inválido ou expirado."
    } else if (err.response?.status === 422) {
      error.value = "Erro na validação dos dados. Verifique os campos."
    } else {
      error.value = err.response?.data?.message || "Ocorreu um erro ao redefinir a senha."
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (!route.query.token || !route.query.email) {
    error.value = "Token ou e-mail não fornecido. Verifique o link de recuperação."
  }
})
</script>
