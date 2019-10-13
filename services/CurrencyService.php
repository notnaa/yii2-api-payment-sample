<?php

namespace app\services;

use app\exceptions\PaymentException;
use app\models\forms\exchange\PaymentExchangeForm;
use app\models\forms\payment\ICurrencyDictionary;
use app\models\forms\payment\PaymentChangeForm;
use app\models\UserWallet;
use app\services\exchange\Dollar2RubleService;
use app\services\exchange\Ruble2DollarService;
use yii\base\BaseObject;

/**
 * Class ExchangeService
 *
 * @package app\services
 */
class CurrencyService extends BaseObject
{
    /** @var Dollar2RubleService */
    protected $dollar2RubleService;
    /** @var Ruble2DollarService */
    protected $ruble2DollarService;

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function init()
    {
        parent::init();

        $this->dollar2RubleService = \Yii::$container->get(Dollar2RubleService::class);
        $this->ruble2DollarService = \Yii::$container->get(Ruble2DollarService::class);
    }

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
            switch ($wallet->currency) {
                case ICurrencyDictionary::USD:
                    $amountUsd = $this->getConvertToUsd($changeForm);
                    $result = $amountUsd;
                    break;
                case ICurrencyDictionary::RUB:
                    $amountUsd = $this->getConvertToUsd($changeForm);
                    $result = $this->dollar2RubleService->convert($amountUsd);
                    break;
                default:
                    $result = null;
                    break;
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
