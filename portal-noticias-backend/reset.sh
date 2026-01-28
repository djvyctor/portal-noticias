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
    echo "[ERRO] PHP não encontrado! Instale o PHP primeiro."
    exit 1
fi

# Verificar se Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "[ERRO] Composer não encontrado! Instale o Composer primeiro."
    exit 1
fi

echo "1. Instalando dependências do Composer..."
if [ ! -d "vendor" ]; then
    echo "   Instalando dependências..."
    composer install --no-interaction --prefer-dist
    echo "[ OK ] Dependências instaladas!"
else
    echo "[ OK ] Dependências já instaladas!"
fi
echo ""

echo "2. Configurando arquivo .env..."
if [ ! -f ".env" ]; then
    echo "   Copiando .env.example para .env..."
    cp ".env.example" ".env"
    echo "[ OK ] Arquivo .env criado!"
else
    echo "[ OK ] Arquivo .env já existe!"
fi
echo ""

echo "3. Gerando chave da aplicação..."
php artisan key:generate --force > /dev/null 2>&1 || echo "[AVISO] Chave já configurada ou erro ao gerar!"
echo "[ OK ] Chave verificada!"
echo ""

echo "4. Criando arquivo de banco de dados SQLite..."
if [ ! -f "database/database.sqlite" ]; then
    echo "   Criando database.sqlite..."
    touch "database/database.sqlite"
    echo "[ OK ] Arquivo database.sqlite criado!"
else
    echo "[ OK ] Arquivo database.sqlite já existe!"
fi
echo ""

echo "5. Limpando todos os caches..."
php artisan optimize:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
echo "[ OK ] Caches limpos!"
echo ""

echo "6. Recriando banco de dados com seeders..."
php artisan migrate:fresh --seed --force
echo "[ OK ] Banco de dados recriado e populado!"
echo ""

echo "7. Linkando storage..."
if [ -L "public/storage" ]; then
    rm "public/storage"
fi
php artisan storage:link > /dev/null 2>&1 || echo "[AVISO] Erro ao linkar storage (pode já estar linkado)!"
echo "[ OK ] Storage linkado!"
echo ""

echo "8. Otimizando aplicação..."
php artisan config:cache > /dev/null 2>&1
php artisan route:cache > /dev/null 2>&1
echo "[ OK ] Aplicação otimizada!"
echo ""

echo "============================================"
echo "  Configuração concluída com sucesso!"
echo "============================================"
echo ""
echo "Credenciais de acesso:"
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
echo "Próximo passo:"
echo "   1. Execute: php artisan serve"
echo "   2. No frontend, execute:"
echo "      cd ../portal-noticias-frontend"
echo "      npm install"
echo "      npm run dev"
echo ""
echo "IMPORTANTE: Não esqueça de limpar o localStorage do navegador:"
echo "   F12 > Console > localStorage.clear() > F5"
echo ""
echo "============================================"
echo ""
