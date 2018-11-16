<?php

if (YII_ENV == "dev") {
    $params = require(__DIR__ . '/params_test.php');
} else {
    $params = require(__DIR__ . '/params.php');
}

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Shanghai',
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\admin',
        ],
        'lottery' => [
            'class' => 'app\modules\lottery\lottery',
        ],
        'index' => [
            'class' => 'app\modules\index\index',
        ],
        'trading' => [
            'class' => 'app\modules\trading\trading',
        ],
        'member' => [
            'class' => 'app\modules\member\member',
        ],
        'agents' => [
            'class' => 'app\modules\agents\agents',
        ],
        'tools' => [
            'class' => 'app\modules\tools\tools',
        ],
        'pyspider' => [
            'class' => 'app\modules\pyspider\pyspider',
        ],
        'expert' => [
            'class' => 'app\modules\expert\expert',
        ],
        'website' => [
            'class' => 'app\modules\website\website',
        ],
        'subagents' => [
            'class' => 'app\modules\subagents\subagents',
        ],
        'promote' => [
            'class' => 'app\modules\promote\promote',
        ],
        'cron' => [
            'class' => 'app\modules\cron\cron',
        ],
        'channel' => [
            'class' => 'app\modules\channel\channel',
        ],
         'subchannel' => [
            'class' => 'app\modules\subchannel\subchannel',
        ],
        'report' => [
            'class' => 'app\modules\report\report',
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'goodluck2017',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'text/json' => 'yii\web\JsonParser',
                'application/xml' => 'yii\web\XmlParser',
                'text/xml' => 'yii\web\XmlParser',
            ],
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
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
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
        'db' => require(__DIR__ . '/db.php'),
    	'db2' => require(__DIR__ . '/db2.php'),
        'redis' => require(__DIR__ . '/redis.php'),
        'redis2' => require(__DIR__ . '/redis2.php'),
        /*
          'urlManager' => [
          'enablePrettyUrl' => true,
          'showScriptName' => false,
          'rules' => [
          ],
          ],
         */
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            // Disable index.php
            'showScriptName' => false,
            // Disable r= routes
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'rules' => [
//                 '<modules:\w+>/<controller:\w+>/<action:\w+>/<code:\w+>'=>'<modules>/<controller>/<action>',
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
            'allowedIPs' => ['127.0.0.1', '::1','211.149.205.*','27.154.231.158','27.155.105.*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            'allowedIPs' => ['127.0.0.1', '::1','211.149.205.*','27.154.231.158','27.155.105.*'],
    ];
}

return $config;
