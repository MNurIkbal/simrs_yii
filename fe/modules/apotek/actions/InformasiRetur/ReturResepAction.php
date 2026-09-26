<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiRetur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\InformasiForm;

class ReturResepAction extends Action
{
    public function run($id)
    {   
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $data_retur = $request->post('data_retur', []);
            $valid = false;
            $payload_retur = [];
            $ruangan_retur = ArrayHelper::getValue($post, 'ruangan');
            $tanggal_retur = ArrayHelper::getValue($post, 'tanggal');
            foreach ($data_retur as $key => $value) {
                $payload_retur['detail'][] = [
                    'identifier' => $value['obatalkes_id'],
                    'qty_retur' => isset($value['qty_retur']) ? $value['qty_retur'] : 0
                ];

                $value['qty_retur'] = isset($value['qty_retur']) ? $value['qty_retur']: 0;

                if ($value['qty_retur'] > 0) {
                    $valid = true;
                } else {
                    $racikan = filter_var($value['is_racikan'], FILTER_VALIDATE_BOOLEAN);
                    if($racikan == false) {
                        return DocoHelpers::response([
                            'response' => [
                                'text' => 'Qty Retur tidak boleh kosong.',
                                'title' => 'Proses Gagal!',
                            ]
                        ], 422);
                    }
                }
            }

            $response = $this->controller->guzzleExec(Yii::$app->docoRest->apotek,[
                'url' => 'inf-retur/retur-resep',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'pendaftaran_id' => $id,
                        'ruangan_retur' => $ruangan_retur,
                        'tanggal_retur' => $tanggal_retur,
                        'data_retur' => $data_retur
                    ]
                ],
            ]);

            if(isset($response['httpStatusCode']) == 422) {
                return DocoHelpers::response($response, 422);
            }

            return DocoHelpers::response($response, 200, true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
