<?php

namespace api\modules\v1\services;

use api\modules\v1\models\CurrencyRate;
use api\modules\v1\models\Money;
use api\modules\v1\models\Transaction;
use api\modules\v1\models\TransactionReason;
use api\modules\v1\models\TransactionType;
use api\modules\v1\models\Wallet;
use Throwable;
use Yii;
use yii\web\ServerErrorHttpException;

class WalletService
{
    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\di\NotInstantiableException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\ErrorException
     * @throws \yii\web\ServerErrorHttpException
     */
    public function update(Wallet $wallet, string $type, Money $amount, string $reason): bool
    {
        $service = Yii::$container->get('api\modules\v1\services\MoneyService');
        $transaction = new Transaction([
            'type'       => TransactionType::getIndex($type),
            'amount'     => $amount->amount,
            'currency'   => $amount->currency,
            'amount_rub' => $service->convert($amount, CurrencyRate::RUB)->amount,
            'reason'     => TransactionReason::getIndex($reason),
        ]);

        $t = Yii::$app->db->beginTransaction();
        try {
            if ($wallet->isNewRecord) {
                if (!$wallet->save()) {
                    throw new ServerErrorHttpException(implode(' ', $wallet->getErrorSummary(true)));
                }
            }

            $wallet_delta = $service->convert($transaction->getMoney(), $wallet->currency);
            $wallet->balance += $wallet_delta->amount;
            if (!$wallet->validate()) {
                throw new ServerErrorHttpException(implode(' ', $wallet->getErrorSummary(true)));
            }

            /**
             * https://logicalread.com/optimize-mysql-perf-part-2-mc13/#.YmO1TC_c5B2
             * to optimize performance and use READ UNCOMMITTED instead of SERIALIZABLE
             * using update counters instead save operation
             */
            if (!$wallet->updateCounters(['balance' => $wallet_delta->amount])) {
                throw new ServerErrorHttpException('Balance not updated');
            }

            $transaction->wallet_id = $wallet->id;
            if (!$transaction->save()) {
                throw new ServerErrorHttpException(implode(' ', $transaction->getErrorSummary(true)));
            }
            $wallet->refresh();
            $t->commit();
        } catch (Throwable $e) {
            $t->rollBack();
            throw $e;
        }
        return true;
    }
}