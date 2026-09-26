<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\web\Response;
use app\components\DocoHelpers;
use app\modules\pengadaan\models\PurchaseRequisitionForm;
use GuzzleHttp\Exception\RequestException;

class GetSatuanKonversiAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            if ($request->post()) {
                $depdrop_parents = $request->post('depdrop_parents');
                $parent_label = $depdrop_parents[0];
            }

            $result = [];
            $result['output'] = [];
            $result['selected'] = '';
            $response = Yii::$app->docoRest->master->get('allow/list-satuan-konversi-item', [
                'query' => [
                    'item_id' => $parent_label,
                    'tipe' => 'obatalkes_id',
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value){
                $result['output'][] = [
                    'id' => $value['satuanbesar_id'],
                    'name' => '1 '.$value['besar'].' = '.$value['nilai_konversi'].' '.$value['kecil']
                ];
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['output'] = [];
            $result['selected'] = '';
            return $result;
        }
    }
}