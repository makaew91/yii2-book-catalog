<?php

namespace app\controllers\api;

use app\models\Book;
use app\repositories\interfaces\BookRepositoryInterface;
use app\services\BookService;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * REST API контроллер для книг
 */
class BookController extends ActiveController
{
    public $modelClass = Book::class;
    
    private BookService $bookService;
    private BookRepositoryInterface $bookRepository;

    public function __construct(
        string $id,
        $module,
        BookService $bookService,
        BookRepositoryInterface $bookRepository,
        array $config = []
    ) {
        $this->bookService = $bookService;
        $this->bookRepository = $bookRepository;
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

    protected function verbs(): array
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionCreate(): array|Book
    {
        $this->checkAuth();

        $book = new Book();
        $book->load(Yii::$app->request->post(), '');
        $book->coverFile = UploadedFile::getInstanceByName('coverFile');

        if ($this->bookService->create($book)) {
            Yii::$app->response->statusCode = 201;
            return $book;
        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $book->errors];
    }

    /**
     * @throws InvalidConfigException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): array|Book
    {
        $this->checkAuth();

        $book = $this->findBook($id);
        $book->load(Yii::$app->request->getBodyParams(), '');
        $book->coverFile = UploadedFile::getInstanceByName('coverFile');

        if ($this->bookService->update($book)) {
            return $book;
        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $book->errors];
    }

    /**
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): array|null
    {
        $this->checkAuth();

        $book = $this->findBook($id);

        if ($this->bookService->delete($book)) {
            Yii::$app->response->statusCode = 204;
            return null;
        }

        Yii::$app->response->statusCode = 500;
        return ['error' => 'Ошибка при удалении'];
    }

    /**
     * Найти книгу или выбросить 404
     * @throws NotFoundHttpException
     */
    private function findBook(int $id): Book
    {
        $book = $this->bookRepository->findById($id);
        if (!$book) {
            throw new NotFoundHttpException('Книга не найдена');
        }
        return $book;
    }

    /**
     * Проверка аутентификации для CRUD операций
     * @throws ForbiddenHttpException
     */
    private function checkAuth(): void
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Требуется аутентификация');
        }
    }
}

