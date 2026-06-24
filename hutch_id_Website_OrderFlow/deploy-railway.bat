@echo off
REM Railway Deploy Script for Hutch Indonesia
REM This script prepares and deploys the Laravel app to Railway

echo ========================================
echo Hutch Indonesia - Railway Deployment
echo ========================================
echo.

REM Step 1: Check git status
echo [1/6] Checking git status...
git status
echo.

REM Step 2: Add all changes
echo [2/6] Staging all changes...
git add .
echo ✓ Files staged
echo.

REM Step 3: Commit
echo [3/6] Creating commit...
set /p COMMIT_MSG="Enter commit message (default: 'Deploy to Railway'): "
if "%COMMIT_MSG%"=="" set COMMIT_MSG=Deploy to Railway
git commit -m "%COMMIT_MSG%"
echo ✓ Committed
echo.

REM Step 4: Check remote
echo [4/6] Checking GitHub remote...
git remote -v
echo.

REM Step 5: Push to GitHub
echo [5/6] Pushing to GitHub...
set /p BRANCH="Enter branch name (default: main): "
if "%BRANCH%"=="" set BRANCH=main
git push origin %BRANCH%
echo ✓ Pushed to GitHub
echo.

REM Step 6: Railway deployment info
echo [6/6] Next steps for Railway deployment:
echo.
echo 1. Go to: https://railway.app
echo 2. Sign in with GitHub
echo 3. Create new project from GitHub repository
echo 4. Add MySQL service
echo 5. Configure environment variables:
echo    - APP_KEY (from: php artisan key:generate --show)
echo    - DB_HOST, DB_USER, DB_PASSWORD (auto from MySQL service)
echo 6. Run migrations: railway run php artisan migrate --force
echo 7. Copy public URL from Railway dashboard
echo.
echo ✓ Deployment script complete!
echo.
pause
