<?php

namespace app\components\rabbitmq;

use app\components\rabbitmq\laporan\InfRencanaKontrolTask;
use Doco\rabbitmq\BaseConsumer;

class InfRencanaKontrolConsumer extends BaseConsumer
{
    public function register()
    { 
        return [
            'import_rencana_kontrol' => InfRencanaKontrolTask::class,
        ];
    }
}