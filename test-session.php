<?php
// Test script to verify session configuration

// Configure session for cross-origin
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 'false');
ini_set('session.cookie_httponly', 'true');
ini_set('session.cookie_lifetime', '86400');

// Start session
session_start();

// Set test data
$_SESSION['test'] = 'Session is working!';
$_SESSION['timestamp'] = time();

// Output session info
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');

echo json_encode([
    'success' => true,
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'cookie_params' => session_get_cookie_params(),
    'session_status' => session_status(),
    'message' => 'Session test successful'
]);
