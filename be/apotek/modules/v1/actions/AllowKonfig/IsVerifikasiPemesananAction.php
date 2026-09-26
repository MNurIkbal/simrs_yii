<?php
namespace app\modules\v1\actions\AllowKonfig;

use Yii;
use yii\base\Action;
use Doco\components\DocoConstants;
use app\modules\v1\models\KonfigFarmasi;

class IsVerifikasiPemesananAction extends Action
{
    public function run()
    {
        try{
            $model = KonfigFarmasi::findOne(1);
            return $model->is_verifpemesanan;
        }catch(\Exception $e){
            return false;
        }
    }
}