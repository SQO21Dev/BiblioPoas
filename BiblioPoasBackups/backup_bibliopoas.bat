@echo off
setlocal

REM ==============================
REM  BiblioPoás - MySQL Backup
REM ==============================

REM Ruta base del proyecto (ajustar solo si se mueve la carpeta)
set "PROJECT_DIR=%~dp0.."
set "BACKUP_DIR=%PROJECT_DIR%\BiblioPoasBackups\backups"

REM Ruta a MySQL (XAMPP por defecto)
set "MYSQL_BIN=C:\xampp\mysql\bin"

REM Base de datos
set "DB_NAME=bibliopoas"
set "DB_USER=root"
set "DB_PASS="

REM Timestamp
for /f "tokens=1-3 delims=/- " %%a in ("%date%") do (
  set "DD=%%a"
  set "MM=%%b"
  set "YYYY=%%c"
)
for /f "tokens=1-2 delims=: " %%a in ("%time%") do (
  set "HH=%%a"
  set "MN=%%b"
)
set "HH=%HH: =0%"
set "STAMP=%YYYY%-%MM%-%DD%_%HH%-%MN%"

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

set "OUT_FILE=%BACKUP_DIR%\%DB_NAME%_%STAMP%.sql"
set "LOG_FILE=%BACKUP_DIR%\backup_log.txt"

echo [%date% %time%] Iniciando backup >> "%LOG_FILE%"

"%MYSQL_BIN%\mysqldump.exe" -u%DB_USER% %DB_PASS% ^
 --single-transaction --routines --triggers --events "%DB_NAME%" > "%OUT_FILE%"

if errorlevel 1 (
  echo [%date% %time%] ERROR en backup >> "%LOG_FILE%"
) else (
  echo [%date% %time%] OK %OUT_FILE% >> "%LOG_FILE%"
)

REM 
forfiles /p "%BACKUP_DIR%" /m "%DB_NAME%_*.sql" /d -7 /c "cmd /c del @path" >nul 2>&1

exit /b 0
