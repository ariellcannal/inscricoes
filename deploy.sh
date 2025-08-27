#!/usr/bin/env bash
# deploy.sh – robusto para WHM/cPanel

# ===== modo seguro (sem pipefail p/ máxima compatibilidade) =====
set -eu

# ===== resolve diretório do script (independe do CWD) =====
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# ===== lock consistente no diretório do projeto =====
LOCKFILE="$SCRIPT_DIR/deploy.lock"
exec 9>"$LOCKFILE"
if ! flock -n 9; then
  echo "Outro deploy em andamento (lock: $LOCKFILE)"
  exit 0
fi
# libera o lock e remove o arquivo mesmo em erro/CTRL+C
cleanup() {
  flock -u 9 || true
  rm -f "$LOCKFILE" || true
}
trap cleanup EXIT

# ===== PATH do composer típico no cPanel =====
export PATH="/opt/cpanel/composer/bin:/usr/local/bin:/usr/bin:/bin:$PATH"

# ===== logs =====
LOG_DIR="$SCRIPT_DIR/application/logs"
mkdir -p "$LOG_DIR"
# redireciona todo output do script para o log (e para a tela)
exec > >(tee -a "$LOG_DIR/deploy.log") 2>&1

echo "-------------------------------"
echo "[$(date '+%F %T')] Iniciando deploy em $SCRIPT_DIR"

# ===== garante que o git confia neste diretório (Git 2.35+) =====
git config --global --add safe.directory "$SCRIPT_DIR" || true

# ===== garante remoto 'origin' =====
if ! git remote | grep -qx 'origin'; then
  git remote add origin git@github.com:ariellcannal/inscricoes.git
fi
# força a URL correta (ajuste se usar HTTPS)
git remote set-url origin git@github.com:ariellcannal/inscricoes.git

# ===== busca remoto =====
git fetch --prune origin

# ===== detecta branch (main > master > HEAD) =====
if git rev-parse --verify origin/main >/dev/null 2>&1; then
  BRANCH="main"
elif git rev-parse --verify origin/master >/dev/null 2>&1; then
  BRANCH="master"
else
  BRANCH="$(git symbolic-ref --short HEAD 2>/dev/null || echo main)"
fi
echo "Branch alvo: $BRANCH"

# ===== posiciona e sincroniza =====
# cria/ajusta branch local para rastrear a remota
git checkout -B "$BRANCH" "origin/$BRANCH"
git reset --hard "origin/$BRANCH"

# --- sempre rodar como o usuário do site (não root) ---
RUN_AS="cannal"
if [ "$(id -un)" != "$RUN_AS" ]; then
  exec sudo -u "$RUN_AS" -H bash -lc "cd '$(cd "$(dirname "$0")"; pwd)' && ./$(basename "$0")"
fi

# --- preferir “dist” (ZIP) globalmente para evitar repositórios Git em vendor ---
composer config -g preferred-install dist || true

# --- se houver repo Git em vendor e estiver “sujo”, limpar ou remover ---
if [ -d vendor/apimatic/unirest-php ]; then
  if git -C vendor/apimatic/unirest-php rev-parse --git-dir >/dev/null 2>&1; then
    # tenta ‘reset/clean’; se falhar, remove o pacote
    git -C vendor/apimatic/unirest-php reset --hard || rm -rf vendor/apimatic/unirest-php
    git -C vendor/apimatic/unirest-php clean -fd || true
  fi
fi

# opcional (mais agressivo e simples): se detectar QUALQUER .git dentro de vendor, zera vendor
# if find vendor -type d -name '.git' | grep -q .; then rm -rf vendor; fi

# ===== Composer (sem dev) =====
composer clear-cache
composer install --no-interaction --prefer-dist --no-dev

echo "[$(date '+%F %T')] Deploy OK"
