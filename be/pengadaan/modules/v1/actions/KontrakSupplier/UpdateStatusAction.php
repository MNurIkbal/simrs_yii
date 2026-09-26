<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use app\modules\v1\models\KontrakSupplier;
use Doco\components\DocoMessages;

class UpdateStatusAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $kontraksupplier_id = $request->get('id');
        $is_active = $request->post('is_active', 1);

        $model = KontrakSupplier::find()->where(['kontraksupplier_id' => $kontraksupplier_id])->one();
        if($model !== null) {
            $model->is_active = $is_active;
            $model->last_modified_by = Yii::$app->getUser()->getId();
            $model->last_modified_date = date("Y-m-d H:i:s");
            if($model->save(false)){
                return $this->controller->responseJson(200, DocoMessages::SUC_MESSAGE_UPDATED);
            } else {
                return $this->controller->responseJson(422, DocoMessages::ERR_MESSAGE, $model->errors);
            }
        }

        return $this->controller->responseJson(422, DocoMessages::ERR_MESSAGE_DATA_NOT_FOUND);
    }
}
