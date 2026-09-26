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
use GuzzleHttp\Exception\RequestException;

class GetSatuanKonversiItemAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            if ($request->post()) {
                $depdrop_parents = $request->post('depdrop_parents');
                $parent_label = $depdrop_parents[0];
            }

            $exp = explode('-', $parent_label);
            list($item_id, $tipe) = $exp;
            $result = [];
            $result['output'] = [];
            $result['selected'] = '';
            $response = Yii::$app->docoRest->master->get('allow/list-satuan-konversi-item', [
                'query' => [
                    'item_id' => $item_id,
                    'tipe' => $tipe,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value){
                if($tipe == 'B') {
                    $result['output'][] = [
                        'id' => $value['satuankonversibrg_id'],
                        'name' => '1 '.$value['besar'].' = '.$value['nilai_konversi'].' '.$value['kecil']
                    ];
                }
                else {
                    $result['output'][] = [
                        'id' => $value['satuankonversi_id'],
                        'name' => '1 '.$value['besar'].' = '.$value['nilai_konversi'].' '.$value['kecil']
                    ];
                }
            }

            // $result['selected'] = [
            //     'id' => $result['output'][0]['id'],
            //     'name' => $result['output'][0]['name']
            // ];

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