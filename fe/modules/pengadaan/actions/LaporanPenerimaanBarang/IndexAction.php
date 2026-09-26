<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPenerimaanBarang;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $module = $this->controller->_module;
        $status_penerimaan = [
            'Sudah Diverifikasi' => 'Sudah Diverifikasi',
            'Belum Diverifikasi' => 'Belum Diverifikasi',
            'Dibatalkan' => 'Dibatalkan'
        ];

        $list_supplier  = self::getList('supplier');
        $list_payterm   = self::getList('payterm');

        return $this->controller->render('index', get_defined_vars());
    }

    private function getList($key)
    {
        try {
            $response = Yii::$app->docoRest->pengadaan->get('allow/get-list-' . $key, [
                'form_params' => []
            ]);
            $response = json_decode($response->getBody(), true);
            $list = isset($response['response']) ? $response['response'] : [];
        } catch (RequestException $e) {
            $list = [];
        }

        return $list;
    }
}
