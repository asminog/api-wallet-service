<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\CurrencyRate;
use api\modules\v1\models\Money;
use api\modules\v1\models\Transaction;
use api\modules\v1\models\TransactionReason;
use api\modules\v1\models\TransactionType;
use api\modules\v1\models\Wallet;
use OpenApi\Annotations as OA;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class BalanceController extends Controller
{
    /**
     * @OA\Get(path="/balance/{id}",
     *   summary="Get wallet balance",
     *   tags={"balance"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Returns Wallet object",
     *     @OA\JsonContent(ref="#/components/schemas/Wallet"),
     *     @OA\XmlContent(ref="#/components/schemas/Wallet"),
     *   ),
     * )
     */
    public function actionView($id): Wallet
    {
        if (($wallet = Wallet::findOne(['id' => $id])) === null) {
            $wallet = new Wallet(['id' => $id, 'balance' => 0, 'currency' => CurrencyRate::RUB]);
        }
        return $wallet;
    }

    /**
     * @OA\Post(path="/balance/{id}/{type}/{reason}",
     *   summary="Update wallet ballance",
     *   tags={"ballance"},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *       type="integer"
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="type",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *       type="string",
     *       enum=api\modules\v1\models\TransactionType::ALL
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="reason",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *       type="string",
     *       enum=api\modules\v1\models\TransactionReason::ALL
     *     ),
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Currency object",
     *     @OA\JsonContent(ref="#/components/schemas/Money"),
     *   ),
     *   @OA\Response(response=200, description="Success"),
     *   @OA\Response(response=500, description="Error"),
     * )
     *
     * @throws \Throwable
     * @throws \yii\base\ErrorException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\di\NotInstantiableException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionUpdate($id, $type, $reason): bool
    {
        if (!in_array($type, TransactionType::ALL)) {
            throw new BadRequestHttpException('Transaction type is wrong.');
        }

        if (!in_array($reason, TransactionReason::ALL)) {
            throw new BadRequestHttpException('Transaction reason is wrong.');
        }

        $money = new Money(Yii::$app->getRequest()->getBodyParams());

        if (!$money->validate()) {
            throw new BadRequestHttpException(implode(' ', $money->getErrorSummary(true)));
        }

        if (($wallet = Wallet::findOne(['id' => $id])) === null) {
            $wallet = new Wallet([
                'id'       => (int)$id,
                'balance'  => 0,
                'currency' => $money->currency,
            ]);
        }

        $service = Yii::$container->get('api\modules\v1\services\WalletService');

        return $service->update($wallet, $type, $money, $reason);
    }

    /**
     * @OA\Get(path="/balance/refund/7days",
     *   summary="Gets refund weekly stat",
     *   tags={"ballance"},
     *   @OA\Response(response=200, description="Query and it result"),
     *   @OA\Response(response=500, description="Error"),
     * )
     */
    public function actionRefundStat(): array
    {
        $statQuery = Transaction::find()
            ->select(['amount' => 'SUM(`amount_rub`)'])
            ->where(['reason' => TransactionReason::getIndex(TransactionReason::REFUND)])
            ->andWhere(['>=', 'created_at', time() - 7 * 24 * 3600]);

        return ['query' => $statQuery->createCommand()->rawSql, 'money' => new Money(['amount' => $statQuery->scalar(), 'currency' => CurrencyRate::RUB])];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
        ];
    }
}