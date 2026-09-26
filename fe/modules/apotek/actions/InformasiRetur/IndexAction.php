<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiRetur;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\InformasiForm;
use app\components\DocoConstants;

class IndexAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $model = new InformasiForm;
        $cara_bayar = \Yii::$app->cache->get('carabayar');
        
        $request = $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'method' => 'get',
            'url' => 'allow/get-ruangan-by',
            'payload' => [
                'query' => [
                    'id' => DocoConstants::INSTALASI_FARMASI
                ]
            ]
        ]);
        
        $ruangan = ArrayHelper::map($request, 'ruangan_nama', 'ruangan_nama');
        $lookup = $this->controller->guzzleExec(Yii::$app->docoRest->apotek,[
            'url' => 'allow/get-lookup',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'type' => 'status_retur'
                ]
            ],
        ]);
        $status_retur = ArrayHelper::map($lookup['data'], 'lookup_name', 'lookup_name');
        if(!$cara_bayar){
            $response = $this->controller->_restApotek->get('allow/get-carabayar?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), True);
            $carabayar_data = ArrayHelper::map($body['response']['data'],'carabayar_nama','carabayar_nama');
            \Yii::$app->cache->set('carabayar', $carabayar_data, 60);
            $cara_bayar = $carabayar_data;
        }

        return $this->controller->render('index', get_defined_vars());
    }
}