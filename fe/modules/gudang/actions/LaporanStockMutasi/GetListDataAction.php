<?php


namespace Doco\gudang\actions\LaporanStockMutasi;

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

        if(isset($payload['advanced-filter']['tanggal_inventory'])) {
            $explode = explode(' - ', $payload['advanced-filter']['tanggal_inventory']);
            $payload['advanced-filter']['tanggal_inventory'] = date('Y-m-d', strtotime($explode[0])).' - '.date('Y-m-d', strtotime($explode[1]));
        }else{
            $payload['advanced-filter']['tanggal_inventory'] = date('Y-m-d').' - '.date('Y-m-d');
        }

        $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'lap-stock-mutasi/get-list-data',
            'payload' => [
                'query' => $payload
            ]
        ]);
        
        if(!empty($response['data'])){
            $no = $request->get('start',1);
            foreach ($response['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['hna'] = DocoHelpers::formatNumber($value['hna']);
                $value['qty_total_awal'] = DocoHelpers::formatNumber($value['qty_total_awal']);
                $value['total_nilai_awal'] = DocoHelpers::formatNumber($value['total_nilai_awal']);
                $value['qtystok_in'] = DocoHelpers::formatNumber($value['qtystok_in']);
                $value['qtystok_out'] = DocoHelpers::formatNumber($value['qtystok_out']);
                $value['total_qty_diterima'] = DocoHelpers::formatNumber($value['total_qty_diterima']);
                $value['total_nilai_diterima'] = DocoHelpers::formatNumber($value['total_nilai_diterima']);
                $value['total_qty_keluar'] = DocoHelpers::formatNumber($value['total_qty_keluar']);
                $value['total_nilai_keluar'] = DocoHelpers::formatNumber($value['total_nilai_keluar']);
                $value['total_qty_akhir'] = DocoHelpers::formatNumber($value['total_qty_akhir']);
                $value['total_nilai_akhir'] = DocoHelpers::formatNumber($value['total_nilai_akhir']);
                $value['turn_over'] = DocoHelpers::formatNumber($value['turn_over']);
                $value['tanggal_inventory'] = '';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
            return $result;
        }else{
            $result['data'] = '';
            
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
            
            return $result;
        }
    }
}
