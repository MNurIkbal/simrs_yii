<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPurchaseRequisitionOutstandingBarang;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action {
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
        }

        $path = Yii::getAlias("@download") . "/laporan_purchase_requisition_outstanding_nonmedis.xlsx";
        try {
            $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
                'url' => 'lap-purchase-requisition-outstanding-barang/export-excel',
                'method' => 'GET',
                'payload' => [
                    'save_to' => $path,
                    'query' => $filter
                ]
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
