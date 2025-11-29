<?php

namespace app\controllers\api;

use app\services\AuthorService;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\Response;

/**
 * REST API контроллер для отчётов
 */
class ReportController extends Controller
{
    private AuthorService $authorService;

    public function __construct(
        string $id,
        $module,
        AuthorService $authorService,
        array $config = []
    ) {
        $this->authorService = $authorService;
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

    /**
     * GET /api/report/top-authors?year=2024
     * ТОП-10 авторов за выбранный год (доступно всем)
     */
    public function actionTopAuthors(): array
    {
        $year = (int)Yii::$app->request->get('year', date('Y'));
        $authors = $this->authorService->getTopAuthorsByYear($year);

        return [
            'year' => $year,
            'authors' => $authors,
        ];
    }
}

