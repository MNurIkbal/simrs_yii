<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\resendtask\ResendTaskJknTask;

class ResendTaskJkn extends BaseConsumer
{
    public function register()
    { 
        return [
            'resend_task_jkn' => ResendTaskJknTask::class,
        ];
    }
}