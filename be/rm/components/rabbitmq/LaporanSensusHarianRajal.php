<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use \app\components\rabbitmq\laporan\{
    LaporanSensusHarianRajalTask,
};

class LaporanSensusHarianRajal extends BaseConsumer
{
    public function register()
    { 
        return [
            'laporan_sensus_harian_rajal' => LaporanSensusHarianRajalTask::class,
        ];
    }
}