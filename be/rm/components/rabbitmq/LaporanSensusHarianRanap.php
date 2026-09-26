<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanSensusHarianRanapTask,
};

class LaporanSensusHarianRanap extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_sensus_harian_ranap' => LaporanSensusHarianRanapTask::class,
        ];
    }
}