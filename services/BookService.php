<?php

namespace app\services;

use app\models\Book;
use app\repositories\interfaces\BookRepositoryInterface;
use app\services\interfaces\FileUploadServiceInterface;
use app\services\interfaces\NotificationServiceInterface;
use Yii;
use yii\db\Exception;

/**
 * Сервис для работы с книгами
 */
class BookService
{
    private BookRepositoryInterface $bookRepository;
    private FileUploadServiceInterface $fileUploadService;
    private NotificationServiceInterface $notificationService;

    public function __construct(
        BookRepositoryInterface $bookRepository,
        FileUploadServiceInterface $fileUploadService,
        NotificationServiceInterface $notificationService
    ) {
        $this->bookRepository = $bookRepository;
        $this->fileUploadService = $fileUploadService;
        $this->notificationService = $notificationService;
    }

    /**
     * Создание книги с авторами
     */
    public function create(Book $book): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->bookRepository->save($book)) {
                throw new Exception('Failed to save book');
            }

            $this->handleCoverUpload($book);
            $this->syncAuthors($book);
            $this->notificationService->notifyNewBook($book);

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Обновление книги с авторами
     */
    public function update(Book $book): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->bookRepository->save($book)) {
                throw new Exception('Failed to update book');
            }

            $this->handleCoverUpload($book);
            $this->syncAuthors($book);

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Удаление книги
     */
    public function delete(Book $book): bool
    {
        try {
            // Удаляем обложку если есть
            if ($book->cover_image_path) {
                $this->fileUploadService->deleteFile($book->cover_image_path);
            }

            return $this->bookRepository->delete($book);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Загрузка обложки
     */
    private function handleCoverUpload(Book $book): void
    {
        if (!$book->coverFile) {
            return;
        }

        $newPath = $this->fileUploadService->uploadCover($book->coverFile);
        if ($newPath) {
            // Удаляем старую обложку
            if ($book->cover_image_path) {
                $this->fileUploadService->deleteFile($book->cover_image_path);
            }

            $book->cover_image_path = $newPath;
            $this->bookRepository->save($book);
        }
    }

    /**
     * Синхронизация авторов книги
     */
    private function syncAuthors(Book $book): void
    {
        if (!empty($book->authorIds)) {
            $this->bookRepository->syncAuthors($book, $book->authorIds);
        }
    }
}

