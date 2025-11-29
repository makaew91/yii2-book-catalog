<?php

namespace app\repositories\interfaces;

use app\models\AuthorSubscription;

/**
 * Интерфейс репозитория подписок
 * Dependency Inversion: контроллеры и сервисы зависят от интерфейса, а не от конкретной реализации
 */
interface SubscriptionRepositoryInterface
{
    public function exists(int $authorId, string $phone): bool;
    
    public function create(int $authorId, string $phone): ?AuthorSubscription;
    
    public function findByAuthorIds(array $authorIds): array;
}

