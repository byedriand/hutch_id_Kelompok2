# ============================================================
# build-push.ps1 — Build Docker image & Push ke Docker Hub
# Jalankan dari PowerShell di direktori project:
#   .\build-push.ps1
#
# Pastikan sudah login Docker Hub:
#   docker login
# ============================================================

param(
    [string]$Username  = "",          # Docker Hub username kamu
    [string]$ImageName = "hutch-app", # nama image di Docker Hub
    [string]$Version   = "latest"     # tag version, bisa "1.0.0", "latest", dll
)

# ── Warna output ──────────────────────────────────────────────────────────
function Write-Step  { param($msg) Write-Host "`n[STEP] $msg" -ForegroundColor Cyan }
function Write-OK    { param($msg) Write-Host "[OK]   $msg" -ForegroundColor Green }
function Write-Warn  { param($msg) Write-Host "[WARN] $msg" -ForegroundColor Yellow }
function Write-Fail  { param($msg) Write-Host "[FAIL] $msg" -ForegroundColor Red; exit 1 }

# ── Validasi username ─────────────────────────────────────────────────────
if ([string]::IsNullOrEmpty($Username)) {
    $Username = Read-Host "Masukkan Docker Hub username kamu"
}
if ([string]::IsNullOrEmpty($Username)) {
    Write-Fail "Username tidak boleh kosong."
}

$FullImageName = "$Username/$ImageName"
$TagLatest     = "${FullImageName}:latest"
$TagVersion    = "${FullImageName}:${Version}"

Write-Host ""
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  Hutch.id — Build & Push ke Docker Hub"    -ForegroundColor Magenta
Write-Host "============================================" -ForegroundColor Magenta
Write-Host "  Image   : $FullImageName"
Write-Host "  Tags    : $TagLatest  /  $TagVersion"
Write-Host "  Platform: linux/amd64 (untuk VPS Linux)"
Write-Host ""

# ── Cek Docker login ──────────────────────────────────────────────────────
Write-Step "Mengecek status Docker login..."
$loginCheck = docker info 2>&1 | Select-String "Username"
if (-not $loginCheck) {
    Write-Warn "Belum login ke Docker Hub."
    Write-Host "Jalankan: docker login" -ForegroundColor Yellow
    docker login
}
Write-OK "Docker login OK."

# ── Build image ───────────────────────────────────────────────────────────
Write-Step "Building Docker image..."
Write-Host "  Ini mungkin butuh 3-10 menit (download base image + composer install + npm build)..."
Write-Host ""

# Build untuk platform linux/amd64 (standar VPS Linux)
# --platform linux/amd64 penting jika kamu build di Mac ARM atau Windows ARM
docker buildx build `
    --platform linux/amd64 `
    --tag $TagLatest `
    --tag $TagVersion `
    --file Dockerfile `
    --push `
    .

if ($LASTEXITCODE -ne 0) {
    Write-Fail "Build gagal! Cek error di atas."
}

Write-OK "Build & push berhasil!"
Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  Image berhasil di-push:"                   -ForegroundColor Green
Write-Host "    docker pull $TagLatest"                  -ForegroundColor Green
Write-Host "    docker pull $TagVersion"                 -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Langkah selanjutnya — di VPS:"              -ForegroundColor Cyan
Write-Host "  1. Copy 3 file ke VPS (lihat instruksi di bawah)"
Write-Host "  2. Set DOCKERHUB_USERNAME=$Username di .env.production.docker"
Write-Host "  3. docker compose -f docker-compose.prod.yml up -d"
Write-Host ""
Write-Host "3 file yang harus ada di VPS:"              -ForegroundColor Yellow
Write-Host "  /opt/hutch/docker-compose.prod.yml"
Write-Host "  /opt/hutch/.env.production.docker"
Write-Host "  /opt/hutch/docker/nginx/production.conf"
