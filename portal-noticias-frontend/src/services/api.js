/**
 * Serviço de API - Configuração do Axios e métodos de comunicação com o backend
 * 
 * Este arquivo centraliza todas as chamadas à API do backend, incluindo:
 * - Configuração base do Axios
 * - Interceptors para autenticação automática
 * - Tratamento de erros de autenticação
 * - Métodos organizados por funcionalidade (auth, news, user, category)
 */

import axios from 'axios'

/**
 * Cria instância do Axios com configuração base
 * 
 * baseURL: URL base da API do backend
 * headers: Headers padrão para todas as requisições
 */
const api = axios.create({
    baseURL: 'http://portal-noticias-backend.test/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})

/**
 * Interceptor de requisições - Adiciona token de autenticação automaticamente
 * 
 * Intercepta todas as requisições antes de serem enviadas e adiciona
 * o token de autenticação do localStorage no header Authorization
 * 
 * @param {Object} config - Configuração da requisição
 * @returns {Object} Configuração modificada com o token
 */
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token')
        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

/**
 * Interceptor de respostas - Trata erros de autenticação
 * 
 * Intercepta todas as respostas e verifica se há erro 401 (não autorizado).
 * Se houver, remove os dados do usuário do localStorage e redireciona para login.
 * 
 * @param {Object} response - Resposta da requisição
 * @param {Object} error - Erro da requisição
 * @returns {Object|Promise} Resposta ou rejeição da promise
 */
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Token inválido ou expirado - limpa dados e redireciona
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

// ========== MÉTODOS DE API POR FUNCIONALIDADE ==========

/**
 * API de Autenticação
 * 
 * Métodos relacionados ao login, registro, logout e recuperação de senha
 */
export const authAPI = {
    /**
     * Realiza login do usuário
     * @param {string} email - Email do usuário
     * @param {string} password - Senha do usuário
     * @returns {Promise} Resposta da API com token e dados do usuário
     */
    login: (email, password) => api.post('/login', { email, password }),
    
    /**
     * Registra um novo usuário
     * @param {string} name - Nome do usuário
     * @param {string} email - Email do usuário
     * @param {string} password - Senha do usuário
     * @param {string} password_confirmation - Confirmação da senha
     * @returns {Promise} Resposta da API
     */
    register: (name, email, password, password_confirmation) => api.post('/register', { 
        name, 
        email, 
        password,
        password_confirmation
    }),
    
    /**
     * Realiza logout do usuário
     * @returns {Promise} Resposta da API
     */
    logout: () => api.post('/logout'),
    
    /**
     * Obtém dados do usuário autenticado
     * @returns {Promise} Resposta da API com dados do usuário
     */
    me: () => api.get('/user'),
    
    /**
     * Altera a senha do usuário autenticado
     * @param {string} currentPassword - Senha atual
     * @param {string} password - Nova senha
     * @param {string} password_confirmation - Confirmação da nova senha
     * @returns {Promise} Resposta da API
     */
    changePassword: (currentPassword, password, password_confirmation) => api.post('/change-password', {
        current_password: currentPassword,
        password,
        password_confirmation
    }),
    
    /**
     * Solicita recuperação de senha
     * @param {string} email - Email do usuário
     * @returns {Promise} Resposta da API
     */
    forgotPassword: (email) => api.post('/forgot-password', { email }),
    
    /**
     * Reseta a senha do usuário com token de recuperação
     * @param {string} email - Email do usuário
     * @param {string} token - Token de recuperação
     * @param {string} password - Nova senha
     * @param {string} password_confirmation - Confirmação da nova senha
     * @returns {Promise} Resposta da API
     */
    resetPassword: (email, token, password, password_confirmation) => api.post('/reset-password', {
        email,
        token,
        password,
        password_confirmation
    })
}

/**
 * API de Notícias
 * 
 * Métodos relacionados ao CRUD de notícias, moderação e visualização pública
 */
export const newsAPI = {
    // ========== NOTÍCIAS DO USUÁRIO ==========
    /**
     * Lista todas as notícias do usuário autenticado
     * @returns {Promise} Resposta da API com lista de notícias
     */
    list: () => api.get('/user/news'),
    
    /**
     * Cria uma nova notícia
     * @param {Object} data - Dados da notícia (title, content, category_id, image, etc)
     * @returns {Promise} Resposta da API com a notícia criada
     */
    create: (data) => api.post('/user/news', data),
    
    /**
     * Atualiza uma notícia existente
     * @param {number} id - ID da notícia
     * @param {Object} data - Dados atualizados da notícia
     * @returns {Promise} Resposta da API com a notícia atualizada
     */
    update: (id, data) => api.put(`/user/news/${id}`, data),
    
    /**
     * Deleta uma notícia
     * @param {number} id - ID da notícia
     * @returns {Promise} Resposta da API
     */
    delete: (id) => api.delete(`/user/news/${id}`),
    
    /**
     * Obtém detalhes de uma notícia do usuário
     * @param {number} id - ID da notícia
     * @returns {Promise} Resposta da API com dados da notícia
     */
    show: (id) => api.get(`/user/news/${id}`),

    // ========== GERENCIAMENTO DE NOTÍCIAS (Admin/Editor) ==========
    /**
     * Aprova uma notícia pendente
     * @param {number} id - ID da notícia
     * @returns {Promise} Resposta da API
     */
    approve: (id) => api.patch(`/news/${id}/approve`),
    
    /**
     * Rejeita uma notícia pendente
     * @param {number} id - ID da notícia
     * @param {string} rejectionReason - Motivo da rejeição
     * @returns {Promise} Resposta da API
     */
    reject: (id, rejectionReason) => api.patch(`/news/${id}/reject`, { rejection_reason: rejectionReason }),
    
    /**
     * Destaca uma notícia (coloca em destaque)
     * @param {number} id - ID da notícia
     * @returns {Promise} Resposta da API
     */
    feature: (id) => api.patch(`/news/${id}/feature`),
    
    /**
     * Lista notícias pendentes de aprovação (com paginação)
     * @param {number} page - Número da página (padrão: 1)
     * @returns {Promise} Resposta da API com lista paginada
     */
    pending: (page = 1) => api.get('/news/pending', { params: { page } }),
    
    /**
     * Lista notícias rejeitadas
     * @param {number|null} userId - ID do usuário (opcional, filtra por usuário)
     * @returns {Promise} Resposta da API com lista de notícias rejeitadas
     */
    rejected: (userId = null) => userId ? api.get(`/news/rejected/${userId}`) : api.get('/news/rejected'),

    // ========== NOTÍCIAS PÚBLICAS ==========
    /**
     * Lista notícias públicas (publicadas)
     * @returns {Promise} Resposta da API com lista de notícias públicas
     */
    public: () => api.get('/news/public'),
    
    /**
     * Lista notícias em destaque
     * @returns {Promise} Resposta da API com lista de notícias destacadas
     */
    featured: () => api.get('/news/featured'),
    
    /**
     * Lista notícias para o carrossel (destaques principais)
     * @returns {Promise} Resposta da API com lista de notícias do carrossel
     */
    carousel: () => api.get('/news/carousel'),
    
    /**
     * Lista notícias do dia
     * @returns {Promise} Resposta da API com lista de notícias do dia
     */
    daily: () => api.get('/news/daily'),
    
    /**
     * Busca notícias por termo
     * @param {string} query - Termo de busca
     * @returns {Promise} Resposta da API com resultados da busca
     */
    search: (query) => api.get('/news/search', { params: { q: query } }),
    
    /**
     * Lista notícias de uma categoria específica
     * @param {string} slug - Slug da categoria
     * @returns {Promise} Resposta da API com notícias da categoria
     */
    byCategory: (slug) => api.get(`/news/category/${slug}`),
    
    /**
     * Obtém uma notícia pelo slug
     * @param {string} slug - Slug da notícia
     * @returns {Promise} Resposta da API com dados da notícia
     */
    bySlug: (slug) => api.get(`/news/${slug}`),
    
    /**
     * Lista todas as notícias (sem filtros)
     * @returns {Promise} Resposta da API com todas as notícias
     */
    all: () => api.get('/news/all')
}

/**
 * API de Usuários
 * 
 * Métodos relacionados ao gerenciamento de usuários (apenas Admin)
 */
export const userAPI = {
    /**
     * Lista todos os usuários
     * @returns {Promise} Resposta da API com lista de usuários
     */
    index: () => api.get('/admin/users'),
    
    /**
     * Cria um novo usuário
     * @param {Object} data - Dados do usuário (name, email, password, role, etc)
     * @returns {Promise} Resposta da API com o usuário criado
     */
    create: (data) => api.post('/admin/users', data),
    
    /**
     * Atualiza um usuário existente
     * @param {number} id - ID do usuário
     * @param {Object} data - Dados atualizados do usuário
     * @returns {Promise} Resposta da API com o usuário atualizado
     */
    update: (id, data) => api.put(`/admin/users/${id}`, data),
    
    /**
     * Deleta um usuário
     * @param {number} id - ID do usuário
     * @returns {Promise} Resposta da API
     */
    delete: (id) => api.delete(`/admin/users/${id}`),
    
    /**
     * Obtém detalhes de um usuário
     * @param {number} id - ID do usuário
     * @returns {Promise} Resposta da API com dados do usuário
     */
    show: (id) => api.get(`/admin/users/${id}`),
    
    /**
     * Atualiza o nome do usuário autenticado
     * @param {string} name - Novo nome
     * @returns {Promise} Resposta da API
     */
    updateName: (name) => api.post('/me/name', { name })
}

/**
 * API de Categorias
 * 
 * Métodos relacionados às categorias de notícias
 */
export const categoryAPI = {
    /**
     * Lista todas as categorias
     * @returns {Promise} Resposta da API com lista de categorias
     */
    index: () => api.get('/categories'),
    
    /**
     * Obtém detalhes de uma categoria pelo slug
     * @param {string} slug - Slug da categoria
     * @returns {Promise} Resposta da API com dados da categoria
     */
    show: (slug) => api.get(`/categories/${slug}`)
}

/**
 * API de Solicitações de Promoção
 * 
 * Métodos relacionados às solicitações de promoção de Jornalista para Editor
 */
export const promotionRequestAPI = {
    /**
     * Lista todas as solicitações (Editor/Admin)
     * @returns {Promise} Resposta da API com lista de solicitações
     */
    index: () => api.get('/promotion-requests'),
    
    /**
     * Lista solicitações do usuário logado (Jornalista)
     * @returns {Promise} Resposta da API com lista de solicitações do usuário
     */
    myRequests: () => api.get('/promotion-requests/my'),
    
    /**
     * Cria uma nova solicitação de promoção
     * @param {Object} data - Dados da solicitação (message)
     * @returns {Promise} Resposta da API com a solicitação criada
     */
    create: (data) => api.post('/promotion-requests', data),
    
    /**
     * Obtém detalhes de uma solicitação
     * @param {number} id - ID da solicitação
     * @returns {Promise} Resposta da API com dados da solicitação
     */
    show: (id) => api.get(`/promotion-requests/${id}`),
    
    /**
     * Aprova uma solicitação de promoção
     * @param {number} id - ID da solicitação
     * @returns {Promise} Resposta da API
     */
    approve: (id) => api.patch(`/promotion-requests/${id}/approve`),
    
    /**
     * Rejeita uma solicitação de promoção
     * @param {number} id - ID da solicitação
     * @param {Object} data - Dados da rejeição (rejection_reason)
     * @returns {Promise} Resposta da API
     */
    reject: (id, data) => api.patch(`/promotion-requests/${id}/reject`, data)
}

// Exporta a instância do axios para uso direto se necessário
export default api