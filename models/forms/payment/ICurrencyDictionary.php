<?php

namespace app\models\forms\payment;

/**
 * Interface ICurrencyDictionary
 *
 * @package app\models\forms\payment
 */
interface ICurrencyDictionary
{
    /** @var int */
    public const USD = 1;
    /** @var int */
    public const RUB = 2;
    /** @var array */
    public const SUPPORTED_CURRENCY = [
        self::USD,
        self::RUB,
    ];
    /** @var array */
    public const CHAR_CODE_LIST = [
        self::USD => 'USD',
        self::RUB => 'RUB',
    ];

    /**
     * @return int|null
     */
    public function getCurrencyIdByName(): ?int;
}
