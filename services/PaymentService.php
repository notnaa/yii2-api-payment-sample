<?php

namespace app\services;

use app\exceptions\PaymentException;
use app\models\forms\payment\PaymentChangeForm;
use app\models\TransactionHistory;
use app\models\UserWallet;
use yii\base\BaseObject;

/**
 * Class PaymentService
 *
 * @package app\services
 */
class PaymentService extends BaseObject
{
    /** @var CurrencyService */
    private $currencyService;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function init()
    {
        parent::init();

        $this->currencyService = \Yii::$container->get(CurrencyService::class);
    }

    /**
     * @param PaymentChangeForm $form
     * @return TransactionHistory
     * @throws PaymentException
     */
    public function changeBalance(PaymentChangeForm $form): TransactionHistory
    {
        if ($form->amount < 0) {
            throw new PaymentException($form->wallet_id, PaymentException::INCORRECT_DATA);
        }

        $currencyId = $form->getCurrencyIdByName();

        if ($currencyId === null) {
            throw new PaymentException($form->wallet_id, PaymentException::INCORRECT_CURRENCY_ERROR_ID);
        }

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $wallet = UserWallet::findOne(['id' => $form->wallet_id]);

            if ($wallet === null) {
                throw new PaymentException($form->wallet_id, PaymentException::INCORRECT_WALLET_ERROR_ID);
            }

            $exchangeAmount = $this->currencyService->exchange($form, $wallet);

            if ($exchangeAmount === null) {
                throw new PaymentException($form->wallet_id, PaymentException::ERROR_EXCHANGE_AMOUNT);
            }

            $amount = $form->getAmountByTransactionType($exchangeAmount);

            if ($amount === null) {
                throw new PaymentException($form->wallet_id, PaymentException::INCORRECT_TRANSACTION_TYPE);
            }

            $this->updateBalance($wallet, $amount);
            $transactionHistory = $this->addTransactionHistory($wallet, $exchangeAmount, $form->getTransactionTypeIdByName());

            $transaction->commit();
        } catch (PaymentException $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new PaymentException($form->wallet_id);
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw new PaymentException($form->wallet_id);
        }

        return $transactionHistory;
    }

    /**
     * @param UserWallet $wallet
     * @param float $amount
     * @param int $transactionType
     * @return TransactionHistory
     * @throws PaymentException
     */
    private function addTransactionHistory(UserWallet $wallet, float $amount, int $transactionType): TransactionHistory
    {
        $transactionHistory = new TransactionHistory();
        $transactionHistory->setAttributes([
            'wallet_id' => $wallet->id,
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'balance' => $wallet->balance,
        ]);

        if (!$transactionHistory->save()) {
            throw new PaymentException($wallet->id, PaymentException::TRANSACTION_ERROR_ID);
        }

        return $transactionHistory;
    }

    /**
     * @param UserWallet $wallet
     * @param float $amount
     * @return bool
     * @throws \Exception
     */
    private function updateBalance(UserWallet $wallet, float $amount): bool
    {
        $balanceIsUpdated = $wallet->updateCounters([
            'balance' => $amount,
        ]);

        if (!$balanceIsUpdated) {
            throw new PaymentException($wallet->id, PaymentException::TRANSACTION_ERROR_ID);
        }

        return true;
    }
}
