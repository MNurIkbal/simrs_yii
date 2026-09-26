<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PenjualanResep;

use Yii;
use yii\base\Action;

class SaveAction extends Action {
    public function run() {
        // to-do: ganti ke reusable extension action
        return Yii::$app->docoPlugin->execute('penjualan_resep');
    }
}