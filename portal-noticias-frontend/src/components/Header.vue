/**
 * Componente Header - Cabeçalho da aplicação
 * 
 * Este componente exibe:
 * - Logo e nome da aplicação
 * - Menu de navegação com categorias
 * - Campo de busca
 * - Link para painel (se logado) ou botão de login
 * - Menu mobile responsivo
 * 
 * Funcionalidades:
 * - Carrega categorias dinamicamente da API
 * - Verifica se o usuário está logado
 * - Permite busca de notícias
 * - Menu mobile com toggle
 */

<template>
  <!-- Header fixo no topo da página -->
  <header class="sticky top-0 z-50 bg-white border-b-4 border-yellow-400 shadow-sm font-sans">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        
        <!-- Logo e botão de menu mobile -->
        <div class="flex items-center gap-4">
          <!-- Botão para abrir/fechar menu mobile -->
          <button 
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="md:hidden p-2 rounded-md text-gray-600 hover:text-black hover:bg-gray-100 focus:outline-none"
            aria-label="Toggle menu"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <!-- Ícone de hambúrguer ou X dependendo do estado -->
              <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Logo clicável que redireciona para home -->
          <div class="flex-shrink-0 flex items-center cursor-pointer" @click="$router.push('/')">
            <div class="w-8 h-8 bg-yellow-400 rounded flex items-center justify-center mr-2">
              <span class="text-white font-black text-lg">P</span>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-gray-900">
              PN <span class="text-yellow-500">News</span>
            </h1>
          </div>
        </div>

        <!-- Menu de navegação desktop com categorias -->
        <nav class="hidden md:flex space-x-8">
          <router-link 
            v-for="category in categories" 
            :key="category.id"
            :to="`/categoria/${category.slug}`"
            class="text-gray-600 hover:text-yellow-600 font-bold text-sm uppercase tracking-wide transition-colors duration-200 border-b-2 border-transparent hover:border-yellow-400 py-1"
          >
            {{ category.name }}
          </router-link>
        </nav>

        <!-- Área de busca e autenticação -->
        <div class="flex items-center gap-4">
          <!-- Campo de busca desktop -->
          <div class="hidden lg:flex items-center bg-gray-100 rounded-full px-3 py-1.5 border border-transparent focus-within:border-yellow-400 focus-within:bg-white transition-all">
            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <form @submit.prevent="handleSearch" class="flex items-center">
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Buscar..." 
                class="bg-transparent border-none focus:ring-0 text-sm ml-2 w-32 text-gray-700 placeholder-gray-400"
              />
            </form>
          </div>

          <!-- Botão de busca mobile -->
          <button @click="handleMobileSearch" class="lg:hidden text-gray-500 hover:text-black" aria-label="Buscar">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>

          <!-- Separador visual -->
          <div class="h-6 w-px bg-gray-300 mx-1 hidden sm:block"></div>

          <!-- Link para painel (se logado) ou botão de login -->
          <div v-if="isLoggedIn" class="flex items-center gap-3">
            <router-link
              to="/painel"
              class="flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-yellow-600 transition-colors"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span class="hidden sm:inline">{{ user?.name || 'Painel' }}</span>
            </router-link>
          </div>
          <button
            v-else
            class="flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-yellow-600 transition-colors"
            @click="goToLogin"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="hidden sm:inline">Entrar</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Menu mobile expandido -->
    <div v-show="isMobileMenuOpen" class="md:hidden border-t border-gray-100 bg-gray-50">
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <router-link
          v-for="category in categories"
          :key="category.id"
          :to="`/categoria/${category.slug}`"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-black hover:bg-yellow-100 transition"
          @click="isMobileMenuOpen = false"
        >
          {{ category.name }}
        </router-link>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

// Estado do componente
const isMobileMenuOpen = ref(false) // Controla abertura/fechamento do menu mobile
const categories = ref([]) // Lista de categorias carregadas da API
const user = ref(null) // Dados do usuário logado
const searchQuery = ref('') // Termo de busca digitado

/**
 * Computed que verifica se o usuário está autenticado
 * @returns {boolean} True se houver token no localStorage
 */
const isLoggedIn = computed(() => {
  const token = localStorage.getItem('token')
  return !!token
})

/**
 * Carrega as categorias da API
 * Em caso de erro, define categories como array vazio
 */
const loadCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = response.data || []
  } catch (error) {
    console.error('Erro ao carregar categorias:', error)
    categories.value = []
  }
}

/**
 * Carrega os dados do usuário do localStorage
 * Se não houver dados, user permanece null
 */
const loadUser = () => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (error) {
      console.error('Erro ao parsear dados do usuário:', error)
      user.value = null
    }
  }
}

/**
 * Processa a busca quando o formulário é submetido
 * Redireciona para a página de busca com o termo pesquisado
 * Requer pelo menos 2 caracteres para realizar a busca
 */
const handleSearch = () => {
  const trimmedQuery = searchQuery.value.trim()
  if (trimmedQuery.length >= 2) {
    router.push(`/busca?q=${encodeURIComponent(trimmedQuery)}`)
    searchQuery.value = '' // Limpa o campo após buscar
  }
}

/**
 * Abre um prompt para busca no mobile
 * Redireciona para a página de busca se o termo tiver pelo menos 2 caracteres
 */
const handleMobileSearch = () => {
  const query = prompt('Digite sua busca:')
  if (query && query.trim().length >= 2) {
    router.push(`/busca?q=${encodeURIComponent(query.trim())}`)
  }
}

/**
 * Redireciona para a página de login
 */
const goToLogin = () => {
  router.push("/login")
}

// Carrega dados ao montar o componente
onMounted(() => {
  loadCategories()
  loadUser()
})
</script>