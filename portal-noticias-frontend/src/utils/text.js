/**
 * Utilitários para manipulação de texto
 * 
 * Funções reutilizáveis para processar e formatar textos,
 * especialmente conteúdo HTML de notícias.
 */

/**
 * Extrai um resumo do conteúdo HTML
 * Remove tags HTML e limita o tamanho do texto
 * 
 * @param {string} content - Conteúdo HTML
 * @param {number} maxLength - Tamanho máximo do resumo (padrão: 150 caracteres)
 * @returns {string} Resumo do conteúdo sem tags HTML
 */
export const getExcerpt = (content, maxLength = 150) => {
  if (!content) return ''
  const text = content.replace(/<[^>]*>/g, '') // Remove todas as tags HTML
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

/**
 * Remove todas as tags HTML de uma string
 * 
 * @param {string} html - String com HTML
 * @returns {string} Texto sem tags HTML
 */
export const stripHtml = (html) => {
  if (!html || html === '') return ''
  const tmp = document.createElement('div')
  tmp.innerHTML = String(html)
  return tmp.textContent || tmp.innerText || ''
}

/**
 * Valida o formato de email usando regex
 * 
 * @param {string} email - Email a ser validado
 * @returns {boolean} True se o email for válido
 */
export const validateEmail = (email) => {
  if (!email) return false
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Capitaliza a primeira letra de cada palavra
 * 
 * @param {string} text - Texto a ser capitalizado
 * @returns {string} Texto capitalizado
 */
export const capitalizeWords = (text) => {
  if (!text) return ''
  const words = text.split(' ')
  const capitalizedWords = words.map(word => {
    if (word.length > 0) {
      return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
    }
    return word
  })
  return capitalizedWords.join(' ')
}
