<?php

namespace app\services;

use app\models\Author;
use app\repositories\interfaces\AuthorRepositoryInterface;
use Yii;

/**
 * Сервис для работы с авторами
 */
class AuthorService
{
    private AuthorRepositoryInterface $authorRepository;

    public function __construct(AuthorRepositoryInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * Создать автора
     */
    public function create(Author $author): bool
    {
        try {
            return $this->authorRepository->save($author);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Обновить автора
     */
    public function update(Author $author): bool
    {
        try {
            return $this->authorRepository->save($author);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Удалить автора
     */
    public function delete(Author $author): bool
    {
        try {
            return $this->authorRepository->delete($author);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Получить ТОП авторов по году
     */
    public function getTopAuthorsByYear(int $year, int $limit = 10): array
    {
        return $this->authorRepository->getTopAuthorsByYear($year, $limit);
    }
}

