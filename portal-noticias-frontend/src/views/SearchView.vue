<template>
  <div class="min-h-screen bg-gray-50">
    <Header />
    
    <main class="max-w-7xl mx-auto px-4 py-12">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Resultados da Busca
        </h1>
        <p class="text-gray-600" v-if="searchQuery">
          Buscando por: "<strong>{{ searchQuery }}</strong>"
        </p>
        <p class="text-gray-600" v-if="newsList.length > 0">
          {{ newsList.length }} notícia(s) encontrada(s)
        </p>
      </div>

      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-500">Buscando...</p>
      </div>

      <div v-else-if="newsList.length === 0 && searchQuery" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <p class="text-gray-500 mb-2">Nenhuma notícia encontrada para "{{ searchQuery }}"</p>
        <p class="text-sm text-gray-400">Tente usar palavras-chave diferentes</p>
      </div>

      <div v-else-if="!searchQuery" class="text-center py-12">
        <p class="text-gray-500">Digite um termo de busca no campo acima</p>
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
              <span v-if="news.published_at">{{ formatDate(news.published_at) }}</span>
              <span v-else>{{ formatDate(news.created_at) }}</span>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4 flex-grow">
              {{ getExcerpt(news.content) }}
            </p>
          </div>
        </article>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Header from '@/components/Header.vue'
import { newsAPI } from '@/services/api'

const route = useRoute()
const router = useRouter()
const newsList = ref([])
const loading = ref(false)
const searchQuery = ref('')

const fetchSearchResults = async () => {
  const query = route.query.q
  if (!query || String(query).trim().length < 2) {
    searchQuery.value = ''
    newsList.value = []
    return
  }

  loading.value = true
  searchQuery.value = String(query).trim()

  try {
    const response = await newsAPI.search(searchQuery.value)
    const res = response.data
    newsList.value = res.data ?? res ?? []
  } catch (err) {
    console.error('Erro na busca:', err)
    newsList.value = []
  } finally {
    loading.value = false
  }
}

const openNews = (slug) => {
  router.push(`/noticia/${slug}`)
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
  const text = content.replace(/<[^>]*>/g, '')
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

onMounted(() => {
  fetchSearchResults()
})

watch(
  () => route.query.q,
  () => {
    fetchSearchResults()
  }
)
</script>
