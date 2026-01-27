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
              <h1 class="text-xl font-bold text-gray-900">Minhas Notícias</h1>
            </router-link>
          </div>
          
          <router-link
            to="/painel/noticias/criar"
            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold text-sm"
          >
            + Nova Notícia
          </router-link>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
            @click="filterStatus = 'published'"
            :class="filterStatus === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
            class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors"
          >
            Publicadas ({{ publishedCount }})
          </button>
          <button
            @click="filterStatus = 'draft'"
            :class="filterStatus === 'draft' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'"
            class="px-4 py-2 rounded-lg font-semibold text-sm transition-colors"
          >
            Rascunhos ({{ draftCount }})
          </button>
        </div>
      </div>

      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-500">Carregando...</p>
      </div>

      <div v-else-if="filteredNews.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma notícia encontrada</h3>
        <p class="text-gray-500 mb-6">Comece criando sua primeira notícia.</p>
        <router-link
          to="/painel/noticias/criar"
          class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold"
        >
          + Criar Notícia
        </router-link>
      </div>

      <div v-else class="bg-white rounded-lg shadow divide-y divide-gray-200">
        <div
          v-for="news in filteredNews"
          :key="news.id"
          class="p-6 hover:bg-gray-50 transition-colors"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1 cursor-pointer" @click="editNews(news.id)">
              <div class="flex items-center gap-2 mb-2">
                <h3 class="text-lg font-semibold text-gray-900">{{ news.title }}</h3>
                <span
                  :class="getStatusBadgeClass(news.status)"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ getStatusLabel(news.status) }}
                </span>
              </div>
              
              <div class="flex items-center gap-4 text-sm text-gray-500 mb-2">
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

            <div class="ml-4 flex flex-col gap-2">
              <button
                @click="editNews(news.id)"
                class="px-3 py-1 text-sm text-blue-600 hover:text-blue-700 font-semibold"
              >
                Editar
              </button>
              <button
                @click="deleteNews(news.id)"
                class="px-3 py-1 text-sm text-red-600 hover:text-red-700 font-semibold"
              >
                Excluir
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
 * View MinhasNoticiasView - Lista de notícias do usuário
 * 
 * Esta view exibe todas as notícias criadas pelo usuário autenticado.
 * 
 * Funcionalidades:
 * - Lista todas as notícias do usuário
 * - Filtros por status (Todas, Publicadas, Rascunhos)
 * - Edição e exclusão de notícias
 * - Contadores de notícias por status
 * 
 * Ações disponíveis:
 * - Editar notícia (redireciona para página de edição)
 * - Excluir notícia (com confirmação)
 */
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { newsAPI } from '../../services/api'
import { formatDate } from '../../utils/date'
import { getExcerpt } from '../../utils/text'
import { getStatusLabel, getStatusBadgeClass } from '../../utils/status'

const router = useRouter()

// Estado do componente
const allNews = ref([]) // Lista de todas as notícias do usuário
const loading = ref(false) // Estado de carregamento
const filterStatus = ref('all') // Filtro atual (all, published, draft)

// Computed properties
const publishedCount = computed(() => allNews.value.filter(n => n.status === 'published').length)
const draftCount = computed(() => allNews.value.filter(n => n.status === 'draft').length)

/**
 * Filtra as notícias baseado no status selecionado
 */
const filteredNews = computed(() => {
  if (filterStatus.value === 'all') return allNews.value
  return allNews.value.filter(n => n.status === filterStatus.value)
})

/**
 * Carrega todas as notícias do usuário da API
 */
const loadNews = async () => {
  loading.value = true
  try {
    const response = await newsAPI.list()
    allNews.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Erro ao carregar notícias:', error)
    allNews.value = []
  } finally {
    loading.value = false
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

/**
 * Exclui uma notícia após confirmação do usuário
 * 
 * @param {number} id - ID da notícia
 */
const deleteNews = async (id) => {
  if (!confirm('Tem certeza que deseja excluir esta notícia?')) return
  
  try {
    await newsAPI.delete(id)
    // Recarrega a lista para garantir que está atualizada
    loadNews()
    alert('Notícia excluída com sucesso!')
  } catch (error) {
    console.error('Erro ao excluir:', error)
    alert('Erro ao excluir notícia: ' + (error.response?.data?.message || error.message))
  }
}

// Carrega notícias ao montar o componente
onMounted(() => {
  loadNews()
})
</script>
