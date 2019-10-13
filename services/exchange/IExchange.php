<?php

namespace app\services\exchange;

/**
 * Interface IExchange
 *
 * @package app\services\exchange
 */
interface IExchange
{
    /**
     * @param float $amount
     * @return float
     */
    public function convert(float $amount): float;
}
