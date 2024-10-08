<?php

use yii\filters\Cors;
use yii\filters\RateLimiter;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'duaWo_dBVX4RiwANHLRgKhfzRLWwqdRa',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'name' => 'cart',
            'timeout' => 3600,
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 1,
            ],
        ],
//        'cache' => [
//            'class' => 'yii\caching\FileCache',
//        ],
//        'cache' => [
//            'class' => 'yii\caching\MemCache',
//            'servers' => [
//                [
//                    'host' => 'server1',
//                    'port' => 11211,
//                    'weight' => 100,
//                ],
//                [
//                    'host' => 'server2',
//                    'port' => 11211,
//                    'weight' => 50,
//                ],
//            ],
//        ],

        'user' => [
            'identityClass' => 'app\modules\v1\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
//        'mailer' => [
//            'class' => \yii\symfonymailer\Mailer::class,
//            'viewPath' => '@app/mail',
//            // send all mails to a file by default.
//            'useFileTransport' => true,
//        ],
//        'queue' => [
//            'class' => \yii\queue\file\Queue::class,
//            'path' => '@runtime/queue',
//        ],

        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db',
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\MysqlMutex::class,
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'encryption' => 'tls',
                'host' => 'smtp.gmail.com',
                'port' => '587',
                'username' => 'kissuot6@gmail.com',
                'password' => 'aopo elpa bfyy tdgp',
            ],
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logFile' => '@runtime/logs/app.log',
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'weather' => [
            'class' => 'app\components\WeatherComponent',
            'apiKey' => env('WEATHER_API_KEY'),
            'apiUrl' => env('WEATHER_URL')
        ],
        'zaloPay' => [
            'class' => 'app\components\ZaloPayComponent',
            'key1' => env('ZALO_PAY_KEY_1'),
            'key2' => env('ZALO_PAY_KEY_2'),
            'appId' => env('ZALO_PAY_APP_ID'),
            'endpoint' => env('ZALO_PAY_END_POINT'),
        ],

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],

    ],
    'as rateLimiter' => [
        'class' => RateLimiter::class,
    ],
//    'modules' => [
//        'api' => app\modules\Module::class
//    ],
    'modules' => [
        'api' => [
            'class' => 'yii\base\Module',
            'modules' => [
                'v1' => [
                    'class' => 'app\modules\v1\Module',
                ],
            ],
            'as corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                ],
            ],
        ],

    ],
    // 'modules' => [
    //     'v1' => [
    //         'class' => 'app\modules\v1\Module',
    //     ],
    //     'v2' => [
    //         'class' => 'app\modules\v2\Module',
    //     ],
    // ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
