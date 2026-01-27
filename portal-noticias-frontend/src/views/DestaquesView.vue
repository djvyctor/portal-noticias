<template>
  <div class="min-h-screen bg-gray-50">
    <Header />

    <main class="max-w-7xl mx-auto px-4 py-12">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Notícias em Destaque
        </h1>
        <p class="text-gray-600" v-if="newsList.length > 0">
          {{ newsList.length }} notícia(s) em destaque
        </p>
      </div>

      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-500">Carregando...</p>
      </div>

      <div v-else-if="newsList.length === 0" class="text-center py-12">
        <p class="text-gray-500">Nenhuma notícia em destaque no momento.</p>
      </div>

      <div v-else class="grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="news in newsList"
          :key="news.id"
          class="group cursor-pointer flex flex-col h-full"
          @click="openNews(news.slug)"
        >
          <div class="relative w-full overflow-hidden rounded-md bg-gray-100 mb-4 aspect-video">
            <span class="absolute top-3 left-3 z-10 bg-red-600 text-white text-[10px] font-bold px-2 py-1 uppercase tracking-wide rounded-sm">
              {{ news.category?.name || 'Destaque' }}
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
            <h3 class="text-xl font-bold text-gray-900 leading-snug mb-2 group-hover:text-red-600 transition-colors">
              {{ news.title }}
            </h3>
            <div class="flex items-center text-xs text-gray-400 mb-3">
              <span>{{ news.author_name || news.user?.name || 'Autor' }}</span>
              <span class="mx-1">•</span>
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
 * View DestaquesView - Página de notícias em destaque
 * 
 * Esta view exibe todas as notícias que foram marcadas como destaque.
 * 
 * Funcionalidades:
 * - Lista todas as notícias em destaque
 * - Grid responsivo com cards de notícias
 * - Exibe contador de notícias encontradas
 * 
 * Estados:
 * - loading: Exibe mensagem de carregamento
 * - newsList: Lista de notícias em destaque
 */
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Header from '../components/Header.vue'
import { newsAPI } from '../services/api'
import { formatDateRelative } from '../utils/date'
import { getExcerpt } from '../utils/text'

const router = useRouter()

// Estado do componente
const newsList = ref([]) // Lista de notícias em destaque
const loading = ref(false) // Estado de carregamento

/**
 * Busca todas as notícias em destaque da API
 * Usa o método newsAPI.featured() para obter as notícias destacadas
 */
const fetchNews = async () => {
  loading.value = true
  try {
    const response = await newsAPI.featured()
    const res = response.data
    // Tenta obter dados de res.data, caso contrário usa res diretamente
    newsList.value = res.data ?? res ?? []
  } catch (err) {
    console.error('Erro ao carregar notícias em destaque:', err)
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
onMounted(() => fetchNews())
</script>
