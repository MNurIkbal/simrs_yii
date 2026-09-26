<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\mcu\actions\PemeriksaanMcu;

use Yii;
use yii\base\Action;
use yii\base\View;

class RiwayatPenyakitAction extends Action {

    public function run() {
        return Yii::$app->docoPlugin->execute($this->controller,'pemeriksaan_periksa');
    }
}