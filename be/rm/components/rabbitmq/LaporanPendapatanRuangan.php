<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanPendapatanRuanganTask,
};

class LaporanPendapatanRuangan extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_pendapatan_ruangan' => LaporanPendapatanRuanganTask::class,
        ];
    }
}