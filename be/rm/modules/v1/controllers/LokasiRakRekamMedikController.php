<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LokasiRakRekamMedik;

use Doco\components\DocoHelpers;


class LokasiRakRekamMedikController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LokasiRakRekamMedik';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["cau"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        // $_GET['expand'] = $request->get('expand', 'kelompokjabatan_m,indexing_m');
        
        $model = new LokasiRakRekamMedik;
        $query = $model::find()
        ->where(['lokasirak_m.is_deleted' => false]);

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['lokasirak_nama'])) {
                $query->andWhere(['lokasirak_nama'=> $_GET['advanced-filter']['lokasirak_nama']]);
                unset($_GET['advanced-filter']['lokasirak_nama']); // Unset Advanced Filter  date range
                $between = true;
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new LokasiRakRekamMedik;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'LokasiRakRekamMedikForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionListRak() 
    {
        $data = LokasiRakRekamMedik::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
        $items = ArrayHelper::map($data->all(), 'lokasirak_id', 'lokasirak_nama');

        return $items;
    }

}