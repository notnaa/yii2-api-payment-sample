<?php

namespace app\exceptions;

use Throwable;
use yii\base\Exception;

/**
 * Class PaymentException
 *
 * @package app\exceptions
 */
class PaymentException extends Exception
{
    /** @var int */
    public const INCORRECT_CURRENCY_ERROR_ID = 1;
    /** @var int */
    public const TRANSACTION_ERROR_ID = 2;
    /** @var int */
    public const INCORRECT_WALLET_ERROR_ID = 3;
    /** @var int */
    public const INCORRECT_DATA = 4;
    /** @var int */
    public const ERROR_EXCHANGE_AMOUNT = 5;
    /** @var int */
    public const INCORRECT_TRANSACTION_TYPE = 6;
    /** @var array */
    protected const ERROR_MESSAGES = [
        self::INCORRECT_CURRENCY_ERROR_ID => 'Incorrect currency.',
        self::TRANSACTION_ERROR_ID => 'Error create transaction.',
        self::INCORRECT_WALLET_ERROR_ID => 'Incorrect wallet id.',
        self::INCORRECT_DATA => 'Incorrect data.',
        self::ERROR_EXCHANGE_AMOUNT => 'Error exchange amount.',
        self::INCORRECT_TRANSACTION_TYPE => 'Incorrect transaction type.',
    ];
    /** @var string */
    protected const UNDEFINED_ERROR_MESSAGE = 'Undefined error.';

    /** @var int|null */
    protected $walletId;

    /**
     * @return int
     */
    public function getWalletId(): int
    {
        return $this->walletId;
    }

    /**
     * PaymentException constructor.
     * @param int|null $walletId
     * @param int $code
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct(?int $walletId = null, int $code = 0, string $message = '', Throwable $previous = null)
    {
        $this->walletId = $walletId;

        if ($message === '') {
            $message = $this->getErrorMessageById($code);
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'PaymentException';
    }

    /**
     * @param int $errorId
     * @return string
     */
    protected function getErrorMessageById(int $errorId): string
    {
        if (array_key_exists($errorId, self::ERROR_MESSAGES)) {
            $errorMessage = self::ERROR_MESSAGES[$errorId];
        } else {
            $errorMessage = self::UNDEFINED_ERROR_MESSAGE;
        }

        return $errorMessage;
    }
}
