<?php

namespace app\controllers\api;

use app\models\SubscriptionForm;
use app\services\SubscriptionService;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\Response;

/**
 * REST API контроллер для подписок
 */
class SubscriptionController extends Controller
{
    private SubscriptionService $subscriptionService;

    public function __construct(
        string $id,
        $module,
        SubscriptionService $subscriptionService,
        array $config = []
    ) {
        $this->subscriptionService = $subscriptionService;
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
     * POST /api/subscription
     * Подписка на автора (доступно всем)
     */
    public function actionCreate(): array
    {
        $form = new SubscriptionForm();
        $form->load(Yii::$app->request->post(), '');

        if ($this->subscriptionService->subscribe($form)) {
            Yii::$app->response->statusCode = 201;
            return [
                'success' => true,
                'message' => 'Подписка успешно создана',
            ];
        }

        Yii::$app->response->statusCode = 422;
        return [
            'success' => false,
            'errors' => $form->errors,
        ];
    }
}