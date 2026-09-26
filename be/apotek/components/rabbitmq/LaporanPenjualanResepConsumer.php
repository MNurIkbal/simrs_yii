<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanPenjualanResepTask,
};

class LaporanPenjualanResepConsumer extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_penjualan_resep' => LaporanPenjualanResepTask::class,
        ];
    }
}