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
        
        // Configure session for cross-origin requests
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_samesite', 'None');
            ini_set('session.cookie_secure', 'false'); // Set to 'true' in production with HTTPS
            ini_set('session.cookie_httponly', 'true');
            ini_set('session.cookie_lifetime', '86400'); // 24 hours
        }
        
        // Load admin credentials from config
        $config = require __DIR__ . '/../../config/admin.php';
        $this->adminUsername = $config['username'] ?? 'admin';
        $this->adminPasswordHash = $config['password_hash'] ?? password_hash('admin123', PASSWORD_DEFAULT);
    }

    public function login(array $request): array
    {
        $username = $request['username'] ?? '';
        $password = $request['password'] ?? '';

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

        // Generate session token
        session_start();
        $token = bin2hex(random_bytes(32));
        $_SESSION['admin_token'] = $token;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_login_time'] = time();

        return [
            'success' => true,
            'token' => $token,
            'username' => $username
        ];
    }

    public function logout(): array
    {
        session_start();
        session_destroy();

        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }

    public function verifyAuth(): bool
    {
        session_start();
        
        if (!isset($_SESSION['admin_token']) || !isset($_SESSION['admin_login_time'])) {
            return false;
        }

        // Check session timeout (24 hours)
        $sessionTimeout = 24 * 60 * 60;
        if (time() - $_SESSION['admin_login_time'] > $sessionTimeout) {
            session_destroy();
            return false;
        }

        return true;
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
