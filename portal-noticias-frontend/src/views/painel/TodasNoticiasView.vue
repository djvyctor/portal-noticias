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
              <h1 class="text-xl font-bold text-gray-900">Todas as Notícias - Moderação</h1>
            </router-link>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filtros -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex gap-4 flex-wrap">
          <button
            @click="filterStatus = 'all'"
            :class="filterStatus === 'all' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700'"
            class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors"
          >
            Todas ({{ allNews.length }})
          </button>
          <button
            @click="filterStatus = 'pending'"
            :class="filterStatus === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-700'"
            class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors"
          >
            Pendentes ({{ pendingCount }})
          </button>
          <button
            @click="filterStatus = 'published'"
            :class="filterStatus === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
            class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors"
          >
            Publicadas ({{ publishedCount }})
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-500">Carregando...</p>
      </div>

      <!-- Lista vazia -->
      <div v-else-if="filteredNews.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma notícia encontrada</h3>
        <p class="text-gray-500">Não há notícias com este status.</p>
      </div>

      <!-- Lista de notícias -->
      <div v-else class="bg-white rounded-lg shadow divide-y divide-gray-200">
        <div
          v-for="news in filteredNews"
          :key="news.id"
          class="p-6 hover:bg-gray-50 transition-colors"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <h3 class="text-lg font-semibold text-gray-900">{{ news.title }}</h3>
                <span
                  :class="getStatusBadgeClass(news.status)"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ getStatusLabel(news.status) }}
                </span>
                <span
                  v-if="news.is_featured"
                  class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700"
                >
                  ⭐ Destaque
                </span>
              </div>
              
              <div class="flex items-center gap-4 text-sm text-gray-500 mb-2">
                <span class="flex items-center gap-1">
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  {{ news.user?.name }}
                </span>
                <span>•</span>
                <span class="flex items-center gap-1">
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                  {{ news.category?.name }}
                </span>
                <span>•</span>
                <span>{{ formatDate(news.created_at) }}</span>
              </div>
              
              <p class="text-gray-600 text-sm line-clamp-2">
                {{ getExcerpt(news.content, 150) }}
              </p>
            </div>

            <div class="ml-4 flex flex-col gap-2 min-w-[120px]">
              <!-- Aprovar/Publicar -->
              <button
                v-if="news.status === 'draft' && (isEditor || isAdmin)"
                @click.stop="approveNews(news.id)"
                type="button"
                class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg shadow-sm transition-colors duration-200 cursor-pointer"
              >
                ✓ Aprovar
              </button>
              
              <!-- Destacar no Carrossel (só Admin, só para notícias publicadas) -->
              <button
                v-if="news.status === 'published' && isAdmin"
                @click.stop="toggleFeature(news.id, news.is_featured)"
                type="button"
                :class="news.is_featured 
                  ? 'bg-purple-600 hover:bg-purple-700 text-white' 
                  : 'bg-yellow-500 hover:bg-yellow-600 text-white'"
                class="px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200 cursor-pointer"
                :title="news.is_featured ? 'Remover do carrossel' : 'Adicionar ao carrossel'"
              >
                {{ news.is_featured ? '★ No Carrossel' : '☆ Destacar' }}
              </button>
              
              <!-- Editar -->
              <button
                @click.stop="editNews(news.id)"
                type="button"
                class="px-4 py-2 text-sm font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200 cursor-pointer"
              >
                Editar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * View TodasNoticiasView - Moderação de todas as notícias
 * 
 * Esta view permite que Editores e Admins visualizem e gerenciem todas as notícias do sistema.
 * 
 * Funcionalidades:
 * - Lista todas as notícias do sistema (de todos os usuários)
 * - Filtros por status (Todas, Pendentes, Publicadas)
 * - Aprovação de notícias pendentes (Editor/Admin)
 * - Destaque de notícias no carrossel (apenas Admin)
 * - Edição de qualquer notícia
 * 
 * Permissões:
 * - Apenas Editor e Admin podem acessar
 * - Editor pode aprovar e editar notícias
 * - Admin pode aprovar, editar e destacar notícias
 */
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { newsAPI } from '../../services/api'
import { formatDate } from '../../utils/date'
import { getExcerpt } from '../../utils/text'
import { getStatusLabel, getStatusBadgeClass } from '../../utils/status'

