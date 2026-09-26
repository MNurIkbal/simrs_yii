<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\laporan\LaporanKunjunganRumahSakitTask;

class LaporanKunjunganRumahSakitExcel extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_kunjungan_rumah_sakit' => LaporanKunjunganRumahSakitTask::class,
        ];
    }
}