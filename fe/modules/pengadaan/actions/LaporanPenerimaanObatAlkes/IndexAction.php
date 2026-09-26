<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPenerimaanObatAlkes;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class IndexAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $module = $this->controller->_module;
        $filter = $this->filter();
        
        return $this->controller->render('index', [
            'title' => $title,
            'module' => $module,
            'filter' => $filter
        ]);
    }

    private function filter()
    {
        $getSupplier =  $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'allow/get-list-supplier',
            'method' => 'get',
            'payload' => [
                'query' => []
            ]
        ]);
        $listSupplier = [];
        foreach($getSupplier as $key => $value) {
            $result['id'] = ArrayHelper::getValue($value, 'supplier_id');
            $result['text'] = ArrayHelper::getValue($value, 'supplier_nama');
            $listSupplier[] = $result;
        }

        $getPayterm =  $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'allow/get-list-payterm',
            'method' => 'get',
            'payload' => [
                'query' => []
            ]
        ]);
        $listPayterm = [];
        foreach($getPayterm as $key => $value) {
            $result['id'] = ArrayHelper::getValue($value, 'payterm_id');
            $result['text'] = ArrayHelper::getValue($value, 'payterm_nama');
            $listPayterm[] = $result;
        }

        return [
            'listSupplier' => $listSupplier,
            'listPayterm' => $listPayterm
        ];
    }
}
