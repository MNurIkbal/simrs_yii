<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions;

use Yii;
use yii\base\Action;

class IndexAction extends Action {
    protected $_title = "Informasi Adjustment Obat Alkes";
    protected $_jenis_adjustment = [
        "adjusmen_masuk" => "Adjustment Masuk",
        "adjusmen_keluar" => "Adjustment Keluar"
    ];

    public function run() {
        $title = $this->_title;
        $jenis_adjustment = $this->_jenis_adjustment;
        return $this->controller->render('index', get_defined_vars());
    }
}