<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\MutasiObat;

use Yii;
use yii\base\Action;
use yii\base\View;

class IndexAction extends Action {

    public function run() {
        return Yii::$app->docoPlugin->execute($this->controller,'mutasi_obat_index');
    }
}