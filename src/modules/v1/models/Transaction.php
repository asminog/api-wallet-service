<?php

namespace api\modules\v1\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(10) unsigned]
 * @property int $wallet_id [int(11) unsigned]
 * @property bool $type [tinyint(3) unsigned]
 * @property float $amount [decimal(19,4)]
 * @property float $amount_rub [decimal(19,4)]
 * @property string $currency [char(3)]
 * @property bool $reason [tinyint(3) unsigned]
 * @property int $created_at [int(11) unsigned]
 *
 * @property-read Wallet $wallet
 */
class Transaction extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'transaction';
    }

    /**
     * @return ActiveQuery
     */
    public function getWallet(): ActiveQuery
    {
        return $this->hasOne(Wallet::class, ['wallet_id' => 'id']);
    }


    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['wallet_id', 'type', 'amount', 'currency', 'reason'], 'required'],
            [['wallet_id'], 'integer', 'min' => 0],
            [['type', 'reason'], 'integer', 'min' => 0],
            [['type'], 'in', 'range' => array_keys(TransactionType::CONVERT)],
            [['amount'], 'number', 'min' => 0],
            [['currency'], 'string', 'length' => 3],
            [['currency'], 'in', 'range' => CurrencyRate::ALL_CURRENCIES],
            [['reason'], 'in', 'range' => array_keys(TransactionReason::CONVERT)],
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

    public function getMoney(): Money
    {
        $amount = (TransactionType::getCode($this->type) === TransactionType::DEBIT ? $this->amount : -$this->amount);
        return new Money(['amount' => $amount, 'currency' => $this->currency]);
    }

}