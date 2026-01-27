<template>
  <div class="min-h-screen bg-gray-50">
    <Header />
    
    <main class="max-w-7xl mx-auto px-4 py-12">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Todas as Notícias
        </h1>
        <p class="text-gray-600" v-if="newsList.length > 0">
          {{ newsList.length }} notícia(s) encontrada(s)
        </p>
      </div>

      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-500">Carregando...</p>
      </div>

      <div v-else-if="newsList.length === 0" class="text-center py-12">
        <p class="text-gray-500">Nenhuma notícia disponível no momento.</p>
      </div>

      <div v-else class="grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="news in newsList"
          :key="news.id"
          class="group cursor-pointer flex flex-col h-full"
          @click="openNews(news.slug)"
        >
          <div class="relative w-full overflow-hidden rounded-md bg-gray-100 mb-4 aspect-video">
            <span class="absolute top-3 left-3 z-10 bg-yellow-400 text-black text-[10px] font-bold px-2 py-1 uppercase tracking-wide rounded-sm shadow-sm">
              {{ news.category?.name || 'Geral' }}
            </span>

            <img
              v-if="news.image_path"
              :src="`http://portal-noticias-backend.test/storage/${news.image_path}`"
              :alt="news.title"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <div v-else class="w-full h-full bg-gray-300 flex items-center justify-center">
              <span class="text-gray-500 text-sm">Sem imagem</span>
            </div>
          </div>

          <div class="flex flex-col flex-grow">
            <h3 class="text-xl font-bold text-gray-900 leading-snug mb-2 group-hover:text-yellow-600 transition-colors">
              {{ news.title }}
            </h3>

            <div class="flex items-center text-xs text-gray-400 mb-3 font-medium">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span v-if="news.published_at">{{ formatDateRelative(news.published_at) }}</span>
              <span v-else>{{ formatDateRelative(news.created_at) }}</span>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4 flex-grow">
              {{ getExcerpt(news.content, 150) }}
            </p>
          </div>
        </article>
      </div>
    </main>
  </div>
</template>

<script setup>
/**
 * View AllNewsView - Página com todas as notícias públicas
 * 
 * Esta view exibe todas as notícias publicadas do portal em um grid responsivo.
 * 
 * Funcionalidades:
 * - Lista todas as notícias públicas (aprovadas e publicadas)
 * - Grid responsivo com cards de notícias
 * - Exibe contador de notícias encontradas
 * 
 * Estados:
 * - loading: Exibe mensagem de carregamento
 * - newsList: Lista de todas as notícias públicas
 */
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Header from '../components/Header.vue'
import { newsAPI } from '../services/api'
import { formatDateRelative } from '../utils/date'
import { getExcerpt } from '../utils/text'

const router = useRouter()

// Estado do componente
const newsList = ref([]) // Lista de todas as notícias públicas
const loading = ref(false) // Estado de carregamento

/**
 * Busca todas as notícias públicas da API
 * Exibe apenas notícias que foram aprovadas e publicadas
 */
const fetchNews = async () => {
  loading.value = true
  try {
    const response = await newsAPI.public()
    // Tenta obter dados de response.data.data, caso contrário usa response.data diretamente
    newsList.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Erro ao carregar notícias:', error)
    newsList.value = []
  } finally {
    loading.value = false
  }
}

/**
 * Redireciona para a página de detalhes da notícia
 * 
 * @param {string} slug - Slug da notícia
 */
const openNews = (slug) => {
  router.push(`/noticia/${slug}`)
}

// Carrega notícias ao montar o componente
onMounted(() => {
  fetchNews()
})
</script>
