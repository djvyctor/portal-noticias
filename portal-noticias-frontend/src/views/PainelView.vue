<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-yellow-400 rounded flex items-center justify-center mr-3">
              <span class="text-white font-black text-lg">P</span>
            </div>
            <h1 class="text-xl font-bold text-gray-900">Painel de Redação</h1>
          </div>
          
          <div class="flex items-center gap-4">
            <router-link 
              to="/" 
              class="text-sm text-gray-600 hover:text-yellow-600 transition-colors"
            >
              Ver site
            </router-link>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-600">{{ user?.name || 'Usuário' }}</span>
              <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                {{ getRoleName(user?.role) }}
              </span>
            </div>
            <router-link
              to="/painel/alterar-senha"
              class="text-sm text-gray-600 hover:text-yellow-600 transition-colors"
            >
              Alterar Senha
            </router-link>
            <button
              @click="handleLogout"
              class="text-sm text-red-600 hover:text-red-700 font-semibold"
            >
              Sair
            </button>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Boas-vindas -->
      <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">
          Olá, {{ user?.name || 'Usuário' }}!
        </h2>
        <p class="text-gray-600 mt-1">{{ getRoleDescription(user?.role) }}</p>
      </div>

      <!-- Estatísticas -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
              <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Minhas Notícias</dt>
                <dd class="text-3xl font-semibold text-gray-900">{{ stats.myNews }}</dd>
              </dl>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
              <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Publicadas</dt>
                <dd class="text-3xl font-semibold text-gray-900">{{ stats.published }}</dd>
              </dl>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
              <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Rascunhos</dt>
                <dd class="text-3xl font-semibold text-gray-900">{{ stats.drafts }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Ações Rápidas -->
      <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Ações Rápidas</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <router-link
              to="/painel/noticias/criar"
              class="flex items-center justify-center px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold"
            >
              <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nova Notícia
            </router-link>

            <router-link
              to="/painel/noticias"
              class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors font-semibold text-gray-700"
            >
              <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
              Minhas Notícias
            </router-link>

            <!-- Notícias Rejeitadas -->
            <router-link
              to="/painel/noticias-rejeitadas"
              class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors font-semibold text-gray-700"
            >
              <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Notícias Rejeitadas
            </router-link>

            <!-- Notícias Pendentes (só para Editor/Admin) -->
            <router-link
              v-if="user?.role === 'editor' || user?.role === 'admin'"
              to="/painel/noticias-pendentes"
              class="flex items-center justify-center px-4 py-3 border border-yellow-300 rounded-lg bg-yellow-50 hover:bg-yellow-100 transition-colors font-semibold text-yellow-800"
            >
              <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Notícias Pendentes
            </router-link>

            <!-- Moderação (só para Editor/Admin) -->
            <router-link
              v-if="user?.role === 'editor' || user?.role === 'admin'"
              to="/painel/moderacao"
              class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors font-semibold text-gray-700"
            >
              <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Todas as Notícias
            </router-link>

            <!-- Gerenciar Usuários (Editor/Admin) -->
            <router-link
              v-if="user?.role === 'editor' || user?.role === 'admin'"
              to="/painel/usuarios"
              class="flex items-center justify-center px-4 py-3 border border-purple-300 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors font-semibold text-purple-800"
            >
              <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              Gerenciar Usuários
            </router-link>

            <!-- Solicitar Promoção (só para Jornalista) -->
            <router-link
              v-if="user?.role === 'jornalista'"
              to="/painel/solicitar-promocao"
              class="flex items-center justify-center px-4 py-3 border border-blue-300 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors font-semibold text-blue-800"
            >
              <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
              Quero ser Editor
            </router-link>

            <!-- Solicitações de Promoção (Editor/Admin) -->
            <router-link
              v-if="user?.role === 'editor' || user?.role === 'admin'"
              to="/painel/solicitacoes-promocao"
              class="flex items-center justify-center px-4 py-3 border border-indigo-300 rounded-lg bg-indigo-50 hover:bg-indigo-100 transition-colors font-semibold text-indigo-800"
            >
              <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Solicitações de Promoção
            </router-link>

            <!-- Alterar Senha -->
            <router-link
              to="/painel/alterar-senha"
              class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors font-semibold text-gray-700"
            >
              <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Alterar Senha
            </router-link>
          </div>
        </div>
      </div>

      <!-- Alerta de Moderação (Editor/Admin) -->
      <div v-if="(user?.role === 'editor' || user?.role === 'admin') && pendingCount > 0" class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
              <h3 class="text-sm font-semibold text-orange-900">Notícias Pendentes de Aprovação</h3>
              <p class="text-sm text-orange-700">Você tem {{ pendingCount }} notícia(s) aguardando aprovação.</p>
            </div>
          </div>
          <router-link
            to="/painel/noticias-pendentes"
            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-semibold text-sm"
          >
            Ir para Moderação →
          </router-link>
        </div>
      </div>

      <!-- Últimas Notícias -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h2 class="text-lg font-semibold text-gray-900">Minhas Últimas Notícias</h2>
          <router-link to="/painel/noticias" class="text-sm text-yellow-600 hover:text-yellow-700 font-semibold">
            Ver todas →
          </router-link>
        </div>
        
        <div v-if="loadingNews" class="p-6 text-center text-gray-500">
          Carregando...
        </div>
        
        <div v-else-if="recentNews.length === 0" class="p-6 text-center text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="mb-2">Você ainda não criou nenhuma notícia.</p>
          <router-link to="/painel/noticias/criar" class="text-yellow-600 hover:text-yellow-700 font-semibold">
            Criar primeira notícia →
          </router-link>
        </div>
        
        <div v-else class="divide-y divide-gray-200">
          <div
            v-for="news in recentNews"
            :key="news.id"
            class="p-6 hover:bg-gray-50 transition-colors cursor-pointer"
            @click="$router.push(`/painel/noticias/editar/${news.id}`)"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <h3 class="text-sm font-medium text-gray-900">{{ news.title }}</h3>
                  <span :class="getStatusBadgeClass(news.status)" class="px-2 py-0.5 text-xs font-semibold rounded-full">
                    {{ getStatusLabel(news.status) }}
                  </span>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-500">
                  <span>{{ news.category?.name }}</span>
                  <span>•</span>
                  <span>{{ formatDate(news.created_at) }}</span>
                </div>
              </div>
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * View PainelView - Página principal do painel de redação
 * 
 * Esta é a página inicial do painel administrativo, exibida após o login.
 * 
 * Funcionalidades:
 * - Dashboard com estatísticas do usuário (total de notícias, publicadas, rascunhos)
 * - Ações rápidas com links para funcionalidades principais
 * - Lista das últimas notícias do usuário
 * - Alerta de notícias pendentes (para Editor/Admin)
 * - Informações do usuário e opção de logout
 * 
 * Permissões:
 * - Todos os usuários autenticados podem acessar
 * - Editor/Admin veem opções adicionais de moderação
 * - Editor/Admin veem opção de gerenciar usuários (Editor só pode criar jornalistas)
 */
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI, newsAPI } from '../services/api'
import { formatDate } from '../utils/date'
import { getStatusLabel, getStatusBadgeClass, getRoleName, getRoleDescription } from '../utils/status'

