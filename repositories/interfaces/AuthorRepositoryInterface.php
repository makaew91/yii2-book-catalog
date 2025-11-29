<?php

namespace app\repositories\interfaces;

use app\models\Author;
use yii\db\ActiveQuery;

/**
 * Интерфейс репозитория авторов
 * Dependency Inversion: контроллеры и сервисы зависят от интерфейса, а не от конкретной реализации
 */
interface AuthorRepositoryInterface
{
    public function findById(int $id): ?Author;
    
    public function findAll(): array;
    
    public function getQuery(): ActiveQuery;
    
    public function save(Author $author): bool;
    
    public function delete(Author $author): bool;
    
    public function findByIds(array $ids): array;
    
    public function getTopAuthorsByYear(int $year, int $limit = 10): array;
}

