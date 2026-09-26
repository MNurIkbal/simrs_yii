<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class PRRecommendationAction extends Action {
    public function run() {
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        try {
            $response = Yii::$app->docoRest->pengadaan->get('purchase-requisition/pr-recommendation', [
                'query' => [
                    'ruangan_id' => $ruangan_id
                ]
            ]);
            $body = json_decode($response->getBody(), True);

            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
}
