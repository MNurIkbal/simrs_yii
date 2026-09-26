<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanResponTimeAnalisis;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class IndexMedisAction extends Action {
    public function run() {
        $title = $this->controller->_title." Medis";
        $module = $this->controller->_module;
        $unduh_excel = $module.'export-excel?tipe=obat';
        return $this->controller->render('index', get_defined_vars());
    }
}
