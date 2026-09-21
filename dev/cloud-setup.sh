#!/usr/bin/env bash
#
# Cloud Agent bootstrap + persistent tunnel supervisor for Grit Fit Nutri.
#
#   install  - ensure PHP, MariaDB and ngrok are present (idempotent)
#   start    - start DB + app, then launch the background keeper and return
#   keeper   - supervise loop: keep MariaDB, the PHP app and ngrok alive
#
# The ngrok auth token is read ONLY from the NGROK_AUTHTOKEN environment
# variable (add it in the Cursor "Secrets" panel). It is never written to the
# repository. If the token is absent, the app still runs locally and the tunnel
# is simply skipped.
set -uo pipefail

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# DB values mirror config.php (which is already committed); no new secrets here.
DB_NAME="u582313683_gritfitnutri"
DB_USER="u582313683_gritfitnutri"
DB_PASS="KiratveerGF!@#123"

APP_PORT="8000"
# Public URL is not a secret. Override with NGROK_DOMAIN if you reserve another.
NGROK_DOMAIN="${NGROK_DOMAIN:-dollop-crestless-rash.ngrok-free.dev}"

log() { echo "$(date -u '+%F %T') [gfn] $*"; }

install_runtime() {
  if ! command -v php >/dev/null 2>&1 \
     || { ! command -v mariadbd >/dev/null 2>&1 && ! command -v mysqld >/dev/null 2>&1; }; then
    log "installing PHP + MariaDB"
    sudo apt-get update -y
    sudo DEBIAN_FRONTEND=noninteractive apt-get install -y \
      php-cli php-mysql php-gd php-mbstring php-curl mariadb-server curl
  fi
  if ! command -v ngrok >/dev/null 2>&1; then
    log "installing ngrok"
    curl -sL https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.tgz -o /tmp/ngrok.tgz \
      && tar xzf /tmp/ngrok.tgz -C /tmp \
      && sudo mv -f /tmp/ngrok /usr/local/bin/ngrok
  fi
}

ensure_mariadb() {
  if ! sudo mysqladmin status >/dev/null 2>&1; then
    log "MariaDB down -> starting"
    sudo service mariadb start >/dev/null 2>&1
    # PHP's pdo_mysql socket default is /var/run/mysqld; MariaDB uses /run/mysqld.
    sudo ln -sfn /run/mysqld /var/run/mysqld
    for _ in $(seq 1 20); do sudo mysqladmin status >/dev/null 2>&1 && break; sleep 1; done
  fi
}

ensure_database() {
  sudo mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4;
    CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
    GRANT ALL ON ${DB_NAME}.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;" 2>/dev/null
  local n
  n=$(sudo mysql -N -e \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}';" \
    2>/dev/null || echo 0)
  if [ "${n:-0}" -lt 5 ]; then
    log "loading db/init.sql (found ${n:-0} tables)"
    sudo mysql "${DB_NAME}" < "${REPO_DIR}/db/init.sql"
  fi
}

ensure_php() {
  if ! curl -s -o /dev/null "http://localhost:${APP_PORT}/"; then
    log "PHP app down -> starting on :${APP_PORT}"
    ( cd "${REPO_DIR}" && nohup php -S "0.0.0.0:${APP_PORT}" -t "${REPO_DIR}" \
        "${REPO_DIR}/dev/router.php" >/tmp/gfn-php.log 2>&1 & )
    sleep 2
  fi
}

ensure_ngrok() {
  if [ -z "${NGROK_AUTHTOKEN:-}" ]; then
    return 0
  fi
  if ! pgrep -x ngrok >/dev/null 2>&1; then
    log "ngrok down -> starting on ${NGROK_DOMAIN}"
    ngrok config add-authtoken "${NGROK_AUTHTOKEN}" >/dev/null 2>&1
    nohup ngrok http --url="https://${NGROK_DOMAIN}" "${APP_PORT}" \
      --log stdout --log-format logfmt >/tmp/gfn-ngrok.log 2>&1 &
    sleep 3
  fi
}

cmd_install() {
  install_runtime
  log "install complete"
}

cmd_start() {
  install_runtime
  ensure_mariadb
  ensure_database
  ensure_php
  ensure_ngrok
  if ! pgrep -f "cloud-setup.sh keeper" >/dev/null 2>&1; then
    nohup bash "${BASH_SOURCE[0]}" keeper >/tmp/gfn-keeper.log 2>&1 &
    log "keeper launched (pid $!)"
  fi
  if [ -z "${NGROK_AUTHTOKEN:-}" ]; then
    log "NGROK_AUTHTOKEN not set: app runs locally on :${APP_PORT}, tunnel skipped."
  else
    log "start complete: https://${NGROK_DOMAIN} -> localhost:${APP_PORT}"
  fi
}

cmd_keeper() {
  log "keeper started"
  while true; do
    ensure_mariadb
    ensure_php
    ensure_ngrok
    sleep 15
  done
}

case "${1:-start}" in
  install) cmd_install ;;
  start)   cmd_start ;;
  keeper)  cmd_keeper ;;
  *) echo "usage: $0 {install|start|keeper}" >&2; exit 2 ;;
esac
