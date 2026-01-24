<script setup>
import Header from '../components/Header.vue'
import Carousel from '../components/Carousel.vue'
import FeaturedNews from '../components/FeaturedNews.vue'
import NewList from '../components/NewList.vue'
</script>

<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    
    <Header />

    <main>
      <!-- Carrossel (máximo 5 notícias destacadas) -->
      <section>
        <Carousel />
      </section>

      <!-- Notícias em Destaque (todas as notícias com destaque) -->
      <FeaturedNews />

      <!-- Notícias do Dia (notícias normais, sem destaque) -->
      <div class="pt-8 pb-16">
        <NewList />
      </div>
    </main>

    <div class="max-w-7xl mx-auto px-4 text-center py-8">
      <p class="text-sm text-gray-400">© 2026 PN News - Todos os direitos reservados.</p>
    </div>

  </div>
</template>