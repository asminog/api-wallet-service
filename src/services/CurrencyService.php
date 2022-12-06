<?php

namespace api\services;

use api\modules\v1\models\CurrencyRate;
use DateTimeImmutable;
use SimpleXMLElement;
use yii\base\ErrorException;

class CurrencyService
{

    private const CBR_CURRENCY_CODES = [
        CurrencyRate::USD => 'R01235',
    ];

    /**
     * @throws \yii\base\ErrorException
     */
    public function update()
    {
        $end = new DateTimeImmutable();
        $start = $end->sub(new \DateInterval('P1M'));

        foreach (CurrencyRate::ALL_CURRENCIES as $code) {
            if ($code === CurrencyRate::RUB) {
                continue;
            }
            $currency = $this->getLastRecord($this->getXmlData($start, $end, $this->convertCurrencyCodeToCbr($code)));
            if ($currency->save() === false) {
                throw new ErrorException(implode("\n", $currency->getErrorSummary(true)));
            }
        }
    }

    private function createUrl(DateTimeImmutable $start, DateTimeImmutable $end, string $currencyCode): string
    {
        $start = $start->format('d/m/Y');
        $end = $end->format('d/m/Y');
        return "https://www.cbr.ru/scripts/XML_dynamic.asp?date_req1={$start}&date_req2={$end}&VAL_NM_RQ={$currencyCode}";
    }

    /**
     * @throws \yii\base\ErrorException
     */
    private function getXmlData(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        string $currencyCode
    ): SimpleXMLElement {
        $url = $this->createUrl($start, $end, $currencyCode);
        $xml = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xml === false) {
            throw new ErrorException('Error to get currency from CBR');
        }

        return $xml;
    }

    /**
     * @throws \yii\base\ErrorException
     */
    private function convertCurrencyCodeToCbr(string $code): string
    {
        if (array_key_exists($code, self::CBR_CURRENCY_CODES) === false) {
            throw new ErrorException('Currency not convertable to CBR');
        }

        return self::CBR_CURRENCY_CODES[$code];
    }

    /**
     * @throws \yii\base\ErrorException
     */
    private function getLastRecord(SimpleXMLElement $response): CurrencyRate
    {
        $lastRecord = null;
        foreach ($response->Record as $record) {
            $lastRecord = $record;
        }

        if ($lastRecord === null) {
            throw new ErrorException('Error to get last record from CBR currency data');
        }

        return new CurrencyRate([
            'code' => $this->convertCurrencyCodeFromCbr((string)$lastRecord['Id']),
            'date' => DateTimeImmutable::createFromFormat('d.m.Y', $lastRecord['Date'])->format('Y-m-d'),
            'rate' => (float)str_replace(',', '.', (string)$lastRecord->Value),
        ]);
    }

    /**
     * @throws \yii\base\ErrorException
     */
    private function convertCurrencyCodeFromCbr(string $code): string
    {
        if (($index = array_search($code, self::CBR_CURRENCY_CODES)) === false) {
            throw new ErrorException('CBR currency code not found');
        }

        return $index;
    }
}