<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\OperasiView;
use app\modules\v1\models\Operasi;
use app\modules\v1\models\DaftarTindakan;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\KegiatanOperasi;
use app\modules\v1\models\GolonganOperasi;

class OperasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Operasi';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new OperasiView;
        $query = $model::find();
        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['golonganoperasi_nama'])) {
                $golonganoperasi_id = $advancedFilter['golonganoperasi_nama'];
                $query->andWhere(['golonganoperasi_id' => $golonganoperasi_id]);
                unset($_GET['advanced-filter']['golonganoperasi_nama']);
            }
            if(isset($advancedFilter['kegiatanoperasi_nama'])) {
                $kegiatanoperasi_id = $advancedFilter['kegiatanoperasi_nama'];
                $query->andWhere(['kegiatanoperasi_id' => $kegiatanoperasi_id]);
                unset($_GET['advanced-filter']['kegiatanoperasi_nama']);
            }
            if(isset($advancedFilter['daftartindakan_nama'])) {
                $daftartindakan_id = $advancedFilter['daftartindakan_nama'];
                $query->andWhere(['daftartindakan_id' => $daftartindakan_id]);
                unset($_GET['advanced-filter']['daftartindakan_nama']);
            }
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionData()
    {
        $model = new Operasi;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionView($id)
    {
        $model = new OperasiView;
        $query = $model::find()->where(['operasi_id' => $id])->asArray()->one();

        return $query;
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new Operasi;
            if ($request->post()) {
                $model->attributes = $request->post();
                $daftartindakan = DaftarTindakan::findOne($request->post('daftartindakan_id'));
                $model->operasi_nama = $daftartindakan->daftartindakan_nama;
                $model->operasi_namalainnya = $daftartindakan->daftartindakan_nama;
                // return $model->attributes;

                if($model->validate() && $model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                }
                else {
                    // $errors = DocoHelpers::parseError($model->errors, 'OperasiForm');
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        }
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Operasi::findOne($id);
            if ($request->post()) {
                $model->attributes = $request->post();
                $daftartindakan = DaftarTindakan::findOne($model->daftartindakan_id);
                $model->operasi_nama = $daftartindakan->daftartindakan_nama;
                $model->operasi_namalainnya = $daftartindakan->daftartindakan_nama;
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
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

    public function actionFilters()
    {
        $type = Yii::$app->request->get('type', null);
        $payload = Yii::$app->request->get('payload', []);
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $term = isset($payload['term']) ? $payload['term'] : null;
        $result = [];
        if($type == 'kegiatan') {
            $result = KegiatanOperasi::find()
                ->select(['kegiatanoperasi_id AS id', "CONCAT(kegiatanoperasi_kode, ' - ',kegiatanoperasi_nama) AS text"])
                ->where(['is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(kegiatanoperasi_nama)', strtolower($term)])
                    ->orWhere(['LIKE', 'LOWER(kegiatanoperasi_kode)', strtolower($term)]);
            }

            $result->orderBy(['kegiatanoperasi_nama' => SORT_ASC]);
        }
        elseif($type == 'golongan') {
            $result = GolonganOperasi::find()
                ->select(['golonganoperasi_id AS id', "CONCAT(golonganoperasi_kode, ' - ',golonganoperasi_nama) AS text"])
                ->where(['is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(golonganoperasi_nama)', strtolower($term)])
                    ->orWhere(['LIKE', 'LOWER(golonganoperasi_kode)', strtolower($term)]);
            }

            $result->orderBy(['golonganoperasi_nama' => SORT_ASC]);
        }
        else {
            $result = DaftarTindakan::find()
                ->select(['daftartindakan_id AS id', "CONCAT(daftartindakan_kode, ' - ',daftartindakan_nama) AS text"])
                ->where(['is_active' => true, 'kelompoktindakan_id' => 3]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(daftartindakan_nama)', strtolower($term)])
                    ->orWhere(['LIKE', 'LOWER(daftartindakan_kode)', strtolower($term)]);
            }

            $result->orderBy(['daftartindakan_nama' => SORT_ASC]);
        }
        
        if(!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }
}