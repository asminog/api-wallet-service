<?php

namespace api\modules\v1;

use OpenApi\Annotations as OA;
use yii\base\Module;

/**
 * @OA\Info(
 *   version="1.0",
 *   title="Wallet API",
 *   description="Server - User Wallet API",
 *   @OA\Contact(
 *     name="Serg Akudovich",
 *     email="serg@akudovich.com",
 *   ),
 * ),
 * @OA\Server(
 *   url="/v1",
 *   description="api server",
 * )
 */
class Version1 extends Module
{
    public function init()
    {
        parent::init();
    }
}