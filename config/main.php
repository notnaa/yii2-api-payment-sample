<?php

use yii\helpers\ArrayHelper;

$params = array_merge(
    require __DIR__ . '/params-local.php',
    require __DIR__ . '/params.php'
);

$config = [
    'id' => 'api-payment-sample',
    'name' => 'Api Payment Sample',
    'language' => 'en_US',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'container' => ArrayHelper::merge(
        require 'container.php',
        is_file(__DIR__ . '/container-local.php') ? require __DIR__ . '/container-local.php' : []
    ),
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'templateFile' => '@app/migrations/views/migration.php',
        ],
    ],
    'params' => $params,
];

return $config;
