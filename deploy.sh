#!/usr/bin/env bash
# deploy.sh – WHM/cPanel (lock fora do repo, HOME/COMPOSER_HOME, heartbeats, timeouts, git clean, composer dist)

set -euo pipefail

# ===================== Configs =====================
RUN_AS="cannal"                      # usuário dono do site
TIMEOUT_SECS="${TIMEOUT_SECS:-1800}" # tempo máx (30 min)
HEARTBEAT_SECS="${HEARTBEAT_SECS:-30}"
REPO_SSH_URL="${REPO_SSH_URL:-git@github.com:ariellcannal/inscricoes.git}"

# ===================== Helpers =====================
# Exibe mensagens com carimbo de data/hora
stage() { echo "[$(date '+%F %T')] $*"; }

# Executa comandos com timeout quando disponível
do_timeout() {
  if command -v timeout >/dev/null 2>&1; then
    timeout --preserve-status "$TIMEOUT_SECS" "$@"
  else
    "$@"
  fi
}

# Verifica se um PID ainda está ativo
is_pid_alive() {
  local _pid="$1"
  [ -n "$_pid" ] && kill -0 "$_pid" 2>/dev/null
}

# ===================== Reexecuta como usuário correto =====================
if [ "$(id -un)" != "$RUN_AS" ]; then
  exec sudo -u "$RUN_AS" -H bash -lc "cd '$(cd \"$(dirname \"$0\")\"; pwd)' && TIMEOUT_SECS='$TIMEOUT_SECS' HEARTBEAT_SECS='$HEARTBEAT_SECS' REPO_SSH_URL='$REPO_SSH_URL' ./$(basename \"$0\")"
fi

# ===================== Diretórios e ambiente =====================
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# LOCK FORA DO REPO (nunca será apagado por git clean/reset)
LOCK_ROOT="/home/$RUN_AS/.locks"
LOCKDIR="$LOCK_ROOT/inscricoes-deploy.lock.d"
PIDFILE="$LOCKDIR/pid"

LOG_DIR="$SCRIPT_DIR/application/logs"
LOG_FILE="$LOG_DIR/deploy.log"

# Ambiente consistente p/ Git/Composer
export HOME="/home/$RUN_AS"
export COMPOSER_HOME="$HOME/.composer"
export PATH="/opt/cpanel/composer/bin:/usr/local/bin:/usr/bin:/bin:$PATH"

mkdir -p "$LOG_DIR" "$LOCK_ROOT" "$COMPOSER_HOME"

# Remoção de lock antigo no diretório do projeto (compatibilidade)
LEGACY_LOCK="$SCRIPT_DIR/deploy.lock"
if [ -e "$LEGACY_LOCK" ]; then
  stage "Removendo lock legado $LEGACY_LOCK"
  rm -f "$LEGACY_LOCK"
fi

# ===================== Helpers =====================
stage() { echo "[$(date '+%F %T')] $*"; }

do_timeout() {
  if command -v timeout >/dev/null 2>&1; then
    timeout --preserve-status "$TIMEOUT_SECS" "$@"
  else
    "$@"
  fi
}

is_pid_alive() {
  local _pid="$1"
  [ -n "$_pid" ] && kill -0 "$_pid" 2>/dev/null
}

# ===================== Stale lock recovery =====================
if [ -d "$LOCKDIR" ]; then
  OLD_PID=""
  [ -s "$PIDFILE" ] && OLD_PID="$(cat "$PIDFILE" 2>/dev/null || true)"
  if [ -n "$OLD_PID" ] && ! is_pid_alive "$OLD_PID"; then
    rm -rf "$LOCKDIR" 2>/dev/null || true
  fi
fi

# ===================== Tenta adquirir lock (mkdir atômico) =====================
if ! mkdir "$LOCKDIR" 2>/dev/null; then
  echo "Outro deploy em andamento (lock: $LOCKDIR)"
  if [ -s "$PIDFILE" ]; then
    echo "PID atual (provável): $(cat "$PIDFILE")"
    ps -p "$(cat "$PIDFILE")" -o pid,etime,cmd 2>/dev/null || true
  fi
  exit 0
fi

# Gravamos o PID e garantimos limpeza ao sair
echo $$ > "$PIDFILE"
cleanup() { rm -rf "$LOCKDIR" 2>/dev/null || true; }
trap cleanup EXIT

# ===================== Logs (só depois do lock) =====================
exec > >(tee -a "$LOG_FILE") 2>&1

echo "-------------------------------"
echo "[$(date '+%F %T')] Iniciando deploy em $SCRIPT_DIR (user: $(id -un), pid: $$, timeout: ${TIMEOUT_SECS}s)"

# Heartbeat para acompanhar progresso
(
  while sleep "$HEARTBEAT_SECS"; do
    echo "[$(date '+%F %T')] heartbeat: deploy em andamento (pid $$)"
  done
) &
HB_PID=$!
trap 'kill "$HB_PID" 2>/dev/null || true; cleanup' EXIT

# ===================== GIT =====================
stage "Git: configurar safe.directory (global, com HOME definido)"
git config --global --add safe.directory "$SCRIPT_DIR" || true

if ! git remote | grep -qx 'origin'; then
  stage "Git: adicionando remote origin $REPO_SSH_URL"
  git remote add origin "$REPO_SSH_URL"
fi
git remote set-url origin "$REPO_SSH_URL"

stage "Git: fetch --prune origin (timeout)"
do_timeout git fetch --prune origin

# Branch alvo
if git rev-parse --verify origin/main >/dev/null 2>&1; then
  BRANCH="main"
elif git rev-parse --verify origin/master >/dev/null 2>&1; then
  BRANCH="master"
else
  BRANCH="$(git symbolic-ref --short HEAD 2>/dev/null || echo main)"
fi
stage "Branch alvo: $BRANCH"

# Working tree limpo
stage "Git: reset --hard e clean -fd"
git reset --hard
git clean -fd

stage "Git: checkout -B $BRANCH origin/$BRANCH"
git checkout -B "$BRANCH" "origin/$BRANCH"

stage "Git: reset --hard origin/$BRANCH"
git reset --hard "origin/$BRANCH"

stage "Git: submodule sync and update"
git submodule sync --recursive
git submodule update --init --recursive

# ===================== COMPOSER =====================
stage "Composer: preferir dist (usar flag na instalação)"
# Evitar composer config -g para não depender do HOME; a flag --prefer-dist resolve.

# Se houver qualquer .git dentro de vendor, reinstalar limpo
if find vendor -type d -name ".git" | grep -q . 2>/dev/null; then
  stage "Composer: detectado .git em vendor — removendo vendor/ para instalação limpa"
  rm -rf vendor
fi

stage "Composer: clear-cache"
composer clear-cache || true

stage "Composer: install --no-dev --prefer-dist --optimize-autoloader --no-progress (timeout)"
do_timeout composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader --no-progress

stage "Deploy OK"
echo "[$(date '+%F %T')] Deploy OK"
