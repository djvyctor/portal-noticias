export const getStatusLabel = (status) => {
  const labels = {
    draft: 'Rascunho',
    published: 'Publicada',
    pending: 'Pendente',
    rejected: 'Rejeitada'
  }
  return labels[status] || status
}

export const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-700',
    published: 'bg-green-100 text-green-700',
    pending: 'bg-yellow-100 text-yellow-700',
    rejected: 'bg-red-100 text-red-700'
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

export const getRoleName = (role) => {
  const roles = {
    admin: 'Administrador',
    editor: 'Editor',
    jornalista: 'Jornalista'
  }
  return roles[role] || role
}

export const getRoleDescription = (role) => {
  const descriptions = {
    admin: 'Você tem acesso total ao sistema. Pode criar, editar, publicar e destacar qualquer notícia.',
    editor: 'Você pode criar notícias e também editar e publicar notícias de outros jornalistas.',
    jornalista: 'Você pode criar e gerenciar suas próprias notícias.'
  }
  return descriptions[role] || 'Bem-vindo ao painel de redação!'
}

export const getRoleLabel = (role) => getRoleName(role)

export const getRoleBadgeClass = (role) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    editor: 'bg-blue-100 text-blue-800',
    jornalista: 'bg-green-100 text-green-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}
