<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\exportpdf\CpptPdfTask;

class CpptPdf extends BaseConsumer
{
    public function register()
    { 
        return [
            'cppt_pdf' => CpptPdfTask::class,
        ];
    }
}
