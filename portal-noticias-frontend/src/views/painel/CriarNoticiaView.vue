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
            <h1 class="text-xl font-bold text-gray-900">Nova Notícia</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
            placeholder="Digite o título da notícia..."
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
          
          <div v-if="imagePreview" class="mb-4">
            <img :src="imagePreview" alt="Preview" class="max-h-64 rounded-lg" />
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
            placeholder="Escreva o conteúdo da notícia..."
          ></textarea>
        </div>

        <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm text-red-600">{{ error }}</p>
        </div>

        <div class="flex gap-4">
          <button
            type="button"
            @click="saveDraft"
            :disabled="saving"
            class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ saving ? 'Salvando...' : 'Salvar como Rascunho' }}
          </button>
          
          <button
            type="submit"
            :disabled="saving"
            class="flex-1 px-6 py-3 bg-yellow-600 text-white rounded-lg font-semibold hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ saving ? 'Enviando...' : getSubmitButtonText() }}
          </button>
        </div>
        
        <p v-if="isJornalista" class="text-sm text-center text-gray-500">
          💡 Suas notícias serão enviadas para aprovação do editor antes de serem publicadas.
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const categories = ref([])
const saving = ref(false)
const error = ref(null)
const imagePreview = ref(null)
const imageFile = ref(null)

const user = JSON.parse(localStorage.getItem('user') || '{}')
const isJornalista = user.role === 'jornalista'

const form = reactive({
  title: '',
  content: '',
  category_id: '',
  status: 'pending' // Sempre cria como pendente de aprovação
})

const loadCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = response.data || []
  } catch (err) {
    categories.value = []
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
  // Verifica o papel do usuário
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  
  // Editor e Admin podem publicar direto
  if (user.role === 'editor' || user.role === 'admin') {
    form.status = 'published'
  } else {
    // Jornalista cria como pending (aguardando aprovação)
    form.status = 'pending'
  }
  
  await saveNews()
}

const saveDraft = async () => {
  form.status = 'pending'
  await saveNews()
}

const saveNews = async () => {
  saving.value = true
  error.value = null

  try {
    const formData = new FormData()
    formData.append('title', form.title)
    formData.append('content', form.content)
    formData.append('category_id', form.category_id)
    formData.append('status', form.status)
    
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    await api.post('/user/news', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    router.push('/painel/noticias')
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao salvar notícia'
  } finally {
    saving.value = false
  }
}

const getSubmitButtonText = () => {
  if (isJornalista) {
    return 'Enviar para Aprovação'
  }
  return 'Publicar Notícia'
}

onMounted(() => {
  loadCategories()
})
</script>
