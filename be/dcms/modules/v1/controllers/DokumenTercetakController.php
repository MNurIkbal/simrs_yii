<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\models\DocMapping;
use Doco\models\DocHeader;
use Doco\models\DocFooter;
use Doco\models\JenisKertas;
use Doco\models\Report;

class DokumenTercetakController extends DocoActiveController
{
    public $modelClass = DocMapping::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET","POST"];
        $verbs["create"] = ["POST","GET"];
        $verbs["update"] = ["POST","PUT","GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $model = new DocMapping;
            $query = $model::find()->joinWith([
                'footer',
                'header',
                'kertas',
                'report'
            ])->asArray();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @response
    **/

    public function actionCreate()
    {
        try {
            $model = new DocMapping;
            $request = Yii::$app->request;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            } else {
                $docHeader = ArrayHelper::map(DocHeader::find()->all(),'docheader_id','nama_header');
                $docFooter = ArrayHelper::map(DocFooter::find()->all(),'docfooter_id','nama_footer');
                $jenisKertas = ArrayHelper::map(JenisKertas::find()->all(),'kertas_id','kertas_nama');

                return [
                    'doc_header' => $docHeader,
                    'doc_footer' => $docFooter,
                    'jenis_kertas' => $jenisKertas
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = DocMapping::find()->where([
                'docmapping_id' => $id
            ])->one();
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            } else {
                $docHeader = ArrayHelper::map(DocHeader::find()->all(),'docheader_id','nama_header');
                $docFooter = ArrayHelper::map(DocFooter::find()->all(),'docfooter_id','nama_footer');
                $jenisKertas = ArrayHelper::map(JenisKertas::find()->all(),'kertas_id','kertas_nama');
                return [
                    'doc_header' => $docHeader,
                    'doc_footer' => $docFooter,
                    'jenis_kertas' => $jenisKertas,
                    'data' => $model
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}