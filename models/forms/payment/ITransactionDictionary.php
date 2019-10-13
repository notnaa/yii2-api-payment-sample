<?php

namespace app\models\forms\payment;

/**
 * Interface ITransactionDictionary
 *
 * @package app\models\forms\payment
 */
interface ITransactionDictionary
{
    /** @var int */
    public const CREDIT = 1;
    /** @var int */
    public const DEBIT = 2;
    /** @var array */
    public const SUPPORTED_TRANSACTION = [
        self::CREDIT,
        self::DEBIT,
    ];

    /**
     * @return int|null
     */
    public function getTransactionTypeIdByName(): ?int;
}
