<?php

namespace app\services\exchange;

use app\models\forms\payment\ICurrencyDictionary;

/**
 * Class Ruble2DollarService
 *
 * @package app\services\exchange
 */
class Ruble2DollarService extends AbstractExchangeService
{
    /**
     * @return int
     */
    public static function getCurrencyIdFrom(): int
    {
        return ICurrencyDictionary::RUB;
    }

    /**
     * @return int
     */
    public static function getCurrencyIdTo(): int
    {
        return ICurrencyDictionary::USD;
    }

    /**
     * @return float|null
     */
    public function getRateRub(): ?float
    {
        return $this->getExchangeRate(ICurrencyDictionary::USD);
    }

    /**
     * @param float $amount
     * @return float
     * @throws \Exception
     */
    public function convert(float $amount): float
    {
        $rate = $this->getRateRub();

        if ($rate === null) {
            throw new \Exception('Invalid rate of currency.');
        }

        return ($amount / $rate);
    }
}
