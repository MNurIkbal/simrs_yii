<?php


namespace Doco\gudang\actions\LaporanStockInventory;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetListDataAction extends Action
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $filter_tgl = ArrayHelper::getValue($filter,'advanced-filter.tanggal_inventory');
        if(is_null($filter_tgl)){
            $filter['advanced-filter']['tanggal_inventory'] = date('d-M-Y');
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $result['is_disabled'] = false;

        try {
            $response = Yii::$app->docoRest->gudang->get('lap-stock-inventory/get-list-data', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), true);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['harga_konversi'] = DocoHelpers::formatNumber($value['harga_konversi']);
                $value['harga_netto'] = DocoHelpers::formatNumber($value['harga_netto']);
                $value['harga'] = DocoHelpers::formatNumber($value['harga']);
                $value['total_harga'] = DocoHelpers::formatNumber($value['total_harga']);
                $value['nilai_konv'] = DocoHelpers::formatNumber($value['nilai_konv']);
                $value['wa_satuan_kecil'] = DocoHelpers::formatNumber($value['wa_satuan_kecil']);
                $value['wa_satuan_besar'] = DocoHelpers::formatNumber($value['wa_satuan_besar']);
                $value['baseprice_besar'] = DocoHelpers::formatNumber($value['baseprice_besar']);
                $value['baseprice_kecil'] = DocoHelpers::formatNumber($value['baseprice_kecil']);
                $value['total_satuankecil'] = DocoHelpers::formatNumber($value['total_satuankecil']);
                $value['total_satuanbesar'] = DocoHelpers::formatNumber($value['total_satuanbesar']);
                $value['total_satuankecil_netto'] = DocoHelpers::formatNumber($value['total_satuankecil_netto']);
                $value['total_satuanbesar_netto'] = DocoHelpers::formatNumber($value['total_satuanbesar_netto']);
                $value['tanggal_inventory'] = '';
                $data[$key] = $value;
            }

            $result['data'] = $data;
        
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['message'] = $e->getMessage();
            
            return $result;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    private function convDate ($date) {
        $result = '-';
        
        if($date != '-' && $date != 'null') {
            $result = DocoHelpers::convDateTime($date, true, false);
        }

        return $result;
    }
}
