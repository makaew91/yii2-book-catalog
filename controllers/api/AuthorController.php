<?php

namespace app\controllers\api;

use app\models\Author;
use app\repositories\interfaces\AuthorRepositoryInterface;
use app\services\AuthorService;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * REST API контроллер для авторов
 */
class AuthorController extends ActiveController
{
    public $modelClass = Author::class;
    
    private AuthorService $authorService;
    private AuthorRepositoryInterface $authorRepository;

    public function __construct(
        string $id,
        $module,
        AuthorService $authorService,
        AuthorRepositoryInterface $authorRepository,
        array $config = []
    ) {
        $this->authorService = $authorService;
        $this->authorRepository = $authorRepository;
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];

        return $behaviors;
    }

    public function actions(): array
    {
        $actions = parent::actions();
        
        unset($actions['create'], $actions['update'], $actions['delete']);
        
        return $actions;
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionCreate(): array|Author
    {
        $this->checkAuth();

        $author = new Author();
        $author->load(Yii::$app->request->post(), '');

        if ($this->authorService->create($author)) {
            Yii::$app->response->statusCode = 201;
            return $author;
        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $author->errors];
    }

    public function actionUpdate(int $id): array|Author
    {
        $this->checkAuth();

        $author = $this->findAuthor($id);
        $author->load(Yii::$app->request->getBodyParams(), '');

        if ($this->authorService->update($author)) {
            return $author;
        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $author->errors];
    }

    /**
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): array|null
    {
        $this->checkAuth();

        $author = $this->findAuthor($id);

        if ($this->authorService->delete($author)) {
            Yii::$app->response->statusCode = 204;
            return null;
        }

        Yii::$app->response->statusCode = 500;
        return ['error' => 'Ошибка при удалении'];
    }

    /**
     * Найти автора или выбросить 404
     * @throws NotFoundHttpException
     */
    private function findAuthor(int $id): Author
    {
        $author = $this->authorRepository->findById($id);
        if (!$author) {
            throw new NotFoundHttpException('Автор не найден');
        }
        return $author;
    }

    /**
     * @throws ForbiddenHttpException
     */
    private function checkAuth(): void
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Требуется аутентификация');
        }
    }
}