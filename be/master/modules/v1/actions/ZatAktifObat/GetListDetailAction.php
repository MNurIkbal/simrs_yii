<?php

/**
 * @author: Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\ZatAktifObat;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoZatAktifObatView;

class GetListDetailAction extends Action {
    public function run($id) {
        try {
            $request = Yii::$app->request;

            $model = new InfoZatAktifObatView;
            $query = $model->find()
                ->where([
                    'obatalkes_id' => $id
                ]);

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
            return new ActiveDataProvider([
                'query' => $query,
            ]);

        } catch (\yii\db\Exception $e) {
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
