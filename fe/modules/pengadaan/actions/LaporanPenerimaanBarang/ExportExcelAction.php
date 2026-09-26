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

class ExportExcelAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($filter['advanced-filter']['tgl_penerimaan'])) {
            $tgl_penerimaan = explode(' - ', $filter['advanced-filter']['tgl_penerimaan']);
            $tgl_awal = $tgl_penerimaan[0];
            $tgl_akhir = $tgl_penerimaan[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $filter['advanced-filter']['tgl_penerimaan_awal'] = $tgl_awal_format;
            $filter['advanced-filter']['tgl_penerimaan_akhir'] = $tgl_akhir_format;
        }

        $path = Yii::getAlias("@download") . "/laporan_penerimaan_barang.xlsx";
        $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'lap-penerimaan-barang/export-excel',
            'method' => 'GET',
            'payload' => [
                'save_to' => $path,
                'query' => $filter
            ]
        ]);

        return DocoHelpers::downloadFile($path, true);
    }
}
