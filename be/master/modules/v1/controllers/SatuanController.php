<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\SatuanLab;

class SatuanController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\SatuanLab';
    
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new SatuanLab;
            $query = $model::find();

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
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

    public function actionExportExcel()
    {
        try {
            $data = array();
            $header = array();

            $request = Yii::$app->request;

            $model = new SatuanLab;
            $query = $model::find()->where(['is_deleted' => false]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($query)) {
                $counter = 0;
                foreach ($query as $index => $value) {
                    $data[$counter]['Kode'] = !empty($value->satuanlab_kode)
                        ? $value->satuanlab_kode
                        : '';
                    $data[$counter]['Nama satuan'] = !empty($value->satuanlab_nama)
                    ? $value->satuanlab_nama
                    : '';
                    $counter++;
                }
            }

            $filePath = DocoHelpers::exportExcel('Master Satuan', $data, $header, array("uploadPath" => "./uploads"));
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
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

    public function actionGenerateApi()
    {
        $model = SatuanLab::find()->where(['is_deleted'=>false])->all();
        $kode = [];
        foreach ($model as $value) {
            if (!empty($value['satuanlab_kode'])) {
                $kode[$value['satuanlab_id']] = $value['satuanlab_kode'];
            }
        }
        return [
            'kode' => $kode,
            'satuan' => ArrayHelper::map($model, 'satuanlab_id', 'satuanlab_nama')
        ];
    }

}