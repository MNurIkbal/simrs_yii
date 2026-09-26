<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPurchaseRequisitionOutstanding;

use Yii;
use yii\base\Action;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\components\helpers\HandlingValueHelper as SetValue;

class GetDataAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['type'] = DocoConstants::JENIS_OBAT;

        if (isset($filter['advanced-filter']['tgl_pr'])) {
            $tgl_pr = explode(' - ', $filter['advanced-filter']['tgl_pr']);
            $tgl_awal = $tgl_pr[0];
            $tgl_akhir = $tgl_pr[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tgl_pr_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tgl_pr_akhir'] = $tgl_akhir_format;
            $filter['advanced-filter']['tgl_pr'] = date('Y-m-d', strtotime($tgl_pr[0])).' - '.date('Y-m-d', strtotime($tgl_pr[1]));
        }

        if (isset($request->get()['is_prcyto'])) {
            $filter['advanced-filter']['is_cyto'] = $request->get('is_prcyto');
        }

        if (isset($request->get()['is_admin'])) {
            $filter['advanced-filter']['is_admin'] = $request->get('is_admin');
        }

        if (isset($request->get()['is_consignment'])) {
            $filter['advanced-filter']['is_consigment'] = $request->get('is_consignment');
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        $laporanPROutstanding = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'lap-purchase-requisition-outstanding/get-data',
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
            $value['tgl_pr'] = SetValue::dateTimeValue($value['tgl_pr']);
            $value['kode_obat'] = SetValue::nullValue($value['kode_obat']);
            $value['obatalkes_nama'] = SetValue::nullValue($value['obatalkes_nama']);
            $value['jenis_obat'] = SetValue::nullValue($value['jenis_obat']);
            $value['qty_input'] = SetValue::nullValue($value['qty_input']);
            $value['satuan'] = SetValue::nullValue($value['satuan']);
            $value['uom'] = SetValue::nullValue($value['uom']);
            $value['status_pr'] = SetValue::nullValue($value['status_pr']);
            $value['manufaktur_nama'] = SetValue::nullValue($value['manufaktur_nama']);
            $value['status_obat'] = SetValue::nullValue($value['status_obat']);
            $value['catatan_pr'] = SetValue::nullValue($value['catatan_pr']);
            $value['alasan'] = SetValue::nullValue($value['alasan']);
            $value['pegawai'] = SetValue::nullValue($value['pegawai']);
            $value['tgl_approve'] = isset($value['tgl_approve']) ? date('d M Y H:i:s', strtotime($value['tgl_approve'])) : '';
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $laporanPROutstanding['data']['_meta']['totalCount'];
        $result['recordsFiltered'] = $laporanPROutstanding['data']['_meta']['totalCount'];
        return $result;
    }
}
