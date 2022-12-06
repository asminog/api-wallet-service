<?php

namespace api\modules\v1\models;

use OpenApi\Annotations as OA;
use yii\base\Model;

/**
 *
 * @property-read float $amount
 * @property-read string $currency
 *
 * @OA\Schema(
 *  schema="Money",
 *  @OA\Property(
 *     property="amount",
 *     type="number",
 *     description="Ammount"
 *  ),
 *  @OA\Property(
 *     property="currency",
 *     type="string",
 *     enum=api\modules\v1\models\CurrencyRate::ALL_CURRENCIES,
 *     description="Currency"
 *  )
 *)
 *
 */
class Money extends Model
{
    private float $amount = 0;
    private string $currency = '';

    public function __construct($config = [])
    {
        if (isset($config['amount'])) {
            $this->amount = $config['amount'];
            unset($config['amount']);
        }
        if (isset($config['currency'])) {
            $this->currency = $config['currency'];
            unset($config['currency']);
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['amount', 'currency'], 'required'],
            [['amount'], 'number'],
            [['amount'], 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],
            [['currency'], 'in', 'range' => CurrencyRate::ALL_CURRENCIES],
        ];
    }

    public function getAmount(): float
    {
        return round($this->amount, 2);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function fields()
    {
        return ['amount', 'currency'];
    }
}