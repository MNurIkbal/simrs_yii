<?php

/**
 * @author: Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\ZatAktifObat;

use Yii;
use yii\base\Action;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\ZatAktifObat;

class AssignPrimaryAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $post = $request->post();
        $multiple = $request->post('multiple', false);

        if(!$multiple && $post['is_primary'] == "true") {
            $reset = ZatAktifObat::updateAll(['is_primary' => false], "obatalkes_id = {$post['obatalkes_id']}");
        }

        $model = ZatAktifObat::find()->where([
                'obatalkes_id' => $post['obatalkes_id'], 
                'zataktif_id' => $post['zataktif_id']
            ])->one();

        $model->is_primary = $post['is_primary'] == "true" ? true : false;

        if(!$model->update()) {
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;

            return ['message' => $e->getMessage()];
        } else {
            return [
                'status' => 200,
                'message' => 'Data berhasil diperbaharui.'
            ];
        }
    }
}
