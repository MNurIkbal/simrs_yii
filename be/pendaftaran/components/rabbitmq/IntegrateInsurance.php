<?php

namespace app\components\rabbitmq;

use app\components\rabbitmq\insurance\IntegrateInsuranceTask;
use Doco\rabbitmq\BaseConsumer;

class IntegrateInsurance extends BaseConsumer
{
    public function register()
    {
        return [
            'integrate_insurance' => IntegrateInsuranceTask::class
        ];
    }
}
