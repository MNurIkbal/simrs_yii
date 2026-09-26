<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\eklaim\DokumenResepKronisTask;

class DokumenResepKronis extends BaseConsumer
{
    public function register()
    { 
        return [
            'dokumen_resep_kronis' => DokumenResepKronisTask::class,
        ];
    }
}