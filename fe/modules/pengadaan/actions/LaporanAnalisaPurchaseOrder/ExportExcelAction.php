<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

class ExportExcelAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();
        $doc_name = $this->setDocName($yiiRestfulParams['advanced-filter']);
        $path = Yii::getAlias("@download") . '/laporan_analisa_purchase_order.xlsx';
        $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'info-purchase-order/export-excel-analisa-po',
            'method' => 'GET',
            'payload' => [
                'save_to' => $path,
                'query' => $yiiRestfulParams
            ]
        ]);
        return DocoHelpers::response($path);
    }

    public function setDocName($params) {
        if(isset($params['tgl_pr'])) {
            $exp = explode(' - ', $params['tgl_pr']);
            $tgl_awal = !empty($exp[0]) ? $exp[0] : date('d-m-y');
            $tgl_akhir = !empty($exp[1]) ? $exp[1] : date('d-m-y');
            $tgl_pr = "_".date('dMY', strtotime($tgl_awal))." - ".date('dMY', strtotime($tgl_akhir));
        } else {
            $tgl_pr = "_".date('dMY');
        }

        return "/laporan_analisa_purchase_order".$tgl_pr.".xlsx";
    }
}
