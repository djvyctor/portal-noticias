/**
 * Configuração de rotas da aplicação
 * 
 * Este arquivo define todas as rotas da aplicação e os guards de navegação
 * que controlam o acesso baseado em autenticação e permissões de usuário.
 * 
 * Guards de navegação:
 * - requireAuth: Exige que o usuário esteja autenticado
 * - requireEditorOrAdmin: Exige permissão de editor ou admin
 * - requireAdmin: Exige permissão de administrador
 */

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

/**
 * Guard de autenticação - verifica se o usuário está logado
 * 
 * @param {Object} to - Rota de destino
 * @param {Object} from - Rota de origem
 * @param {Function} next - Função para continuar a navegação
 * 
 * Redireciona para /login se não houver token de autenticação
 */
const requireAuth = (to, from, next) => {
  const token = localStorage.getItem('token')
  if (!token) {
    next('/login')
  } else {
    next()
  }
}

/**
 * Guard para Editor/Admin - verifica se o usuário tem permissão de editor ou admin
 * 
 * @param {Object} to - Rota de destino
 * @param {Object} from - Rota de origem
 * @param {Function} next - Função para continuar a navegação
 * 
 * Redireciona para /login se não autenticado ou para /painel se não tiver permissão
 */
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

/**
 * Guard apenas para Admin - verifica se o usuário é administrador
 * 
 * @param {Object} to - Rota de destino
 * @param {Object} from - Rota de origem
 * @param {Function} next - Função para continuar a navegação
 * 
 * Redireciona para /login se não autenticado ou para /painel se não for admin
 */
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

/**
 * Definição de todas as rotas da aplicação
 * 
 * Rotas públicas: Home, detalhes de notícia, categorias, busca
 * Rotas de autenticação: Login, registro, recuperação de senha
 * Rotas do painel: Requerem autenticação e podem ter restrições de permissão
 */
const routes = [
  // ========== ROTAS PÚBLICAS ==========
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
  
  // ========== ROTAS DE AUTENTICAÇÃO ==========
  {
    path: "/login",
    name: "login",
    component: Login,
    meta: { hideLayout: true } // Esconde o layout padrão (Header/Footer)
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
  
  // ========== ROTAS DO PAINEL (Requerem autenticação) ==========
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
    beforeEnter: requireEditorOrAdmin // Apenas Editor ou Admin
  },
  {
    path: "/painel/noticias-pendentes",
    name: "noticias-pendentes",
    component: NoticiasPendentesView,
    beforeEnter: requireEditorOrAdmin // Apenas Editor ou Admin
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
    beforeEnter: requireEditorOrAdmin // Editor e Admin podem acessar
  },
  {
    path: "/painel/alterar-senha",
    name: "change-password",
    component: ChangePasswordView,
    beforeEnter: requireAuth
  }
]

/**
 * Cria e exporta a instância do router
 * 
 * Usa createWebHistory para URLs limpas (sem hash)
 * Exemplo: /noticia/titulo-da-noticia ao invés de /#/noticia/titulo-da-noticia
 */
export default createRouter({
  history: createWebHistory(),
  routes
})