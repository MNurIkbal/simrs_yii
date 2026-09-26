<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\SubSpesialisV;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;

class SubSpesialisController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
        $verbs["data-api"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionDataApi() {
        try {
            $request = Yii::$app->request;
            $model = new SubSpesialisV;
            $query = $model->find()->select([
                'ruangan_id',
                'subspesialis_id',
                'ruangan_nama',
                'subspesialis_nama',
                'subspesialis_kode',
                'is_online',
                'subspesialis_image'
            ]);

            if($request->get('ruangan_id')) {
                $query->andWhere(['ruangan_id' => $request->get('ruangan_id')]);
            }

            if($request->get('ruangan_nama')) {
                $ruangan = strtolower($request->get('ruangan_nama'));
                $query->andWhere(['like', 'lower(ruangan_nama)', $ruangan]);
            }

            if($request->get('subspesialis_id')) {
                $query->andWhere(['subspesialis_id' => $request->get('subspesialis_id')]);
            }

            if($request->get('subspesialis_nama')) {
                $subspesialis = strtolower($request->get('subspesialis_nama'));
                $query->andWhere(['like', 'lower(subspesialis_nama)', $subspesialis]);
            }

            if($request->get('subspesialis_kode')) {
                $query->andWhere(['subspesialis_kode' => $request->get('subspesialis_kode')]);
            }

            $data = $query->asArray()->all();
            
            if(empty($data)) {
                return $this->responseJson(200, 'Data Tidak Ditemukan', null, 201);
            }

            return $data;

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}