<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\ServiceGroup;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\ServiceGroup;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;

class SaveAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $model = new ServiceGroup;
            $post = $request->post();
            $model->attributes = $post;
            if($model->validate()){
                if ($model->save()) {
                    Yii::$app->cache->delete(DocoConstants::CACHE_SERVICE_GROUP);
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'ServiceGroupForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'ServiceGroupForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }
}