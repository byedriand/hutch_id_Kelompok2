#!/bin/bash
# ============================================================
# deploy-vps.sh — Hutch.id VPS Deployment Script
# Jalankan ini di VPS Ubuntu kamu sebagai root atau sudo user.
# Domain  : hutch-prestige.my.id
# ============================================================

set -e

# ── Warna terminal ────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; NC='\033[0m'

info()    { echo -e "${CYAN}[INFO]${NC} $1"; }
success() { echo -e "${GREEN}[OK]${NC} $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

DOMAIN="hutch-prestige.my.id"
APP_DIR="/opt/hutch"

# ============================================================
# LANGKAH 1 — Siapkan direktori di VPS
# ============================================================
info "Membuat direktori aplikasi: ${APP_DIR}"
mkdir -p "${APP_DIR}"

# ============================================================
# LANGKAH 2 — Copy file project ke VPS
# ============================================================
# Jalankan perintah ini dari LOKAL (Windows) kamu menggunakan SCP:
#
#   scp -r . root@<IP_VPS>:/opt/hutch/
#
# Atau bisa pakai rsync untuk skip file besar:
#
#   rsync -avz --exclude='.git' --exclude='vendor' --exclude='node_modules' \
#     --exclude='n8n' --exclude='*.apk' --exclude='android' --exclude='ios' \
#     . root@<IP_VPS>:/opt/hutch/
#
# ── Jika sudah di VPS, lanjut dari sini ──────────────────────────────────

cd "${APP_DIR}" || error "Direktori ${APP_DIR} tidak ditemukan"

# ============================================================
# LANGKAH 3 — Buat .env.production.docker dari template
# ============================================================
if [ ! -f ".env.production.docker" ]; then
    warn ".env.production.docker belum ada!"
    echo "Salin template:"
    echo "  cp .env.production.docker.example .env.production.docker"
    echo "  nano .env.production.docker"
    echo "Lalu isi APP_KEY, DB_PASSWORD, FONNTE_API_TOKEN, dll."
    echo ""
    error "Buat .env.production.docker dulu sebelum lanjut."
fi

# Load env untuk validasi
source <(grep -E '^(APP_KEY|DB_PASSWORD)=' .env.production.docker 2>/dev/null || true)

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "<GANTI_INI_DENGAN_APP_KEY>" ]; then
    error "APP_KEY belum diisi di .env.production.docker! Generate dengan:\n  php artisan key:generate --show"
fi

success ".env.production.docker terdeteksi."

# ============================================================
# LANGKAH 4 — Pastikan Certbot & SSL sudah ada (atau skip dulu)
# ============================================================
info "Mengecek SSL certificate..."

if [ ! -d "/etc/letsencrypt/live/${DOMAIN}" ]; then
    warn "SSL certificate untuk ${DOMAIN} belum ada."
    echo ""
    echo "Opsi A — Jalankan Certbot (butuh port 80 free sementara):"
    echo "  # Pastikan DNS A record hutch-prestige.my.id sudah mengarah ke IP VPS ini"
    echo "  apt-get install -y certbot"
    echo "  certbot certonly --standalone -d ${DOMAIN} -d www.${DOMAIN}"
    echo ""
    echo "Opsi B — Pakai HTTP dulu (tanpa SSL, ganti port 443 ke 8090):"
    echo "  Edit docker-compose.prod.yml, ganti ports nginx ke:"
    echo "    - \"8090:80\""
    echo "  Dan edit docker/nginx/production.conf pakai config HTTP-only."
    echo ""
    read -rp "Lanjut tanpa SSL? (y/N): " SKIP_SSL
    if [[ "$SKIP_SSL" != "y" && "$SKIP_SSL" != "Y" ]]; then
        error "Setup SSL dulu dengan Certbot, lalu jalankan script ini lagi."
    fi
    warn "Melanjutkan tanpa SSL (mode HTTP sementara)..."
else
    success "SSL certificate ditemukan: /etc/letsencrypt/live/${DOMAIN}/"
fi

# ============================================================
# LANGKAH 5 — Build Docker image
# ============================================================
info "Building Docker image hutch_app..."
docker build -t hutch_app:latest .
success "Docker image hutch_app:latest berhasil dibuild."

# ============================================================
# LANGKAH 6 — Jalankan stack
# ============================================================
info "Starting Hutch.id stack..."
docker compose -f docker-compose.prod.yml --env-file .env.production.docker up -d
success "Stack running!"

# ============================================================
# LANGKAH 7 — Verifikasi
# ============================================================
sleep 5
info "Status container:"
docker compose -f docker-compose.prod.yml ps

echo ""
info "Cek log hutch_app:"
docker logs hutch_app --tail 30

echo ""
success "============================================"
success " Hutch.id berhasil di-deploy!"
success " HTTP  : http://${DOMAIN}:8090"
success " HTTPS : https://${DOMAIN}"
success " Admin : https://${DOMAIN}/login"
success "============================================"
echo ""
echo "Perintah berguna:"
echo "  docker logs hutch_app -f              # Live log app"
echo "  docker logs hutch_nginx -f            # Live log nginx"
echo "  docker exec hutch_app php artisan ...  # Artisan commands"
echo "  docker compose -f docker-compose.prod.yml restart  # Restart semua"
echo "  docker compose -f docker-compose.prod.yml down     # Stop semua"
