<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanPemakaianBmhpRuangan;

use Yii;
use yii\base\Action;
use yii\base\View;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class IndexAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $response = $this->controller->guzzleExec(Yii::$app->docoRest->gudang,[
            'url' => 'lap-pemakaian-bmhp-ruangan/list-data-filter',
            'method' => 'GET',
            'payload' => [
                'query' => []
            ],
        ]);
        $response = ArrayHelper::getValue($response, 'data', []);
        $listRuangan = ArrayHelper::getValue($response, 'ruangan', []);
        $listJenisObat = ArrayHelper::getValue($response, 'jenisObat', []);

        return $this->controller->render('index', get_defined_vars());
    }
}