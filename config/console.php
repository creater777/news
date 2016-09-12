    <?php
use yii\console\controllers\MigrateController;
Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\controllers',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'dbtest' => require(__DIR__ . '/dbtest.php'),
        'authManager' => [
            'class' => 'yii\rbac\DBManager',
        ],
    ],
    'params' => $params,
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => '@app/migrations/db'
        ],
        'migrate-test' => [
            'class' => 'yii\console\controllers\MigrateController',
            'db' => 'dbtest',
            'migrationPath' => '@app/migrations/test'
        ],
        'rbac-test' => [
            'class' => 'yii\rbac\DBManager',
            'db' => 'dbtest',
        ],
    ],    
];

return $config;
