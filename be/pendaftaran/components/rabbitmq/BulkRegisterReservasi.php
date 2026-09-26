<?php

namespace app\components\rabbitmq;

use app\components\rabbitmq\autoRegister\BulkRegisterReservasiTask;
use Doco\rabbitmq\BaseConsumer;

class BulkRegisterReservasi extends BaseConsumer
{
    public function register()
    { 
        return [
            'bulk_register_reservasi' => BulkRegisterReservasiTask::class,
        ];
    }
}