@echo off
setlocal

REM ==============================
REM  BiblioPoás - Launcher Windows
REM ==============================

REM 1) Ruta al proyecto (ajusta esto)
set "APP_DIR=D:\SebasProgramming\TCU\BiblioPoas"

REM 2) Puerto a usar
set "PORT=8000"

REM 3) URL
set "URL=http://localhost:%PORT%/"

REM 4) Si PHP NO está en PATH, define la ruta al php.exe aquí:
REM    Ejemplo XAMPP: C:\xampp\php\php.exe
REM    Si PHP SI está en PATH, deja PHP_EXE=php
set "PHP_EXE=php"

REM 5) Validaciones rápidas
if not exist "%APP_DIR%\public\index.php" (
  echo ERROR: No se encuentra "%APP_DIR%\public\index.php"
  echo Revisa APP_DIR en este .bat
  pause
  exit /b 1
)

REM 6) Ir al directorio del proyecto
cd /d "%APP_DIR%"

REM 7) Levantar el servidor en segundo plano (sin dejarte pegado)
REM    -N: sin prompt, -W: espera a terminar (pero lo mandamos en background con start)
REM    /MIN: minimiza la ventana
start "BiblioPoas Server" /MIN cmd /c "%PHP_EXE% -S localhost:%PORT% -t public"

REM 8) Esperar un momento para que el server arranque
timeout /t 1 /nobreak >nul

REM 9) Abrir el navegador en la URL
start "" "%URL%"

REM 10) Mensaje opcional
echo BiblioPoas iniciado en %URL%
echo Si cierras la PC, se cierra el servidor.
echo Para detenerlo, usa el stop (recomendado) o cierra el proceso.
exit /b 0
