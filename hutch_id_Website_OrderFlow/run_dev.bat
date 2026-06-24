@echo off
REM =================================================================
REM Hutch Website & Mobile App - Start Script
REM =================================================================
REM This script helps you run the website and mobile app together
REM =================================================================

echo.
echo ========================================
echo Hutch Indonesia - Web & Mobile Setup
echo ========================================
echo.
echo Choose what you want to run:
echo.
echo 1. Run Website (Docker) - http://localhost:8082/
echo 2. Run Mobile App (Flutter) - Android
echo 3. Run Both (Website + Mobile in separate terminals)
echo 4. Setup Only (no run)
echo.
set /p choice="Enter your choice (1-4): "

if "%choice%"=="1" (
    echo.
    echo Starting Docker containers for website...
    echo Website will be available at: http://localhost:8082/
    echo.
    call docker-compose up
) else if "%choice%"=="2" (
    echo.
    echo Starting Flutter Mobile App...
    echo Make sure Docker is running website at http://localhost:8082/
    echo.
    call flutter run
) else if "%choice%"=="3" (
    echo.
    echo Starting Website in new terminal...
    start cmd /k "docker-compose up"
    
    timeout /t 5 /nobreak
    
    echo.
    echo Starting Flutter Mobile App...
    call flutter run
) else if "%choice%"=="4" (
    echo.
    echo Setup completed! You can now:
    echo.
    echo For Website:
    echo   docker-compose up
    echo.
    echo For Mobile:
    echo   flutter run
    echo.
) else (
    echo Invalid choice!
)
