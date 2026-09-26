<?php

namespace app\modules\gudang\processes;

use Yii;

class AdjustmentObatScenarioProcess extends \app\components\DocoBaseProcessExtension
{
    const DEFAULT_SCENARIO = 'def_required';

    protected function processFlow($controller)
    {
        return [
            'scenario' => self::DEFAULT_SCENARIO,
            'current_user' => false
        ];
    }
}