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
use app\modules\v1\models\KonfigRak;
use Doco\components\DocoRestActiveFilter;

class UpdateAction extends Action {
    public function run($id) {
        try {
            $request = Yii::$app->request;
            $payload = $request->post();
            $model = KonfigRak::find()->where(['konfigrak_id' => $id])->one();
            $model->attributes = $payload;

            if($model->validate() && $model->save()) {
                return [
                    'title' => 'Proses Berhasil',
                    'message' => 'Konfigurasi obat ruangan berhasil di update',
                ];
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
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
