<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\excel\{
    LaporanMutasiObatAlkesExcelTask,
};

class LaporanMutasiObatAlkesExcelConsumer extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_mutasi_obat_alkes_excel' => LaporanMutasiObatAlkesExcelTask::class,
        ];
    }
}