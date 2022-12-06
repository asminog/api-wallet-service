<?php

namespace api\modules\v1\models;


/**
 * This is the ActiveQuery class for [[Currency]].
 *
 * @see CurrencyRate
 */
class CurrencyQuery extends \yii\db\ActiveQuery
{
    public function last($code): CurrencyRate
    {
        return $this->andWhere(['code' => $code])->orderBy(['date' => SORT_DESC])->limit(1)->one();
    }

    /**
     * {@inheritdoc}
     * @return CurrencyRate[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CurrencyRate|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
