<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;

class GetDetailAction extends Action {
    public function run($id) {
        $result = $this->controller->detailReturResep($id);
        return $result->asArray()->all();
    }
}