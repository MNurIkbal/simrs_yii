<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pengadaan\models\KontrakSupplierForm;

class SaveAction extends Action {
    public function run() {
        $post = Yii::$app->request->post();
        $form = new KontrakSupplierForm;
        $form->load($post);
        $body_json = [
            'header' => $post['KontrakSupplierForm'],
            'list_obat' => json_decode($post['list_obat'])
        ];

        if(!$form->validate()) {
            $response = $form->errors;
            return DocoHelpers::response($response, 422, "KontrakSupplierForm");
        }

        try {
            $post = Yii::$app->docoRest->pengadaan->post('kontrak-supplier/save',[
                'json' => $body_json
            ]);
            $response = json_decode($post->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, false);
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, false);
        }
    }
}
