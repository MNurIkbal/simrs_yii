<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanKunjunganPasienRsTask,
};

class LaporanKunjunganPasienRs extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_kunjungan_pasien_rs' => LaporanKunjunganPasienRsTask::class,
        ];
    }
}