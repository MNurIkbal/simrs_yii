<?php

namespace app\modules\v1\actions\TransaksiPengajuanKlaim;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;

class BaseCurrentAction extends Action
{
    protected function beforeRun()
    {
        return true;
    }
}
