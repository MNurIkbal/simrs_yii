<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\TransaksiResep;

use Yii;
use yii\base\Action;

class SimpanResepturAction extends Action {
    public function run() {
        return Yii::$app->docoPlugin->execute('reseptur');
    }
}