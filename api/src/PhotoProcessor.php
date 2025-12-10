<?php

namespace App;

use Intervention\Image\ImageManagerStatic as Image;
use Psr\Http\Message\UploadedFileInterface;

class PhotoProcessor
{
    private string $storagePath;
    private int $maxWidth = 800;
    private int $maxHeight = 1000;
    private int $quality = 85;

    public function __construct()
    {
        $this->storagePath = __DIR__ . '/../../storage/photos';
        
        // Create storage directory if it doesn't exist
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }

        // Configure Intervention Image
        Image::configure(['driver' => 'gd']);
    }

    public function process(UploadedFileInterface $file): array
    {
        // Validate
        $this->validate($file);

        // Generate unique filename
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $filename = 'photo_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
        
        // Create year/month directory structure
        $yearMonth = date('Y/m');
        $targetDir = $this->storagePath . '/' . $yearMonth;
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $filename;

        // Move uploaded file to temp location
        $tempPath = sys_get_temp_dir() . '/' . $filename;
        $file->moveTo($tempPath);

        // Process image
        $image = Image::make($tempPath);
        
        // Resize if needed
        if ($image->width() > $this->maxWidth || $image->height() > $this->maxHeight) {
            $image->resize($this->maxWidth, $this->maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Save processed image
        $image->save($targetPath, $this->quality);

        // Clean up temp file
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        return [
            'path' => $targetPath,
            'url' => '/storage/photos/' . $yearMonth . '/' . $filename,
            'filename' => $filename
        ];
    }

    public function validate(UploadedFileInterface $file): void
    {
        // Check if file was uploaded
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new \Exception('File upload error');
        }

        // Check file size (5MB max)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('File size exceeds 5MB limit');
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getClientMediaType(), $allowedTypes)) {
            throw new \Exception('Invalid file type. Only JPEG and PNG are allowed');
        }
    }

    public function resize(string $path, int $width, int $height): string
    {
        $image = Image::make($path);
        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        
        $newPath = pathinfo($path, PATHINFO_DIRNAME) . '/resized_' . pathinfo($path, PATHINFO_BASENAME);
        $image->save($newPath, $this->quality);
        
        return $newPath;
    }

    public function compress(string $path, int $quality): string
    {
        $image = Image::make($path);
        $image->save($path, $quality);
        return $path;
    }
}
