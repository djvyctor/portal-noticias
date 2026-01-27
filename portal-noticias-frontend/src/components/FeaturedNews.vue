<template>
  <section class="max-w-7xl mx-auto px-4 py-8 bg-white rounded-lg shadow-sm my-8">
    <div class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4">
      <div class="flex items-center">
        <span class="w-3 h-8 bg-red-600 rounded-sm mr-3"></span>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
          Notícias em Destaque
        </h2>
      </div>
      <router-link to="/destaques" class="text-sm font-semibold text-red-600 hover:text-red-700 hover:underline flex items-center transition-colors">
        Ver todas
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 ml-1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
      </router-link>
    </div>

    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-500">Carregando notícias em destaque...</p>
    </div>

    <div v-else-if="featuredList.length === 0" class="text-center py-12">
      <p class="text-gray-500">Nenhuma notícia em destaque no momento.</p>
    </div>

    <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <article
        v-for="news in featuredList.slice(0, 6)"
        :key="news.id"
        class="group cursor-pointer flex flex-col h-full bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
        @click="openNews(news.slug || news.id)"
      >
        <div class="relative w-full overflow-hidden bg-gray-100 aspect-video">
          <span class="absolute top-3 left-3 z-10 bg-red-600 text-white text-[10px] font-bold px-2 py-1 uppercase tracking-wide rounded-sm shadow-sm flex items-center">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            Destaque
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

        <div class="flex flex-col flex-grow p-4">
          <div class="mb-2">
            <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded">
              {{ news.category?.name || 'Geral' }}
            </span>
          </div>
          
          <h3 class="text-lg font-bold text-gray-900 leading-snug mb-2 group-hover:text-red-600 transition-colors line-clamp-2">
            {{ news.title }}
          </h3>

          <div class="flex flex-col gap-1 text-xs text-gray-500 mb-3 font-medium">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span>{{ news.author_name || news.user?.name || 'Autor' }}</span>
            </div>
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span v-if="news.published_at">{{ formatDate(news.published_at) }}</span>
              <span v-else>{{ formatDate(news.created_at) }}</span>
            </div>
          </div>

          <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 flex-grow">
            {{ getExcerpt(news.content) }}
          </p>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

// Estado do componente
const featuredList = ref([]) // Lista de notícias em destaque
const loading = ref(false) // Estado de carregamento

/**
 * Busca notícias em destaque da API
 * Exibe até 6 notícias no grid
 */
const fetchFeaturedNews = async () => {
  loading.value = true
  try {
    const response = await api.get('/news/featured')
    // Tenta obter dados de response.data.data, caso contrário usa response.data diretamente
    featuredList.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Erro ao carregar notícias em destaque:', error)
    featuredList.value = []
  } finally {
    loading.value = false
  }
}

/**
 * Redireciona para a página de detalhes da notícia
 * 
 * @param {string|number} slugOrId - Slug ou ID da notícia
 */
const openNews = (slugOrId) => {
  router.push(`/noticia/${slugOrId}`)
}

/**
 * Formata a data para exibição relativa (ex: "Há 2 horas", "Há 3 dias")
 * Se a data for muito antiga (> 7 dias), exibe a data formatada em pt-BR
 * 
 * @param {string} dateString - Data em formato ISO string
 * @returns {string} Data formatada para exibição
 */
const formatDate = (dateString) => {
  if (!dateString) return ''
  
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
  
  // Menos de 1 hora
  if (diffInHours < 1) return 'Há menos de 1 hora'
  
  // Menos de 24 horas
  if (diffInHours < 24) {
    return `Há ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`
  }
  
  // Menos de 7 dias
  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 7) {
    return `Há ${diffInDays} dia${diffInDays > 1 ? 's' : ''}`
  }
  
  // Mais de 7 dias - exibe data formatada
  return date.toLocaleDateString('pt-BR')
}

/**
 * Extrai um resumo do conteúdo HTML
 * Remove tags HTML e limita o tamanho do texto
 * 
 * @param {string} content - Conteúdo HTML da notícia
 * @param {number} maxLength - Tamanho máximo do resumo (padrão: 100 caracteres)
 * @returns {string} Resumo do conteúdo
 */
const getExcerpt = (content, maxLength = 100) => {
  if (!content) return ''
  const text = content.replace(/<[^>]*>/g, '') // Remove todas as tags HTML
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

// Carrega notícias ao montar o componente
onMounted(() => {
  fetchFeaturedNews()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
