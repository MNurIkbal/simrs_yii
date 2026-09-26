<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class GetDataAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($filter['advanced-filter']['tanggal_po'])) {
            $tgl_po = explode(' - ', $filter['advanced-filter']['tanggal_po']);
            $tgl_awal_validasi = $tgl_po[0];
            $tgl_akhir_validasi = $tgl_po[1];
            $tgl_awal_format_validasi = date('Y-m-d H:i:s', strtotime($tgl_awal_validasi . ' 00:00:00'));
            $tgl_akhir_format_validasi = date('Y-m-d H:i:s', strtotime($tgl_akhir_validasi . ' 23:59:59'));
            $filter['advanced-filter']['tanggal_po_awal_validasi'] = $tgl_awal_format_validasi;
            $filter['advanced-filter']['tanggal_po_akhir_validasi'] = $tgl_akhir_format_validasi;

            unset($filter['advanced-filter']['tanggal_po']);
        }

        if (isset($filter['advanced-filter']['tanggal_buat_po'])) {
            $tgl_buat_po = explode(' - ', $filter['advanced-filter']['tanggal_buat_po']);
            $tgl_awal = $tgl_buat_po[0];
            $tgl_akhir = $tgl_buat_po[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tanggal_po_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tanggal_po_akhir'] = $tgl_akhir_format;

            unset($filter['advanced-filter']['tanggal_buat_po']);
        }

        if (isset($request->get()['po_cito'])) {
            $filter['advanced-filter']['po_cito'] = $request->get('po_cito');
        }

        if (isset($request->get()['po_admin'])) {
            $filter['advanced-filter']['po_admin'] = $request->get('po_admin');
        }

        if (isset($request->get()['po_consigment'])) {
            $filter['advanced-filter']['po_consigment'] = $request->get('po_consigment');
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'info-purchase-order',
            'method' => 'GET',
            'payload' => [
                'query' => $filter
            ]
        ]);
        $body = ArrayHelper::getValue($response, 'data', []);
        $meta = ArrayHelper::getValue($response, '_meta', []);

        $no = $request->get('start',1);
        foreach ($body as $key => $value) {
            $no++;
            $primaryKey = !empty($value['transaksi_id']) ? DocoHelpers::encrypt($value['transaksi_id']) : null;
            $type_po = !empty($value['type_po']) ? DocoHelpers::encrypt($value['type_po']) : null;
            unset($value['transaksi_id']);
            $value['primary'] = $primaryKey;
            $value['type_po'] = $type_po;
            $value['rowNum'] = $no;
            $value['tanggal_buat_po'] = !empty($value['tanggal_buat_po']) ? date('d M Y', strtotime($value['tanggal_buat_po'])) : '-';
            $value['tanggal_po'] = !empty($value['tanggal_po']) ? date('d M Y', strtotime($value['tanggal_po'])) : "-";
            $value['tgl_batal_po'] = !empty($value['tgl_batal_po']) ? date('d M Y H:m', strtotime($value['tgl_batal_po'])) : "-";
            $value['total_harga_po'] = DocoHelpers::formatNumber($value['total_harga_po']);
            $value['no_transaksi'] = ArrayHelper::getValue($value, 'no_transaksi');
            $value['pegawai_validasi'] = isset($value['pegawai_validasi']) ? $value['pegawai_validasi'] : "";
            $value['asal_transaksi'] = isset(DocoConstants::$statusAsal[$value['asal_transaksi']])
                ? DocoConstants::$statusAsal[$value['asal_transaksi']] : null ;
            $value['tgl_cetak_po'] = !empty($value['tgl_tercetak']) ? date('d M Y H:i:s', strtotime($value['tgl_tercetak'])) : "-";
            $value['po_cito'] = ArrayHelper::getValue($value, 'po_cito');
            $value['po_admin'] = ArrayHelper::getValue($value, 'po_admin');
            $value['po_consigment'] = ArrayHelper::getValue($value, 'po_consigment');
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = ArrayHelper::getValue($meta, 'totalCount', 0);
        $result['recordsFiltered'] = ArrayHelper::getValue($meta, 'totalCount', 0);
        return $result;
    }
}
