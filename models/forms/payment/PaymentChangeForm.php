<?php

namespace app\models\forms\payment;

use yii\base\Model;

/**
 * Class PaymentChangeForm
 *
 * @package app\models\forms\payment
 */
class PaymentChangeForm extends Model implements ICurrencyDictionary, ITransactionDictionary
{
    /** @var string */
    public const DEBIT_TRANSACTION_TYPE = 'DEBIT';
    /** @var string */
    public const CREDIT_TRANSACTION_TYPE = 'CREDIT';
    /** @var string */
    public const USD_CURRENCY_TYPE = 'USD';
    /** @var string */
    public const RUB_CURRENCY_TYPE = 'RUB';

    /** @var array */
    public const SUPPORTED_TRANSACTION_TYPES = [
        self::DEBIT_TRANSACTION_TYPE,
        self::CREDIT_TRANSACTION_TYPE,
    ];
    /** @var array */
    public const SUPPORTED_CURRENCY_TYPES = [
        self::USD_CURRENCY_TYPE,
        self::RUB_CURRENCY_TYPE,
    ];

    /** @var int */
    public $wallet_id;
    /** @var string */
    public $transaction_type;
    /** @var float */
    public $amount;
    /** @var string */
    public $currency;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['wallet_id', 'transaction_type', 'amount', 'currency'], 'required'],
            [['wallet_id', 'transaction_type', 'currency'], 'string'],
            [['amount'], 'number'],
            [['transaction_type'], 'in', 'range' => self::SUPPORTED_TRANSACTION_TYPES],
            [['currency'], 'in', 'range' => self::SUPPORTED_CURRENCY_TYPES],
        ];
    }

    /**
     * @return int|null
     */
    public function getCurrencyIdByName(): ?int
    {
        if ($this->currency === self::USD_CURRENCY_TYPE) {
            $currencyId = self::USD;
        } elseif ($this->currency === self::RUB_CURRENCY_TYPE) {
            $currencyId = self::RUB;
        } else {
            $currencyId = null;
        }

        return $currencyId;
    }

    /**
     * @param float $amount
     * @return float|null
     */
    public function getAmountByTransactionType(float $amount): ?float
    {
        $transactionTypeId = $this->getTransactionTypeIdByName();

        if ($transactionTypeId === PaymentChangeForm::CREDIT) {
            $result = $amount;
        } elseif ($transactionTypeId === PaymentChangeForm::DEBIT) {
            $result = (0 - $amount);
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @return int|null
     */
    public function getTransactionTypeIdByName(): ?int
    {
        if ($this->transaction_type === self::CREDIT_TRANSACTION_TYPE) {
            $transactionTypeId = self::CREDIT;
        } elseif ($this->transaction_type === self::DEBIT_TRANSACTION_TYPE) {
            $transactionTypeId = self::DEBIT;
        } else {
            $transactionTypeId = null;
        }

        return $transactionTypeId;
    }
}
