<?php

namespace app\repositories;

use app\models\Book;
use app\repositories\interfaces\BookRepositoryInterface;
use yii\db\ActiveQuery;

/**
 * Репозиторий для работы с книгами
 * Single Responsibility: только доступ к данным книг
 * Interface Segregation: реализует интерфейс с минимальным набором методов
 */
class BookRepository implements BookRepositoryInterface
{
    /**
     * Найти книгу по ID
     */
    public function findById(int $id): ?Book
    {
        return Book::findOne($id);
    }

    /**
     * Получить все книги
     */
    public function findAll(): array
    {
        return Book::find()->with('authors')->orderBy(['created_at' => SORT_DESC])->all();
    }

    /**
     * Получить query для книг
     */
    public function getQuery(): ActiveQuery
    {
        return Book::find();
    }

    /**
     * Сохранить книгу
     */
    public function save(Book $book): bool
    {
        return $book->save();
    }

    /**
     * Удалить книгу
     */
    public function delete(Book $book): bool
    {
        return (bool)$book->delete();
    }

    /**
     * Синхронизировать авторов книги
     */
    public function syncAuthors(Book $book, array $authorIds): void
    {
        // Удаляем старые связи
        \Yii::$app->db->createCommand()
            ->delete('{{%book_author}}', ['book_id' => $book->id])
            ->execute();

        // Добавляем новые связи
        if (!empty($authorIds)) {
            $rows = [];
            foreach ($authorIds as $authorId) {
                $rows[] = [$book->id, $authorId];
            }
            \Yii::$app->db->createCommand()
                ->batchInsert('{{%book_author}}', ['book_id', 'author_id'], $rows)
                ->execute();
        }
    }

    /**
     * Получить ID авторов книги
     */
    public function getAuthorIds(Book $book): array
    {
        return $book->getAuthors()->select('id')->column();
    }
}

