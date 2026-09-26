<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanPemakaianBmhpPasien;

use Yii;
use yii\base\Action;
use yii\base\View;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        return $this->controller->render('index', get_defined_vars());
    }
}
