<?php

namespace app\components\rabbitmq\notifikasi;

use Yii;
use Doco\rabbitmq\task\IntegrasiTask;
use app\modules\v1\models\InformasiResepturView;
use Doco\Notifications\FarmasiNotification;

class FarmasiNotificationTask extends IntegrasiTask
{
    protected $reseptur_id;

    public function prosesSync()
    {
        $time = microtime(true);
        $infoReseptur = InformasiResepturView::find()->where(['reseptur_id' => $this->reseptur_id])->asArray()->one();
        FarmasiNotification::newResep($infoReseptur);
        Yii::error(json_encode([
            'Service' => 'Notifikasi Farmasi',
            'payload' => $this->reseptur_id,
            'timestamp' => date('Y-m-d H:i:s'),
            'stimeNotifikasi' => number_format(microtime(true)-$time, 3),
        ]));
    }
}
