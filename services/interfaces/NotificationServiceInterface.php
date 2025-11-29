<?php

namespace app\services\interfaces;

use app\models\Book;

/**
 * Интерфейс сервиса уведомлений
 */
interface NotificationServiceInterface
{
    public function notifyNewBook(Book $book): void;
}

