<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\eklaim;
use app\components\rabbitmq\eklaim\SinkronEklaimTask;

class SinkronEklaim extends BaseConsumer
{
    public function register()
    { 
        return [
            'sinkron_eklaim' => SinkronEklaimTask::class,
        ];
    }
}