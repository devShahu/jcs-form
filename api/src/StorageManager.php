<?php

namespace App;

class StorageManager
{
    private string $storagePath;
    private string $submissionsPath;
    private string $photosPath;

    public function __construct()
    {
        $this->storagePath = __DIR__ . '/../../storage';
        $this->submissionsPath = $this->storagePath . '/submissions';
        $this->photosPath = $this->storagePath . '/photos';

        $this->ensureDirectories();
    }

    private function ensureDirectories(): void
    {
        $dirs = [
            $this->storagePath,
            $this->submissionsPath,
            $this->photosPath,
            $this->storagePath . '/temp'
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public function saveSubmission(array $data, string $pdfContent): string
    {
        // Generate submission ID
        $submissionId = 'JCS_' . date('YmdHis') . '_' . substr(md5(uniqid()), 0, 8);

        // Create year/month directory
        $yearMonth = date('Y/m');
        $targetDir = $this->submissionsPath . '/' . $yearMonth;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Save PDF
        $pdfFilename = $submissionId . '.pdf';
        $pdfPath = $targetDir . '/' . $pdfFilename;
        file_put_contents($pdfPath, $pdfContent);

        // Save JSON data
        $jsonFilename = $submissionId . '.json';
        $jsonPath = $targetDir . '/' . $jsonFilename;
        
        $submissionData = [
            'submission_id' => $submissionId,
            'submitted_at' => date('Y-m-d H:i:s'),
            'data' => $this->sanitizeForStorage($data),
            'pdf_path' => $pdfPath,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];

        file_put_contents($jsonPath, json_encode($submissionData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $submissionId;
    }

    public function savePDF(string $content, string $filename): string
    {
        $yearMonth = date('Y/m');
        $targetDir = $this->submissionsPath . '/' . $yearMonth;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filePath = $targetDir . '/' . $filename;
        file_put_contents($filePath, $content);

        return $filePath;
    }

    public function savePhoto(string $photoData): string
    {
        $yearMonth = date('Y/m');
        $targetDir = $this->photosPath . '/' . $yearMonth;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = 'photo_' . date('YmdHis') . '_' . uniqid() . '.jpg';
        $filePath = $targetDir . '/' . $filename;

        // Handle base64 encoded image
        if (preg_match('/^data:image\/(jpeg|png|jpg);base64,(.+)$/', $photoData, $matches)) {
            $imageData = base64_decode($matches[2]);
            file_put_contents($filePath, $imageData);
        }

        return $filePath;
    }

    public function getSubmission(string $id): ?array
    {
        // Search for submission in year/month directories
        $years = glob($this->submissionsPath . '/*', GLOB_ONLYDIR);
        
        foreach ($years as $yearDir) {
            $months = glob($yearDir . '/*', GLOB_ONLYDIR);
            
            foreach ($months as $monthDir) {
                $jsonPath = $monthDir . '/' . $id . '.json';
                
                if (file_exists($jsonPath)) {
                    $content = file_get_contents($jsonPath);
                    return json_decode($content, true);
                }
            }
        }

        return null;
    }

    public function getPDF(string $id): ?string
    {
        // Search for PDF in year/month directories
        $years = glob($this->submissionsPath . '/*', GLOB_ONLYDIR);
        
        foreach ($years as $yearDir) {
            $months = glob($yearDir . '/*', GLOB_ONLYDIR);
            
            foreach ($months as $monthDir) {
                $pdfPath = $monthDir . '/' . $id . '.pdf';
                
                if (file_exists($pdfPath)) {
                    return $pdfPath;
                }
            }
        }

        return null;
    }

    public function listSubmissions(int $page = 1, int $limit = 20): array
    {
        $submissions = $this->getAllSubmissions();

        // Sort by date (newest first)
        usort($submissions, function($a, $b) {
            return strtotime($b['submitted_at']) - strtotime($a['submitted_at']);
        });

        // Paginate
        $total = count($submissions);
        $offset = ($page - 1) * $limit;
        $items = array_slice($submissions, $offset, $limit);

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    public function searchSubmissions(string $query, ?string $dateFrom, ?string $dateTo, int $page = 1, int $limit = 20): array
    {
        $submissions = $this->getAllSubmissions();
        $filtered = [];

        foreach ($submissions as $submission) {
            $matches = false;

            // Search in name, NID, mobile number
            if (!empty($query)) {
                $searchFields = [
                    $submission['name'] ?? '',
                    $submission['name_english'] ?? '',
                    $submission['nid'] ?? '',
                    $submission['mobile'] ?? ''
                ];

                foreach ($searchFields as $field) {
                    if (stripos($field, $query) !== false) {
                        $matches = true;
                        break;
                    }
                }

                if (!$matches) {
                    continue;
                }
            }

            // Filter by date range
            if ($dateFrom && strtotime($submission['submitted_at']) < strtotime($dateFrom)) {
                continue;
            }

            if ($dateTo && strtotime($submission['submitted_at']) > strtotime($dateTo . ' 23:59:59')) {
                continue;
            }

            $filtered[] = $submission;
        }

        // Sort by date (newest first)
        usort($filtered, function($a, $b) {
            return strtotime($b['submitted_at']) - strtotime($a['submitted_at']);
        });

        // Paginate
        $total = count($filtered);
        $offset = ($page - 1) * $limit;
        $items = array_slice($filtered, $offset, $limit);

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    private function getAllSubmissions(): array
    {
        $submissions = [];
        $years = glob($this->submissionsPath . '/*', GLOB_ONLYDIR);
        
        foreach ($years as $yearDir) {
            $months = glob($yearDir . '/*', GLOB_ONLYDIR);
            
            foreach ($months as $monthDir) {
                $jsonFiles = glob($monthDir . '/*.json');
                
                foreach ($jsonFiles as $jsonFile) {
                    $content = file_get_contents($jsonFile);
                    $data = json_decode($content, true);
                    
                    if ($data) {
                        $submissions[] = [
                            'submission_id' => $data['submission_id'],
                            'submitted_at' => $data['submitted_at'],
                            'name' => $data['data']['name_bangla'] ?? 'N/A',
                            'name_english' => $data['data']['name_english'] ?? '',
                            'nid' => $data['data']['nid_birth_reg'] ?? '',
                            'mobile' => $data['data']['mobile_number'] ?? '',
                            'email' => $data['data']['email'] ?? ''
                        ];
                    }
                }
            }
        }

        return $submissions;
    }

    private function sanitizeForStorage(array $data): array
    {
        // Remove sensitive data or encrypt if needed
        $sanitized = $data;
        
        // Remove photo data (already saved separately)
        if (isset($sanitized['photo'])) {
            $sanitized['photo'] = '[PHOTO_SAVED_SEPARATELY]';
        }

        return $sanitized;
    }
}
