/**
 * View HomeView - Página inicial do portal de notícias
 * 
 * Esta é a página principal que exibe:
 * - Carrossel com notícias em destaque (máximo 5)
 * - Grid de notícias em destaque (todas as notícias marcadas como destaque)
 * - Lista de notícias do dia (notícias normais publicadas recentemente)
 * 
 * Layout:
 * - Header fixo no topo
 * - Seções de conteúdo organizadas verticalmente
 * - Footer simples no final
 */

<script setup>
import Header from '../components/Header.vue'
import Carousel from '../components/Carousel.vue'
import FeaturedNews from '../components/FeaturedNews.vue'
import NewList from '../components/NewList.vue'
</script>

<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    <!-- Cabeçalho da aplicação -->
    <Header />

    <main>
      <!-- Carrossel com notícias principais em destaque (máximo 5 notícias) -->
      <section>
        <Carousel />
      </section>

      <!-- Grid de notícias em destaque (todas as notícias marcadas como destaque) -->
      <FeaturedNews />

      <!-- Lista de notícias do dia (notícias normais, sem destaque) -->
      <div class="pt-8 pb-16">
        <NewList />
      </div>
    </main>

    <!-- Footer simples -->
    <div class="max-w-7xl mx-auto px-4 text-center py-8">
      <p class="text-sm text-gray-400">© 2026 PN News - Todos os direitos reservados.</p>
    </div>
  </div>
</template>