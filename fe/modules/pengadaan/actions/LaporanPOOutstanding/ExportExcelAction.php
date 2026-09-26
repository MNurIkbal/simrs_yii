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
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['type'] = DocoConstants::JENIS_OBAT;
        $doc_name = $this->setDocName($yiiRestfulParams['advanced-filter']);
        $path = Yii::getAlias("@download") . $doc_name;
        try {
            $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
                'url' => 'info-purchase-order/export-excel-po-outstanding',
                'method' => 'GET',
                'payload' => [
                    'save_to' => $path,
                    'query' => $yiiRestfulParams
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

    public function setDocName($params) {
        if(isset($params['tgl_po_dibuat'])) {
            $exp = explode(' - ', $params['tgl_po_dibuat']);
            $tgl_awal = $exp[0];
            $tgl_akhir = $exp[1];
            $tgl_po_dibuat = "_".date('dMY', strtotime($tgl_awal))." - ".date('dMY', strtotime($tgl_akhir));
        } else {
            $tgl_po_dibuat = "_".date('dMY');
        }

        return "/laporan_purchase_order_outstanding".$tgl_po_dibuat.".xlsx";
    }
}
