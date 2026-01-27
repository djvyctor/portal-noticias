/**
 * Utilitários para gerenciamento de status
 * 
 * Funções reutilizáveis para trabalhar com status de notícias
 * e papéis de usuários.
 */

/**
 * Retorna o label legível do status da notícia
 * 
 * @param {string} status - Status da notícia (draft, pending, published, rejected)
 * @returns {string} Label do status em português
 */
export const getStatusLabel = (status) => {
  const labels = {
    draft: 'Rascunho',
    published: 'Publicada',
    pending: 'Pendente',
    rejected: 'Rejeitada'
  }
  return labels[status] || status
}

/**
 * Retorna as classes CSS do Tailwind para o badge de status
 * 
 * @param {string} status - Status da notícia
 * @returns {string} Classes CSS do Tailwind para o badge
 */
export const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-700',
    published: 'bg-green-100 text-green-700',
    pending: 'bg-yellow-100 text-yellow-700',
    rejected: 'bg-red-100 text-red-700'
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

/**
 * Retorna o nome legível do papel do usuário
 * 
 * @param {string} role - Papel do usuário (admin, editor, jornalista)
 * @returns {string} Nome legível do papel
 */
export const getRoleName = (role) => {
  const roles = {
    admin: 'Administrador',
    editor: 'Editor',
    jornalista: 'Jornalista'
  }
  return roles[role] || role
}

/**
 * Retorna a descrição do papel do usuário
 * 
 * @param {string} role - Papel do usuário
 * @returns {string} Descrição das permissões do papel
 */
export const getRoleDescription = (role) => {
  const descriptions = {
    admin: 'Você tem acesso total ao sistema. Pode criar, editar, publicar e destacar qualquer notícia.',
    editor: 'Você pode criar notícias e também editar e publicar notícias de outros jornalistas.',
    jornalista: 'Você pode criar e gerenciar suas próprias notícias.'
  }
  return descriptions[role] || 'Bem-vindo ao painel de redação!'
}

/**
 * Retorna o label legível do papel do usuário (alias para getRoleName)
 * 
 * @param {string} role - Papel do usuário
 * @returns {string} Label do papel
 */
export const getRoleLabel = (role) => {
  return getRoleName(role)
}

/**
 * Retorna as classes CSS do Tailwind para o badge do papel
 * 
 * @param {string} role - Papel do usuário
 * @returns {string} Classes CSS do Tailwind para o badge
 */
export const getRoleBadgeClass = (role) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    editor: 'bg-blue-100 text-blue-800',
    jornalista: 'bg-green-100 text-green-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}
