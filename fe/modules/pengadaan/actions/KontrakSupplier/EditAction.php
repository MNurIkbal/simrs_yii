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
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pengadaan\models\KontrakSupplierForm;

class EditAction extends Action {
    public function run($id) {
        $title = "Detail Kontrak Supplier";
        $module = $this->controller->_module;
        $kontraksupplier_id = $id;

        try{
            $request = Yii::$app->docoRest->pengadaan
                        ->get('kontrak-supplier/edit-filler',[
                            'query'=>['id'=>DocoHelpers::decrypt($id)]
                        ]);
            $response = json_decode($request->getBody(), true);
            $model      = new KontrakSupplierForm;
            $options    = $this->getAttributes();
            $header     = $response['response']['data']['header'];
            $detail     = $response['response']['data']['detail'];
            $payterm    = $response['response']['data']['payterm'];
        }catch(RequestException $e){
            throw $e;
        }

        return $this->controller->render('edit', get_defined_vars());
    }

    private function getAttributes() {
        try {
            $request = Yii::$app->docoRest->gudang->get('penerimaan-obat-supplier/get-attribute', [
                'query' => []
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
        } catch (RequestException $e) {
            (new DocoHelpers)->logError($e);
            $attributes = [
                'payterm' => [],
                'ppn' => []
            ];
        }

        return $attributes;
    }
}
