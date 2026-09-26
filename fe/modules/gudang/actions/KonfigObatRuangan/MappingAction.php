<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\gudang\actions\KonfigObatRuangan;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\gudang\models\KonfigRakForm;

class MappingAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $module = $this->controller->_module;
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $ruangan_id = $request->get('ruangan_id', null);

        $getKonfigRuangan = Yii::$app->docoRest->gudang->get('konfig-obat-ruangan/get-by-id?id='.$id);
        $body = json_decode($getKonfigRuangan->getBody(), true);
        $data = $body['response'];
        $model = new KonfigRakForm;
        $model->attributes = $data;

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $saveRes = Yii::$app->docoRest->gudang->post('konfig-obat-ruangan/update-data?id='.$id, [
                    'form_params' => $model->attributes
                ]);
                $result = json_decode($saveRes->getBody(),true);

                return DocoHelpers::response($result);
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'KonfigRakForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ], 422);
            }
        } else {
            $getRakList = Yii::$app->docoRest->gudang->get('allow/get-list-rak?ruangan_id='.$ruangan_id);
            $resRakList = json_decode($getRakList->getBody(), true);
            $rakobat_list = ArrayHelper::map($resRakList['response'], 'rakobat_id', 'nama_rak_laci');

            return $this->controller->renderAjax('form', get_defined_vars());
        }
    }
}
