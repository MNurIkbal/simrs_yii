<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanKasirTask,
    LaporanCaraBayarKasirTask,
};

class KasirLaporan extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_kasir' => LaporanKasirTask::class,
            'laporan_cara_bayar_kasir' => LaporanCaraBayarKasirTask::class,
        ];
    }
}