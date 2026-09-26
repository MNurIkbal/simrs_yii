<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\helpers\HandlingValueHelper as SetValue;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = $this->controller->getFilter($request);

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {   
            $url = 'lap-purchase-order/get-data?' . http_build_query($yiiRestfulParams);
            $response = Yii::$app->docoRest->pengadaan->get($url);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                if(is_null($value['qty_terima'])) {
                    $qty_terima = " - ";
                } else {
                    $qty_terima = $value['qty_terima'] . " " . $value['satuan_terima'];
                }

                if(is_null($value['ppn'])) {
                    $value['ppn'] = '0';
                }

                $no++;
                $value['rowNum'] = $no;
                $value['no_penerimaan'] = SetValue::nullValue($value['no_penerimaan']);
                $value['no_pr'] = SetValue::nullValue($value['no_pr']);
                $value['catatan_1'] = SetValue::nullValue($value['catatan_1']);
                $value['catatan_2'] = SetValue::nullValue($value['catatan_2']);
                $value['alasan_batal_po'] = SetValue::nullValue($value['alasan_batal_po']);
                $value['alasan_batal_pr'] = SetValue::nullValue($value['alasan_batal_pr']);
                $value['tgl_verifikasi'] = SetValue::dateValue($value['tgl_verifikasi']);
                $value['tgl_create_po'] = SetValue::dateValue($value['tgl_create_po']);
                $value['tanggal_penerimaan'] = SetValue::dateValue($value['tanggal_penerimaan']);
                $value['tanggal_pr'] = SetValue::dateValue($value['tanggal_pr']);
                $value['hna'] = DocoHelpers::formatNumber($value['hna']);
                $value['harga_akhir'] = DocoHelpers::formatNumber($value['harga_akhir']);
                $value['jenis_pr'] = $value['jenis_pr'] == false ? 'Non Cito' : 'Cito';
                $value['status_pr'] = SetValue::nullValue($value['status_pr']);
                $value['qty_po'] = $value['qty_po']." ".$value['satuan_po'];
                $value['qty_terima'] = $qty_terima;
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
}
