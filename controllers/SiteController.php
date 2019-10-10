<?php

namespace app\controllers;

use app\components\rest\AbstractController;

/**
 * Class SiteController
 *
 * @package app\controllers
 */
class SiteController extends AbstractController
{
    /**
     * @return array
     */
    public function actionError()
    {
        $this->addMessage('Bad request');
        $this->fail();

        return $this->responseData;
    }
}
