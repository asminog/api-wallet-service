<?php

namespace api\modules\v1\services;

use api\modules\v1\models\CurrencyRate;
use api\modules\v1\models\Money;
use DateTimeImmutable;
use SimpleXMLElement;
use yii\base\ErrorException;

class MoneyService
{
    /**
     * @throws \yii\base\ErrorException
     */
    public function sum(Money $money1, Money $money2): Money
    {
        $money2 = $this->convert($money2, $money1->currency);
        return new Money(['amount' => $money1->amount + $money2->amount, 'currency' => $money1->currency]);
    }

    /**
     * @throws \yii\base\ErrorException
     */
    public function convert(Money $money, string $to): Money
    {
        if ($money->currency == $to) {
            return $money;
        }

        if (($from_currency = CurrencyRate::find()->last($money->currency)) === null) {
            throw new ErrorException('Currency rate "' . $money->currency . '" not found');
        }
        if (($to_currency = CurrencyRate::find()->last($to)) === null) {
            throw new ErrorException('Currency rate "' . $to . '" not found');
        }

        return new Money(['amount' => $money->amount * $from_currency->rate / $to_currency->rate, 'currency' => $to]);
    }
}