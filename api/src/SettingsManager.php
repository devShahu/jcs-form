<?php

namespace App;

class SettingsManager
{
    private string $settingsFile;
    private string $logoDir;
    private array $settings;

    public function __construct()
    {
        $this->settingsFile = __DIR__ . '/../../storage/settings.json';
        $this->logoDir = __DIR__ . '/../../public/images';
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        if (file_exists($this->settingsFile)) {
            $json = file_get_contents($this->settingsFile);
            $this->settings = json_decode($json, true) ?? $this->getDefaultSettings();
        } else {
            $this->settings = $this->getDefaultSettings();
        }
    }

    private function getDefaultSettings(): array
    {
        return [
            'org_name_bn' => 'জাতীয় ছাত্রশক্তি',
            'org_name_en' => 'Jatiya Chhatra Shakti',
            'logo_path' => '/images/logo.png',
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    public function updateSettings(array $newSettings): bool
    {
        try {
            // Merge with existing settings
            $this->settings = array_merge($this->settings, $newSettings);
            $this->settings['updated_at'] = date('Y-m-d H:i:s');

            // Ensure storage directory exists
            $dir = dirname($this->settingsFile);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Save to file
            $json = json_encode($this->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($this->settingsFile, $json) !== false;
        } catch (\Exception $e) {
            error_log('Settings save error: ' . $e->getMessage());
            return false;
        }
    }

    public function uploadLogo($uploadedFile): array
    {
        try {
            // Validate file
            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                throw new \Exception('File upload error');
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $uploadedFile->getClientMediaType();
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new \Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
            }

            // Validate file size (max 2MB)
            if ($uploadedFile->getSize() > 2 * 1024 * 1024) {
                throw new \Exception('File too large. Maximum size is 2MB.');
            }

            // Ensure logo directory exists
            if (!is_dir($this->logoDir)) {
                mkdir($this->logoDir, 0755, true);
            }

            // Generate filename with timestamp to avoid caching
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $timestamp = time();
            $filename = 'logo_' . $timestamp . '.' . $extension;
            $filepath = $this->logoDir . '/' . $filename;

            // Delete old logo files
            $oldFiles = glob($this->logoDir . '/logo_*.*');
            foreach ($oldFiles as $oldFile) {
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }

            // Move uploaded file
            $uploadedFile->moveTo($filepath);

            // Update settings
            $logoPath = '/images/' . $filename;
            $this->updateSettings(['logo_path' => $logoPath]);

            return [
                'success' => true,
                'path' => $logoPath,
                'message' => 'Logo uploaded successfully'
            ];
        } catch (\Exception $e) {
            error_log('Logo upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getLogoPath(): string
    {
        $logoPath = $this->getSetting('logo_path', '/images/logo.png');
        $fullPath = __DIR__ . '/../../public' . $logoPath;
        
        // Return path if file exists, otherwise return default
        if (file_exists($fullPath)) {
            return $fullPath;
        }
        
        return __DIR__ . '/../../public/images/logo.png';
    }
}
