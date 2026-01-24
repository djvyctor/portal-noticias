import axios from 'axios'

const api = axios.create({
    baseURL: 'http://portal-noticias-backend.test/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})

// Interceptor para adicionar token em todas as requisições
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

// Interceptor para tratar erros de autenticação
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

// Métodos de API
export const authAPI = {
    login: (email, password) => api.post('/login', { email, password }),
    register: (name, email, password, password_confirmation) => api.post('/register', { 
        name, 
        email, 
        password,
        password_confirmation
    }),
    logout: () => api.post('/logout'),
    me: () => api.get('/user'),
    changePassword: (currentPassword, password, password_confirmation) => api.post('/change-password', {
        current_password: currentPassword,
        password,
        password_confirmation
    }),
    forgotPassword: (email) => api.post('/forgot-password', { email }),
    resetPassword: (email, token, password, password_confirmation) => api.post('/reset-password', {
        email,
        token,
        password,
        password_confirmation
    })
}

export const newsAPI = {
    // Notícias do usuário
    list: () => api.get('/user/news'),
    create: (data) => api.post('/user/news', data),
    update: (id, data) => api.put(`/user/news/${id}`, data),
    delete: (id) => api.delete(`/user/news/${id}`),
    show: (id) => api.get(`/user/news/${id}`),

    // Gerenciamento de notícias (Admin/Editor)
    approve: (id) => api.patch(`/news/${id}/approve`),
    reject: (id, rejectionReason) => api.patch(`/news/${id}/reject`, { rejection_reason: rejectionReason }),
    feature: (id) => api.patch(`/news/${id}/feature`),
    pending: (page = 1) => api.get('/news/pending', { params: { page } }),
    rejected: (userId = null) => userId ? api.get(`/news/rejected/${userId}`) : api.get('/news/rejected'),

    // Notícias públicas
    public: () => api.get('/news/public'),
    featured: () => api.get('/news/featured'),
    carousel: () => api.get('/news/carousel'),
    daily: () => api.get('/news/daily'),
    search: (query) => api.get('/news/search', { params: { q: query } }),
    byCategory: (slug) => api.get(`/news/category/${slug}`),
    bySlug: (slug) => api.get(`/news/${slug}`),
    all: () => api.get('/news/all')
}

export const userAPI = {
    index: () => api.get('/admin/users'),
    create: (data) => api.post('/admin/users', data),
    update: (id, data) => api.put(`/admin/users/${id}`, data),
    delete: (id) => api.delete(`/admin/users/${id}`),
    show: (id) => api.get(`/admin/users/${id}`),
    updateName: (name) => api.post('/me/name', { name })
}

export const categoryAPI = {
    index: () => api.get('/categories'),
    show: (slug) => api.get(`/categories/${slug}`)
}

export default api