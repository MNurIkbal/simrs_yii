<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class ExportExcelAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = $this->controller->getFilter($request);

        $url = 'purchase-requisition/export-excel?'. http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/laporan_purchase_requisition.xlsx";
        try {
            $response = Yii::$app->docoRest->pengadaan->get($url, [
                'save_to' => $path
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
