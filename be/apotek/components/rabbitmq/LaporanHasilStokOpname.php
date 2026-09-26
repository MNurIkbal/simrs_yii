<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanHasilStokOpnameTask,
};

class LaporanHasilStokOpname extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_hasil_stok_opname' => LaporanHasilStokOpnameTask::class,
        ];
    }
}