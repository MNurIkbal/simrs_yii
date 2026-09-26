<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPenerimaanObatAlkes;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\helpers\HandlingValueHelper as SetValue;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class GetDataAction extends Action {
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $filter = DocoDatatableHelper::advancedFilterParam();

        if(!isset($filter['advanced-filter']['tgl_penerimaan'])){
            $filter['advanced-filter']['tgl_penerimaan'] = date('Y-M-d').' - '.date('Y-M-d');
        }
        
        $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'lap-penerimaan-obat-alkes/get-data',
            'payload' => [
                'query' => $filter,
            ]
        ]);
        foreach(ArrayHelper::getValue($response, 'data', []) as $key => $value) {
            $response['data'][$key]['harga'] = SetValue::nullValue(DocoHelpers::formatNumber($value['harga']));
            $response['data'][$key]['total'] = SetValue::nullValue(DocoHelpers::formatNumber($value['total']));
            $response['data'][$key]['sub_total'] = SetValue::nullValue(DocoHelpers::formatNumber($value['sub_total']));
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }
}
