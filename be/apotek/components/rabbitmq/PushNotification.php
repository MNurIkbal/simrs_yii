<?php

namespace app\components\rabbitmq;

use Yii;
use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\notifikasi\{
    FarmasiNotificationTask,
    FarmasiUpdateNotificationTask
};

class PushNotification extends BaseConsumer
{
    public function register()
    { 
        return [
            'trigger_push_notif_farmasi' => FarmasiNotificationTask::class,
            'trigger_update_notif_farmasi' => FarmasiUpdateNotificationTask::class,
        ];
    }
}