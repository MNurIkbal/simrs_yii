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

class MarginHargaObatAction extends Action {
    protected $_title = "Margin Harga";

    public function run() {
        $title = $this->_title;
        return $this->controller->renderAjax('margin_harga_obat', get_defined_vars());
    }
}