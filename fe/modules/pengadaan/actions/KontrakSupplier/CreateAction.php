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

class CreateAction extends Action {
    public function run() {
        $moduleAlias = Yii::$app->docoVars->workspace('modul_alias');
        $modulePath = Yii::$app->docoVars->workspace('url');
        $model = new KontrakSupplierForm;
        $model->tgl_berlaku = date('d-M-Y');
        $title = $this->controller->_title;
        $module = $this->controller->_module;
        $options = $this->getAttributes();
        $list_no_kontraksupplier = $this->getNoKontrakSupplier();
        return $this->controller->render('create', get_defined_vars());
    }

    private function getNoKontrakSupplier() {
        try {
            $request = Yii::$app->docoRest->pengadaan->get('kontrak-supplier/get-no-kontrak');
            $response = json_decode($request->getBody(), true);
            $list_no_kontraksupplier = $response['response']['data'];
        } catch (RequestException $e) {
            (new DocoHelpers)->logError($e);
            $list_no_kontraksupplier = [];
        }

        return $list_no_kontraksupplier;
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