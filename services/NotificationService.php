<?php

namespace app\services;

use app\models\Book;
use app\repositories\interfaces\SubscriptionRepositoryInterface;
use app\services\interfaces\NotificationServiceInterface;
use app\services\interfaces\SmsServiceInterface;
use Yii;

/**
 * Сервис уведомлений
 */
class NotificationService implements NotificationServiceInterface
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private SmsServiceInterface $smsService;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        SmsServiceInterface $smsService
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->smsService = $smsService;
    }

    /**
     * Уведомить подписчиков о новой книге
     */
    public function notifyNewBook(Book $book): void
    {
        if (empty($book->authorIds)) {
            return;
        }

        $subscriptions = $this->subscriptionRepository->findByAuthorIds($book->authorIds);

        foreach ($subscriptions as $subscription) {
            $message = $this->buildNewBookMessage($book, $subscription->author->full_name);
            $this->smsService->send($subscription->phone, $message);
        }

        Yii::info(
            sprintf('Sent %d notifications for book "%s"', count($subscriptions), $book->title),
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

