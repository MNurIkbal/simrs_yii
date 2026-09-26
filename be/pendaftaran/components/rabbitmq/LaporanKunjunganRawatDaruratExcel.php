<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\laporan\LaporanKunjunganRawatDaruratTask;

class LaporanKunjunganRawatDaruratExcel extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_kunjungan_rawat_darurat' => LaporanKunjunganRawatDaruratTask::class,
        ];
    }
}