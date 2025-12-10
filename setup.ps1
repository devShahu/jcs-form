# JCS Form Filler - Setup Script for Windows
# This script helps set up the development environment

Write-Host "=== JCS Membership Form - Setup Script ===" -ForegroundColor Cyan
Write-Host ""

# Check PHP
Write-Host "Checking PHP..." -ForegroundColor Yellow
try {
    $phpVersion = php -v 2>&1 | Select-String -Pattern "PHP (\d+\.\d+)" | ForEach-Object { $_.Matches.Groups[1].Value }
    if ($phpVersion) {
        Write-Host "✓ PHP $phpVersion found" -ForegroundColor Green
    } else {
        Write-Host "✗ PHP not found. Please install PHP 8.0 or higher." -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "✗ PHP not found. Please install PHP 8.0 or higher." -ForegroundColor Red
    exit 1
}

# Check Composer
Write-Host "Checking Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version 2>&1 | Select-String -Pattern "Composer version (\d+\.\d+)" | ForEach-Object { $_.Matches.Groups[1].Value }
    if ($composerVersion) {
        Write-Host "✓ Composer $composerVersion found" -ForegroundColor Green
    } else {
        Write-Host "✗ Composer not found. Please install Composer." -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "✗ Composer not found. Please install Composer." -ForegroundColor Red
    exit 1
}

# Check Node.js
Write-Host "Checking Node.js..." -ForegroundColor Yellow
try {
    $nodeVersion = node -v 2>&1
    if ($nodeVersion) {
        Write-Host "✓ Node.js $nodeVersion found" -ForegroundColor Green
    } else {
        Write-Host "✗ Node.js not found. Please install Node.js 18 or higher." -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "✗ Node.js not found. Please install Node.js 18 or higher." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Installing dependencies..." -ForegroundColor Cyan

# Install PHP dependencies
Write-Host "Installing PHP dependencies with Composer..." -ForegroundColor Yellow
composer install
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ PHP dependencies installed" -ForegroundColor Green
} else {
    Write-Host "✗ Failed to install PHP dependencies" -ForegroundColor Red
    exit 1
}

# Install Node dependencies
Write-Host "Installing Node.js dependencies with npm..." -ForegroundColor Yellow
npm install
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Node.js dependencies installed" -ForegroundColor Green
} else {
    Write-Host "✗ Failed to install Node.js dependencies" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Checking Bengali fonts..." -ForegroundColor Yellow
$nikoshExists = Test-Path "fonts\Nikosh.ttf"
$notoExists = Test-Path "fonts\NotoSansBengali-Regular.ttf"

if (-not $nikoshExists -or -not $notoExists) {
    Write-Host "Bengali fonts not found. Attempting to download..." -ForegroundColor Yellow
    
    if (-not $nikoshExists) {
        try {
            Write-Host "Downloading Nikosh.ttf..." -ForegroundColor Yellow
            Invoke-WebRequest -Uri "https://github.com/potasiyam/Bangla-Fonts/raw/master/Nikosh.ttf" -OutFile "fonts\Nikosh.ttf"
            Write-Host "✓ Nikosh.ttf downloaded" -ForegroundColor Green
        } catch {
            Write-Host "✗ Failed to download Nikosh.ttf. Please download manually." -ForegroundColor Red
        }
    }
    
    if (-not $notoExists) {
        try {
            Write-Host "Downloading NotoSansBengali-Regular.ttf..." -ForegroundColor Yellow
            Invoke-WebRequest -Uri "https://github.com/notofonts/bengali/raw/main/fonts/NotoSansBengali/hinted/ttf/NotoSansBengali-Regular.ttf" -OutFile "fonts\NotoSansBengali-Regular.ttf"
            Write-Host "✓ NotoSansBengali-Regular.ttf downloaded" -ForegroundColor Green
        } catch {
            Write-Host "✗ Failed to download NotoSansBengali-Regular.ttf. Please download manually." -ForegroundColor Red
        }
    }
} else {
    Write-Host "✓ Bengali fonts found" -ForegroundColor Green
}

Write-Host ""
Write-Host "=== Setup Complete ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "To start development:" -ForegroundColor Yellow
Write-Host "  1. Start the backend:  php -S localhost:8000 -t api" -ForegroundColor White
Write-Host "  2. Start the frontend: npm run dev" -ForegroundColor White
Write-Host ""
Write-Host "The frontend will be available at: http://localhost:3000" -ForegroundColor Green
Write-Host "The API will be available at: http://localhost:8000/api" -ForegroundColor Green
Write-Host ""
