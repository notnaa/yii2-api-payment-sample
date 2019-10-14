<?php

namespace app\services;

use app\exceptions\PaymentException;
use app\models\forms\exchange\PaymentExchangeForm;
use app\models\forms\payment\ICurrencyDictionary;
use app\models\forms\payment\PaymentChangeForm;
use app\models\UserWallet;
use app\services\exchange\AbstractExchangeService;
use yii\base\BaseObject;

/**
 * Class ExchangeService
 *
 * @package app\services
 */
class CurrencyService extends BaseObject
{
    /**
     * @param PaymentChangeForm $changeForm
     * @param UserWallet $wallet
     * @return float|null
     * @throws \Exception
     */
    public function exchange(PaymentChangeForm $changeForm, UserWallet $wallet): ?float
    {
        if ($wallet->currency === $changeForm->getCurrencyIdByName()) {
            $result = $changeForm->amount;
        } else {
            $exchangeServiceClass = null;

            /** @var AbstractExchangeService $exchangeService */
            foreach (AbstractExchangeService::getSupportedExchangeServices() as $exchangeService) {
                if ($wallet->currency === ICurrencyDictionary::USD) {
                    break;
                }

                if ($exchangeService::getCurrencyIdFrom() !== ICurrencyDictionary::USD) {
                    continue;
                }

                if ($exchangeService::getCurrencyIdTo() === $wallet->currency) {
                    $exchangeServiceClass = $exchangeService;
                    break;
                }
            }

            if ($exchangeServiceClass === null) {
                if ($wallet->currency === ICurrencyDictionary::USD) {
                    $result = $this->getConvertToUsd($changeForm);
                } else {
                    $result = null;
                }
            } else {
                $amountUsd = $this->getConvertToUsd($changeForm);
                $service = \Yii::$container->get($exchangeServiceClass);
                $result = $service->convert($amountUsd);
            }
        }

        return $result;
    }

    /**
     * @param PaymentChangeForm $changeForm
     * @return float
     * @throws PaymentException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function getConvertToUsd(PaymentChangeForm $changeForm): float
    {
        if ($changeForm->getCurrencyIdByName() === ICurrencyDictionary::USD) {
            return $changeForm->amount;
        }

        $form = new PaymentExchangeForm();
        $form->load([
            'amount' => $changeForm->amount,
            'currencyId' => $changeForm->getCurrencyIdByName(),
        ], '');

        if (!$form->validate()) {
            throw new PaymentException($changeForm->wallet_id, PaymentException::ERROR_EXCHANGE_AMOUNT);
        }

        $exchangedAmount = $form->getExchangeAmountToUsd();

        if ($exchangedAmount === null) {
            throw new PaymentException($changeForm->wallet_id, PaymentException::ERROR_EXCHANGE_AMOUNT);
        }

        return $exchangedAmount;
    }
}
