<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center gap-4">
            <router-link to="/painel/noticias" class="text-gray-600 hover:text-gray-900">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </router-link>
            <h1 class="text-xl font-bold text-gray-900">Editar Notícia</h1>
          </div>
        </div>
      </div>
    </header>

    <div v-if="loading" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center">
      <p class="text-gray-500">Carregando...</p>
    </div>

    <div v-else-if="news" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
          <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
            Título da Notícia *
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"
          />
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
            Categoria *
          </label>
          <select
            id="category"
            v-model="form.category_id"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"
          >
            <option value="">Selecione uma categoria</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Imagem da Notícia
          </label>
          
          <div v-if="imagePreview || news.image_path" class="mb-4">
            <img 
              :src="imagePreview || `http://portal-noticias-backend.test/storage/${news.image_path}`" 
              alt="Preview" 
              class="max-h-64 rounded-lg" 
            />
            <button
              type="button"
              @click="removeImage"
              class="mt-2 text-sm text-red-600 hover:text-red-700"
            >
              Remover imagem
            </button>
          </div>
          
          <input
            type="file"
            accept="image/*"
            @change="handleImageChange"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100"
          />
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
            Conteúdo *
          </label>
          <textarea
            id="content"
            v-model="form.content"
            required
            rows="15"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"
          ></textarea>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
            Status
          </label>
          <select
            id="status"
            v-model="form.status"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500"
          >
            <option value="draft">Rascunho</option>
            <option value="published">Publicada</option>
          </select>
        </div>

        <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm text-red-600">{{ error }}</p>
        </div>

        <div class="flex gap-4">
          <router-link
            to="/painel/noticias"
            class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 text-center transition-colors"
          >
            Cancelar
          </router-link>
          
          <button
            type="submit"
            :disabled="saving"
            class="flex-1 px-6 py-3 bg-yellow-600 text-white rounded-lg font-semibold hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ saving ? 'Salvando...' : 'Salvar Alterações' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const router = useRouter()
const news = ref(null)
const categories = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref(null)
const imagePreview = ref(null)
const imageFile = ref(null)

const form = reactive({
  title: '',
  content: '',
  category_id: '',
  status: 'draft'
})

const loadNews = async () => {
  try {
    const response = await api.get(`/api/user/news/${route.params.id}`)
    news.value = response.data
    
    form.title = news.value.title
    form.content = news.value.content
    form.category_id = news.value.category_id
    form.status = news.value.status
  } catch (err) {
    console.error('Erro ao carregar notícia:', err)
    error.value = 'Erro ao carregar notícia'
    setTimeout(() => {
      router.push('/painel/noticias')
    }, 2000)
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await api.get('/api/categories')
    categories.value = response.data || []
  } catch (err) {
    console.error('Erro ao carregar categorias:', err)
  }
}

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    imageFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeImage = () => {
  imagePreview.value = null
  imageFile.value = null
}

const handleSubmit = async () => {
  saving.value = true
  error.value = null

  try {
    const formData = new FormData()
    formData.append('title', form.title)
    formData.append('content', form.content)
    formData.append('category_id', form.category_id)
    formData.append('status', form.status)
    formData.append('_method', 'PUT')
    
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    await api.post(`/api/user/news/${route.params.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    router.push('/painel/noticias')
  } catch (err) {
    console.error('Erro ao atualizar notícia:', err)
    error.value = err.response?.data?.message || 'Erro ao atualizar notícia'
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadNews()
  loadCategories()
})
</script>
