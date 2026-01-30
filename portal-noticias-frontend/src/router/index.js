import { createRouter, createWebHistory } from "vue-router"
import Login from "@/views/Login.vue"
import RegisterView from "@/views/RegisterView.vue"
import ForgotPasswordView from "@/views/ForgotPasswordView.vue"
import ResetPasswordView from "@/views/ResetPasswordView.vue"
import HomeView from "@/views/HomeView.vue"
import NewsDetail from "@/views/NewsDetail.vue"
import CategoryView from "@/views/CategoryView.vue"
import AllNewsView from "@/views/AllNewsView.vue"
import DestaquesView from "@/views/DestaquesView.vue"
import NoticiasView from "@/views/NoticiasView.vue"
import SearchView from "@/views/SearchView.vue"
import PainelView from "@/views/PainelView.vue"
import MinhasNoticiasView from "@/views/painel/MinhasNoticiasView.vue"
import CriarNoticiaView from "@/views/painel/CriarNoticiaView.vue"
import EditarNoticiaView from "@/views/painel/EditarNoticiaView.vue"
import TodasNoticiasView from "@/views/painel/TodasNoticiasView.vue"
import ChangePasswordView from "@/views/painel/ChangePasswordView.vue"
import GerenciarUsuariosView from "@/views/painel/GerenciarUsuariosView.vue"
import NoticiasPendentesView from "@/views/painel/NoticiasPendentesView.vue"
import NoticiasRejeitadasView from "@/views/painel/NoticiasRejeitadasView.vue"
import SolicitarPromocaoView from "@/views/painel/SolicitarPromocaoView.vue"
import MinhasSolicitacoesView from "@/views/painel/MinhasSolicitacoesView.vue"
import SolicitacoesPromocaoView from "@/views/painel/SolicitacoesPromocaoView.vue"

const requireAuth = (to, from, next) => {
  const token = localStorage.getItem('token')
  if (!token) {
    next('/login')
  } else {
    next()
  }
}

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

const requireAdmin = (to, from, next) => {
  const token = localStorage.getItem('token')
  const user = JSON.parse(localStorage.getItem('user') || '{}')

  if (!token) {
    next('/login')
  } else if (user.role === 'admin') {
    next()
  } else {
    alert('Apenas administradores podem acessar esta página')
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
    path: "/destaques",
    name: "destaques",
    component: DestaquesView
  },
  {
    path: "/noticias",
    name: "noticias",
    component: NoticiasView
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
    path: "/registro",
    name: "register",
    component: RegisterView,
    meta: { hideLayout: true }
  },
  {
    path: "/esqueci-senha",
    name: "forgot-password",
    component: ForgotPasswordView,
    meta: { hideLayout: true }
  },
  {
    path: "/resetar-senha",
    name: "reset-password",
    component: ResetPasswordView,
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
  },
  {
    path: "/painel/noticias-pendentes",
    name: "noticias-pendentes",
    component: NoticiasPendentesView,
    beforeEnter: requireEditorOrAdmin
  },
  {
    path: "/painel/noticias-rejeitadas",
    name: "noticias-rejeitadas",
    component: NoticiasRejeitadasView,
    beforeEnter: requireAuth
  },
  {
    path: "/painel/usuarios",
    name: "gerenciar-usuarios",
    component: GerenciarUsuariosView,
    beforeEnter: requireEditorOrAdmin
  },
  {
    path: "/painel/alterar-senha",
    name: "change-password",
    component: ChangePasswordView,
    beforeEnter: requireAuth
  },
  {
    path: "/painel/solicitar-promocao",
    name: "solicitar-promocao",
    component: SolicitarPromocaoView,
    beforeEnter: requireAuth
  },
  {
    path: "/painel/minhas-solicitacoes",
    name: "minhas-solicitacoes",
    component: MinhasSolicitacoesView,
    beforeEnter: requireAuth
  },
  {
    path: "/painel/solicitacoes-promocao",
    name: "solicitacoes-promocao",
    component: SolicitacoesPromocaoView,
    beforeEnter: requireEditorOrAdmin
  }
]

export default createRouter({
  history: createWebHistory(),
  routes
})
