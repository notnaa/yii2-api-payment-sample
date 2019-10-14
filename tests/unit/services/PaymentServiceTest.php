<?php

use app\exceptions\PaymentException;
use app\models\forms\payment\PaymentChangeForm;
use app\models\UserWallet;
use app\services\PaymentService;
use app\tests\fixtures\TransactionHistoryFixture;
use app\tests\fixtures\UserFixture;
use app\tests\fixtures\UserWalletFixture;
use Codeception\Test\Unit;

/**
 * Class PaymentServiceCest
 */
class PaymentServiceTest extends Unit
{
    /** @var UnitTester */
    protected $tester;

    /** @var PaymentService */
    private $service;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function _before()
    {
        $this->service = \Yii::$container->get(PaymentService::class);

        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
            'user_wallet' => [
                'class' => UserWalletFixture::class,
                'dataFile' => codecept_data_dir() . 'user_wallet.php',
            ],
            'transaction_history' => [
                'class' => TransactionHistoryFixture::class,
                'dataFile' => codecept_data_dir() . 'transaction_history.php',
            ],
        ]);
    }

    /**
     * @throws \app\exceptions\PaymentException
     */
    public function testCreditUsdToWalletUsd()
    {
        $walletId = 1;

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'CREDIT',
            'amount' => 100,
            'currency' => 'USD',
        ]);

        $form->validate();
        expect($form->getErrors())->isEmpty();

        $this->service->changeBalance($form);

        $userWallet = UserWallet::findOne(['id' => $walletId]);

        expect($userWallet->balance)->equals(100.00);
    }

    /**
     * @throws \app\exceptions\PaymentException
     */
    public function testCreditRubToWalletUsd()
    {
        $walletId = 1;

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'CREDIT',
            'amount' => 1000,
            'currency' => 'RUB',
        ]);

        $form->validate();
        expect($form->getErrors())->isEmpty();

        $this->service->changeBalance($form);

        $userWallet = UserWallet::findOne(['id' => $walletId]);

        expect($userWallet->balance)->equals((1000.00 / 60.00));
    }

    /**
     * @throws \app\exceptions\PaymentException
     */
    public function testCreditUsdToWalletRub()
    {
        $walletId = 2;

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'CREDIT',
            'amount' => 100,
            'currency' => 'USD',
        ]);

        $form->validate();
        expect($form->getErrors())->isEmpty();

        $this->service->changeBalance($form);

        $userWallet = UserWallet::findOne(['id' => $walletId]);

        expect($userWallet->balance)->equals((100.00 * 60.00));
    }

    /**
     * @throws \app\exceptions\PaymentException
     */
    public function testCreditRubToWalletRub()
    {
        $walletId = 2;

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'CREDIT',
            'amount' => 100,
            'currency' => 'RUB',
        ]);

        $form->validate();
        expect($form->getErrors())->isEmpty();

        $this->service->changeBalance($form);

        $userWallet = UserWallet::findOne(['id' => $walletId]);

        expect($userWallet->balance)->equals(100.00);
    }

    /**
     * @throws \app\exceptions\PaymentException
     */
    public function testDebitUsdToWalletUsd()
    {
        $walletId = 1;

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'DEBIT',
            'amount' => 100,
            'currency' => 'USD',
        ]);

        $form->validate();
        expect($form->getErrors())->isEmpty();

        $this->service->changeBalance($form);

        $userWallet = UserWallet::findOne(['id' => $walletId]);

        expect($userWallet->balance)->equals(-100.00);
    }

    /**
     * @throws \app\exceptions\PaymentException
     */
    public function testDebitUsdToWalletRub()
    {
        $walletId = 2;

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'DEBIT',
            'amount' => 100,
            'currency' => 'USD',
        ]);

        $form->validate();
        expect($form->getErrors())->isEmpty();

        $this->service->changeBalance($form);

        $userWallet = UserWallet::findOne(['id' => $walletId]);

        expect($userWallet->balance)->equals((-100.00 * 60.00));
    }

    /**
     * @throws \app\exceptions\PaymentException
     */
    public function testDebitRubToWalletRub()
    {
        $walletId = 2;

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'DEBIT',
            'amount' => 1000,
            'currency' => 'RUB',
        ]);

        $form->validate();
        expect($form->getErrors())->isEmpty();

        $this->service->changeBalance($form);

        $userWallet = UserWallet::findOne(['id' => $walletId]);

        expect($userWallet->balance)->equals(-1000.00);
    }

    /**
     * @throws \app\exceptions\PaymentException
     */
    public function testDebitRubToWalletUsd()
    {
        $walletId = 1;

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'DEBIT',
            'amount' => 1000,
            'currency' => 'RUB',
        ]);

        $form->validate();
        expect($form->getErrors())->isEmpty();

        $this->service->changeBalance($form);

        $userWallet = UserWallet::findOne(['id' => $walletId]);

        expect($userWallet->balance)->equals((-1000.00 / 60.00));
    }

    /**
     * @throws \app\exceptions\PaymentException
     */
    public function testCreditUsdToWalletUsdNegativeNumber()
    {
        $walletId = 1;

        $form = new PaymentChangeForm();
        $form->setAttributes([
            'wallet_id' => $walletId,
            'transaction_type' => 'CREDIT',
            'amount' => -100,
            'currency' => 'USD',
        ]);

        $form->validate();
        expect($form->getErrors())->notNull();
    }
}
