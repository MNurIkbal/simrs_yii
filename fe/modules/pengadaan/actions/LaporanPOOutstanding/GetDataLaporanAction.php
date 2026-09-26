<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPOOutstanding;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\helpers\HandlingValueHelper as SetValue;

class GetDataLaporanAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $yiiRestfulParams = $this->controller->getFilter($request);

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

        $laporanPOOutstanding = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'info-purchase-order/laporan-po-outstanding',
            'method' => 'GET',
            'payload' => [
                'query' => $yiiRestfulParams
            ],
            'returnResponse' => true
        ]);
        $no = $request->get('start',1);
        foreach ($laporanPOOutstanding['data']['data'] as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $value['tgl_pr'] = SetValue::dateTimeValue($value['tgl_pr']);
            $value['tgl_po_dibuat'] = SetValue::dateTimeValue($value['tgl_po_dibuat']);
            $value['harga'] = DocoHelpers::formatNumber($value['harga']);
            $value['tgl_validasi'] = SetValue::dateTimeValue($value['tgl_validasi']);
            $value['sub_total'] = DocoHelpers::formatNumber($value['sub_total']);
            $value['total'] = DocoHelpers::formatNumber($value['total']);
            $value['supplier_kode'] = SetValue::nullValue($value['supplier_kode']);
            $value['supplier_nama'] = SetValue::nullValue($value['supplier_nama']);
            $value['manufaktur_nama'] = SetValue::nullValue($value['manufaktur_nama']);
            $value['po_balance'] = SetValue::nullValue($value['po_balance']);
            $value['catatan1'] = SetValue::nullValue($value['catatan1']);
            $value['catatan2'] = SetValue::nullValue($value['catatan2']);
            $value['tgl_approve'] = isset($value['tgl_approve']) ? date('d M Y H:i:s', strtotime($value['tgl_approve'])) : '';
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $laporanPOOutstanding['data']['_meta']['totalCount'];
        $result['recordsFiltered'] = $laporanPOOutstanding['data']['_meta']['totalCount'];
        return $result;
    }
}
