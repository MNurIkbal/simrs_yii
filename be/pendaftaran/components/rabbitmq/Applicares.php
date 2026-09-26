<?php

namespace app\components\rabbitmq;

use app\components\rabbitmq\applicares\SyncApplicaresTask;
use app\components\rabbitmq\applicares\SyncDeleteKamarApplicaresTask;
use Doco\rabbitmq\BaseConsumer;

class Applicares extends BaseConsumer
{
    public function register()
    { 
        return [
            'sync_applicares' => SyncApplicaresTask::class,
            'sync_delete_applicares' => SyncDeleteKamarApplicaresTask::class
        ];
    }
}