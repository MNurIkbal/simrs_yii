<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\InformasiPemusnahanObatAlkes;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    protected $_title = "Informasi Pemusnahan Obat Alkes";

    public function run() {
        $title = $this->_title;
        $instalasi = Yii::$app->docoVars->workspace('instalasi_name');
        $status_pemusnahan = [
            false   => "Belum Verifikasi",
            true    => "Sudah Verifikasi"
        ];
        return $this->controller->render('index', get_defined_vars());
    }
}