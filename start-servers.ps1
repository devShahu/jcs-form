# Start both backend and frontend servers

Write-Host "Starting JCS Form Application..." -ForegroundColor Green
Write-Host ""

# Check if PHP is installed
try {
    $phpVersion = php -v 2>&1 | Select-String "PHP" | Select-Object -First 1
    Write-Host "✓ PHP found: $phpVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ PHP not found. Please install PHP 8.0 or higher." -ForegroundColor Red
    exit 1
}

# Check if Node is installed
try {
    $nodeVersion = node -v
    Write-Host "✓ Node found: $nodeVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Node not found. Please install Node.js 18 or higher." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Starting servers..." -ForegroundColor Yellow
Write-Host ""

# Start PHP backend server in background
Write-Host "Starting PHP backend on http://localhost:8000..." -ForegroundColor Cyan
Start-Process powershell -ArgumentList "-NoExit", "-Command", "php -S localhost:8000 -t ." -WindowStyle Normal

# Wait a moment for PHP server to start
Start-Sleep -Seconds 2

# Test if backend is running
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/health" -UseBasicParsing -TimeoutSec 5
    Write-Host "✓ Backend API is running" -ForegroundColor Green
} catch {
    Write-Host "✗ Backend API failed to start" -ForegroundColor Red
    Write-Host "  Check if port 8000 is already in use" -ForegroundColor Yellow
}

Write-Host ""

# Start frontend dev server
Write-Host "Starting React frontend on http://localhost:3000..." -ForegroundColor Cyan
Write-Host ""
Write-Host "Press Ctrl+C to stop the frontend server" -ForegroundColor Yellow
Write-Host "Close the PHP server window to stop the backend" -ForegroundColor Yellow
Write-Host ""

npm run dev
