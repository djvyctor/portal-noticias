<template>
  <header class="sticky top-0 z-50 bg-white border-b-4 border-yellow-400 shadow-sm font-sans">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        
        <div class="flex items-center gap-4">
          <button 
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="md:hidden p-2 rounded-md text-gray-600 hover:text-black hover:bg-gray-100 focus:outline-none"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <div class="flex-shrink-0 flex items-center cursor-pointer" @click="$router.push('/')">
            <div class="w-8 h-8 bg-yellow-400 rounded flex items-center justify-center mr-2">
              <span class="text-white font-black text-lg">P</span>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-gray-900">
              PN <span class="text-yellow-500">News</span>
            </h1>
          </div>
        </div>

        <nav class="hidden md:flex space-x-8">
          <a 
            v-for="topic in topics" 
            :key="topic"
            href="#"
            class="text-gray-600 hover:text-yellow-600 font-bold text-sm uppercase tracking-wide transition-colors duration-200 border-b-2 border-transparent hover:border-yellow-400 py-1"
          >
            {{ topic }}
          </a>
        </nav>

        <div class="flex items-center gap-4">
          <div class="hidden lg:flex items-center bg-gray-100 rounded-full px-3 py-1.5 border border-transparent focus-within:border-yellow-400 focus-within:bg-white transition-all">
            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
              type="text" 
              placeholder="Buscar..." 
              class="bg-transparent border-none focus:ring-0 text-sm ml-2 w-32 text-gray-700 placeholder-gray-400"
            />
          </div>

          <button class="lg:hidden text-gray-500 hover:text-black">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>

          <div class="h-6 w-px bg-gray-300 mx-1 hidden sm:block"></div>

          <button
            class="flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-yellow-600 transition-colors"
            @click="goToLogin"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="hidden sm:inline">Entrar</span>
          </button>
        </div>
      </div>
    </div>

    <div v-show="isMobileMenuOpen" class="md:hidden border-t border-gray-100 bg-gray-50">
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <a
          v-for="topic in topics"
          :key="topic"
          href="#"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-black hover:bg-yellow-100 transition"
        >
          {{ topic }}
        </a>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const isMobileMenuOpen = ref(false)

const topics = ref([
  "Política",
  "Economia",
  "Esportes",
  "Tecnologia",
  "Mundo"
])

const goToLogin = () => {
  router.push("/login")
}
</script>