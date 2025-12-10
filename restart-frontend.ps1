# Restart Frontend Script

Write-Host "Stopping any existing Node processes on port 3000..." -ForegroundColor Yellow

# Find and kill process on port 3000
$process = Get-NetTCPConnection -LocalPort 3000 -ErrorAction SilentlyContinue | Select-Object -First 1
if ($process) {
    $pid = $process.OwningProcess
    Write-Host "Found process $pid on port 3000, stopping it..." -ForegroundColor Yellow
    Stop-Process -Id $pid -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
}

Write-Host "Starting frontend server..." -ForegroundColor Green
Write-Host "This will open in a new window. Keep it running." -ForegroundColor Cyan
Write-Host ""
Write-Host "Once started, open your browser to: http://localhost:3000" -ForegroundColor Green
Write-Host ""

# Start npm in a new window
Start-Process powershell -ArgumentList "-NoExit", "-Command", "npm run dev"

Write-Host "Frontend server starting in new window..." -ForegroundColor Green
Write-Host "Wait a few seconds, then open: http://localhost:3000" -ForegroundColor Cyan
