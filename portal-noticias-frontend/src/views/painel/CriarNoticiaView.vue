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

        <!-- Campo de destaque (apenas para Editor/Admin) -->
        <div v-if="canManageFeature" class="bg-white rounded-lg shadow p-6">
          <label class="flex items-center gap-3 cursor-pointer">
            <input
              v-model="form.is_featured"
              type="checkbox"
              class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500"
            />
            <span class="text-sm font-medium text-gray-700">
              Destacar no carrossel da home
            </span>
          </label>
          <p class="text-xs text-gray-500 mt-2">
            Notícias destacadas aparecem no carrossel da página inicial (máximo 5). Apenas Editor e Admin podem alterar.
          </p>
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

/**
 * View CriarNoticiaView - Formulário para criar nova notícia
 * 
 * Esta view permite que usuários autenticados criem novas notícias.
 * 
 * Funcionalidades:
 * - Formulário completo para criação de notícia (título, categoria, conteúdo, imagem)
 * - Preview da imagem antes do upload
 * - Validação de campos obrigatórios
 * - Diferentes comportamentos baseados no papel do usuário:
 *   - Jornalista: Cria como "pending" (aguarda aprovação)
 *   - Editor/Admin: Pode publicar diretamente e definir destaque
 * 
 * Campos do formulário:
 * - Título (obrigatório)
 * - Categoria (obrigatório)
 * - Imagem (opcional)
 * - Conteúdo (obrigatório)
 * - Destaque (opcional, apenas Editor/Admin)
 */

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { newsAPI, categoryAPI } from '../../services/api'

const router = useRouter()

// Estado do componente
const categories = ref([]) // Lista de categorias disponíveis
const saving = ref(false) // Estado de salvamento
const error = ref(null) // Mensagem de erro
const imagePreview = ref(null) // URL da preview da imagem
const imageFile = ref(null) // Arquivo de imagem selecionado

// Verifica o papel do usuário
const user = JSON.parse(localStorage.getItem('user') || '{}')
const isJornalista = user.role === 'jornalista'

/**
 * Verifica se o usuário pode gerenciar destaque
 * Apenas Editor e Admin têm essa permissão
 */
const canManageFeature = user.role === 'editor' || user.role === 'admin'

// Dados do formulário
const form = reactive({
  title: '',
  content: '',
  category_id: '',
  status: 'pending', // Status padrão (será alterado baseado no papel do usuário)
  is_featured: false // Destaque (apenas Editor/Admin podem definir)
})

/**
 * Carrega as categorias disponíveis da API
 */
const loadCategories = async () => {
  try {
    const response = await categoryAPI.index()
    categories.value = response.data || []
  } catch (err) {
    console.error('Erro ao carregar categorias:', err)
    categories.value = []
  }
}

/**
 * Processa a seleção de imagem e cria preview
 * 
 * @param {Event} event - Evento de mudança do input de arquivo
 */
const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    imageFile.value = file
    // Cria preview da imagem usando FileReader
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

/**
 * Remove a imagem selecionada e limpa o preview
 */
const removeImage = () => {
  imagePreview.value = null
  imageFile.value = null
}

/**
 * Processa o envio do formulário (publicar)
 * Define o status baseado no papel do usuário antes de salvar
 */
const handleSubmit = async () => {
  // Verifica o papel do usuário novamente (pode ter mudado)
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  
  // Editor e Admin podem publicar diretamente
  if (user.role === 'editor' || user.role === 'admin') {
    form.status = 'published'
  } else {
    // Jornalista cria como pending (aguardando aprovação)
    form.status = 'pending'
  }
  
  await saveNews()
}

/**
 * Salva a notícia como rascunho (sempre como pending)
 */
const saveDraft = async () => {
  form.status = 'pending'
  await saveNews()
}

/**
 * Salva a notícia na API usando FormData para suportar upload de imagem
 */
const saveNews = async () => {
  saving.value = true
  error.value = null

  try {
    // Cria FormData para enviar dados e arquivo
    const formData = new FormData()
    formData.append('title', form.title)
    formData.append('content', form.content)
    formData.append('category_id', form.category_id)
    formData.append('status', form.status)
    
    // Apenas Editor/Admin podem definir destaque
    if (canManageFeature) {
      formData.append('is_featured', form.is_featured ? '1' : '0')
    }
    
    // Adiciona imagem se houver
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    await newsAPI.create(formData)

    // Redireciona para a lista de notícias após sucesso
    router.push('/painel/noticias')
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao salvar notícia'
    console.error('Erro ao salvar notícia:', err)
  } finally {
    saving.value = false
  }
}

/**
 * Retorna o texto do botão de submit baseado no papel do usuário
 * 
 * @returns {string} Texto do botão
 */
const getSubmitButtonText = () => {
  if (isJornalista) {
    return 'Enviar para Aprovação'
  }
  return 'Publicar Notícia'
}

// Carrega categorias ao montar o componente
onMounted(() => {
  loadCategories()
})
</script>
