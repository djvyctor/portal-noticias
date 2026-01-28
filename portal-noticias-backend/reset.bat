@echo off
setlocal enabledelayedexpansion

REM Impede que o script feche automaticamente em caso de erro
set "ERROR_OCCURRED=0"

echo.
echo ============================================
echo   Configurando Portal de Noticias - Backend
echo ============================================
echo.

REM Verifica se PHP está instalado
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo [ ERRO ] PHP nao encontrado! Instale o PHP primeiro.
    set "ERROR_OCCURRED=1"
    goto :end
)

REM Verificar se Composer está instalado
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo [ ERRO ] Composer nao encontrado! Instale o Composer primeiro.
    set "ERROR_OCCURRED=1"
    goto :end
)

echo 1. Instalando dependencias do Composer...
if not exist "vendor" (
    echo    Instalando dependencias...
    call composer install --no-interaction --prefer-dist
    if !errorlevel! neq 0 (
        echo [ ERRO ] Falha ao instalar dependencias do Composer!
        set "ERROR_OCCURRED=1"
        goto :end
    )
    echo [ OK ] Dependencias instaladas!
) else (
    echo [ OK ] Dependencias ja instaladas!
)
echo.

echo 2. Configurando arquivo .env...
if not exist ".env" (
    if not exist ".env.example" (
        echo [ ERRO ] Arquivo .env.example nao encontrado!
        set "ERROR_OCCURRED=1"
        goto :end
    )
    echo    Copiando .env.example para .env...
    copy ".env.example" ".env" >nul
    if !errorlevel! neq 0 (
        echo [ ERRO ] Falha ao copiar .env.example!
        set "ERROR_OCCURRED=1"
        goto :end
    )
    echo [ OK ] Arquivo .env criado!
) else (
    echo [ OK ] Arquivo .env ja existe!
)
echo.

echo 3. Gerando chave da aplicacao...
call php artisan key:generate --force
if !errorlevel! neq 0 (
    echo [ AVISO ] Chave ja configurada ou erro ao gerar!
    echo    Continuando mesmo assim...
)
echo [ OK ] Chave verificada!
echo.

echo 4. Criando arquivo de banco de dados SQLite...
if not exist "database" (
    echo    Criando pasta database...
    mkdir database >nul 2>&1
)
if not exist "database\database.sqlite" (
    echo    Criando database.sqlite...
    type nul > "database\database.sqlite"
    if !errorlevel! neq 0 (
        echo [ ERRO ] Falha ao criar database.sqlite!
        set "ERROR_OCCURRED=1"
        goto :end
    )
    echo [ OK ] Arquivo database.sqlite criado!
) else (
    echo [ OK ] Arquivo database.sqlite ja existe!
)
echo.

echo 5. Limpando todos os caches...
call php artisan optimize:clear
call php artisan route:clear
call php artisan cache:clear
call php artisan config:clear
call php artisan view:clear
echo [ OK ] Caches limpos!
echo.

echo 6. Recriando banco de dados com seeders...
echo    Isso pode levar alguns segundos...
call php artisan migrate:fresh --seed --force
if !errorlevel! neq 0 (
    echo [ ERRO ] Falha ao recriar banco de dados!
    echo    Verifique os erros acima e tente novamente.
    set "ERROR_OCCURRED=1"
    goto :end
)
echo [ OK ] Banco de dados recriado e populado!
echo.

echo 7. Linkando storage...
if exist "public\storage" (
    rmdir /s /q "public\storage" 2>nul
)
call php artisan storage:link
if !errorlevel! equ 0 (
    echo [ OK ] Storage linkado!
) else (
    echo [ AVISO ] Erro ao linkar storage (pode ja estar linkado)!
    echo    Continuando mesmo assim...
)
echo.

echo 8. Otimizando aplicacao...
call php artisan config:cache
call php artisan route:cache
echo [ OK ] Aplicacao otimizada!
echo.

:end

if "%ERROR_OCCURRED%"=="1" (
    echo.
    echo ============================================
    echo   ERRO durante a configuracao!
    echo ============================================
    echo.
    echo Verifique as mensagens de erro acima e corrija os problemas.
    echo.
    pause
    exit /b 1
)

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
endlocal
