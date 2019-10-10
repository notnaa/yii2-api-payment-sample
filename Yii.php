<?php

use yii\base\Security;
use yii\BaseYii;
use yii\swiftmailer\Mailer;

/**
 * Class Yii
 */
class Yii extends BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

/**
 * Class BaseApplication
 * @property \frontend\components\web\UrlManager $urlManagerFrontend $urlManagerFrontend
 */
abstract class BaseApplication extends yii\base\Application
{

}

/**
 * Class WebApplication
 * @property \common\components\web\User $user
 * @property Mailer $mailer
 * @property Security $security
 */
class WebApplication extends yii\web\Application
{

}

/**
 * Class ConsoleApplication
 */
class ConsoleApplication extends yii\console\Application
{

}
