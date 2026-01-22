<template>
  <header class="bg-yellow-400 shadow-md">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex items-center justify-between h-16">
        
        <div class="flex items-center">
          <h1 class="text-2xl font-bold text-black cursor-pointer">
            PN News
          </h1>
        </div>

        <!-- Tópicos -->
        <nav class="hidden md:flex space-x-6">
          <button
            v-for="topic in topics"
            :key="topic"
            class="text-black font-medium hover:underline"
          >
            {{ topic }}
          </button>
        </nav>

        <!-- Login -->
        <div>
          <button
            class="bg-black text-yellow-400 px-4 py-2 rounded-md font-semibold hover:bg-gray-800 transition"
            @click="goToLogin"
          >
            Login
          </button>
        </div>

      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: "Header",
  data() {
    return {
      topics: [
        "Esportes",
        "Política",
        "Tecnologia",
      ]
    }
  },
  methods: {
    goToLogin() {
      this.$router.push("/login")
    }
  }
}
</script>
