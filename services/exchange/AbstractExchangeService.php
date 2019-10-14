<?php

namespace app\services\exchange;

use app\models\forms\payment\ICurrencyDictionary;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\httpclient\XmlParser;

/**
 * Class AbstractExchangeService
 *
 * @package app\services\exchange
 */
abstract class AbstractExchangeService extends BaseObject implements IExchange
{
    /** @var string */
    private const CB_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';

    /** @var bool */
    public $isTest = false;

    /** @var array */
    protected static $exchangeTestValues = [
        'USD' => 60.00,
    ];

    /** @var array|null */
    private $exchangeRate = null;

    /**
     * @return int
     */
    abstract public static function getCurrencyIdFrom(): int;

    /**
     * @return int
     */
    abstract public static function getCurrencyIdTo(): int;

    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        $this->isTest = ArrayHelper::getValue(\Yii::$container->definitions, sprintf("%s.isTest", self::class));

        if (!$this->isTest) {
            if (!$this->requestExchangeRate()) {
                throw new \Exception('Incorrect api response.');
            }
        } else {
            $this->exchangeRate = static::$exchangeTestValues;
        }
    }

    /**
     * @return array
     */
    public static function getSupportedExchangeServices(): array
    {
        return [
            Dollar2RubleService::class,
            Ruble2DollarService::class,
        ];
    }

    /**
     * @param int $currencyId
     * @return float|null
     */
    protected function getExchangeRate(int $currencyId): ?float
    {
        if (!array_key_exists($currencyId, ICurrencyDictionary::CHAR_CODE_LIST)) {
            return null;
        }

        $currencyCharCode = ICurrencyDictionary::CHAR_CODE_LIST[$currencyId];
        return array_key_exists($currencyCharCode, $this->exchangeRate) ? $this->exchangeRate[$currencyCharCode] : null;
    }

    /**
     * @return bool
     */
    private function requestExchangeRate(): bool
    {
        try {
            $response = (new Client())
                ->get(self::CB_URL)
                ->addOptions(['timeout' => 0.2])
                ->send();

            $xml = (new XmlParser())->parse($response);

            if (!is_array($xml) || !array_key_exists('Valute', $xml)) {
                throw new \Exception('Incorrect response.');
            }

            foreach ($xml['Valute'] as $item) {
                if (!array_key_exists('CharCode', $item) || !array_key_exists('Value', $item)) {
                    continue;
                }

                $this->exchangeRate[$item['CharCode']] = (float)$item['Value'];
            }
        } catch (\Exception $e) {
            $this->exchangeRate = null;
        }

        return is_array($this->exchangeRate) ? true : false;
    }
}
