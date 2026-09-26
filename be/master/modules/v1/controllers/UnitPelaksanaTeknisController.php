<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\UnitPelaksanaTeknisRm;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;

class UnitPelaksanaTeknisController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\UnitPelaksanaTeknisRm';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['index'] = ['GET', 'POST'];
        $verbs['create'] = ['POST'];
        $verbs['update'] = ['PUT', 'PATCH', 'POST'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['create'], $actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new UnitPelaksanaTeknisRm();
        $query = UnitPelaksanaTeknisRm::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        $model = new UnitPelaksanaTeknisRm();
        try {
            if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
                return $this->responseJson(200, 'Data berhasil disimpan', $model);
            }

            return $this->responseJson(422, 'Validasi gagal', [
                'errors' => DocoHelpers::parseError($model->errors, 'UnitPelaksanaTeknisRm'),
            ]);
        } catch (\Throwable $e) {
            return $this->responseJson(500, $e->getMessage());
        }
    }

    public function actionUpdate()
    {
        try {
            $upt_sync_id = Yii::$app->request->post('upt_sync_id');
            $model = UnitPelaksanaTeknisRm::findOne(['upt_sync_id' => $upt_sync_id]);
            $isNewRecord = false;

            if (!$model) {
                $model = new UnitPelaksanaTeknisRm();
                $model->upt_sync_id = $upt_sync_id;
                $isNewRecord = true;
            }

            if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
                $statusCode = $isNewRecord ? 201 : 200;
                $message = $isNewRecord ? 'Data berhasil disimpan' : 'Data berhasil diperbarui';

                return $this->responseJson($statusCode, $message, $model);
            }

            return $this->responseJson(422, 'Validasi gagal', [
                'errors' => DocoHelpers::parseError($model->errors, 'UnitPelaksanaTeknisRm'),
            ]);
        } catch (\Throwable $e) {
            return $this->responseJson(500, $e->getMessage());
        }
    }
}
