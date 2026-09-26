<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\mcu\actions\PemeriksaanFisik;

use Yii;
use yii\base\Action;
use yii\base\View;

class PemeriksaanFisikAction extends Action {

    public function run() {
        return Yii::$app->docoPlugin->execute($this->controller,'pemeriksaan_fisik_periksa');
    }
}
