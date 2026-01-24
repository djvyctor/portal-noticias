<template>
  <section class="max-w-7xl mx-auto px-4 py-12">
    
    <div class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4">
      <div class="flex items-center">
        <span class="w-3 h-8 bg-yellow-400 rounded-sm mr-3"></span>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
          Últimas do Dia
        </h2>
      </div>
      <router-link to="/todas-noticias" class="text-sm font-semibold text-yellow-600 hover:text-yellow-700 hover:underline flex items-center transition-colors">
        Ver todas
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 ml-1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
      </router-link>
    </div>

    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-500">Carregando notícias...</p>
    </div>

    <div v-else-if="newsList.length === 0" class="text-center py-12">
      <p class="text-gray-500">Nenhuma notícia disponível no momento.</p>
    </div>

    <div v-else class="grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
      <article
        v-for="news in newsList"
        :key="news.id"
        class="group cursor-pointer flex flex-col h-full"
        @click="openNews(news.slug || news.id)"
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
            <span v-if="news.published_at">{{ formatDate(news.published_at) }}</span>
            <span v-else>{{ formatDate(news.created_at) }}</span>
          </div>

          <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4 flex-grow">
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
const newsList = ref([])
const loading = ref(false)

// Buscar notícias da API
const fetchNews = async () => {
  loading.value = true
  try {
    const response = await api.get('/api/news/public')
    newsList.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Erro ao buscar notícias:', error)
    newsList.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchNews()
})

const openNews = (slugOrId) => {
  // Se for slug, usa direto; se for ID, precisa buscar o slug primeiro
  router.push(`/noticia/${slugOrId}`)
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
  
  if (diffInHours < 1) return 'Há menos de 1 hora'
  if (diffInHours < 24) return `Há ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`
  
  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 7) return `Há ${diffInDays} dia${diffInDays > 1 ? 's' : ''}`
  
  return date.toLocaleDateString('pt-BR')
}

const getExcerpt = (content, maxLength = 150) => {
  if (!content) return ''
  const text = content.replace(/<[^>]*>/g, '') // Remove HTML tags
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}
</script>