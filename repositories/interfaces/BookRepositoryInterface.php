<?php

namespace app\repositories\interfaces;

use app\models\Book;
use yii\db\ActiveQuery;

/**
 * Интерфейс репозитория книг
 * Dependency Inversion: контроллеры и сервисы зависят от интерфейса, а не от конкретной реализации
 */
interface BookRepositoryInterface
{
    public function findById(int $id): ?Book;
    
    public function findAll(): array;
    
    public function getQuery(): ActiveQuery;
    
    public function save(Book $book): bool;
    
    public function delete(Book $book): bool;
    
    public function syncAuthors(Book $book, array $authorIds): void;
    
    public function getAuthorIds(Book $book): array;
}

