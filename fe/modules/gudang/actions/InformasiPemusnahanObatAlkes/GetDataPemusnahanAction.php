<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\InformasiPemusnahanObatAlkes;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataPemusnahanAction extends Action {
    public function run() {
        $id = DocoHelpers::decrypt($_GET['id']);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['pemusnahanobat_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = Yii::$app->docoRest->gudang->get('inf-pemusnahan-obat/data-pemusnahan?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data_pemusnahan = $body['response']['data'];
            $no = $request->get('start',1);
            $totalharga = 0;
            foreach ($data_pemusnahan as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['stok_satuan'] = DocoHelpers::formatNumber($value['stok']).' '.$value['satuan_kecil'];
                $totalharga = $totalharga + $value['jumlah_harganetto'];
                $value['tglkadaluarsa'] = date('d-M-Y', strtotime($value['tglkadaluarsa']));
                $value['jumlah_harganetto'] = DocoHelpers::formatNumber($value['jumlah_harganetto']);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['totalobat'] = $totalharga;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = 'x-'.$e->getMessage();
            return $result;
        }
    }
}