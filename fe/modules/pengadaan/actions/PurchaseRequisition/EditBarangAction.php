<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
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

class EditBarangAction extends Action {
    public function run($id) {
        $title = 'Edit Purchase Request';
        try{
            $request = Yii::$app->docoRest->pengadaan
                        ->get('purchase-requisition/edit-pr',[
                            'query'=>[
                                'id' => DocoHelpers::decrypt($id),
                                'type' => "barang"
                            ]
                        ]);
            $response = json_decode($request->getBody(), true);
            $header = $response['response']['data']['header'];
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            if($header['ruangan_id'] != $ruangan_id){
                throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
            }

            $detail = $response['response']['data']['detail'];
            $konversi = $response['response']['data']['konversi'];
            $satuan = $response['response']['data']['satuan'];
            $hasil_konversi = $response['response']['data']['hasil_konversi'];

            $detail = $this->castingStok($detail);
        }catch(RequestException $e){
            throw $e;
        }

        return $this->controller->render('edit-barang', get_defined_vars());
    }

    private function castingStok($detail) {
        foreach ($detail as $key => $value) {
            if ($value['nilai_konversi'] > 1) {
                $detail[$key]['stok_saatini'] = $value['stok'] / $value['nilai_konversi'];
                $detail[$key]['stok_saatini'] = DocoHelpers::formatNumber($detail[$key]['stok_saatini']) . ' ' . $value['satuan'];
            } else {
                $detail[$key]['stok_saatini'] = DocoHelpers::formatNumber($detail[$key]['stok_saatini']) . ' ' . $value['satuan_stok'];
            }
        }
        return $detail;
    }
}
