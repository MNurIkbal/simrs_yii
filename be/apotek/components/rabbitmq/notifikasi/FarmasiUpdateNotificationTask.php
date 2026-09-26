<?php

namespace app\components\rabbitmq\notifikasi;

use Yii;
use Doco\rabbitmq\task\IntegrasiTask;
use Doco\Notifications\FarmasiNotification;

class FarmasiUpdateNotificationTask extends FarmasiNotificationTask
{
    public function prosesSync()
    {
        FarmasiNotification::updateNotif();
    }
}
