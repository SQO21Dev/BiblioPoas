#!/usr/bin/env bash
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
BACKUP_DIR="$PROJECT_DIR/BiblioPoasBackups/backups"

DB_NAME="bibliopoas"
HOST="localhost"

STAMP="$(date +'%Y-%m-%d_%H-%M')"
OUT_FILE="${BACKUP_DIR}/${DB_NAME}_${STAMP}.sql"
LOG_FILE="${BACKUP_DIR}/backup_log.txt"

mkdir -p "${BACKUP_DIR}"

echo "[${STAMP}] Iniciando backup" >> "${LOG_FILE}"

mysqldump --single-transaction --routines --triggers --events \
  "${DB_NAME}" > "${OUT_FILE}"

echo "[${STAMP}] OK ${OUT_FILE}" >> "${LOG_FILE}"

# Eliminar backups mayores a 7 días
find "${BACKUP_DIR}" -type f -name "${DB_NAME}_*.sql" -mtime +7 -delete
