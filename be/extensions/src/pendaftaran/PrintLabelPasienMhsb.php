<?php

namespace Extensions\pendaftaran;

use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\Services\Cache;

class PrintLabelPasienMhsb extends \Doco\processes\PrintLabelPasienProcess
{
    protected function processFlow()
    {
        $this->populateData();
        $kunjungan = $this->getDataPasien();
        $this->setTemplete();

        $print = new DocoPrint(null,'P');
        $print->attributes = [
            '#data#' => Yii::$app->controller->renderPartial($this->loc, [
                'data' => $kunjungan,
                'jumlah_data' => $this->jumlah,
                'jenis' => $this->jenis,
                'ktp' => $this->ktp,
                self::JK => Cache::getLookupByType(self::JK)
            ])
        ];
        $print->Output();
    }
}