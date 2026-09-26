<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    EsignGenerateTask,
    EsignRegStatusTask,
    EsignCertStatusTask,
};

class Esign extends BaseConsumer
{
    public function register()
    { 
        return [
            'esign_generate' => EsignGenerateTask::class,
            'esign_reg_status' => EsignRegStatusTask::class,
            'esign_cert_status' => EsignCertStatusTask::class,
        ];
    }
}