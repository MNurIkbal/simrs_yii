<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\KonfigObatRuangan;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\KonfigRakView;
use Doco\components\DocoRestActiveFilter;

class GetByIdAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id', null);
            $model = KonfigRakView::find()->where(['konfigrak_id' => $id])->one();
            return $model;
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
