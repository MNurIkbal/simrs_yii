<?php

/**
 * @author: Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\ZatAktifObat;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\ZatAktifObat;

class CreateAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $post = $request->post();

        $listZatAktif = ZatAktifObat::find()
            ->select(['zataktif_id'])
            ->where(['obatalkes_id' => $post['obatalkes_id']])
            ->asArray()
            ->all();

        if(in_array($post['zataktif_id'], ArrayHelper::getColumn($listZatAktif, 'zataktif_id'))) {
            return [
                'status' => 422,
                'code' => 422,
                'message' => 'Zat Aktif sudah terdaftar.'
            ];
        }

        $model = new ZatAktifObat;
        $model->attributes = $post;
        $model->is_primary = count($listZatAktif) > 0 ? false : true;

        if(!$model->save()) {
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;

            return ['message' => $e->getMessage()];
        } else {
            return [
                'status' => 200,
                'code' => 200,
                'message' => 'Data berhasil disimpan.'
            ];
        }
    }
}
