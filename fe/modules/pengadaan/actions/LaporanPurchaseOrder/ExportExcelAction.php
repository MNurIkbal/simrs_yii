<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPurchaseOrder;

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
        $actionId = $request->get('actionId','index');
        $tipe = $actionId == 'index' ? 'OBAT' : 'BARANG';
        $url = 'lap-purchase-order/export-excel?tipe='.$tipe.'&'. http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/laporan_purchase_order.xlsx";
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
