<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanRekapitulasiPenjualanTask,
};

class LaporanRekapitulasiPenjualanConsumer extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_rekapitulasi_penjualan' => LaporanRekapitulasiPenjualanTask::class,
        ];
    }
}