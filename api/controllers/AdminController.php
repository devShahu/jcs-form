<?php

namespace App\Controllers;

use App\StorageManager;

class AdminController
{
    private StorageManager $storage;
    private string $adminUsername;
    private string $adminPasswordHash;

    public function __construct()
    {
        $this->storage = new StorageManager();
        
        // Start session if not already started
        $this->initSession();
        
        // Load admin credentials from config
        $config = require __DIR__ . '/../../config/admin.php';
        $this->adminUsername = $config['username'] ?? 'admin';
        $this->adminPasswordHash = $config['password_hash'] ?? password_hash('admin123', PASSWORD_DEFAULT);
    }

    private function initSession(): void
    {
        // Session is already started in api/index.php
        // This is kept for safety in case controller is used elsewhere
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 86400,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    public function login(array $request): array
    {
        $username = $request['username'] ?? '';
        $password = $request['password'] ?? '';
        
        error_log("Login attempt for user: $username");

        // Validate credentials
        if ($username !== $this->adminUsername) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        if (!password_verify($password, $this->adminPasswordHash)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }

        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Generate session token
        $token = bin2hex(random_bytes(32));
        $_SESSION['admin_token'] = $token;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_login_time'] = time();
        
        // Also save token to file (fallback for when cookies don't work)
        $tokenFile = __DIR__ . '/../../storage/admin_tokens.json';
        $tokens = [];
        if (file_exists($tokenFile)) {
            $tokens = json_decode(file_get_contents($tokenFile), true) ?: [];
        }
        // Clean old tokens
        $tokens = array_filter($tokens, fn($t) => time() - $t['created_at'] < 86400);
        // Add new token
        $tokens[$token] = [
            'username' => $username,
            'created_at' => time()
        ];
        file_put_contents($tokenFile, json_encode($tokens));

        return [
            'success' => true,
            'token' => $token,
            'username' => $username
        ];
    }

    public function logout(): array
    {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();

        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }

    public function verifyAuth(): bool
    {
        // First try session-based auth
        if (isset($_SESSION['admin_token']) && isset($_SESSION['admin_login_time'])) {
            // Check session timeout (24 hours)
            $sessionTimeout = 24 * 60 * 60;
            if (time() - $_SESSION['admin_login_time'] > $sessionTimeout) {
                session_destroy();
                return false;
            }
            return true;
        }
        
        // Fallback: Check for token in header (for when cookies don't work)
        $headerToken = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? null;
        if ($headerToken) {
            // Verify token from file-based storage
            $tokenFile = __DIR__ . '/../../storage/admin_tokens.json';
            if (file_exists($tokenFile)) {
                $tokens = json_decode(file_get_contents($tokenFile), true) ?: [];
                if (isset($tokens[$headerToken])) {
                    $tokenData = $tokens[$headerToken];
                    // Check token timeout (24 hours)
                    if (time() - $tokenData['created_at'] < 86400) {
                        return true;
                    }
                    // Token expired, remove it
                    unset($tokens[$headerToken]);
                    file_put_contents($tokenFile, json_encode($tokens));
                }
            }
        }
        
        return false;
    }

    public function listSubmissions(array $params): array
    {
        if (!$this->verifyAuth()) {
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }

        $page = (int)($params['page'] ?? 1);
        $limit = (int)($params['limit'] ?? 20);

        try {
            $submissions = $this->storage->listSubmissions($page, $limit);
            
            return [
                'success' => true,
                'data' => $submissions['items'],
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $submissions['total'],
                    'pages' => ceil($submissions['total'] / $limit)
                ]
            ];
        } catch (\Exception $e) {
            error_log('Error listing submissions: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error retrieving submissions'
            ];
        }
    }

    public function getSubmission(string $id): array
    {
        if (!$this->verifyAuth()) {
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }

        try {
            $submission = $this->storage->getSubmission($id);
            
            if (!$submission) {
                return [
                    'success' => false,
                    'message' => 'Submission not found'
                ];
            }

            return [
                'success' => true,
                'data' => $submission
            ];
        } catch (\Exception $e) {
            error_log('Error getting submission: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error retrieving submission'
            ];
        }
    }

    public function downloadPDF(string $id): array
    {
        if (!$this->verifyAuth()) {
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }

        try {
            $pdfPath = $this->storage->getPDF($id);
            
            if (!$pdfPath || !file_exists($pdfPath)) {
                return [
                    'success' => false,
                    'message' => 'PDF not found'
                ];
            }

            return [
                'success' => true,
                'path' => $pdfPath,
                'filename' => basename($pdfPath)
            ];
        } catch (\Exception $e) {
            error_log('Error downloading PDF: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error retrieving PDF'
            ];
        }
    }

    public function searchSubmissions(array $params): array
    {
        if (!$this->verifyAuth()) {
            return [
                'success' => false,
                'message' => 'Unauthorized'
            ];
        }

        $query = $params['query'] ?? '';
        $dateFrom = $params['date_from'] ?? null;
        $dateTo = $params['date_to'] ?? null;
        $page = (int)($params['page'] ?? 1);
        $limit = (int)($params['limit'] ?? 20);

        try {
            $results = $this->storage->searchSubmissions($query, $dateFrom, $dateTo, $page, $limit);
            
            return [
                'success' => true,
                'data' => $results['items'],
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $results['total'],
                    'pages' => ceil($results['total'] / $limit)
                ]
            ];
        } catch (\Exception $e) {
            error_log('Error searching submissions: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error searching submissions'
            ];
        }
    }
}
