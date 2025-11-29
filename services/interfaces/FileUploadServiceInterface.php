<?php

namespace app\services\interfaces;

use yii\web\UploadedFile;

/**
 * Интерфейс сервиса загрузки файлов
 */
interface FileUploadServiceInterface
{
    public function uploadCover(?UploadedFile $file): ?string;
    
    public function deleteFile(string $relativePath): bool;
}

