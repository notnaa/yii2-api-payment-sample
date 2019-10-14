<?php

use app\models\User;

return [
    [
        'id' => 1,
        'status' => User::STATUS_ACTIVE,
        'email' => 'test1@test.ru',
        'password' => '$2y$13$d17z0w/wKC4LFwtzBcmx6up4jErQuandJqhzKGKczfWuiEhLBtQBK',
        'username' => 'with rub wallet',
    ],
    [
        'id' => 2,
        'status' => User::STATUS_ACTIVE,
        'email' => 'test2@test.ru',
        'password' => '$2y$13$d17z0w/wKC4LFwtzBcmx6up4jErQuandJqhzKGKczfWuiEhLBtQBK',
        'username' => 'with usd wallet',
    ],
    [
        'id' => 3,
        'status' => User::STATUS_INACTIVE,
        'email' => 'test3@test.ru',
        'password' => '$2y$13$d17z0w/wKC4LFwtzBcmx6up4jErQuandJqhzKGKczfWuiEhLBtQBK',
        'username' => 'without wallets',
    ],
];
