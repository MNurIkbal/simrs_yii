<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\exportpdf\CpptPdfIgdTask;

class CpptPdfIgd extends BaseConsumer
{
    public function register()
    { 
        return [
            'cppt_pdf_igd' => CpptPdfIgdTask::class,
        ];
    }
}
