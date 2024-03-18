<?php

namespace app\bootstrap;

use yii\base\BootstrapInterface;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        //$container->setSingleton(DocumentOrderController::class, DocumentOrderService::class);

    }

}