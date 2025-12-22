<?php

namespace OmniPOS\Services;

class UploadService
{
    protected string $uploadDir;
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    protected int $maxSize = 5242880; // 5MB

    public function __construct(string $subDir = 'products')
    {
        $this->uploadDir = __DIR__ . '/../../public/uploads/' . $subDir . '/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > $this->maxSize) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            return null;
        }

        $filename = uniqid() . '.' . $ext;
        $target = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $filename;
        }

        return null;
    }

    public function delete(?string $filename): void
    {
        if ($filename && file_exists($this->uploadDir . $filename)) {
            unlink($this->uploadDir . $filename);
        }
    }
}
