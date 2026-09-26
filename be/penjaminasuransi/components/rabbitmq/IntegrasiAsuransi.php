<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\integrasi\IntegrasiAsuransiTask;

class IntegrasiAsuransi extends BaseConsumer
{
    public function register()
    { 
        return [
            'sync_integrasi_asuransi_data' => IntegrasiAsuransiTask::class,
        ];
    }
}