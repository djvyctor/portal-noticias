# Documentação da API - Portal de Notícias

**URL Base:** `http://portal-noticias-backend.test/api`

**Autenticação:** Bearer Token (Laravel Sanctum) - exceto rotas públicas

---

## 🔓 ROTAS PÚBLICAS (Sem autenticação)

### Autenticação

#### POST `/register`
**Descrição:** Registra um novo usuário no sistema (sempre como Jornalista)
**Body:**
```json
{
  "name": "Nome do Usuário",
  "email": "email@exemplo.com",
  "password": "senha123",
  "password_confirmation": "senha123"
}
```
**Resposta:** Retorna usuário e token de autenticação

#### POST `/login`
**Descrição:** Autentica um usuário e retorna token
**Body:**
```json
{
  "email": "email@exemplo.com",
  "password": "senha123"
}
```
**Resposta:** Retorna usuário e token de autenticação

#### POST `/forgot-password`
**Descrição:** Gera token para recuperação de senha
**Body:**
```json
{
  "email": "email@exemplo.com"
}
```
**Resposta:** Retorna token e URL de reset

#### POST `/reset-password`
**Descrição:** Redefine senha usando token de recuperação
**Body:**
```json
{
  "email": "email@exemplo.com",
  "token": "token_gerado",
  "password": "nova_senha",
  "password_confirmation": "nova_senha"
}
```
**Resposta:** Confirmação de senha alterada

---

### Notícias Públicas

#### GET `/news/public`
**Descrição:** Lista todas as notícias publicadas (paginação)
**Resposta:** Lista de notícias publicadas

#### GET `/news/featured`
**Descrição:** Lista todas as notícias em destaque (paginação)
**Resposta:** Lista de notícias destacadas

#### GET `/news/carousel`
**Descrição:** Retorna as 5 notícias principais para o carrossel da home
**Resposta:** Array com até 5 notícias destacadas

#### GET `/news/daily`
**Descrição:** Lista notícias comuns (não destacadas) publicadas
**Resposta:** Lista de notícias normais (paginação)

#### GET `/news/category/{categorySlug}`
**Descrição:** Lista notícias publicadas de uma categoria específica
**Parâmetros:** `categorySlug` (slug da categoria)
**Resposta:** Lista de notícias da categoria (paginação)

#### GET `/news/search`
**Descrição:** Busca notícias publicadas por título ou conteúdo
**Query Params:** `q` (termo de busca, mínimo 2 caracteres)
**Exemplo:** `/news/search?q=tecnologia`
**Resposta:** Lista de notícias encontradas (paginação)

#### GET `/news/{slug}`
**Descrição:** Retorna detalhes de uma notícia publicada pelo slug
**Parâmetros:** `slug` (slug da notícia)
**Resposta:** Detalhes completos da notícia

---

### Categorias Públicas

#### GET `/categories`
**Descrição:** Lista todas as categorias disponíveis
**Resposta:** Lista de categorias com contagem de notícias

#### GET `/categories/{slug}`
**Descrição:** Retorna detalhes de uma categoria específica
**Parâmetros:** `slug` (slug da categoria)
**Resposta:** Detalhes da categoria com contagem de notícias

---

## 🔒 ROTAS AUTENTICADAS (Requer Bearer Token)

### Autenticação do Usuário

#### POST `/logout`
**Descrição:** Faz logout do usuário autenticado (revoga token)
**Headers:** `Authorization: Bearer {token}`
**Resposta:** Mensagem de sucesso

#### GET `/user`
**Descrição:** Retorna dados do usuário autenticado
**Headers:** `Authorization: Bearer {token}`
**Resposta:** Dados do usuário logado

#### POST `/change-password`
**Descrição:** Altera senha do usuário autenticado
**Headers:** `Authorization: Bearer {token}`
**Body:**
```json
{
  "current_password": "senha_atual",
  "password": "nova_senha",
  "password_confirmation": "nova_senha"
}
```
**Resposta:** Confirmação de senha alterada

#### POST `/me/name`
**Descrição:** Atualiza o nome do usuário autenticado
**Headers:** `Authorization: Bearer {token}`
**Body:**
```json
{
  "name": "Novo Nome"
}
```
**Resposta:** Dados atualizados do usuário

---

### Notícias do Usuário (CRUD)

#### GET `/user/news`
**Descrição:** Lista todas as notícias do usuário logado
**Headers:** `Authorization: Bearer {token}`
**Resposta:** Lista paginada das notícias do usuário

