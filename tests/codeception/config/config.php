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
        'db' => require(__DIR__ . '/../../../config/dbtest.php'),
        'urlManager' => [
            'enablePrettyUrl' => false,
        ],
    ],
    'params' => require(__DIR__ . '/../../../config/params.php'),
];