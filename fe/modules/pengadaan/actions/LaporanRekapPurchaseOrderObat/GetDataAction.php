<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanRekapPurchaseOrderObat;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\components\helpers\HandlingValueHelper as SetValue;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends Action {
    public function run() {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $filter  = DocoDatatableHelper::convertToRestfulParams($request->get());

            $response = Yii::$app->docoRest->pengadaan->get('lap-rekap-purchase-order-obat/get-data', [
                'query' => $filter
            ]);

            $body = json_decode($response->getBody(), true);
            $no   = $request->get('start', 1);
            $data = [];

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum']          = $no;
                $value['supplier_kode']   = SetValue::nullValue($value['supplier_kode']);
                $value['supplier_nama']   = SetValue::nullValue($value['supplier_nama']);
                $value['no_po']           = SetValue::nullValue($value['no_po']);
                $value['tgl_po']          = !isset($value['tgl_po']) ? "-" : date('d-m-Y', strtotime($value['tgl_po']));
                $value['tgl_validasi']    = !isset($value['tgl_validasi']) ? "-" : date('d-m-Y', strtotime($value['tgl_validasi']));
                $value['status_po']       = SetValue::nullValue($value['status_po']);
                $value['tgl_batal_po']    = !isset($value['tgl_batal_po']) ? "-" : date('d-m-Y', strtotime($value['tgl_batal_po']));
                $value['alasan_batal_po'] = SetValue::nullValue($value['alasan_batal_po']);
                $value['total_harga'] = DocoHelpers::formatNumber($value['total_harga']);
                $data[$key]               = $value;
            }

            $result['data']            = $data;
            $result['recordsTotal']    = $body['response']['_meta']['totalCount'];
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
