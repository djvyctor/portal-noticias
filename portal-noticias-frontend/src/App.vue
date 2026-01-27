/**
 * Componente raiz da aplicação
 * 
 * Este componente serve como container principal que renderiza
 * as diferentes views através do router-view baseado na rota atual.
 * 
 * O router-view é substituído dinamicamente pelo componente
 * correspondente à rota ativa definida no router/index.js
 */

<script setup>
// App.vue é apenas o container das rotas
// Não há lógica adicional necessária aqui
</script>

<template>
  <!-- Renderiza o componente correspondente à rota atual -->
  <router-view />
</template>

<style scoped></style>

