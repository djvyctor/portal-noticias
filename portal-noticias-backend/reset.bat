@echo off
setlocal enabledelayedexpansion

echo.
echo ============================================
echo   Configurando Portal de Noticias - Backend
echo ============================================
echo.

REM Verifica se PHP está instalado
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo [ ERRO ] PHP nao encontrado! Instale o PHP primeiro.
    pause
    exit /b 1
)

REM Verificar se Composer está instalado
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo [ ERRO ] Composer nao encontrado! Instale o Composer primeiro.
    pause
    exit /b 1
)

echo 1. Instalando dependencias do Composer...
if not exist "vendor" (
    echo    Instalando dependencias...
    call composer install --no-interaction --prefer-dist
    if !errorlevel! neq 0 (
        echo [ ERRO ] Falha ao instalar dependencias do Composer!
        pause
        exit /b 1
    )
    echo [ OK ] Dependencias instaladas!
) else (
    echo [ OK ] Dependencias ja instaladas!
)
echo.

echo 2. Configurando arquivo .env...
if not exist ".env" (
    echo    Copiando .env.example para .env...
    copy ".env.example" ".env" >nul
    if !errorlevel! neq 0 (
        echo [ ERRO ] Falha ao copiar .env.example!
        pause
        exit /b 1
    )
    echo [ OK ] Arquivo .env criado!
) else (
    echo [ OK ] Arquivo .env ja existe!
)
echo.

echo 3. Gerando chave da aplicacao...
call php artisan key:generate --force >nul 2>&1
if !errorlevel! equ 0 (
    echo [ OK ] Chave gerada!
) else (
    echo [ AVISO ] Chave ja configurada ou erro ao gerar!
)
echo.

echo 4. Criando arquivo de banco de dados SQLite...
if not exist "database\database.sqlite" (
    echo    Criando database.sqlite...
    type nul > "database\database.sqlite"
    if !errorlevel! neq 0 (
        echo [ ERRO ] Falha ao criar database.sqlite!
        pause
        exit /b 1
    )
    echo [ OK ] Arquivo database.sqlite criado!
) else (
    echo [ OK ] Arquivo database.sqlite ja existe!
)
echo.

echo 5. Limpando todos os caches...
call php artisan optimize:clear >nul 2>&1
call php artisan route:clear >nul 2>&1
call php artisan cache:clear >nul 2>&1
call php artisan config:clear >nul 2>&1
call php artisan view:clear >nul 2>&1
echo [ OK ] Caches limpos!
echo.

echo 6. Recriando banco de dados com seeders...
call php artisan migrate:fresh --seed --force
if !errorlevel! neq 0 (
    echo [ ERRO ] Falha ao recriar banco de dados!
    pause
    exit /b 1
)
echo [ OK ] Banco de dados recriado e populado!
echo.

echo 7. Linkando storage...
if exist "public\storage" (
    rmdir /s /q "public\storage" >nul 2>&1
)
call php artisan storage:link >nul 2>&1
if !errorlevel! equ 0 (
    echo [ OK ] Storage linkado!
) else (
    echo [ AVISO ] Erro ao linkar storage (pode ja estar linkado)!
)
echo.

echo 8. Otimizando aplicacao...
call php artisan config:cache >nul 2>&1
call php artisan route:cache >nul 2>&1
echo [ OK ] Aplicacao otimizada!
echo.

echo ============================================
echo   Configuracao concluida com sucesso!
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
echo Proximo passo:
echo   1. Execute: php artisan serve
echo   2. No frontend, execute:
echo      cd ..\portal-noticias-frontend
echo      npm install
echo      npm run dev
echo.
echo IMPORTANTE: Limpe o localStorage do navegador:
echo   F12 ^> Console ^> localStorage.clear() ^> F5
echo.
echo ============================================
echo.
pause
