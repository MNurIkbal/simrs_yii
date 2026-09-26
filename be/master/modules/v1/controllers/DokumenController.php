<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Dokumen;
use app\modules\v1\models\Lookup;

class DokumenController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Dokumen';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new Dokumen;
            $query = $model::find();
            $query = $query
                ->select(['dokumen_m.dokumen_id', 'dokumen_m.nama_dokumen', 'dokumen_m.jenis_dokumen_id',
                'dokumen_m.is_active', 'lookup_m.lookup_name AS jenis_dokumen_nama'])
                ->innerJoin('lookup_m', 'lookup_m.lookup_id = dokumen_m.jenis_dokumen_id')
                ->where(['lookup_m.lookup_type' => 'jenis_dokumen']);

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['is_active'])) {
                    $is_active = ($advancedFilter['is_active'] == 1) ? true : false;
                    $query->andWhere(['dokumen_m.is_active' => $is_active]);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query->asArray(),
            ]);
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

    public function actionGetJenisDokumen()
    {
        return Lookup::find()
            ->where(['lookup_type' => 'jenis_dokumen', 'is_active' => true])
            ->orderBy(['lookup_name' => SORT_ASC])
            ->all();
    }

    public function actionSimpan()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $model = new Dokumen;
            if($id) {
                $model = $model::findOne($id);
            }
            
            if ($request->post() && !empty($model)) {
                $post = $request->post();
                $model->attributes = $post;
                if ($model->validate() && $model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            } else {
                throw new \Exception('Data Tidak Di Temukan');
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

    public function actionDelete($id)
    {
        $data = Dokumen::findOne($id);
        if ($data) {
            $delete = (new Dokumen)->delete($id);
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
        }
        return [
            'status' => 422,
            'title' => 'Proses Gagal !',
            'text' => 'Data sudah di gunakan'
        ];
    }
}
