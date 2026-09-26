<?php

namespace app\modules\v1\actions\LapStockInventoryBarang;

use app\modules\v1\models\Instalasi;
use yii\helpers\ArrayHelper;
use yii\base\Action;

class GetListInstalasiAction extends Action
{
    public function run() {
        return Instalasi::find()->select(["instalasi_id","instalasi_nama"])->all();
    }
    
}
?>