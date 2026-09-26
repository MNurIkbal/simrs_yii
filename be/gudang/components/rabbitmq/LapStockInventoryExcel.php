<?php

namespace app\components\rabbitmq;

use Doco\rabbitmq\BaseConsumer;
use app\components\rabbitmq\excel\LapStockInventoryTask;

class LapStockInventoryExcel extends BaseConsumer
{
    public function register()
    { 
        return [
            'excel_lap_stock_inventory' => LapStockInventoryTask::class,
        ];
    }
}