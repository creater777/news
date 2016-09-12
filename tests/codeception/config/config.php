<?php
/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-test',
    'language' => 'en-US',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'fixtureDataPath' => '@tests/codeception/fixtures',
            'templatePath' => '@tests/codeception/templates',
            'namespace' => 'tests\codeception\fixtures',
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'app\models\User',
            'loginUrl' => ['access/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'db' => require(__DIR__ . '/../../../config/dbtest.php'),
        'authManager' => [
            'class' => 'yii\rbac\DBManager',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
    ],
];