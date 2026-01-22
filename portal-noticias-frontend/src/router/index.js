import { createRouter, createWebHistory } from "vue-router"
import Login from "@/views/Login.vue"
import HomeView from "@/views/HomeView.vue" // <--- 1. Importe a Home

const routes = [
  {
    path: "/",      // <--- 2. Defina que o caminho raiz...
    name: "home",
    component: HomeView // ...carrega a HomeView
  },
  {
    path: "/login",
    name: "login",
    component: Login,
    meta: { hideLayout: true } // (Opcional) Útil se usar a lógica de esconder o Header no App.vue
  }
]

export default createRouter({
  history: createWebHistory(),
  routes
})