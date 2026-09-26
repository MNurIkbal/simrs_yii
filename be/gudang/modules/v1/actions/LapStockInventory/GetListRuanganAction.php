<?php

namespace app\modules\v1\actions\LapStockInventory;

use app\modules\v1\models\Ruangan;
use yii\helpers\ArrayHelper;
use yii\base\Action;

class GetListRuanganAction extends Action
{
    public function run() {
        try {
            return ArrayHelper::map(Ruangan::find()->orderBy(["ruangan_nama" => SORT_ASC])->all(), 'ruangan_nama', 'ruangan_nama');
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}


?>