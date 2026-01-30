export const getExcerpt = (content, maxLength = 150) => {
  if (!content) return ''
  const text = content.replace(/<[^>]*>/g, '')
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

export const stripHtml = (html) => {
  if (!html || html === '') return ''
  const tmp = document.createElement('div')
  tmp.innerHTML = String(html)
  return tmp.textContent || tmp.innerText || ''
}

export const validateEmail = (email) => {
  if (!email) return false
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

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
