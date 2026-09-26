<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\eklaim\DokumenEklaimTask;

class DokumenEklaim extends BaseConsumer
{
    public function register()
    { 
        return [
            'integrasi_dokumen_eklaim' => DokumenEklaimTask::class,
        ];
    }
}