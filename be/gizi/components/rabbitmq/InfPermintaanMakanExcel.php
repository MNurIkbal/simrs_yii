<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\excel\InfPermintaanMakanTask;

class InfPermintaanMakanExcel extends BaseConsumer
{
    public function register()
    { 
        return [
            'excel_inf_permintaan_makan' => InfPermintaanMakanTask::class,
        ];
    }
}