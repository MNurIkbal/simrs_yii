<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\resendantrianjkn\ResendAntrianJknTask;

class ResendAntrianJknConsumer extends BaseConsumer
{
    public function register()
    { 
        return [
            'resend_antrian_jkn' => ResendAntrianJknTask::class,
        ];
    }
}