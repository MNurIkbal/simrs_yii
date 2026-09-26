<?php

namespace Doco\gudang\actions\LaporanPemakaianBarang;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\Traits\ControllerHelperTrait;

class GetListDataAction extends Action
{
    use ControllerHelperTrait;
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payload = DocoDatatableHelper::advancedFilterParam();

        if(isset($payload['advanced-filter']['tgl_transaksi'])) {
            $explode = explode(' - ', $payload['advanced-filter']['tgl_transaksi']);
            $payload['advanced-filter']['tgl_transaksi'] = date('Y-m-d', strtotime($explode[0])).' - '.date('Y-m-d', strtotime($explode[1]));
        }else{
            $payload['advanced-filter']['tgl_transaksi'] = date('Y-m-d').' - '.date('Y-m-d');
        }

        $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'lap-pemakaian-barang/get-list-data',
            'payload' => [
                'query' => $payload
            ]
        ]);
        
        if(!empty($response['data'])){
            $no = $request->get('start',1);
            foreach ($response['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['qty'] = DocoHelpers::formatNumber($value['qty']);
                $value['harga_netto'] = DocoHelpers::formatNumber($value['harga_netto']);
                $value['total_harga'] = DocoHelpers::formatNumber($value['total_harga']);
                $value['tgl_transaksi'] = date('d M Y H:i:s', strtotime($value['tgl_transaksi']));
                $data[$key] = $value;
            }

             $result['data'] = $data;
        }else{
             $result['data'] = '';
        }
            
         $result['recordsTotal'] = $response['_meta']['totalCount'];
         $result['recordsFiltered'] = $response['_meta']['totalCount'];
            
         return $result;
    }
}
