<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    <Header />

    <main v-if="loading" class="max-w-4xl mx-auto px-4 py-12">
      <div class="text-center py-12">
        <p class="text-gray-500">Carregando notícia...</p>
      </div>
    </main>

    <main v-else-if="error" class="max-w-4xl mx-auto px-4 py-12">
      <div class="text-center py-12">
        <p class="text-red-500 mb-4">{{ error }}</p>
        <button
          @click="$router.push('/')"
          class="text-yellow-600 hover:text-yellow-700 font-semibold"
        >
          Voltar para a página inicial
        </button>
      </div>
    </main>

    <main v-else-if="news" class="max-w-4xl mx-auto px-4 py-8">
      <!-- Botão voltar -->
      <button
        @click="$router.push('/')"
        class="flex items-center text-gray-600 hover:text-yellow-600 mb-6 transition-colors"
      >
        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Voltar
      </button>

      <!-- Categoria -->
      <div class="mb-4">
        <span class="inline-block px-3 py-1 text-xs font-bold tracking-wider text-white uppercase bg-yellow-400 rounded-sm">
          {{ news.category?.name || 'Geral' }}
        </span>
      </div>

      <!-- Título -->
      <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-4">
        {{ news.title }}
      </h1>

      <!-- Meta informações -->
      <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-6 pb-6 border-b border-gray-200">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span class="font-medium">Por {{ news.author_name || news.user?.name || 'Autor' }}</span>
        </div>
        <span class="text-gray-300">•</span>
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span>{{ formatDate(news.published_at || news.created_at) }}</span>
        </div>
        <span class="text-gray-300">•</span>
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ formatTime(news.published_at || news.created_at) }}</span>
        </div>
      </div>

      <!-- Imagem principal -->
      <div v-if="news.image_path" class="mb-8 rounded-lg overflow-hidden">
        <img
          :src="`http://portal-noticias-backend.test/storage/${news.image_path}`"
          :alt="news.title"
          class="w-full h-auto object-cover"
        />
      </div>

      <!-- Conteúdo -->
      <article class="prose prose-lg max-w-none mb-12">
        <div v-html="news.content" class="text-gray-700 leading-relaxed"></div>
      </article>

    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'
import Header from '../components/Header.vue'

const route = useRoute()
const router = useRouter()
const news = ref(null)
const loading = ref(true)
const error = ref(null)

const fetchNews = async () => {
  loading.value = true
  error.value = null
  
  try {
    const slug = route.params.slug
    const response = await api.get(`/news/${slug}`)
    const res = response.data
    news.value = res.data ?? res
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'Notícia não encontrada.'
    } else {
      error.value = 'Erro ao carregar a notícia. Tente novamente mais tarde.'
    }
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleTimeString('pt-BR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  fetchNews()
})
</script>

<style scoped>
/* Estilos para o conteúdo HTML da notícia */
.prose :deep(p) {
  margin-bottom: 1.5rem;
  line-height: 1.75;
}

.prose :deep(h2) {
  font-size: 1.875rem;
  font-weight: 700;
  margin-top: 2rem;
  margin-bottom: 1rem;
  color: #111827;
}

.prose :deep(h3) {
  font-size: 1.5rem;
  font-weight: 600;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
  color: #111827;
}

.prose :deep(ul),
.prose :deep(ol) {
  margin-bottom: 1.5rem;
  padding-left: 1.5rem;
}

.prose :deep(li) {
  margin-bottom: 0.5rem;
}

.prose :deep(strong) {
  font-weight: 600;
  color: #111827;
}

.prose :deep(a) {
  color: #eab308;
  text-decoration: underline;
}

.prose :deep(a:hover) {
  color: #ca8a04;
}
</style>
