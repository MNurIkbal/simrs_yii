<?php

namespace Doco\pendaftaran\actions\RujukanBantaran;

use Yii;
use yii\base\Action;

class PeriksaAction extends Action
{
    public function run()
    {   
        return Yii::$app->docoPlugin->execute($this->controller, 'rujukan_bantaran');
    }
}
