<?php

namespace app\services;

use app\services\interfaces\FileUploadServiceInterface;
use Yii;
use yii\web\UploadedFile;

/**
 * Сервис загрузки файлов
 */
class FileUploadService implements FileUploadServiceInterface
{
    private string $uploadPath;

    public function __construct(string $uploadPath = '@webroot/uploads/covers/')
    {
        $this->uploadPath = Yii::getAlias($uploadPath);
    }

    /**
     * Загрузить файл обложки
     */
    public function uploadCover(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        $fileName = uniqid('cover_') . '.' . $file->extension;
        $filePath = $this->uploadPath . $fileName;

        if ($file->saveAs($filePath)) {
            return '/uploads/covers/' . $fileName;
        }

        return null;
    }

    /**
     * Удалить файл
     */
    public function deleteFile(string $relativePath): bool
    {
        $fullPath = Yii::getAlias('@webroot') . $relativePath;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}