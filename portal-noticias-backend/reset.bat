@echo off
echo.
echo ============================================
echo   Resetando Portal de Noticias - Backend
echo ============================================
echo.

echo 1. Limpando caches...
call php artisan optimize:clear
call php artisan route:clear
call php artisan cache:clear
call php artisan config:clear
echo [ OK ] Caches limpos!
echo.

echo 2. Recriando banco de dados...
call php artisan migrate:fresh --seed
echo [ OK ] Banco de dados recriado!
echo.

echo 3. Linkando storage...
call php artisan storage:link
echo [ OK ] Storage linkado!
echo.

echo 4. Verificando rotas da API...
call php artisan route:list --path=api
echo.

echo ============================================
echo   Processo concluido!
echo ============================================
echo.
echo Credenciais de acesso:
echo.
echo Admin:
echo   Email: admin@portaldenoticias.com
echo   Senha: admin123
echo.
echo Editor:
echo   Email: editor@portaldenoticias.com
echo   Senha: editor123
echo.
echo Jornalista:
echo   Email: jornalista@portaldenoticias.com
echo   Senha: jornalista123
echo.
echo ============================================
echo.
echo Agora execute: php artisan serve
echo.
echo IMPORTANTE: Limpe o localStorage do navegador:
echo   F12 ^> Console ^> localStorage.clear() ^> F5
echo.
pause
