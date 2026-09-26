<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class UpdateAction extends Action {
    public function run($id) {
        $post = Yii::$app->request->post();
        $post['KontrakSupplierForm']['kontraksupplier_id'] = DocoHelpers::decrypt($post['KontrakSupplierForm']['kontraksupplier_id']);

        $body_json = [
            'header' => $post['KontrakSupplierForm'],
            'details' => json_decode($post['list_obat'])
        ];
        try {
            $post = Yii::$app->docoRest->pengadaan->post('kontrak-supplier/update-kontrak-supplier', [
                'json' => $body_json
            ]);
            $response = json_decode($post->getBody(), true);

            return DocoHelpers::response($response);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
