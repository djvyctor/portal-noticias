import { createRouter, createWebHistory } from "vue-router"
import Login from "@/views/Login.vue"
import HomeView from "@/views/HomeView.vue"
import NewsDetail from "@/views/NewsDetail.vue"
import CategoryView from "@/views/CategoryView.vue"
import AllNewsView from "@/views/AllNewsView.vue"
import SearchView from "@/views/SearchView.vue"
import PainelView from "@/views/PainelView.vue"
import MinhasNoticiasView from "@/views/painel/MinhasNoticiasView.vue"
import CriarNoticiaView from "@/views/painel/CriarNoticiaView.vue"
import EditarNoticiaView from "@/views/painel/EditarNoticiaView.vue"
import TodasNoticiasView from "@/views/painel/TodasNoticiasView.vue"

// Guard de autenticação - verifica se o usuário está logado
const requireAuth = (to, from, next) => {
  const token = localStorage.getItem('token')
  if (!token) {
    next('/login')
  } else {
    next()
  }
}

// Guard para Editor/Admin
const requireEditorOrAdmin = (to, from, next) => {
  const token = localStorage.getItem('token')
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  
  if (!token) {
    next('/login')
  } else if (user.role === 'editor' || user.role === 'admin') {
    next()
  } else {
    alert('Você não tem permissão para acessar esta página')
    next('/painel')
  }
}

const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView
  },
  {
    path: "/noticia/:slug",
    name: "news-detail",
    component: NewsDetail
  },
  {
    path: "/categoria/:slug",
    name: "category",
    component: CategoryView
  },
  {
    path: "/todas-noticias",
    name: "all-news",
    component: AllNewsView
  },
  {
    path: "/busca",
    name: "search",
    component: SearchView
  },
  {
    path: "/login",
    name: "login",
    component: Login,
    meta: { hideLayout: true }
  },
  {
    path: "/painel",
    name: "painel",
    component: PainelView,
    beforeEnter: requireAuth
  },
  {
    path: "/painel/noticias",
    name: "minhas-noticias",
    component: MinhasNoticiasView,
    beforeEnter: requireAuth
  },
  {
    path: "/painel/noticias/criar",
    name: "criar-noticia",
    component: CriarNoticiaView,
    beforeEnter: requireAuth
  },
  {
    path: "/painel/noticias/editar/:id",
    name: "editar-noticia",
    component: EditarNoticiaView,
    beforeEnter: requireAuth
  },
  {
    path: "/painel/moderacao",
    name: "moderacao",
    component: TodasNoticiasView,
    beforeEnter: requireEditorOrAdmin
  }
]

export default createRouter({
  history: createWebHistory(),
  routes
})