<template>
  <!-- Carrossel de Notícias em Destaque -->
  <section class="w-full relative group">
    <!-- Loading -->
    <div v-if="loading" class="w-full h-[500px] bg-gray-100 flex items-center justify-center">
      <div class="text-center">
        <p class="text-gray-500">Carregando destaques...</p>
      </div>
    </div>
    
    <div v-else-if="highlights.length === 0" class="w-full h-[500px] bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300">
      <div class="text-center max-w-md px-4">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-gray-500 mb-2 font-semibold">Nenhuma notícia em destaque no momento</p>
        <p class="text-sm text-gray-400 mb-4">Para destacar uma notícia:</p>
        <ol class="text-sm text-gray-500 text-left inline-block list-decimal list-inside space-y-1">
          <li>Faça login como Admin</li>
          <li>Vá em "Moderação"</li>
          <li>Encontre uma notícia publicada</li>
          <li>Clique em "☆ Destacar"</li>
        </ol>
      </div>
    </div>
    
    <!-- Carrossel com notícias -->
    <div v-else class="relative w-full h-[500px] overflow-hidden bg-gray-900" @mouseenter="stopSlide" @mouseleave="startSlide">
      
      <div 
        class="flex w-full h-full transition-transform duration-700 ease-out"
        :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
      >
        <div 
          v-for="(item, index) in highlights" 
          :key="index"
          class="min-w-full w-full h-full relative"
        >
          <img 
            v-if="item.image_path"
            :src="`http://portal-noticias-backend.test/storage/${item.image_path}`" 
            :alt="item.title"
            class="w-full h-full object-cover opacity-90 transition-transform duration-700 hover:scale-105"
          />
          <div 
            v-else
            class="w-full h-full bg-gray-800 flex items-center justify-center"
          >
            <span class="text-gray-400 text-lg">Sem imagem</span>
          </div>

          <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

          <div class="absolute bottom-0 left-0 w-full p-8 md:p-12">
            <div class="container mx-auto">
              <span class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-wider text-white uppercase bg-red-700 rounded-sm">
                {{ item.category?.name || 'Destaque' }}
              </span>

              <h2 
                @click="openNews(item.slug)"
                class="text-3xl md:text-5xl font-bold text-white leading-tight mb-2 max-w-4xl cursor-pointer hover:underline decoration-red-600 decoration-4 underline-offset-4"
              >
                {{ item.title }}
              </h2>

              <p class="hidden md:block text-gray-300 text-lg mt-2 max-w-2xl font-light">
                {{ getExcerpt(item.content) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <button 
        v-if="highlights.length > 1"
        @click="prev"
        type="button"
        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-3 rounded-full z-10 transition-all duration-300"
        title="Anterior"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
      </button>

      <button 
        v-if="highlights.length > 1"
        @click="next"
        type="button"
        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-3 rounded-full z-10 transition-all duration-300"
        title="Próximo"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
      </button>

      <div v-if="highlights.length > 0" class="absolute bottom-8 right-8 flex space-x-2">
        <button
          v-for="(_, index) in highlights"
          :key="index"
          @click="currentIndex = index"
          class="h-1 transition-all duration-300 rounded-full"
          :class="currentIndex === index ? 'w-8 bg-red-600' : 'w-4 bg-white/50 hover:bg-white'"
        ></button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()
const currentIndex = ref(0)
let timer = null
const highlights = ref([])
const loading = ref(true)

const fetchFeaturedNews = async () => {
  loading.value = true
  try {
    const response = await api.get('/news/carousel')
    const res = response.data
    highlights.value = res.data ?? (Array.isArray(res) ? res : [])
    if (highlights.value.length > 0) startSlide()
  } catch (err) {
    highlights.value = []
  } finally {
    loading.value = false
  }
}

const next = () => {
  if (highlights.value.length === 0) return
  currentIndex.value = (currentIndex.value + 1) % highlights.value.length
}

const prev = () => {
  if (highlights.value.length === 0) return
  currentIndex.value = (currentIndex.value - 1 + highlights.value.length) % highlights.value.length
}

const startSlide = () => {
  stopSlide()
  if (highlights.value.length <= 1) return
  timer = setInterval(next, 5000)
}

const stopSlide = () => {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

onMounted(() => {
  fetchFeaturedNews()
})

onUnmounted(() => stopSlide())

const getExcerpt = (content, maxLength = 200) => {
  if (!content) return ''
  const text = content.replace(/<[^>]*>/g, '') // Remove HTML tags
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

const openNews = (slug) => {
  router.push(`/noticia/${slug}`)
}
</script>