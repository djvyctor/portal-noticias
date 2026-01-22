/** @type {import("tailwindcss").Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    // A propriedade 'container' aqui configura a classe .container globalmente
    container: {
      center: true, // Centraliza automaticamente (igual Bootstrap)
      padding: '1rem', // Adiciona um respiro nas laterais no mobile
      screens: {
        sm: '600px',
        md: '728px',
        lg: '984px',
        xl: '1240px', // Limita a largura máxima para não ficar muito esticado em telas gigantes
      },
    },
    extend: {
      // Aqui estendemos as configurações padrão
      fontFamily: {
        // Define uma fonte padrão mais bonita se você importar no index.html (ex: Inter ou Roboto)
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      // Dica: Crie uma cor "brand" (marca). 
      // Se amanhã você quiser mudar o amarelo do site todo para vermelho, muda só aqui.
      colors: {
        brand: {
          light: '#FEF08A', // yellow-200
          DEFAULT: '#FACC15', // yellow-400 (Sua cor principal atual)
          dark: '#EAB308',    // yellow-500
        }
      }
    },
  },
  plugins: [
    // Se quiser inputs de formulário mais bonitos (para o Login),
    // rode: npm install -D @tailwindcss/forms
    // e descomente a linha abaixo:
    // require('@tailwindcss/forms'),
  ],
}