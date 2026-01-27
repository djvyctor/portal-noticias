/**
 * Componente Footer - Rodapé da aplicação
 * 
 * Este componente exibe:
 * - Logo e slogan da aplicação
 * - Links de navegação (Sobre, Contato, Privacidade)
 * - Copyright
 * 
 * Layout responsivo que se adapta a diferentes tamanhos de tela
 */

<template>
  <footer class="bg-gray-900 text-white py-8 mt-12 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Conteúdo principal do footer -->
      <div class="flex flex-col md:flex-row justify-between items-center">
        
        <!-- Logo e slogan -->
        <div class="mb-4 md:mb-0">
          <span class="text-2xl font-bold">PN <span class="text-yellow-500">News</span></span>
          <p class="text-sm text-gray-400 mt-1">Jornalismo sério e imparcial.</p>
        </div>

        <!-- Links de navegação -->
        <div class="flex space-x-6 text-sm text-gray-400">
          <a href="#" class="hover:text-yellow-400 transition">Sobre</a>
          <a href="#" class="hover:text-yellow-400 transition">Contato</a>
          <a href="#" class="hover:text-yellow-400 transition">Privacidade</a>
        </div>

      </div>

      <!-- Copyright -->
      <div class="mt-8 text-center text-xs text-gray-500 border-t border-gray-800 pt-8">
        &copy; 2026 Portal de Notícias. Todos os direitos reservados.
      </div>
    </div>
  </footer>
</template>