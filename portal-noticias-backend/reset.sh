#!/bin/bash

echo "🔧 Resetando Portal de Notícias - Backend"
echo "=========================================="
echo ""

echo "1️⃣ Limpando caches..."
php artisan optimize:clear
php artisan route:clear
php artisan cache:clear
php artisan config:clear
echo "✅ Caches limpos!"
echo ""

echo "2️⃣ Recriando banco de dados..."
php artisan migrate:fresh --seed
echo "✅ Banco de dados recriado!"
echo ""

echo "3️⃣ Linkando storage..."
php artisan storage:link
echo "✅ Storage linkado!"
echo ""

echo "4️⃣ Verificando rotas da API..."
php artisan route:list --path=api | head -20
echo ""

echo "✅ Processo concluído!"
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
echo "🚀 Agora execute: php artisan serve"
echo ""
echo "⚠️  Não esqueça de limpar o localStorage do navegador:"
echo "   F12 > Console > localStorage.clear() > F5"
