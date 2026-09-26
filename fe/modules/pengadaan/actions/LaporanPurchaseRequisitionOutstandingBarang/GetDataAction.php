<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPurchaseRequisitionOutstandingBarang;

use Yii;
use yii\base\Action;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\components\helpers\HandlingValueHelper as SetValue;

class GetDataAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['type'] = DocoConstants::JENIS_BARANG;

        if (isset($filter['advanced-filter']['create_date'])) {
            $tgl_pr = explode(' - ', $filter['advanced-filter']['create_date']);
            $tgl_awal = $tgl_pr[0];
            $tgl_akhir = $tgl_pr[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tgl_pr_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tgl_pr_akhir'] = $tgl_akhir_format;
            unset($filter['advanced-filter']['create_date']);
        }

        if (isset($request->get()['is_prcyto'])) {
            $filter['advanced-filter']['is_cyto'] = $request->get('is_prcyto');
        }

        if (isset($request->get()['is_admin'])) {
            $filter['advanced-filter']['is_admin'] = $request->get('is_admin');
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        $laporanPROutstanding = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'lap-purchase-requisition-outstanding-barang/get-data',
            'method' => 'GET',
            'payload' => [
                'query' => $filter
            ],
            'returnResponse' => true
        ]);
        $no = $request->get('start',1);
        foreach ($laporanPROutstanding['data']['data'] as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $value['no_pr'] = SetValue::nullValue($value['no_pr']);
            $value['create_date'] = SetValue::dateTimeValue($value['create_date']);
            $value['approval_date'] = SetValue::dateTimeValue($value['approval_date']);
            $value['item_code'] = SetValue::nullValue($value['item_code']);
            $value['item_name'] = SetValue::nullValue($value['item_name']);
            $value['category'] = SetValue::nullValue($value['category']);
            $value['qty'] = SetValue::nullValue($value['qty']);
            $value['uom'] = SetValue::nullValue($value['uom']);
            $value['from_uom'] = SetValue::nullValue($value['from_uom']);
            $value['factor'] = SetValue::nullValue($value['factor']);
            $value['to_uom'] = SetValue::nullValue($value['to_uom']);
            $value['remarks'] = SetValue::nullValue($value['remarks']);
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $laporanPROutstanding['data']['_meta']['totalCount'];
        $result['recordsFiltered'] = $laporanPROutstanding['data']['_meta']['totalCount'];
        return $result;
    }
}
