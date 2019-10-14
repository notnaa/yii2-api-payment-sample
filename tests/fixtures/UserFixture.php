<?php

namespace app\tests\fixtures;

use app\models\User;
use yii\test\ActiveFixture;

/**
 * Class UserFixture
 *
 * @package app\tests\fixtures
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = User::class;
}