const router = useRouter()

// Estado do componente
const allNews = ref([]) // Lista de todas as notícias
const loading = ref(false) // Estado de carregamento
const filterStatus = ref('pending') // Filtro atual (all, pending, published)
const currentUser = ref(null) // Usuário atual

// Computed properties para verificar permissões
const isEditor = computed(() => currentUser.value?.role === 'editor')
const isAdmin = computed(() => currentUser.value?.role === 'admin')

// Computed properties para contadores
const pendingCount = computed(() => allNews.value.filter(n => n.status === 'pending').length)
const publishedCount = computed(() => allNews.value.filter(n => n.status === 'published').length)

/**
 * Filtra as notícias baseado no status selecionado
 */
const filteredNews = computed(() => {
  if (filterStatus.value === 'all') return allNews.value
  return allNews.value.filter(n => n.status === filterStatus.value)
})

/**
 * Carrega os dados do usuário atual do localStorage
 */
const loadCurrentUser = () => {
  const user = localStorage.getItem('user')
  if (user) {
    try {
      currentUser.value = JSON.parse(user)
    } catch (error) {
      console.error('Erro ao parsear dados do usuário:', error)
    }
  }
}

/**
 * Carrega todas as notícias do sistema da API
 */
const loadAllNews = async () => {
  loading.value = true
  try {
    const response = await newsAPI.all()
    const res = response.data
    allNews.value = res.data ?? res ?? []
  } catch (error) {
    console.error('Erro ao carregar notícias:', error)
    allNews.value = []
  } finally {
    loading.value = false
  }
}

/**
 * Aprova e publica uma notícia pendente
 * 
 * @param {number} id - ID da notícia
 */
const approveNews = async (id) => {
  if (!confirm('Deseja aprovar e publicar esta notícia?')) return
  
  try {
    await newsAPI.approve(id)
    await loadAllNews() // Recarrega a lista
    alert('Notícia aprovada e publicada com sucesso! Ela já está visível no site público.')
  } catch (error) {
    alert('Erro ao aprovar notícia: ' + (error.response?.data?.message || error.message))
    console.error('Erro ao aprovar notícia:', error)
  }
}

/**
 * Alterna o destaque de uma notícia no carrossel (apenas Admin)
 * 
 * @param {number} id - ID da notícia
 * @param {boolean} currentStatus - Status atual do destaque
 */
const toggleFeature = async (id, currentStatus) => {
  const action = currentStatus ? 'remover dos destaques' : 'destacar no carrossel'
  if (!confirm(`Deseja ${action} esta notícia?`)) return
  
  try {
    await newsAPI.feature(id)
    await loadAllNews() // Recarrega a lista
    alert(`Notícia ${currentStatus ? 'removida dos destaques' : 'destacada no carrossel'} com sucesso!`)
  } catch (error) {
    alert('Erro ao destacar notícia: ' + (error.response?.data?.message || error.message))
    console.error('Erro ao destacar notícia:', error)
  }
}

/**
 * Redireciona para a página de edição da notícia
 * 
 * @param {number} id - ID da notícia
 */
const editNews = (id) => {
  router.push(`/painel/noticias/editar/${id}`)
}


// Carrega dados ao montar o componente
onMounted(() => {
  loadCurrentUser()
  
  // Verifica permissão - apenas Editor e Admin podem acessar
  if (!isEditor.value && !isAdmin.value) {
    alert('Você não tem permissão para acessar esta página')
    router.push('/painel')
    return
  }
  
  loadAllNews()
})
</script>
