<template>
  <div class="min-h-screen bg-gray-50">
    <Header />

    <main class="max-w-7xl mx-auto px-4 py-12">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Últimas do Dia
        </h1>
        <p class="text-gray-600" v-if="newsList.length > 0">
          {{ newsList.length }} notícia(s)
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
            <span class="absolute top-3 left-3 z-10 bg-yellow-400 text-black text-[10px] font-bold px-2 py-1 uppercase tracking-wide rounded-sm">
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
            <div class="flex items-center text-xs text-gray-400 mb-3">
              <span>{{ news.author_name || news.user?.name || 'Autor' }}</span>
              <span class="mx-1">•</span>
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Header from '@/components/Header.vue'
import { newsAPI } from '@/services/api'

const router = useRouter()
const newsList = ref([])
const loading = ref(false)

const fetchNews = async () => {
  loading.value = true
  try {
    const response = await newsAPI.daily()
    const res = response.data
    newsList.value = res.data ?? res ?? []
  } catch (err) {
    newsList.value = []
  } finally {
    loading.value = false
  }
}

const openNews = (slug) => {
  router.push(`/noticia/${slug}`)
}

const formatDate = (d) => {
  if (!d) return ''
  const date = new Date(d)
  return date.toLocaleDateString('pt-BR')
}

const getExcerpt = (content, max = 150) => {
  if (!content) return ''
  const text = content.replace(/<[^>]*>/g, '')
  return text.length <= max ? text : text.slice(0, max) + '...'
}

onMounted(() => fetchNews())
</script>
