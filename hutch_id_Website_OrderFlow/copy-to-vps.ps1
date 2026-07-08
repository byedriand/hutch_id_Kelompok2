# ============================================================
# copy-to-vps.ps1 — Copy 3 file yang dibutuhkan ke VPS
# Jalankan dari PowerShell di direktori project:
#   .\copy-to-vps.ps1 -VpsIp "xxx.xxx.xxx.xxx"
# ============================================================

param(
    [string]$VpsIp   = "",
    [string]$VpsUser = "root",
    [string]$VpsDir  = "/opt/hutch"
)

function Write-Step { param($msg) Write-Host "`n[STEP] $msg" -ForegroundColor Cyan }
function Write-OK   { param($msg) Write-Host "[OK]   $msg" -ForegroundColor Green }
function Write-Fail { param($msg) Write-Host "[FAIL] $msg" -ForegroundColor Red; exit 1 }

if ([string]::IsNullOrEmpty($VpsIp)) {
    $VpsIp = Read-Host "Masukkan IP VPS kamu"
}

$VpsTarget = "${VpsUser}@${VpsIp}"

Write-Host ""
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  Hutch.id — Copy files ke VPS"             -ForegroundColor Magenta
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  VPS    : $VpsTarget"
Write-Host "  Dir    : $VpsDir"
Write-Host ""
Write-Host "File yang akan di-copy:"
Write-Host "  docker-compose.prod.yml"
Write-Host "  .env.production.docker"
Write-Host "  docker/nginx/production.conf"
Write-Host ""

# ── Buat direktori di VPS ─────────────────────────────────────────────────
Write-Step "Membuat direktori $VpsDir di VPS..."
ssh "${VpsTarget}" "mkdir -p ${VpsDir}/docker/nginx"
Write-OK "Direktori siap."

# ── Copy docker-compose.prod.yml ─────────────────────────────────────────
Write-Step "Copy docker-compose.prod.yml..."
scp docker-compose.prod.yml "${VpsTarget}:${VpsDir}/docker-compose.prod.yml"
Write-OK "docker-compose.prod.yml ter-copy."

# ── Copy .env.production.docker ──────────────────────────────────────────
Write-Step "Copy .env.production.docker..."
scp .env.production.docker "${VpsTarget}:${VpsDir}/.env.production.docker"
Write-OK ".env.production.docker ter-copy."

# ── Copy nginx production.conf ───────────────────────────────────────────
Write-Step "Copy docker/nginx/production.conf..."
scp docker/nginx/production.conf "${VpsTarget}:${VpsDir}/docker/nginx/production.conf"
Write-OK "production.conf ter-copy."

# ── Selesai ───────────────────────────────────────────────────────────────
Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  Semua file berhasil di-copy ke VPS!"      -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Langkah selanjutnya — SSH ke VPS:" -ForegroundColor Cyan
Write-Host "  ssh $VpsTarget"
Write-Host "  cd $VpsDir"
Write-Host ""
Write-Host "  # Edit .env jika belum lengkap:"
Write-Host "  nano .env.production.docker"
Write-Host ""
Write-Host "  # Setup SSL (jika belum):"
Write-Host "  apt-get install -y certbot"
Write-Host "  certbot certonly --standalone -d hutch-prestige.my.id"
Write-Host ""
Write-Host "  # Deploy:"
Write-Host "  docker compose -f docker-compose.prod.yml --env-file .env.production.docker up -d"
Write-Host ""
Write-Host "  # Cek status:"
Write-Host "  docker ps | grep hutch"
Write-Host "  docker logs hutch_app --tail 50"
