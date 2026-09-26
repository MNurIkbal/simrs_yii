<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\ExtractCsvAccEntryTask;

class ExtractCsvConsumer extends BaseConsumer
{
    public function register()
    { 
        return [
            'extract_csv' => ExtractCsvAccEntryTask::class,
        ];
    }
}