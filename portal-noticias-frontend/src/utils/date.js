/**
 * Utilitários para formatação de datas
 * 
 * Funções reutilizáveis para formatar datas em diferentes formatos
 * usados em toda a aplicação.
 */

/**
 * Formata a data para exibição em formato brasileiro (DD/MM/YYYY)
 * 
 * @param {string|Date} dateString - Data em formato ISO string ou objeto Date
 * @returns {string} Data formatada (ex: "26/01/2026") ou string vazia se inválida
 */
export const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return ''
  return date.toLocaleDateString('pt-BR')
}

/**
 * Formata data e hora para exibição em formato brasileiro
 * 
 * @param {string|Date} dateString - Data em formato ISO string ou objeto Date
 * @returns {string} Data e hora formatadas (ex: "26/01/2026, 14:30") ou '—' se inválida
 */
export const formatDateTime = (dateString) => {
  if (!dateString) return '—'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return '—'
  return date.toLocaleString('pt-BR')
}

/**
 * Formata a data para exibição completa em português
 * Exemplo: "26 de janeiro de 2026"
 * 
 * @param {string|Date} dateString - Data em formato ISO string ou objeto Date
 * @returns {string} Data formatada ou string vazia se inválida
 */
export const formatDateFull = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return ''
  return date.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

/**
 * Formata o horário para exibição
 * Exemplo: "14:30"
 * 
 * @param {string|Date} dateString - Data em formato ISO string ou objeto Date
 * @returns {string} Horário formatado ou string vazia se inválida
 */
export const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return ''
  return date.toLocaleTimeString('pt-BR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

/**
 * Formata a data para exibição relativa (ex: "Há 2 horas", "Há 3 dias")
 * Se a data for muito antiga (> 7 dias), exibe a data formatada em pt-BR
 * 
 * @param {string|Date} dateString - Data em formato ISO string ou objeto Date
 * @returns {string} Data formatada para exibição relativa
 */
export const formatDateRelative = (dateString) => {
  if (!dateString) return ''
  
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return ''
  
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
  
  // Menos de 1 hora
  if (diffInHours < 1) return 'Há menos de 1 hora'
  
  // Menos de 24 horas
  if (diffInHours < 24) {
    return `Há ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`
  }
  
  // Menos de 7 dias
  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 7) {
    return `Há ${diffInDays} dia${diffInDays > 1 ? 's' : ''}`
  }
  
  // Mais de 7 dias - exibe data formatada
  return date.toLocaleDateString('pt-BR')
}
