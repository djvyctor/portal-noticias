/**
 * Arquivo principal de inicialização da aplicação Vue.js
 * 
 * Este arquivo é o ponto de entrada da aplicação. Ele:
 * - Cria a instância do Vue
 * - Configura o Pinia para gerenciamento de estado
 * - Configura o Vue Router para navegação
 * - Importa os estilos globais
 * - Monta a aplicação no elemento #app do HTML
 */

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import './assets/main.css'

// Cria a instância da aplicação Vue
const app = createApp(App)

// Configura o Pinia para gerenciamento de estado global
app.use(createPinia())

// Configura o Vue Router para navegação entre páginas
app.use(router)

// Monta a aplicação no elemento com id "app" do HTML
app.mount('#app')
