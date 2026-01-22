<template>
  <section class="w-full relative group">
    
    <div class="relative w-full h-[500px] overflow-hidden bg-gray-900">
      
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
            :src="item.image" 
            :alt="item.title"
            class="w-full h-full object-cover opacity-90 transition-transform duration-700 hover:scale-105"
          />

          <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

          <div class="absolute bottom-0 left-0 w-full p-8 md:p-12">
            <div class="container mx-auto">
              <span class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-wider text-white uppercase bg-red-700 rounded-sm">
                {{ item.category || 'Destaque' }}
              </span>

              <h2 class="text-3xl md:text-5xl font-bold text-white leading-tight mb-2 max-w-4xl cursor-pointer hover:underline decoration-red-600 decoration-4 underline-offset-4">
                {{ item.title }}
              </h2>

              <p class="hidden md:block text-gray-300 text-lg mt-2 max-w-2xl font-light">
                {{ item.description }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <button 
        @click="prev"
        class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white p-3 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 transform -translate-x-4 group-hover:translate-x-0"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
      </button>

      <button 
        @click="next"
        class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white p-3 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-4 group-hover:translate-x-0"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
      </button>

      <div class="absolute bottom-8 right-8 flex space-x-2">
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

const currentIndex = ref(0)
let timer = null

const highlights = ref([
  {
    category: "Economia",
    title: "Mercado financeiro reage positivamente às novas medidas fiscais",
    description: "Bolsa fecha em alta e dólar recua após anúncio do Banco Central nesta tarde.",
    image: "https://images.unsplash.com/photo-1611974765270-ca1258634369?q=80&w=1920&auto=format&fit=crop"
  },
  {
    category: "Tecnologia",
    title: "Inteligência Artificial: O que muda no mercado de trabalho em 2026?",
    description: "Especialistas discutem o futuro das profissões com o avanço acelerado da IA.",
    image: "https://images.unsplash.com/photo-1485827404703-89b55fcc595e?q=80&w=1920&auto=format&fit=crop"
  },
  {
    category: "Esporte",
    title: "Final histórica: Time local vence campeonato nos últimos minutos",
    description: "Estádio lotado presenciou uma virada inesquecível neste domingo.",
    image: "https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=1920&auto=format&fit=crop"
  }
])

const next = () => {
  currentIndex.value = (currentIndex.value + 1) % highlights.value.length
}

const prev = () => {
  currentIndex.value = (currentIndex.value - 1 + highlights.value.length) % highlights.value.length
}

// Auto-play opcional (padrão em portais)
const startSlide = () => {
  timer = setInterval(next, 5000)
}

const stopSlide = () => {
  clearInterval(timer)
}

onMounted(() => startSlide())
onUnmounted(() => stopSlide())
</script>