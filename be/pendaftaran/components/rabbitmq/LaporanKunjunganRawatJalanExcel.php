<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\laporan\LaporanKunjunganRawatJalanTask;

class LaporanKunjunganRawatJalanExcel extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_kunjungan_rawat_jalan' => LaporanKunjunganRawatJalanTask::class,
        ];
    }
}