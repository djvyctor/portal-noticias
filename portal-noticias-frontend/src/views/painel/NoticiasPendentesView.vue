<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Notícias Pendentes de Aprovação</h1>

    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-500">Carregando notícias...</p>
    </div>

    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
      {{ error }}
    </div>

    <template v-else>
      <div v-if="approveError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex justify-between items-center">
        <span>{{ approveError }}</span>
        <button type="button" @click="approveError = null" class="text-red-700 hover:text-red-900 font-bold px-2">×</button>
      </div>

      <div v-if="news.data && news.data.length === 0" class="text-center py-12">
        <p class="text-gray-500">Nenhuma notícia pendente no momento.</p>
      </div>

      <div v-else class="space-y-6">
      <div
        v-for="item in news.data"
        :key="item.id"
        class="bg-white shadow-md rounded-lg overflow-hidden border-l-4 border-yellow-500"
      >
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
              <h2 class="text-xl font-bold text-gray-800 mb-2">{{ item.title }}</h2>
              <div class="text-sm text-gray-600 space-y-1">
                <p><strong>Autor:</strong> {{ item.author_name || item.user?.name }}</p>
                <p><strong>Email:</strong> {{ item.user?.email }}</p>
                <p><strong>Categoria:</strong> {{ item.category?.name }}</p>
                <p><strong>Criado em:</strong> {{ formatDate(item.created_at) }}</p>
              </div>
            </div>
            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">
              Pendente
            </span>
          </div>

          <div class="mb-4">
            <img
              v-if="item.image_path"
              :src="`http://portal-noticias-backend.test/storage/${item.image_path}`"
              :alt="item.title"
              class="w-full h-48 object-cover rounded-lg mb-4"
            />
            <div class="prose max-w-none">
              <p class="text-gray-700 line-clamp-3">{{ stripHtml(item.content) }}</p>
            </div>
          </div>

          <div class="flex gap-2">
            <button
              @click="viewNews(item)"
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200"
            >
              Ver Detalhes
            </button>
            <button
              @click="approveNews(item.id)"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition duration-200"
            >
              ✓ Aprovar
            </button>
            <button
              @click="openRejectModal(item)"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition duration-200"
            >
              ✗ Rejeitar
            </button>
          </div>
        </div>
      </div>

      <!-- Paginação -->
      <div v-if="news.last_page > 1" class="flex justify-center gap-2 mt-8">
        <button
          @click="loadNews(news.current_page - 1)"
          :disabled="news.current_page === 1"
          class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
        >
          Anterior
        </button>
        <span class="px-4 py-2">Página {{ news.current_page }} de {{ news.last_page }}</span>
        <button
          @click="loadNews(news.current_page + 1)"
          :disabled="news.current_page === news.last_page"
          class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
        >
          Próxima
        </button>
      </div>
    </div>
    </template>

    <!-- Modal de Rejeição -->
    <div v-if="showRejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showRejectModal = false">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Rejeitar Notícia</h3>
          <p class="text-gray-700 mb-2"><strong>Título:</strong> {{ newsToReject?.title }}</p>
          <form @submit.prevent="rejectNews">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Motivo da Rejeição</label>
              <textarea
                v-model="rejectionReason"
                required
                rows="4"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Descreva o motivo da rejeição..."
              ></textarea>
            </div>
            <div v-if="rejectError" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
              {{ rejectError }}
            </div>
            <div class="flex justify-end gap-2">
              <button
                type="button"
                @click="showRejectModal = false"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
              >
                Rejeitar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal de Visualização -->
    <div v-if="showViewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showViewModal = false">
      <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white my-8">
        <div class="mt-3">
          <div class="flex justify-between items-start mb-4">
            <h3 class="text-2xl font-bold text-gray-900">{{ viewingNews?.title }}</h3>
            <button
              @click="showViewModal = false"
              class="text-gray-400 hover:text-gray-600 text-2xl"
            >
              ×
            </button>
          </div>
          <div class="text-sm text-gray-600 mb-4">
            <p><strong>Autor:</strong> {{ viewingNews?.author_name || viewingNews?.user?.name }}</p>
            <p><strong>Categoria:</strong> {{ viewingNews?.category?.name }}</p>
            <p><strong>Criado em:</strong> {{ formatDateTime(viewingNews?.created_at) }}</p>
          </div>
          <img
            v-if="viewingNews?.image_path"
            :src="`http://portal-noticias-backend.test/storage/${viewingNews.image_path}`"
            :alt="viewingNews.title"
            class="w-full h-64 object-cover rounded-lg mb-4"
          />
          <div class="prose max-w-none" v-html="viewingNews?.content"></div>
          <div class="flex justify-end gap-2 mt-6">
            <button
              @click="approveNews(viewingNews.id)"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition duration-200"
            >
              ✓ Aprovar
            </button>
            <button
              @click="openRejectModal(viewingNews)"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition duration-200"
            >
              ✗ Rejeitar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { newsAPI } from '@/services/api'

const loading = ref(false)
const error = ref(null)
const approveError = ref(null)
const news = ref({ data: [], current_page: 1, last_page: 1 })
const showRejectModal = ref(false)
const showViewModal = ref(false)
const newsToReject = ref(null)
const viewingNews = ref(null)
const rejectionReason = ref('')
const rejectError = ref(null)

const loadNews = async (page = 1) => {
  loading.value = true
  error.value = null
  approveError.value = null
  try {
    const response = await newsAPI.pending(page)
    const res = response.data
    news.value = {
      data: res.data ?? [],
      current_page: res.meta?.current_page ?? 1,
      last_page: res.meta?.last_page ?? 1,
      links: res.links,
      meta: res.meta
    }
  } catch (err) {
    console.error('Erro ao carregar notícias pendentes:', err)
    error.value = 'Erro ao carregar notícias: ' + (err.response?.data?.message || err.message)
  } finally {
    loading.value = false
  }
}

const approveNews = async (id) => {
  approveError.value = null
  try {
    await newsAPI.approve(id)
    alert('Notícia aprovada com sucesso!')
    await loadNews(news.value.current_page || 1)
  } catch (err) {
    console.error('Erro ao aprovar:', err)
    const msg = err.response?.data?.message || err.message || 'Erro ao aprovar.'
    approveError.value = `Erro ao aprovar notícia: ${msg}`
  }
}

const openRejectModal = (item) => {
  newsToReject.value = item
  rejectionReason.value = ''
  rejectError.value = null
  showRejectModal.value = true
  showViewModal.value = false
}

const rejectNews = async () => {
  rejectError.value = null
  try {
    await newsAPI.reject(newsToReject.value.id, rejectionReason.value)
    showRejectModal.value = false
    await loadNews(news.value.current_page || 1)
  } catch (err) {
    rejectError.value = 'Erro ao rejeitar notícia: ' + (err.response?.data?.message || err.message)
  }
}

const viewNews = (item) => {
  viewingNews.value = item
  showViewModal.value = true
}

const stripHtml = (html) => {
  if (html == null || html === '') return ''
  const tmp = document.createElement('div')
  tmp.innerHTML = String(html)
  return tmp.textContent || tmp.innerText || ''
}

const formatDate = (date) => {
  if (date == null) return '—'
  const d = new Date(date)
  return Number.isNaN(d.getTime()) ? '—' : d.toLocaleDateString('pt-BR')
}

const formatDateTime = (date) => {
  if (date == null) return '—'
  const d = new Date(date)
  return Number.isNaN(d.getTime()) ? '—' : d.toLocaleString('pt-BR')
}

onMounted(() => {
  loadNews()
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
