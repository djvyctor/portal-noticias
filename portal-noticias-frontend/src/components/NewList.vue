<template>
  <section class="max-w-7xl mx-auto px-4 py-12">
    
    <div class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4">
      <div class="flex items-center">
        <span class="w-3 h-8 bg-yellow-400 rounded-sm mr-3"></span>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
          Últimas do Dia
        </h2>
      </div>
      <a href="#" class="text-sm font-semibold text-yellow-600 hover:text-yellow-700 hover:underline flex items-center">
        Ver todas
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 ml-1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
      </a>
    </div>

    <div class="grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
      
      <article
        v-for="news in newsList"
        :key="news.id"
        class="group cursor-pointer flex flex-col h-full"
        @click="openNews(news.id)"
      >
        <div class="relative w-full overflow-hidden rounded-md bg-gray-100 mb-4 aspect-video">
          <span class="absolute top-3 left-3 z-10 bg-yellow-400 text-black text-[10px] font-bold px-2 py-1 uppercase tracking-wide rounded-sm shadow-sm">
            {{ news.category }}
          </span>

          <img
            :src="news.image"
            :alt="news.title"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
          />
        </div>

        <div class="flex flex-col flex-grow">
          <h3 class="text-xl font-bold text-gray-900 leading-snug mb-2 group-hover:text-yellow-600 transition-colors">
            {{ news.title }}
          </h3>

          <div class="flex items-center text-xs text-gray-400 mb-3 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ news.timeAgo }}</span>
            <span class="mx-2">•</span>
            <span>{{ news.readTime }} de leitura</span>
          </div>

          <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4 flex-grow">
            {{ news.summary }}
          </p>
        </div>
      </article>

    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Dados simulados (No futuro virão da sua API Laravel)
const newsList = ref([
  {
    id: 1,
    title: "Banco Central anuncia novas regras para o Pix a partir de fevereiro",
    summary: "Mudanças visam aumentar a segurança das transações noturnas e limitar valores para contas novas. Especialistas analisam o impacto.",
    category: "Economia",
    timeAgo: "Há 2 horas",
    readTime: "3 min",
    image: "https://images.unsplash.com/photo-1580519542036-c47de6196ba5?q=80&w=800&auto=format&fit=crop"
  },
  {
    id: 2,
    title: "Startup brasileira de IA recebe aporte milionário de fundo global",
    summary: "Empresa desenvolveu algoritmo capaz de prever safras agrícolas com 99% de precisão e chamou atenção do Vale do Silício.",
    category: "Tecnologia",
    timeAgo: "Há 4 horas",
    readTime: "5 min",
    image: "https://images.unsplash.com/photo-1555949963-aa79dcee981c?q=80&w=800&auto=format&fit=crop"
  },
  {
    id: 3,
    title: "Final do campeonato estadual terá esquema especial de trânsito",
    summary: "Prefeitura divulgou as rotas alternativas para evitar congestionamentos no entorno do estádio neste domingo.",
    category: "Esportes",
    timeAgo: "Há 6 horas",
    readTime: "2 min",
    image: "https://images.unsplash.com/photo-1522770179533-24471fcdba45?q=80&w=800&auto=format&fit=crop"
  }
])

const openNews = (id) => {
  router.push(`/noticia/${id}`)
}
</script>