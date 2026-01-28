# Portal de Notícias

Sistema de portal de notícias com painel administrativo, fluxo de aprovação de matérias e perfis de usuário (Admin, Editor, Jornalista). O público vê notícias publicadas na home; jornalistas criam e enviam para revisão; editores aprovam ou rejeitam; admins gerenciam usuários e o sistema.

---

## Requisitos

- **PHP** 8.2 ou superior
- **Composer** (gerenciador de dependências PHP)
- **Node.js** 20.x ou 22.x (para o frontend)
- **npm** (vem com o Node)
- **SQLite** (usado como banco de dados, sem instalação extra no Windows na maioria dos casos)

Verifique no terminal:

```bash
php -v
composer -V
node -v
npm -v
```

Se algum não for encontrado, instale antes de continuar.

---

## Tecnologias

**Backend**
- Laravel 12 (PHP)
- Laravel Sanctum (autenticação API)
- SQLite (banco de dados)

**Frontend**
- Vue 3
- Vite 7
- Vue Router
- Pinia
- Axios
- Tailwind CSS

---

## Como rodar o projeto

### 1. Clonar o repositório

```bash
git clone https://github.com/djvyctor/portal-noticias.git
cd dev
```

### 2. Configurar o backend

Entre na pasta do backend e rode o script de configuração:

```bash
cd portal-noticias-backend
reset.bat
```

O `reset.bat` faz o seguinte:

- Verifica se PHP e Composer estão instalados
- Instala dependências do Composer (`composer install`)
- Cria o arquivo `.env` a partir do `.env.example` (se não existir)
- Gera a chave da aplicação
- Cria o banco SQLite em `database/database.sqlite`
- Limpa caches
- Roda as migrations e os seeders (recria as tabelas e insere dados iniciais)
- Cria o link do storage
- Deixa o backend pronto para uso

Se der certo, ao final ele mostra as credenciais de teste e os próximos passos. Não feche a janela com "pause" até ler.

**Credenciais após o reset:**

| Perfil     | Email                          | Senha       |
|-----------|---------------------------------|-------------|
| Admin     | admin@portaldenoticias.com      | admin123    |
| Editor    | editor@portaldenoticias.com     | editor123   |
| Jornalista| jornalista@portaldenoticias.com | jornalista123 |

### 3. Configurar o frontend

Em outro terminal, na raiz do projeto (`dev`):

```bash
cd portal-noticias-frontend
npm install
```

### 4. URL da API (importante)

O frontend chama a API em `http://portal-noticias-backend.test/api`. Se você **não** usa um domínio local (tipo Valet ou hosts apontando para esse endereço), vai precisar apontar para o servidor que o Laravel sobe.

Com `php artisan serve` o backend fica em `http://127.0.0.1:8000`. Nesse caso, abra:

`portal-noticias-frontend/src/services/api.js`

e troque a linha do `baseURL` para:

```javascript
baseURL: 'http://127.0.0.1:8000/api',
```

Salve o arquivo.

### 5. Subir o backend

No terminal, dentro de `portal-noticias-backend`:

```bash
php artisan serve
```

Deixe esse terminal aberto. O backend estará em `http://127.0.0.1:8000` (ou na URL que aparecer no terminal).

### 6. Subir o frontend

Em outro terminal, dentro de `portal-noticias-frontend`:

```bash
npm run dev
```

O Vite vai mostrar a URL do frontend (geralmente `http://localhost:5173`). Acesse essa URL no navegador.

### 7. Primeiro acesso

- Abra o frontend no navegador (ex.: `http://localhost:5173`).
- Se já tiver usado o sistema nesse navegador, limpe o armazenamento local: F12, aba Console, digite `localStorage.clear()` e dê Enter, depois F5 para recarregar.
- Faça login com uma das contas da tabela acima (por exemplo Admin: `admin@portaldenoticias.com` / `admin123`).

A partir daí você pode navegar pelo portal, criar notícias (como jornalista), aprovar/rejeitar (como editor) e gerenciar usuários (como admin).

---

## Estrutura do repositório

- `portal-noticias-backend/` – API Laravel (PHP). Contém o `reset.bat`, migrations, seeders, controllers e documentação da API em `API_DOCUMENTATION.md`.
- `portal-noticias-frontend/` – Aplicação Vue (Vite). Contém componentes, views, serviços de API e estilos.

Para detalhes da API, consulte `portal-noticias-backend/API_DOCUMENTATION.md`.
