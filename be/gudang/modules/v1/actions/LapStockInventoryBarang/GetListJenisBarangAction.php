<?php
namespace app\modules\v1\actions\LapStockInventoryBarang;

use app\modules\v1\models\KelompokBarang;
use yii\helpers\ArrayHelper;
use yii\base\Action;

class GetListJenisBarangAction extends Action
{
    public function run()
    {
        return ArrayHelper::map(KelompokBarang::find()->orderBy(['kelompokbarang_nama' => SORT_ASC])->select(['kelompokbarang_id','kelompokbarang_nama'])->all(),'kelompokbarang_nama','kelompokbarang_nama'); 
    }
}

?>