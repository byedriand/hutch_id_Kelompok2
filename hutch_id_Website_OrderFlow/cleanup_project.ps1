$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $projectRoot

$paths = @(
    '.\build',
    '.\.dart_tool',
    '.\.flutter-plugins',
    '.\.flutter-plugins-dependencies',
    '.\.packages',
    '.\.pub-cache',
    '.\.pub',
    '.\android\.gradle',
    '.\android\app\build',
    '.\ios\Flutter\ephemeral',
    '.\macos\Flutter\ephemeral',
    '.\linux\flutter\ephemeral',
    '.\windows\flutter\ephemeral',
    '.\web\.dart_tool',
    '.\storage\framework\cache',
    '.\storage\framework\views',
    '.\storage\framework\sessions',
    '.\storage\logs',
    '.\bootstrap\cache',
    '.\public\build'
)

foreach ($path in $paths) {
    if (Test-Path $path) {
        Remove-Item $path -Recurse -Force -ErrorAction SilentlyContinue
        Write-Host "Removed: $path"
    }
}

New-Item -ItemType Directory -Path '.\bootstrap\cache', '.\storage\framework\cache', '.\storage\framework\views', '.\storage\framework\sessions', '.\storage\logs' -Force | Out-Null

if (Test-Path '.\artisan') {
    php artisan config:clear | Out-Null
    php artisan route:clear | Out-Null
    php artisan view:clear | Out-Null
    php artisan cache:clear | Out-Null
    php artisan config:cache | Out-Null
    php artisan route:cache | Out-Null
    php artisan view:cache | Out-Null
}

Write-Host 'Cleanup selesai.'
