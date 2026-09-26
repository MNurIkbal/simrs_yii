<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanSummaryStokOpname;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $module = $this->controller->_module;

        $apiRuangan = $this->controller->_restMaster->get('allow/list-ruangan');
        $apiRuangan = json_decode($apiRuangan->getBody(), true);
        $ruangan = $apiRuangan['response']['data'];

        $apiJenisObat = $this->controller->_restApotek->get('allow/get-jenis-obat-alkes');
        $apiJenisObat = json_decode($apiJenisObat->getBody(), true);
        $jenisObatAlkes = $apiJenisObat['response']['data'];

        return $this->controller->render('index', get_defined_vars());
    }
}
