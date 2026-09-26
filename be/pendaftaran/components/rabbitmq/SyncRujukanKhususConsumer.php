<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\syncrujukankhusus\SyncRujukanKhususTask;

class SyncRujukanKhususConsumer extends BaseConsumer
{
    public function register()
    { 
        return [
            'sync_rujukan_khusus' => SyncRujukanKhususTask::class,
        ];
    }
}