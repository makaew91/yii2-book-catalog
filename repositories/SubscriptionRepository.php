<?php

namespace app\repositories;

use app\models\AuthorSubscription;
use app\repositories\interfaces\SubscriptionRepositoryInterface;

/**
 * Репозиторий для работы с подписками
 * Single Responsibility: только доступ к данным подписок
 * Interface Segregation: реализует интерфейс с минимальным набором методов
 */
class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    /**
     * Проверить существование подписки
     */
    public function exists(int $authorId, string $phone): bool
    {
        return AuthorSubscription::find()
            ->where(['author_id' => $authorId, 'phone' => $phone])
            ->exists();
    }

    /**
     * Создать подписку
     */
    public function create(int $authorId, string $phone): ?AuthorSubscription
    {
        $subscription = new AuthorSubscription();
        $subscription->author_id = $authorId;
        $subscription->phone = $phone;

        return $subscription->save() ? $subscription : null;
    }

    /**
     * Получить подписки по ID авторов
     */
    public function findByAuthorIds(array $authorIds): array
    {
        return AuthorSubscription::find()
            ->where(['author_id' => $authorIds])
            ->with('author')
            ->all();
    }
}

