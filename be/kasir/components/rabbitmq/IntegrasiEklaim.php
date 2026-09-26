<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\eklaim\IntegrasiEklaimTask;

class IntegrasiEklaim extends BaseConsumer
{
    public function register()
    { 
        return [
            'integrasi_eklaim' => IntegrasiEklaimTask::class,
        ];
    }
}