<?php

namespace api\commands;

use Throwable;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class CurrencyController extends Controller
{
    /**
     * @throws \yii\di\NotInstantiableException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate(): int
    {
        $service = Yii::$container->get('api\services\CurrencyService');
        try {
            $service->update();
        } catch (Throwable $e) {
            echo $e->getMessage()."\n";
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }
}