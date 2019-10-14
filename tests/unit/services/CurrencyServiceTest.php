<?php

use app\models\forms\payment\PaymentChangeForm;
use app\models\UserWallet;
use app\services\CurrencyService;
use app\tests\fixtures\UserFixture;
use app\tests\fixtures\UserWalletFixture;
use Codeception\Test\Unit;

/**
 * Class CurrencyServiceTest
 */
class CurrencyServiceTest extends Unit
{
    /** @var UnitTester */
    protected $tester;

    /** @var CurrencyService */
    private $service;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function _before()
    {
        $this->service = \Yii::$container->get(CurrencyService::class);

        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
            'user_wallet' => [
                'class' => UserWalletFixture::class,
                'dataFile' => codecept_data_dir() . 'user_wallet.php',
            ],
        ]);
    }

    /**
     * @throws Exception
     */
    public function testExchangeUsdToWalletUsd()
    {
        $walletId = 1;
        $userWallet = UserWallet::findOne(['id' => $walletId]);

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'CREDIT',
            'amount' => 100,
            'currency' => 'USD',
        ]);

        $result = $this->service->exchange($form, $userWallet);

        expect($result)->equals(100.00);
    }

    /**
     * @throws Exception
     */
    public function testExchangeRubToWalletUsd()
    {
        $walletId = 1;
        $userWallet = UserWallet::findOne(['id' => $walletId]);

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'CREDIT',
            'amount' => 1000,
            'currency' => 'RUB',
        ]);

        $result = $this->service->exchange($form, $userWallet);

        expect($result)->equals((1000.00 / 60.00));
    }

    /**
     * @throws Exception
     */
    public function testExchangeUsdToWalletRub()
    {
        $walletId = 2;
        $userWallet = UserWallet::findOne(['id' => $walletId]);

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'CREDIT',
            'amount' => 100,
            'currency' => 'USD',
        ]);

        $result = $this->service->exchange($form, $userWallet);

        expect($result)->equals((100.00 * 60.00));
    }

    /**
     * @throws Exception
     */
    public function testExchangeRubToWalletRub()
    {
        $walletId = 2;
        $userWallet = UserWallet::findOne(['id' => $walletId]);

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'CREDIT',
            'amount' => 100,
            'currency' => 'RUB',
        ]);

        $result = $this->service->exchange($form, $userWallet);

        expect($result)->equals(100.00);
    }
}
