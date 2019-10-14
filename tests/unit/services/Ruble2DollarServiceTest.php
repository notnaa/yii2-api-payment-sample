<?php

use app\services\exchange\Ruble2DollarService;
use Codeception\Test\Unit;

/**
 * Class Ruble2DollarServiceTest
 */
class Ruble2DollarServiceTest extends Unit
{
    /** @var UnitTester */
    protected $tester;

    /** @var Ruble2DollarService */
    private $service;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function _before()
    {
        $this->service = \Yii::$container->get(Ruble2DollarService::class);
    }

    /**
     * @throws Exception
     */
    public function testConvertInt()
    {
        $result = $this->service->convert(1000);
        expect($result)->equals((1000 / 60.00));
    }

    /**
     * @throws Exception
     */
    public function testConvertFloat()
    {
        $result = $this->service->convert(1000.50);
        expect($result)->equals((1000.50 / 60.00));
    }
}
