<?php

namespace app\repositories;

use app\models\Author;
use app\repositories\interfaces\AuthorRepositoryInterface;
use yii\db\ActiveQuery;

/**
 * Репозиторий для работы с авторами
 * Single Responsibility: только доступ к данным авторов
 * Interface Segregation: реализует интерфейс с минимальным набором методов
 */
class AuthorRepository implements AuthorRepositoryInterface
{
    /**
     * Найти автора по ID
     */
    public function findById(int $id): ?Author
    {
        return Author::findOne($id);
    }

    /**
     * Получить всех авторов
     */
    public function findAll(): array
    {
        return Author::find()->orderBy(['full_name' => SORT_ASC])->all();
    }

    /**
     * Получить query для авторов
     */
    public function getQuery(): ActiveQuery
    {
        return Author::find();
    }

    /**
     * Сохранить автора
     */
    public function save(Author $author): bool
    {
        return $author->save();
    }

    /**
     * Удалить автора
     */
    public function delete(Author $author): bool
    {
        return (bool)$author->delete();
    }

    /**
     * Получить авторов по IDs
     */
    public function findByIds(array $ids): array
    {
        return Author::find()->where(['id' => $ids])->all();
    }

    /**
     * Получить авторов с количеством книг за год
     */
    public function getTopAuthorsByYear(int $year, int $limit = 10): array
    {
        return Author::find()
            ->select(['{{%author}}.*', 'COUNT({{%book}}.id) as book_count'])
            ->innerJoinWith('books')
            ->where(['{{%book}}.year' => $year])
            ->groupBy('{{%author}}.id')
            ->orderBy(['book_count' => SORT_DESC])
            ->limit($limit)
            ->asArray()
            ->all();
    }
}

