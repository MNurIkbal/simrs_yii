<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * A product of PT. CRN
 * Powered by Sirs
 */

namespace app\modules\pengadaan\processes;

use Yii;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\helpers\HandlingValueHelper as SetValue;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class LaporanPurchaseOrderGetdataProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller){
        $request = Yii::$app->request;
        $yiiRestfulParams = $controller->getFilter($request);
        $actionId = $request->get('actionId','BARANG');
        $tipe = $actionId == 'index' ? 'OBAT' : 'BARANG';

        if (!isset($yiiRestfulParams['advanced-filter']['type'])) {
            $yiiRestfulParams['advanced-filter']['type'] = $tipe;
        }
        
        if (isset($request->get()['is_prcyto'])) {
            $yiiRestfulParams['advanced-filter']['is_cito'] = $request->get('is_prcyto');
        }

        if (isset($request->get()['is_admin'])) {
            $yiiRestfulParams['advanced-filter']['is_admin'] = $request->get('is_admin');
        }

        if (isset($request->get()['is_consignment'])) {
            $yiiRestfulParams['advanced-filter']['is_consigment'] = $request->get('is_consignment');
        }


        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        $laporanPO = $controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'lap-purchase-order/get-data?tipe='.$tipe,
            'method' => 'GET',
            'payload' => [
                'query' => $yiiRestfulParams
            ],
            'returnResponse' => true
        ]);
        $no = $request->get('start',1);
        $data = [];
        foreach ($laporanPO['data']['data'] as $key => $value) {
            $PoRemarks = ArrayHelper::getValue($value, 'remarks');
            $catatanInternal = ArrayHelper::getValue($value, 'catatan_1');
            $catatanEksternal = ArrayHelper::getValue($value, 'catatan_2');

            $no++;
            $value['rowNum'] = $no;
            $value['jenis_pr'] = $value['jenis_pr'] == false ? 'Non Cito' : 'Cito';
            $value['tanggal_verifikasi_pr'] = SetValue::dateTimeValue($value['tanggal_verifikasi_pr']);
            $value['tanggal_po'] = SetValue::dateTimeValue($value['tanggal_po']);
            $value['tgl_verifikasi_po'] = SetValue::dateTimeValue($value['tgl_verifikasi_po']);
            $value['reject_date'] = SetValue::dateTimeValue($value['reject_date']);
            $value['price'] = isset($value['price']) && !empty($value['price']) ? DocoHelpers::formatNumber($value['price']) : 0;
            $value['gross_amount'] = isset($value['gross_amount']) && !empty($value['gross_amount']) ? DocoHelpers::formatNumber($value['gross_amount']) : 0;
            $value['nett_amount'] = isset($value['nett_amount']) && !empty($value['nett_amount']) ? DocoHelpers::formatNumber($value['nett_amount']) : 0;
            $value['supplier_id'] = SetValue::nullValue($value['supplier_id']);
            $value['deduction_rupiah'] = isset($value['deduction_rupiah']) && !empty($value['deduction_rupiah']) ? DocoHelpers::formatNumber($value['deduction_rupiah']) : 0;
            $value['tanggal_penerimaan'] = SetValue::dateTimeValue($value['tanggal_penerimaan']);
            $value['remarks'] = trim($PoRemarks);
            $value['catatan_internal'] = trim($catatanInternal);
            $value['catatan_eksternal'] = trim($catatanEksternal);
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $laporanPO['data']['_meta']['totalCount'];
        $result['recordsFiltered'] = $laporanPO['data']['_meta']['totalCount'];
        return $result;
    }

}
