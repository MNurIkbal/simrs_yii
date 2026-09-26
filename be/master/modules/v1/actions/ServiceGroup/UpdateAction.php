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

class UpdateAction extends Action {
    public function run($id) {
        $request = Yii::$app->request;
        try {
            $model = ServiceGroup::findOne($id);
            $model->scenario = 'default';
            $model->attributes = $request->post();
            if($model->validate()){
                if ($model->update()) {
                    Yii::$app->cache->delete(DocoConstants::CACHE_SERVICE_GROUP);
                    $return = [
                        'text' => 'Data Berhasil di ubah',
                        'title' => 'Proses berhasil !',
                        'code' => 200
                    ];

                    return $return;
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'ServiceGroupForm');
                    return ['data' => $errors,'status' => 422];
                }
            }else{
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