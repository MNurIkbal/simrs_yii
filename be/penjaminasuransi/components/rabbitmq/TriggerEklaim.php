<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\eklaim\TriggerSinkronTask;

class TriggerEklaim extends BaseConsumer
{
    public function register()
    { 
        return [
            'trigger_sinkron_eklaim' => TriggerSinkronTask::class,
        ];
    }
}