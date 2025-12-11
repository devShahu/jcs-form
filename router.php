<?php
/**
 * Router script for PHP built-in server
 * Routes API requests to api/index.php and serves static files
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Health check at root level
if ($path === '/health') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'ok',
        'message' => 'Server is running',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Route API requests to api/index.php
if (strpos($path, '/api') === 0) {
    require __DIR__ . '/api/index.php';
    return true;
}

// Serve static files from public directory (built frontend)
$publicPath = __DIR__ . '/public' . $path;
if ($path !== '/' && file_exists($publicPath) && is_file($publicPath)) {
    return false; // Let PHP serve the file
}

// For SPA routing, serve index.html from public directory
$indexPath = __DIR__ . '/public/index.html';
if (file_exists($indexPath)) {
    readfile($indexPath);
    return true;
}

// Fallback to root index.html (development)
if (file_exists(__DIR__ . '/index.html')) {
    return false;
}

http_response_code(404);
echo 'Not Found';
