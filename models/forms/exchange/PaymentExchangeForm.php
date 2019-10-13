<?php

namespace app\models\forms\exchange;

use app\models\forms\payment\ICurrencyDictionary;
use app\services\exchange\AbstractExchangeService;
use yii\base\Model;

/**
 * Class PaymentExchangeForm
 *
 * @package app\models\forms\exchange
 */
class PaymentExchangeForm extends Model
{
    /** @var float */
    public $amount;
    /** @var int */
    public $currencyId;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['amount', 'currencyId'], 'required'],
            [['amount'], 'number'],
            [['currencyId'], 'integer'],
            [['currencyId'], 'in', 'range' => array_keys(ICurrencyDictionary::CHAR_CODE_LIST)],
        ];
    }

    /**
     * @return float|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getExchangeAmountToUsd(): ?float
    {
        $result = null;

        /** @var AbstractExchangeService $exchangeService */
        foreach (AbstractExchangeService::getSupportedExchangeServices() as $exchangeService) {
            if ($exchangeService::getCurrencyIdTo() !== ICurrencyDictionary::USD) {
                continue;
            }

            if ($exchangeService::getCurrencyIdFrom() === $this->currencyId) {
                /** @var AbstractExchangeService $service */
                $service = \Yii::$container->get($exchangeService);
                $result = $service->convert((float)$this->amount);
            }
        }

        return $result;
    }
}
