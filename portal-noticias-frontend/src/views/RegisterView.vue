<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100">
      
      <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-yellow-400 rounded flex items-center justify-center mb-4">
          <span class="text-white font-black text-2xl">P</span>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
          Criar Conta de Jornalista
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Ou 
          <router-link to="/login" class="font-medium text-yellow-600 hover:text-yellow-500 transition-colors">
            fazer login
          </router-link>
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

      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="space-y-5">
          
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
            <div class="relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400 sm:text-sm transition-shadow placeholder-gray-400"
                placeholder="Seu nome completo"
              />
            </div>
          </div>

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
                v-model="form.email"
                type="email"
                required
                @blur="emailError = form.email && !validateEmail(form.email) ? 'Por favor, informe um e-mail válido' : null"
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
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
            <div class="relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                minlength="8"
                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400 sm:text-sm transition-shadow placeholder-gray-400"
                placeholder="Mínimo 8 caracteres"
              />
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <button type="button" @click="showPassword = !showPassword" class="text-gray-400 hover:text-gray-600 focus:outline-none">
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
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar senha</label>
            <div class="relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                :type="showPassword ? 'text' : 'password'"
                required
                minlength="8"
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400 sm:text-sm transition-shadow placeholder-gray-400"
                placeholder="Digite a senha novamente"
              />
            </div>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-black bg-yellow-400 hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg v-if="!loading" class="h-5 w-5 text-yellow-700 group-hover:text-yellow-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              <svg v-else class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ loading ? 'Criando conta...' : 'Criar Conta' }}
          </button>
        </div>
      </form>

      <div class="mt-6">
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">
              Já tem uma conta?
            </span>
          </div>
        </div>

        <div class="mt-6 text-center">
          <router-link to="/login" class="font-medium text-yellow-600 hover:text-yellow-500">
            Fazer login
          </router-link>
        </div>
      </div>

      <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-xs text-blue-800">
          <strong>Nota:</strong> Ao criar sua conta, você será registrado como <strong>Jornalista</strong>. 
          Suas notícias precisarão ser aprovadas por um Editor antes de serem publicadas.
        </p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from "@/services/api"

const router = useRouter()

const loading = ref(false)
const error = ref(null)
const success = ref(null)
const showPassword = ref(false)
const emailError = ref(null)

const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: ""
})

// Função para validar formato de email
const validateEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

const handleRegister = async () => {
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

  // Validação básica
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
    const response = await api.post("/register", {
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation
    })
    
    // Armazena o token e dados do usuário
    localStorage.setItem("token", response.data.token)
    localStorage.setItem("user", JSON.stringify(response.data.user))
    
    success.value = "Conta criada com sucesso! Redirecionando..."
    
    // Redireciona para o painel após 1 segundo
    setTimeout(() => {
      router.push("/painel")
    }, 1000)
  } catch (err) {
    if (err.response?.status === 422) {
      const errors = err.response.data.errors
      if (errors.email) {
        error.value = "Este e-mail já está cadastrado."
      } else if (errors.password) {
        error.value = errors.password[0] || "Erro na validação da senha."
      } else {
        error.value = "Erro na validação dos dados. Verifique os campos."
      }
    } else {
      error.value = err.response?.data?.message || "Ocorreu um erro ao criar a conta."
    }
  } finally {
    loading.value = false
  }
}
</script>
