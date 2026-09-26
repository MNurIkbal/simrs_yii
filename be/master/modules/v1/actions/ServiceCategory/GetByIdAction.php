<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\ServiceCategory;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use app\modules\v1\models\ServiceCategory;
use Doco\components\DocoRestActiveFilter;

class GetByIdAction extends Action {
    public function run($id) {
        try {
            $data = ServiceCategory::find()->where(['servicecategory_id' => $id])->one();
            return $data;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}