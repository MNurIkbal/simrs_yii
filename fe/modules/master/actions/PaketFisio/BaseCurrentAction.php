<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use yii\base\Action;

class BaseCurrentAction extends Action
{
    public $restMaster;

    public function init()
    {
        $this->restMaster = Yii::$app->docoRest->master;
    }
}
