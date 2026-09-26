<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPenerimaanBarang;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\helpers\HandlingValueHelper as SetValue;

class GetDataAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($filter['advanced-filter']['tgl_penerimaan'])) {
            $tgl_pr = explode(' - ', $filter['advanced-filter']['tgl_penerimaan']);
            $tgl_awal = $tgl_pr[0];
            $tgl_akhir = $tgl_pr[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tgl_penerimaan_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tgl_penerimaan_akhir'] = $tgl_akhir_format;
            unset($filter['advanced-filter']['tgl_penerimaan']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        $laporanPenerimaanBarang = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'lap-penerimaan-barang/get-data',
            'method' => 'GET',
            'payload' => [
                'query' => $filter
            ],
            'returnResponse' => true
        ]);
        $no = $request->get('start',1);
        foreach ($laporanPenerimaanBarang['data']['data'] as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $value['supplier_kode'] = SetValue::nullValue($value['supplier_kode']);
            $value['supplier_nama'] = SetValue::nullValue($value['supplier_nama']);
            $value['supplier_id'] = SetValue::nullValue($value['supplier_id']);
            $value['payterm_nama'] = SetValue::nullValue($value['payterm_nama']);
            $value['tgl_penerimaan'] = SetValue::dateTimeValue($value['tgl_penerimaan']);
            $value['no_penerimaan'] = SetValue::nullValue($value['no_penerimaan']);
            $value['diterima_oleh'] = SetValue::nullValue($value['diterima_oleh']);
            $value['status_penerimaan'] = SetValue::nullValue($value['status_penerimaan']);
            $value['tgl_po'] = SetValue::dateTimeValue($value['tgl_po']);
            $value['tgl_validasi_po'] = SetValue::dateTimeValue($value['tgl_validasi_po']);
            $value['nomor_po'] = SetValue::nullValue($value['nomor_po']);
            $value['kode_item'] = SetValue::nullValue($value['kode_item']);
            $value['barang_nama'] = SetValue::nullValue($value['barang_nama']); 
            $value['qty_po'] = SetValue::nullValue($value['qty_po']);
            $value['satuan_po'] = SetValue::nullValue($value['satuan_po']);
            $value['qty_diterima'] = SetValue::nullValue($value['qty_diterima']);
            $value['satuan_terima'] = SetValue::nullValue($value['satuan_terima']);
            $value['po_balance'] = SetValue::nullValue($value['po_balance']);
            $value['satuan_balance'] = SetValue::nullValue($value['satuan_balance']);
            $value['nilai_konversi'] = SetValue::nullValue($value['nilai_konversi']);
            $value['satuan_kecil'] = SetValue::nullValue($value['satuan_kecil']);
            $value['harga'] = SetValue::nullValue(DocoHelpers::formatNumber($value['harga']));
            $value['discount'] = SetValue::nullValue($value['discount']);
            $value['ppn_persen'] = SetValue::nullValue($value['ppn_persen']);
            $value['sub_total'] = SetValue::nullValue(DocoHelpers::formatNumber($value['sub_total']));
            $value['total'] = SetValue::nullValue(DocoHelpers::formatNumber($value['total']));
            $value['catatan_po'] = SetValue::nullValue($value['catatan_po']);
            $value['tgl_pr'] = SetValue::dateTimeValue($value['tgl_pr']);
            $value['no_pr'] = SetValue::nullValue($value['no_pr']);
            $value['no_batch'] = SetValue::nullValue($value['no_batch']);
            $value['tgl_kadaluarsa'] = SetValue::dateValue($value['tgl_kadaluarsa']);
            $value['no_suratjalan'] = SetValue::nullValue($value['no_suratjalan']);
            $value['no_faktur'] = SetValue::nullValue($value['no_faktur']);
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $laporanPenerimaanBarang['data']['_meta']['totalCount'];
        $result['recordsFiltered'] = $laporanPenerimaanBarang['data']['_meta']['totalCount'];
        return $result;
    }
}