#### POST `/user/news`
**Descrição:** Cria uma nova notícia
**Headers:** `Authorization: Bearer {token}`
**Body (FormData):**
- `title` (string, obrigatório)
- `content` (string, obrigatório)
- `category_id` (integer, obrigatório)
- `image` (file, opcional - jpeg,png,jpg,gif,svg, max 2MB)
- `status` (string, opcional - apenas Editor/Admin: 'pending'|'published')
- `is_featured` (boolean, opcional - apenas Editor/Admin)
**Resposta:** Notícia criada (201)
**Nota:** Jornalista sempre cria como 'pending', Editor/Admin podem publicar direto

#### GET `/user/news/{id}`
**Descrição:** Retorna detalhes de uma notícia do usuário
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID da notícia)
**Resposta:** Detalhes da notícia
**Permissão:** Usuário deve ser dono, Editor ou Admin

#### PUT `/user/news/{id}`
**Descrição:** Atualiza uma notícia
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID da notícia)
**Body (FormData):**
- `title` (string, opcional)
- `content` (string, opcional)
- `category_id` (integer, opcional)
- `image` (file, opcional)
- `status` (string, opcional - apenas Editor/Admin)
- `is_featured` (boolean, opcional - apenas Editor/Admin)
**Resposta:** Mensagem de sucesso
**Permissão:** Usuário deve ser dono, Editor ou Admin

#### DELETE `/user/news/{id}`
**Descrição:** Deleta uma notícia
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID da notícia)
**Resposta:** 204 No Content
**Permissão:** Usuário deve ser dono, Editor ou Admin

---

### Ações de Moderação (Editor/Admin)

#### PATCH `/news/{id}/approve`
**Descrição:** Aprova e publica uma notícia pendente
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID da notícia)
**Resposta:** Mensagem de sucesso
**Permissão:** Apenas Editor ou Admin

#### PATCH `/news/{id}/feature`
**Descrição:** Alterna destaque de uma notícia (adiciona/remove do carrossel)
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID da notícia)
**Resposta:** Status atualizado do destaque
**Permissão:** Apenas Editor ou Admin

#### PATCH `/news/{id}/reject`
**Descrição:** Rejeita uma notícia pendente com motivo
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID da notícia)
**Body:**
```json
{
  "rejection_reason": "Motivo da rejeição"
}
```
**Resposta:** Mensagem de sucesso
**Permissão:** Apenas Editor ou Admin

---

### Listagens de Moderação (Editor/Admin)

#### GET `/news/pending`
**Descrição:** Lista todas as notícias pendentes de aprovação
**Headers:** `Authorization: Bearer {token}`
**Query Params:** `per_page` (opcional, padrão: 15, máximo: 50)
**Resposta:** Lista paginada de notícias pendentes
**Permissão:** Apenas Editor ou Admin

#### GET `/news/rejected`
**Descrição:** Lista notícias rejeitadas do usuário logado
**Headers:** `Authorization: Bearer {token}`
**Resposta:** Lista paginada de notícias rejeitadas

#### GET `/news/rejected/{userId}`
**Descrição:** Lista notícias rejeitadas de um usuário específico
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `userId` (ID do usuário)
**Resposta:** Lista paginada de notícias rejeitadas
**Permissão:** Admin/Editor podem ver de qualquer usuário, usuário comum só vê as próprias

#### GET `/news/all`
**Descrição:** Lista todas as notícias do sistema (de todos os usuários)
**Headers:** `Authorization: Bearer {token}`
**Resposta:** Lista paginada de todas as notícias
**Permissão:** Apenas Editor ou Admin

---

### Gerenciamento de Usuários

#### GET `/admin/users`
**Descrição:** Lista todos os usuários do sistema
**Headers:** `Authorization: Bearer {token}`
**Query Params:** `page` (opcional, para paginação)
**Resposta:** Lista paginada de usuários
**Permissão:** Editor ou Admin

#### POST `/admin/users`
**Descrição:** Cria um novo usuário
**Headers:** `Authorization: Bearer {token}`
**Body:**
```json
{
  "name": "Nome do Usuário",
  "email": "email@exemplo.com",
  "password": "senha123",
  "role": "jornalista" // ou "editor" ou "admin"
}
```
**Resposta:** Usuário criado (201)
**Permissão:** Editor ou Admin
**Nota:** Editor só pode criar jornalistas, Admin pode criar qualquer tipo

#### GET `/admin/users/{id}`
**Descrição:** Retorna detalhes de um usuário
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID do usuário)
**Resposta:** Detalhes do usuário
**Permissão:** Editor ou Admin

