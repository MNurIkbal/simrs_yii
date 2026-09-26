<?php
namespace app\modules\v1\actions\LapStockInventory;

use app\modules\v1\models\JenisObatAlkes;
use yii\helpers\ArrayHelper;
use yii\base\Action;

class GetListJenisObatAction extends Action
{
    public function run()
    {
        try{
            return ArrayHelper::map(JenisObatAlkes::find()->orderBy(['jenisobatalkes_nama' => SORT_ASC])->select(['jenisobatalkes_id','jenisobatalkes_nama'])->all(),'jenisobatalkes_nama','jenisobatalkes_nama');
        }catch (\yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());

        }
    }
}

?>