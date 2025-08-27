#!/bin/bash
set -euo pipefail

# Lock para evitar deploy simultâneo
LOCKFILE="$(dirname "$0")/deploy.lock"
exec 9>"$LOCKFILE"
flock -n 9 || { echo "Outro deploy em andamento"; exit 0; }

# Ajuste do PATH para composer no WHM/cPanel
export PATH="/opt/cpanel/composer/bin:/usr/local/bin:/usr/bin:/bin:$PATH"

PROJECT_DIR="$(dirname "$0")"
BRANCH="main"
LOG="$PROJECT_DIR/application/logs/deploy.log"

{
  echo "-------------------------------"
  echo "[$(date '+%F %T')] Iniciando deploy"

  cd "$PROJECT_DIR"
  git fetch origin "$BRANCH"
  git checkout "$BRANCH"
  git reset --hard "origin/$BRANCH"

  # Instala dependências de produção
  composer install --no-interaction --prefer-dist --no-dev

  echo "[$(date '+%F %T')] Deploy OK"
} >> "$LOG" 2>&1
