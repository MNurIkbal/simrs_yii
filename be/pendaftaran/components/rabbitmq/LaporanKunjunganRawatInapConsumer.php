<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanKunjunganRawatInapTask,
};

class LaporanKunjunganRawatInapConsumer extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_kunjungan_rawat_inap' => LaporanKunjunganRawatInapTask::class,
        ];
    }
}
