#!/bin/bash
# Arquivo: deploy.sh (na raiz do projeto)
set -euo pipefail

# Lock para evitar deploy simultâneo
LOCKFILE="$(pwd)/deploy.lock"
exec 9>"$LOCKFILE"
flock -n 9 || { echo "Outro deploy em andamento"; exit 0; }

# Composer no cPanel/WHM
export PATH="/opt/cpanel/composer/bin:/usr/local/bin:/usr/bin:/bin:$PATH"

PROJECT_DIR="$(pwd)"
BRANCH="main"
LOG_DIR="$PROJECT_DIR/application/logs"

mkdir -p "$LOG_DIR"
echo "-------------------------------" >> "$LOG_DIR/deploy.log"
echo "[$(date '+%F %T')] Iniciando deploy" >> "$LOG_DIR/deploy.log"

cd "$PROJECT_DIR"

# Garante estado limpo na branch certa
git fetch origin "$BRANCH"
git checkout "$BRANCH"
git reset --hard "origin/$BRANCH"   # NÃO remove arquivos não rastreados (.env permanece)

# Dependências (sem dev)
composer install --no-interaction --prefer-dist --no-dev

# (Opcional) passos pós-deploy
# php artisan migrate --force
# npm ci && npm run build

echo "[$(date '+%F %T')] Deploy OK" >> "$LOG_DIR/deploy.log"
