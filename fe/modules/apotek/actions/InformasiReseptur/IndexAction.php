<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\InformasiForm;

class IndexAction extends Action {
    protected $_title = "Informasi Reseptur";
    protected $_status_resep =  [
        346 => "Belum Proses",
        347 => "Dalam Proses",
        432 => "Batal Reseptur",
        660 => "Diserahkan"
    ];

    public function run() {
        return Yii::$app->docoPlugin->execute($this->controller,'informasi_reseptur_index');
    }
}