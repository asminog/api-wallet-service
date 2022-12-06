<?php

namespace api\modules\v1\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(10) unsigned]
 * @property string $code [char(3)]
 * @property string $date [date]
 * @property string $rate [decimal(19,4) unsigned]
 * @property int $created_at [int(11) unsigned]
 *
 */
class CurrencyRate extends ActiveRecord
{
    const USD = 'USD';
    const RUB = 'RUB';

    const ALL_CURRENCIES = [self::USD, self::RUB];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'currency';
    }


    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['code', 'rate', 'date'], 'required'],
            [['rate'], 'number', 'min' => 0],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['code'], 'string', 'length' => 3],
            [['code', 'date'], 'unique', 'targetAttribute' => ['code', 'date']],
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            [
                'class'      => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     * @return CurrencyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CurrencyQuery(get_called_class());
    }

}