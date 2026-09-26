<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\AllowKonfig;

use Yii;
use yii\base\Action;
use Doco\components\DocoConstants;
use app\modules\v1\models\KonfigFarmasi;

class BatalPemesananByAction extends Action {
    public function run() {
        try {
            $model = KonfigFarmasi::findOne(1);
            return $model->batal_pesan_by;
        } catch(\Exception $e){
            return false;
        }
    }
}