const router = useRouter()

// Estado do componente
const user = ref(null) // Dados do usuário logado
const stats = ref({
  myNews: 0, // Total de notícias do usuário
  published: 0, // Notícias publicadas
  drafts: 0 // Notícias pendentes (rascunhos)
})
const recentNews = ref([]) // Últimas 5 notícias do usuário
const loadingNews = ref(false) // Estado de carregamento das notícias
const pendingCount = ref(0) // Contador de notícias pendentes (Editor/Admin)

/**
 * Carrega os dados atualizados do usuário da API
 * Atualiza o localStorage com os dados mais recentes
 * Redireciona para login se houver erro de autenticação
 */
const loadUserData = async () => {
  try {
    const response = await authAPI.me()
    user.value = response.data
    localStorage.setItem('user', JSON.stringify(response.data))
  } catch (error) {
    // Erro de autenticação - redireciona para login
    router.push('/login')
  }
}

/**
 * Carrega estatísticas das notícias do usuário
 * Calcula total, publicadas e pendentes
 * Se for Editor ou Admin, também conta notícias pendentes de todos os usuários
 */
const loadStats = async () => {
  try {
    const response = await newsAPI.list()
    const news = response.data.data || response.data || []
    
    // Calcula estatísticas das notícias do usuário
    stats.value.myNews = news.length
    stats.value.published = news.filter(n => n.status === 'published').length
    stats.value.drafts = news.filter(n => n.status === 'pending').length
    
    // Se for Editor ou Admin, carrega notícias pendentes de TODOS os usuários
    if (user.value?.role === 'editor' || user.value?.role === 'admin') {
      try {
        const allNewsResponse = await newsAPI.all()
        const allNews = allNewsResponse.data.data || allNewsResponse.data || []
        pendingCount.value = allNews.filter(n => n.status === 'pending').length
      } catch (error) {
        pendingCount.value = 0
      }
    }
  } catch (error) {
    // Erro silencioso - estatísticas não são críticas para o funcionamento
    console.error('Erro ao carregar estatísticas:', error)
  }
}

/**
 * Carrega as últimas 5 notícias do usuário
 * Exibidas na seção "Minhas Últimas Notícias"
 */
const loadRecentNews = async () => {
  loadingNews.value = true
  try {
    const response = await newsAPI.list()
    const news = response.data.data || response.data || []
    recentNews.value = news.slice(0, 5) // Pega apenas as 5 primeiras
  } catch (error) {
    console.error('Erro ao carregar notícias recentes:', error)
  } finally {
    loadingNews.value = false
  }
}

/**
 * Processa o logout do usuário
 * Limpa token e dados do usuário do localStorage
 * Redireciona para a página de login
 */
const handleLogout = async () => {
  try {
    await authAPI.logout()
  } catch (error) {
    // Continua mesmo se houver erro no logout (pode ser token expirado)
    console.error('Erro no logout:', error)
  } finally {
    // Sempre limpa os dados locais e redireciona
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

// Carrega dados ao montar o componente
onMounted(() => {
  // Verifica se há token de autenticação
  const token = localStorage.getItem('token')
  if (!token) {
    router.push('/login')
    return
  }
  
  // Carrega dados do usuário do localStorage (para exibição imediata)
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (error) {
      console.error('Erro ao parsear dados do usuário:', error)
    }
  }
  
  // Carrega dados atualizados da API
  loadUserData()
  loadStats()
  loadRecentNews()
})
</script>