#### PUT `/admin/users/{id}`
**Descrição:** Atualiza um usuário
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID do usuário)
**Body:**
```json
{
  "name": "Novo Nome", // opcional
  "email": "novo@email.com", // opcional
  "password": "nova_senha", // opcional
  "role": "editor" // opcional
}
```
**Resposta:** Usuário atualizado
**Permissão:** Editor ou Admin
**Nota:** Editor não pode alterar role ou senha de outros usuários

#### DELETE `/admin/users/{id}`
**Descrição:** Deleta um usuário
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID do usuário)
**Resposta:** 204 No Content
**Permissão:** Apenas Admin
**Nota:** Não é possível deletar o próprio usuário

---

### Solicitações de Promoção

#### GET `/promotion-requests/my`
**Descrição:** Lista solicitações de promoção do usuário logado (Jornalista)
**Headers:** `Authorization: Bearer {token}`
**Resposta:** Lista de solicitações do usuário
**Permissão:** Jornalista vê suas próprias solicitações

#### POST `/promotion-requests`
**Descrição:** Cria uma nova solicitação de promoção (Jornalista -> Editor)
**Headers:** `Authorization: Bearer {token}`
**Body:**
```json
{
  "message": "Mensagem explicando por que deseja ser editor (10-1000 caracteres)"
}
```
**Resposta:** Solicitação criada (201)
**Permissão:** Apenas Jornalista
**Nota:** Não permite criar nova se já houver uma pendente

#### GET `/promotion-requests`
**Descrição:** Lista todas as solicitações de promoção
**Headers:** `Authorization: Bearer {token}`
**Query Params:** `page` (opcional, para paginação)
**Resposta:** Lista paginada de todas as solicitações
**Permissão:** Apenas Editor ou Admin

#### GET `/promotion-requests/{id}`
**Descrição:** Retorna detalhes de uma solicitação de promoção
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID da solicitação)
**Resposta:** Detalhes da solicitação
**Permissão:** Jornalista vê apenas suas próprias, Editor/Admin veem todas

#### PATCH `/promotion-requests/{id}/approve`
**Descrição:** Aprova uma solicitação de promoção e promove o usuário para Editor
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID da solicitação)
**Resposta:** Mensagem de sucesso
**Permissão:** Apenas Editor ou Admin
**Nota:** Promove automaticamente o usuário para Editor ao aprovar

#### PATCH `/promotion-requests/{id}/reject`
**Descrição:** Rejeita uma solicitação de promoção
**Headers:** `Authorization: Bearer {token}`
**Parâmetros:** `id` (ID da solicitação)
**Body:**
```json
{
  "rejection_reason": "Motivo da rejeição (opcional, máximo 500 caracteres)"
}
```
**Resposta:** Mensagem de sucesso
**Permissão:** Apenas Editor ou Admin

---

## 📝 NOTAS IMPORTANTES

### Autenticação
- Todas as rotas autenticadas requerem header: `Authorization: Bearer {token}`
- Token é obtido através de `/login` ou `/register`
- Token expira quando usuário faz logout ou token é revogado

### Permissões por Role

**Jornalista:**
- Pode criar, editar e deletar suas próprias notícias
- Notícias criadas ficam como 'pending' (aguardando aprovação)
- Pode criar solicitação de promoção
- Pode ver suas próprias notícias rejeitadas

**Editor:**
- Todas as permissões de Jornalista
- Pode aprovar/rejeitar notícias
- Pode destacar notícias no carrossel
- Pode editar qualquer notícia
- Pode criar jornalistas
- Pode ver e gerenciar todas as notícias
- Pode aprovar/rejeitar solicitações de promoção
- NÃO pode criar editores/admins
- NÃO pode alterar role de usuários
- NÃO pode alterar senha de outros usuários
- NÃO pode excluir usuários

**Admin:**
- Todas as permissões de Editor
- Pode criar qualquer tipo de usuário
- Pode alterar role de usuários
- Pode alterar senha de qualquer usuário
- Pode excluir usuários (exceto a si mesmo)

### Formato de Resposta

**Sucesso:**
```json
{
  "data": {...},
  "message": "Mensagem de sucesso"
}
```

**Erro:**
```json
{
  "message": "Mensagem de erro",
  "errors": {...} // quando houver erros de validação
}
```

### Paginação

Rotas que retornam listas paginadas incluem:
```json
{
  "data": [...],
  "current_page": 1,
  "last_page": 5,
  "per_page": 10,
  "total": 50,
  ...
}
```
