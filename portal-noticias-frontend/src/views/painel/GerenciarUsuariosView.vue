<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Gerenciar Usuários</h1>
      <button
        @click="showCreateModal = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200"
      >
        + Novo Usuário
      </button>
    </div>

    <!-- Lista de Usuários -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-500">Carregando usuários...</p>
    </div>

    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>

    <div v-else class="bg-white shadow-md rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Função</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users.data" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getRoleBadgeClass(user.role)" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ getRoleLabel(user.role) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="editUser(user)"
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(user)"
                class="text-red-600 hover:text-red-900"
                :disabled="user.id === currentUserId"
              >
                Excluir
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Paginação -->
      <div v-if="users.last_page > 1" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="loadUsers(users.current_page - 1)"
            :disabled="users.current_page === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Anterior
          </button>
          <button
            @click="loadUsers(users.current_page + 1)"
            :disabled="users.current_page === users.last_page"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Próxima
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Criar/Editar Usuário -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="closeModals">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showCreateModal ? 'Criar Novo Usuário' : 'Editar Usuário' }}
          </h3>
          <form @submit.prevent="showCreateModal ? createUser() : updateUser()">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Nome</label>
              <input
                v-model="formData.name"
                @input="capitalizeName"
                type="text"
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
              <input
                v-model="formData.email"
                type="email"
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Senha {{ showEditModal ? '(deixe em branco para não alterar)' : '' }}</label>
              <input
                v-model="formData.password"
                type="password"
                :required="showCreateModal"
                minlength="8"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Função</label>
              <select
                v-model="formData.role"
                required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              >
                <option value="jornalista">Jornalista</option>
                <option value="editor">Editor</option>
                <option value="admin">Administrador</option>
              </select>
            </div>
            <div v-if="formError" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
              {{ formError }}
            </div>
            <div class="flex justify-end gap-2">
              <button
                type="button"
                @click="closeModals"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
              >
                Cancelar
              </button>
              <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              >
                {{ showCreateModal ? 'Criar' : 'Atualizar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Confirmar Exclusão -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showDeleteModal = false">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar Exclusão</h3>
          <p class="text-gray-600 mb-6">Tem certeza que deseja excluir o usuário <strong>{{ userToDelete?.name }}</strong>?</p>
          <div class="flex justify-end gap-2">
            <button
              @click="showDeleteModal = false"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
            >
              Cancelar
            </button>
            <button
              @click="deleteUser"
              class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
            >
              Excluir
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { userAPI } from '@/services/api'

const loading = ref(false)
const error = ref(null)
const users = ref({ data: [], current_page: 1, last_page: 1 })
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const userToDelete = ref(null)
const formError = ref(null)
const currentUserId = ref(JSON.parse(localStorage.getItem('user'))?.id)

const formData = ref({
  name: '',
  email: '',
  password: '',
  role: 'jornalista'
})

const editingUserId = ref(null)

const capitalizeName = (event) => {
  const words = event.target.value.split(' ')
  const capitalizedWords = words.map(word => {
    if (word.length > 0) {
      return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
    }
    return word
  })
  formData.value.name = capitalizedWords.join(' ')
}

const loadUsers = async (page = 1) => {
  loading.value = true
  error.value = null
  try {
    const response = await userAPI.index({ params: { page } })
    users.value = response.data
  } catch (err) {
    error.value = 'Erro ao carregar usuários: ' + (err.response?.data?.message || err.message)
  } finally {
    loading.value = false
  }
}

const createUser = async () => {
  formError.value = null
  try {
    await userAPI.create(formData.value)
    closeModals()
    loadUsers()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Erro ao criar usuário'
  }
}

const editUser = (user) => {
  editingUserId.value = user.id
  formData.value = {
    name: user.name,
    email: user.email,
    password: '',
    role: user.role
  }
  showEditModal.value = true
}

const updateUser = async () => {
  formError.value = null
  try {
    const data = { ...formData.value }
    if (!data.password) {
      delete data.password
    }
    await userAPI.update(editingUserId.value, data)
    closeModals()
    loadUsers()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Erro ao atualizar usuário'
  }
}

const confirmDelete = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const deleteUser = async () => {
  try {
    await userAPI.delete(userToDelete.value.id)
    showDeleteModal.value = false
    loadUsers()
  } catch (err) {
    error.value = 'Erro ao excluir usuário: ' + (err.response?.data?.message || err.message)
  }
}

const closeModals = () => {
  showCreateModal.value = false
  showEditModal.value = false
  formData.value = {
    name: '',
    email: '',
    password: '',
    role: 'jornalista'
  }
  editingUserId.value = null
  formError.value = null
}

const getRoleLabel = (role) => {
  const labels = {
    admin: 'Administrador',
    editor: 'Editor',
    jornalista: 'Jornalista'
  }
  return labels[role] || role
}

const getRoleBadgeClass = (role) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    editor: 'bg-blue-100 text-blue-800',
    jornalista: 'bg-green-100 text-green-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

onMounted(() => {
  loadUsers()
})
</script>
