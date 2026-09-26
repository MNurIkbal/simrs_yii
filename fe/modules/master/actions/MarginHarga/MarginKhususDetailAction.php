<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class MarginKhususDetailAction extends Action {
    public function run($id) {
        return $this->controller->renderAjax('_detail_margin_khusus', get_defined_vars());
    }
}