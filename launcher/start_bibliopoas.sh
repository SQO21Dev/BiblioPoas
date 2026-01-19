#!/usr/bin/env bash
set -euo pipefail

# ==============================
#  BiblioPoas - Launcher PRO (Linux)
#  - Mata cualquier proceso en el puerto
#  - Arranca servidor PHP en segundo plano
#  - Abre navegador
#  - Dar permisos: chmod +x start_bibliopoas.sh
#  - Ejecutar: ./start_bibliopoas.sh
# ==============================

APP_DIR="/ruta/a/tu/proyecto/BiblioPoas"   # <-- AJUSTA ESTA RUTA
PORT="8000"
URL="http://localhost:${PORT}/"

# 1) Validaciones
if [[ ! -f "${APP_DIR}/public/index.php" ]]; then
  echo "ERROR: No se encuentra ${APP_DIR}/public/index.php"
  echo "Revisa APP_DIR en este script."
  exit 1
fi

if ! command -v php >/dev/null 2>&1; then
  echo "ERROR: No se encuentra 'php' en el PATH."
  echo "Instala PHP o ajusta el PATH."
  exit 1
fi

cd "${APP_DIR}"

# 2) Matar proceso que esté usando el puerto (si existe)
echo "[BiblioPoas] Verificando puerto ${PORT}..."

if command -v lsof >/dev/null 2>&1; then
  PIDS="$(lsof -ti tcp:${PORT} || true)"
  if [[ -n "${PIDS}" ]]; then
    echo "[BiblioPoas] Puerto ${PORT} ocupado. Cerrando PID(s): ${PIDS}"
    kill -9 ${PIDS} 2>/dev/null || true
  fi
else
  # fallback si no tienes lsof
  if command -v fuser >/dev/null 2>&1; then
    fuser -k "${PORT}/tcp" 2>/dev/null || true
  else
    echo "WARN: No tienes lsof ni fuser. No puedo liberar el puerto automáticamente."
  fi
fi

# 3) Arrancar servidor en background
echo "[BiblioPoas] Iniciando servidor..."
nohup php -S "localhost:${PORT}" -t public > /tmp/bibliopoas_server.log 2>&1 &

# Guardar PID (opcional, útil si luego quieres un stop)
echo $! > /tmp/bibliopoas_server.pid

# 4) Esperar un momento
sleep 2

# 5) Abrir navegador
if command -v xdg-open >/dev/null 2>&1; then
  xdg-open "${URL}" >/dev/null 2>&1 || true
elif command -v gnome-open >/dev/null 2>&1; then
  gnome-open "${URL}" >/dev/null 2>&1 || true
else
  echo "Servidor iniciado. Abre manualmente: ${URL}"
fi

echo "[BiblioPoas] Iniciado en ${URL}"
echo "Log: /tmp/bibliopoas_server.log"
exit 0
