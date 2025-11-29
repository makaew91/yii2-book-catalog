<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'container' => [
        'singletons' => [
            // Repositories
            \app\repositories\interfaces\AuthorRepositoryInterface::class => \app\repositories\AuthorRepository::class,
            \app\repositories\interfaces\BookRepositoryInterface::class => \app\repositories\BookRepository::class,
            \app\repositories\interfaces\SubscriptionRepositoryInterface::class => \app\repositories\SubscriptionRepository::class,
            
            // Services
            \app\services\interfaces\SmsServiceInterface::class => \app\services\SmsService::class,
            \app\services\interfaces\FileUploadServiceInterface::class => \app\services\FileUploadService::class,
            \app\services\interfaces\NotificationServiceInterface::class => [
                'class' => \app\services\NotificationService::class,
                '__construct()' => [
                    'subscriptionRepository' => \yii\di\Instance::of(\app\repositories\interfaces\SubscriptionRepositoryInterface::class),
                    'smsService' => \yii\di\Instance::of(\app\services\interfaces\SmsServiceInterface::class),
                ],
            ],
            \app\services\BookService::class => [
                'class' => \app\services\BookService::class,
                '__construct()' => [
                    'bookRepository' => \yii\di\Instance::of(\app\repositories\interfaces\BookRepositoryInterface::class),
                    'fileUploadService' => \yii\di\Instance::of(\app\services\interfaces\FileUploadServiceInterface::class),
                    'notificationService' => \yii\di\Instance::of(\app\services\interfaces\NotificationServiceInterface::class),
                ],
            ],
            \app\services\AuthorService::class => [
                'class' => \app\services\AuthorService::class,
                '__construct()' => [
                    'authorRepository' => \yii\di\Instance::of(\app\repositories\interfaces\AuthorRepositoryInterface::class),
                ],
            ],
            \app\services\SubscriptionService::class => [
                'class' => \app\services\SubscriptionService::class,
                '__construct()' => [
                    'subscriptionRepository' => \yii\di\Instance::of(\app\repositories\interfaces\SubscriptionRepositoryInterface::class),
                ],
            ],
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'eoAY71fAjzvnAJ4nM2mvg8gZLDlbJWBt',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/book'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/author'],
                'POST api/subscription' => 'api/subscription/create',
                'GET api/report/top-authors' => 'api/report/top-authors',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
