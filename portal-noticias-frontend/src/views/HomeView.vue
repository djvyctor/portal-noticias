<script setup>
// Importando as peças do nosso Lego (verifique se os nomes dos arquivos batem com a sua pasta)
import Header from '../components/Header.vue'
import Carousel from '../components/Carousel.vue' // Você chamou de Carolseu.vue antes, sugiro renomear para Carousel.vue
import NewList from '../components/NewList.vue'
</script>

<template>
  <div class="min-h-screen bg-gray-50 font-sans">
    
    <Header />

    <main>
      <section>
        <Carousel />
      </section>

      <div class="pt-8 pb-16">
        <NewList />
      </div>
    </main>

    <div class="max-w-7xl mx-auto px-4 text-center">
      <p class="text-sm text-gray-400">© 2026 PN News - Todos os direitos reservados.</p>
    </div>

  </div>
</template>