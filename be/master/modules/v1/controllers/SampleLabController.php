<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\SampleLab;

class SampleLabController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\SampleLab';
    
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
            $model = new SampleLab;
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

            $model = new SampleLab;
            $query = $model::find()->where(['is_deleted' => false]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($query)) {
                $counter = 0;
                foreach ($query as $index => $value) {
                    $data[$counter]['Kode'] = !empty($value->kode_sample)
                        ? $value->kode_sample
                        : '';
                    $data[$counter]['Nama sample'] = !empty($value->nama_sample)
                    ? $value->nama_sample
                    : '';
                    $counter++;
                }
            }

            $filePath = DocoHelpers::exportExcel('Sample Lab', $data, $header, array("uploadPath" => "./uploads"));
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

    /**
    * @controller actionExportPdf
    * @attribute #table_exportpdf# => table 
    **/
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $model = new SampleLab;
            $query = $model::find()->where(['is_deleted' => false]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($model)) {
                $print = new DocoPrint();
                $print->attributes = [
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'model' => $query,
                    ]),
                ];

                $print->Output();
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

    public function actionGenerateApi()
    {
        $model = SampleLab::find()->all();
        $kode = [];
        foreach ($model as $value) {
            if (!empty($value['kode_sample'])) {
                $kode[$value['samplelab_id']] = $value['kode_sample'];
            }
        }
        return [
            'kode' => $kode,
            'satuan' => ArrayHelper::map($model, 'samplelab_id', 'nama_sample')
        ];
    }

}