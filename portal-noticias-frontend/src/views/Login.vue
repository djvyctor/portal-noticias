<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">

      <h1 class="text-2xl font-bold text-center text-gray-900 mb-6">
        Entrar no <span class="text-yellow-500">PN News</span>
      </h1>

      <div
        v-if="error"
        class="bg-red-100 text-red-700 text-sm p-3 rounded mb-4"
      >
        {{ error }}
      </div>

      <form @submit.prevent="login">
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-1">
            E-mail
          </label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-yellow-400"
            placeholder="seu@email.com"
          />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 text-sm font-medium mb-1">
            Senha
          </label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-yellow-400"
            placeholder="••••••••"
          />
        </div>

        <button
          type="submit"
          class="w-full bg-yellow-400 text-black font-semibold py-2 rounded-md hover:bg-yellow-500 transition"
          :disabled="loading"
        >
          <span v-if="!loading">Entrar</span>
          <span v-else>Entrando...</span>
        </button>
      </form>

      <p class="text-center text-sm text-gray-500 mt-6">
        © {{ new Date().getFullYear() }} PN News
      </p>

    </div>
  </div>
</template>

<script>

import api from "@/services/api"

export default {
  name: "Login",
  data() {
    return {
      form: {
        email: "",
        password: ""
      },
      loading: false,
      error: null
    }
  },
  methods: {
    async login() {
      this.loading = true
      this.error = null

      try {
        const response = await api.post("api/login", this.form)

        console.log(response.data)

        localStorage.setItem("token", response.data.token)
        this.$router.push("/")
      } catch (err) {
        this.error =
          err.response?.data?.message ||
          "E-mail ou senha inválidos."
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped></style>
