<?php

namespace api\modules\v1\models;

use yii\base\InvalidArgumentException;

class TransactionReason
{
    const STOCK = 'stock';
    const REFUND = 'refund';

    const ALL = [
        self::STOCK,
        self::REFUND,
    ];

    const CONVERT = [
        1 => self::STOCK,
        2 => self::REFUND,
    ];

    public static function getCode(int $index): string
    {
        if (!array_key_exists($index, self::CONVERT)) {
            throw new InvalidArgumentException();
        }

        return self::CONVERT[$index];
    }

    public static function getIndex(string $code): int
    {
        if (($index = array_search($code, self::CONVERT)) === false) {
            throw new InvalidArgumentException();
        }

        return $index;
    }
}