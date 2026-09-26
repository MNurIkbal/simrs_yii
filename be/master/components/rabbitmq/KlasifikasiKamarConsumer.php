<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanKlasifikasiKamarTask,
};

class KlasifikasiKamarConsumer extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_klasifikasi_kamar' => LaporanKlasifikasiKamarTask::class,
        ];
    }
}