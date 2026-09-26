<?php

namespace app\modules\v1\actions;

use Yii;
use yii\base\Action;

class GenerateResepKronisAction extends Action
{
    public function run() {
        return Yii::$app->docoPlugin->execute('generate_resep_kronis');
    }
}
