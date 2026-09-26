<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanTotalRekapitulasiPenjualanFarmasi;

use Yii;
use yii\base\Action;
use yii\base\View;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $title  = $this->controller->_title;
        
        $api = $this->controller->_restApotek->get('allow/get-jenis-obat-alkes');
        $api = json_decode($api->getBody(), true);
        $jenisObatAlkes = $api['response']['data'];

        return $this->controller->render('index', get_defined_vars());
    }
}
