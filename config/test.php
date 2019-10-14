<?php

use yii\helpers\ArrayHelper;

return [
    'id' => 'api-payment-sample-tests',
    'basePath' => dirname(__DIR__),
    'container' => ArrayHelper::merge(
        require 'container.php',
        is_file(__DIR__ . '/container-local.php') ? require __DIR__ . '/container-local.php' : [],
        [
            'singletons' => [
                \app\services\exchange\AbstractExchangeService::class => [
                    'isTest' => true,
                ],
            ],
        ]
    ),
];
