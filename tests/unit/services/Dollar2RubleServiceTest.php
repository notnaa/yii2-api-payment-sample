<?php

use app\services\exchange\Dollar2RubleService;
use Codeception\Test\Unit;

/**
 * Class Dollar2RubleServiceTest
 */
class Dollar2RubleServiceTest extends Unit
{
    /** @var UnitTester */
    protected $tester;

    /** @var Dollar2RubleService */
    private $service;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function _before()
    {
        $this->service = \Yii::$container->get(Dollar2RubleService::class);
    }

    /**
     * @throws Exception
     */
    public function testConvertInt()
    {
        $result = $this->service->convert(100);
        expect($result)->equals((100 * 60.00));
    }

    /**
     * @throws Exception
     */
    public function testConvertFloat()
    {
        $result = $this->service->convert(100.50);
        expect($result)->equals((100.50 * 60.00));
    }
}
