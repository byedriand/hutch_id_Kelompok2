#!/bin/bash
# =================================================================
# Hutch Website & Mobile App - Start Script (Linux/Mac)
# =================================================================
# This script helps you run the website and mobile app together
# =================================================================

echo ""
echo "========================================"
echo "Hutch Indonesia - Web & Mobile Setup"
echo "========================================"
echo ""
echo "Choose what you want to run:"
echo ""
echo "1. Run Website (Docker) - http://localhost:8082/"
echo "2. Run Mobile App (Flutter) - Android"
echo "3. Run Both (Website + Mobile in separate terminals)"
echo "4. Setup Only (no run)"
echo ""
read -p "Enter your choice (1-4): " choice

case $choice in
    1)
        echo ""
        echo "Starting Docker containers for website..."
        echo "Website will be available at: http://localhost:8082/"
        echo ""
        docker-compose up
        ;;
    2)
        echo ""
        echo "Starting Flutter Mobile App..."
        echo "Make sure Docker is running website at http://localhost:8082/"
        echo ""
        flutter run
        ;;
    3)
        echo ""
        echo "Starting Website in new terminal..."
        open -a Terminal "docker-compose up"
        
        sleep 5
        
        echo ""
        echo "Starting Flutter Mobile App..."
        flutter run
        ;;
    4)
        echo ""
        echo "Setup completed! You can now:"
        echo ""
        echo "For Website:"
        echo "  docker-compose up"
        echo ""
        echo "For Mobile:"
        echo "  flutter run"
        echo ""
        ;;
    *)
        echo "Invalid choice!"
        ;;
esac
