<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;

class CetakDetailPdfAction extends Action {
    public function run($id,$type) {
        try {
            $path = Yii::getAlias("@download") . "/info-purchase-requisition.pdf";
            $response = Yii::$app->docoRest->pengadaan->get('purchase-requisition/cetak-detail-pdf' ,[
                'query' => [
                    'id' => DocoHelpers::decrypt($id),
                    'type' => $type
                ],
                'save_to' => $path
            ]);

            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
}
