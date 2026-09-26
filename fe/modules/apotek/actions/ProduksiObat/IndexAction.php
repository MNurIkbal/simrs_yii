<?php

/**
 * @author : Asri Nurul M
 * Powered by Sirs
 */

namespace Doco\apotek\actions\ProduksiObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $title  = \Yii::t('fe', 'Informasi Produksi Obat');
        $_status =  [
            'Produksi' => "Produksi",
            'Define Material' => "Define Material",
            'Sudah Verifikasi' => "Sudah Verifikasi",
            'Batal Produksi' => "Batal Produksi"
        ];

        return $this->controller->render('index-produksi', get_defined_vars());
    }
}
