<template>
  <section class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- Título da seção -->
    <h2 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-yellow-400 pl-3">
      Notícias em destaque
    </h2>

    <!-- Grid de notícias -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      
      <article
        v-for="news in newsList"
        :key="news.id"
        class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden cursor-pointer"
        @click="openNews(news.id)"
      >
        <!-- Imagem -->
        <img
          :src="news.image"
          :alt="news.title"
          class="w-full h-40 object-cover"
        />

        <!-- Conteúdo -->
        <div class="p-4">
          <span class="text-yellow-500 text-xs font-semibold uppercase">
            {{ news.category }}
          </span>

          <h3 class="text-gray-900 font-bold text-lg mt-1 leading-snug">
            {{ news.title }}
          </h3>

          <p class="text-gray-600 text-sm mt-2 line-clamp-3">
            {{ news.summary }}
          </p>
        </div>
      </article>

    </div>
  </section>
</template>

<script>
export default {
  name: "NewsList",
  data() {
    return {
      newsList: [
        {
          id: 1,
          title: "Economia brasileira mostra sinais de recuperação",
          summary: "Indicadores apontam crescimento gradual nos últimos meses.",
          category: "Economia",
          image: "https://via.placeholder.com/400x300"
        },
        {
          id: 2,
          title: "Nova tecnologia promete revolucionar o mercado",
          summary: "Startups apostam em soluções baseadas em inteligência artificial.",
          category: "Tecnologia",
          image: "https://via.placeholder.com/400x300"
        },
        {
          id: 3,
          title: "Clássico do futebol termina em empate eletrizante",
          summary: "Torcedores lotam estádio em partida histórica.",
          category: "Esportes",
          image: "https://via.placeholder.com/400x300"
        }
      ]
    }
  },
  methods: {
    openNews(id) {
      this.$router.push(`/noticia/${id}`)
    }
  }
}
</script>
