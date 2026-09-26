<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    CetakPdfCpptRajalTask,
};

class CetakPdfCpptRajal extends BaseConsumer
{
    public function register()
    { 
        return [
            'cppt_pdf_rajal' => CetakPdfCpptRajalTask::class,
        ];
    }
}