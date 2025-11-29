<?php

namespace app\services;

use app\jobs\SendSmsJob;
use app\models\Book;
use app\repositories\interfaces\SubscriptionRepositoryInterface;
use app\services\interfaces\NotificationServiceInterface;
use Yii;
use yii\queue\Queue;

/**
 * Сервис уведомлений
 * Отправляет задачи в очередь RabbitMQ для асинхронной обработки
 */
class NotificationService implements NotificationServiceInterface
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private Queue $queue;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->queue = Yii::$app->queue;
    }

    /**
     * Уведомить подписчиков о новой книге (асинхронно через очередь)
     */
    public function notifyNewBook(Book $book): void
    {
        if (empty($book->authorIds)) {
            return;
        }

        $subscriptions = $this->subscriptionRepository->findByAuthorIds($book->authorIds);
        $jobsCount = 0;

        foreach ($subscriptions as $subscription) {
            $message = $this->buildNewBookMessage($book, $subscription->author->full_name);
            
            // Отправляем задачу в очередь
            $this->queue->push(new SendSmsJob([
                'phone' => $subscription->phone,
                'message' => $message,
            ]));
            
            $jobsCount++;
        }

        Yii::info(
            sprintf('Queued %d SMS notifications for book "%s"', $jobsCount, $book->title),
            __METHOD__
        );
    }

    /**
     * Построить сообщение о новой книге
     */
    private function buildNewBookMessage(Book $book, string $authorName): string
    {
        return sprintf(
            'Новая книга "%s" (%d) автора %s',
            $book->title,
            $book->year,
            $authorName
        );
    }
}

