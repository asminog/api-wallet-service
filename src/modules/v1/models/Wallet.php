<?php

namespace api\modules\v1\models;

use yii\behaviors\AttributeTypecastBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id [int(10) unsigned]
 * @property float $balance [decimal(19,4) unsigned]
 * @property string $currency [char(3)]
 *
 * @property-read Transaction[] $transactions
 *
 * @OA\Schema(
 *  schema="Wallet",
 *  @OA\Property(
 *     property="id",
 *     type="integer",
 *     description="Wallet ID"
 *  ),
 *  @OA\Property(
 *     property="balance",
 *     type="number",
 *     description="Wallet Ballance"
 *  ),
 *  @OA\Property(
 *     property="currency",
 *     type="string",
 *     enum=api\modules\v1\models\CurrencyRate::ALL_CURRENCIES,
 *     description="Wallet currency"
 *  )
 *)
 *
 */
class Wallet extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'wallet';
    }

    /**
     * @return ActiveQuery
     */
    public function getTransactions(): ActiveQuery
    {
        return $this->hasMany(Transaction::class, ['id' => 'wallet_id']);
    }

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['balance', 'currency'], 'required'],
            [['balance'], 'number', 'min' => 0],
            [['currency'], 'string', 'length' => 3],
            [['currency'], 'in', 'range' => CurrencyRate::ALL_CURRENCIES],
        ];
    }

    public function behaviors()
    {
        return [
            'typecast' => [
                'class'                 => AttributeTypecastBehavior::class,
                'attributeTypes'        => [
                    'balance' => AttributeTypecastBehavior::TYPE_FLOAT,
                ],
                'typecastAfterValidate' => true,
                'typecastBeforeSave'    => false,
                'typecastAfterFind'     => true,
            ],
        ];
    }
}