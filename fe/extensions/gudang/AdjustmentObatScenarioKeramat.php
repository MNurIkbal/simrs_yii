<?php

namespace app\extensions\gudang;

use Yii;

class AdjustmentObatScenarioKeramat extends \app\components\DocoBaseProcessExtension
{
    const PEGAWAI_SCENARIO = 'peg_required';

    protected function processFlow($controller)
    {
        return [
            'scenario' => self::PEGAWAI_SCENARIO,
            'current_user' => true
        ];
    }
}