<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseOrderManual;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pengadaan\models\PoManualForm;
use app\modules\pengadaan\models\CustomPoManualDetail;

class SimpanAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $model = new PoManualForm;
        if(Yii::$app->request->post()) {
            $model->attributes = $request->post();
            $post = $request->post();
            $data = isset($post['data']) ? $post['data'] : '' ;
            $is_consignment = false;

            if(isset($post['is_consignment'])) {
                $is_consignment = $post['is_consignment'] == 'true' ? true : false;
            }
            $model->diorder_oleh = Yii::$app->user->identity->id_pegawai;
            $model->tgl_pomanual = date('Y-m-d H:i:s');
            $model->is_consigment = $is_consignment;
            $valid = $model->validate();
            if($valid) {
                $modelDetail = new CustomPoManualDetail;
                $formNameDetail = substr(strrchr(get_class($modelDetail), "\\"), 1);
                $modelDetail->item_id = $data;
                if($modelDetail->validate()) {
                    try {
                        $result = $this->controller->guzzleExec($this->controller->_restPengadaan, [
                            'url' => 'purchase-order-manual/simpan',
                            'method' => 'post',
                            'payload' => [
                                'form_params' => [
                                    'header' => $model->attributes,
                                    'detail' => $modelDetail->attributes,
                                    'instalasi_id' => $request->post('instalasi_id')
                                ]
                            ]
                        ]);

                        if($result['meta']['code'] != 200) {
                            return DocoHelpers::response([
                                'message' => $result['message'],
                                'response' => $result['data']
                            ], 422);
                        } else {
                            return DocoHelpers::response($result['data']);
                        }
                    } catch (RequestException $e) {
                        Yii::info($e->getMessage());
                        $response['response']['text'] = 'Terjadi kesalahan pada sistem';
                        $response['response']['message'] = $e->getMessage();
                        return DocoHelpers::response($response, 422);
                    }
                } else {
                    return DocoHelpers::response($modelDetail->errors, 422, 'PoManualDetailForm');
                }
            } else {
                return DocoHelpers::response($model->errors, 422, 'PoManualForm');
            }
        }
    }
}