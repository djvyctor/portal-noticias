<template>
  <section class="w-full bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-6">
      
      <!-- Container do carrossel -->
      <div class="relative overflow-hidden rounded-lg shadow-lg">

        <!-- Slides -->
        <div
          class="flex transition-transform duration-500"
          :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
        >
          <div
            v-for="(item, index) in highlights"
            :key="index"
            class="min-w-full h-72 md:h-96 relative"
          >
            <!-- Imagem -->
            <img
              :src="item.image"
              :alt="item.title"
              class="w-full h-full object-cover"
            />

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-end">
              <div class="p-6">
                <span class="text-yellow-400 text-sm font-semibold uppercase">
                  Destaque
                </span>
                <h2 class="text-white text-2xl md:text-3xl font-bold mt-1">
                  {{ item.title }}
                </h2>
                <p class="text-gray-200 mt-2 hidden md:block">
                  {{ item.description }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Botões -->
        <button
          @click="prev"
          class="absolute left-3 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-2 rounded-full hover:bg-opacity-70"
        >
          ‹
        </button>

        <button
          @click="next"
          class="absolute right-3 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white px-3 py-2 rounded-full hover:bg-opacity-70"
        >
          ›
        </button>

      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "Carrossel",
  data() {
    return {
      currentIndex: 0,
      highlights: [
        {
          title: "Mercado financeiro reage a novas medidas",
          description: "Entenda o impacto das decisões econômicas desta semana.",
          image: "https://via.placeholder.com/1200x600"
        },
        {
          title: "Tecnologia: IA domina debates globais",
          description: "Especialistas discutem o futuro da inteligência artificial.",
          image: "https://via.placeholder.com/1200x600"
        },
        {
          title: "Esportes: final histórica emociona torcida",
          description: "Jogo decisivo entra para a história do campeonato.",
          image: "https://via.placeholder.com/1200x600"
        }
      ]
    }
  },
  methods: {
    next() {
      this.currentIndex =
        (this.currentIndex + 1) % this.highlights.length
    },
    prev() {
      this.currentIndex =
        (this.currentIndex - 1 + this.highlights.length) %
        this.highlights.length
    }
  }
}
</script>
