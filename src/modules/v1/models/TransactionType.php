<?php

namespace api\modules\v1\models;

use yii\base\InvalidArgumentException;

class TransactionType
{
    const DEBIT = 'debit';
    const CREDIT = 'credit';

    const ALL = [
        self::DEBIT,
        self::CREDIT,
    ];

    const CONVERT = [
        1 => self::DEBIT,
        2 => self::CREDIT,
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