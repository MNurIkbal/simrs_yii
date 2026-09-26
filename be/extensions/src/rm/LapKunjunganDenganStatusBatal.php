<?php

namespace Extensions\rm;

use Yii;
use Doco\components\DocoConstants;

class LapKunjunganDenganStatusBatal extends \Doco\processes\LapKunjunganStatusProcess
{
    protected function processFlow()
    {
        return [
            DocoConstants::STATUS_PERIKSA_BTL_KUNJ,
            DocoConstants::STATUS_PERIKSA_BTL_PERIKSA,
            DocoConstants::STATUS_RANAP_BATAL_RAWAT,
        ];
    }
}
