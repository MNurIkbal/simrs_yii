<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pengadaan\models\InfoPoForm;

class SaveAction extends Action {
    const ACTION_SAVE = 'save';
    const ACTION_VALIDASI = 'validasi';

    public function run($id, $type_po) {
        $request = Yii::$app->request;
        $model = new InfoPoForm;
        $model->load($request->post());
        $model->list_data = $request->post('list_data','{}');
        $model->diorder_oleh = $request->post('diorder_oleh');
        $model->is_validasi = $request->post('is_validasi');
        $alasan_edit = $request->post('alasan_edit');
        if ($model->validate()) {
            $post = [
                'info_po' => $model->attributes,
                'history_edit' => $alasan_edit
            ];
            $action = $model->is_validasi ? self::ACTION_VALIDASI : self::ACTION_SAVE;
            return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
                'url' => 'info-purchase-order/'.$action,
                'method' => 'POST',
                'payload' => [
                    'query' => [
                        'id' => DocoHelpers::decrypt($id),
                        'type_po' => DocoHelpers::decrypt($type_po)
                    ],
                    'form_params' => $post
                ],
                'returnResponse' => true
            ]);
        } else {
            return DocoHelpers::response($model->errors, 422, 'InfoPoForm');
        }
    }
}
