#!/bin/bash

set -e

echo ""
echo "============================================"
echo "  Configurando Portal de Notícias - Backend"
echo "  (Como se fosse a primeira vez)"
echo "============================================"
echo ""

# Verificar se PHP está instalado
if ! command -v php &> /dev/null; then
    echo "❌ [ERRO] PHP não encontrado! Instale o PHP primeiro."
    exit 1
fi

# Verificar se Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "❌ [ERRO] Composer não encontrado! Instale o Composer primeiro."
    exit 1
fi

echo "1️⃣ Instalando dependências do Composer..."
if [ ! -d "vendor" ]; then
    echo "   Instalando dependências..."
    composer install --no-interaction --prefer-dist
    echo "✅ Dependências instaladas!"
else
    echo "✅ Dependências já instaladas!"
fi
echo ""

echo "2️⃣ Configurando arquivo .env..."
if [ ! -f ".env" ]; then
    echo "   Copiando .env.example para .env..."
    cp ".env.example" ".env"
    echo "✅ Arquivo .env criado!"
else
    echo "✅ Arquivo .env já existe!"
fi
echo ""

echo "3️⃣ Gerando chave da aplicação..."
php artisan key:generate --force > /dev/null 2>&1 || echo "⚠️  Chave já configurada ou erro ao gerar!"
echo "✅ Chave verificada!"
echo ""

echo "4️⃣ Criando arquivo de banco de dados SQLite..."
if [ ! -f "database/database.sqlite" ]; then
    echo "   Criando database.sqlite..."
    touch "database/database.sqlite"
    echo "✅ Arquivo database.sqlite criado!"
else
    echo "✅ Arquivo database.sqlite já existe!"
fi
echo ""

echo "5️⃣ Limpando todos os caches..."
php artisan optimize:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
echo "✅ Caches limpos!"
echo ""

echo "6️⃣ Recriando banco de dados com seeders..."
php artisan migrate:fresh --seed --force
echo "✅ Banco de dados recriado e populado!"
echo ""

echo "7️⃣ Linkando storage..."
if [ -L "public/storage" ]; then
    rm "public/storage"
fi
php artisan storage:link > /dev/null 2>&1 || echo "⚠️  Erro ao linkar storage (pode já estar linkado)!"
echo "✅ Storage linkado!"
echo ""

echo "8️⃣ Otimizando aplicação..."
php artisan config:cache > /dev/null 2>&1
php artisan route:cache > /dev/null 2>&1
echo "✅ Aplicação otimizada!"
echo ""

echo "============================================"
echo "  Configuração concluída com sucesso!"
echo "============================================"
echo ""
echo "📱 Credenciais de acesso:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Admin:"
echo "  Email: admin@portaldenoticias.com"
echo "  Senha: admin123"
echo ""
echo "Editor:"
echo "  Email: editor@portaldenoticias.com"
echo "  Senha: editor123"
echo ""
echo "Jornalista:"
echo "  Email: jornalista@portaldenoticias.com"
echo "  Senha: jornalista123"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🚀 Próximo passo:"
echo "   1. Execute: php artisan serve"
echo "   2. No frontend, execute:"
echo "      cd ../portal-noticias-frontend"
echo "      npm install"
echo "      npm run dev"
echo ""
echo "⚠️  Não esqueça de limpar o localStorage do navegador:"
echo "   F12 > Console > localStorage.clear() > F5"
echo ""
echo "============================================"
echo ""
