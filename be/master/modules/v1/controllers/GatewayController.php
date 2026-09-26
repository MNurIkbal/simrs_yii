<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\GtObatView;
use app\modules\v1\models\GtAlkesView;
use app\modules\v1\models\GtPemeriksaanLabView;
use app\modules\v1\models\GtPemeriksaanRadView;
use app\modules\v1\models\GtOperasiView;
use app\modules\v1\models\GtPoliklinikView;
use app\modules\v1\models\GtPasienView;
use app\modules\v1\models\GtTarifTindakanView;
use app\modules\v1\models\GtSatuanKonversiView;

class GatewayController extends DocoActiveController
{
    public $modelClass = '';
    
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
        unset($actions['delete']);
        return $actions;
    }

    public function actionObat()
    {
        try {
            $request = Yii::$app->request;
            $model = new GtObatView;
            $query = $model->find();
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

    public function actionAlkes()
    {
        try {
            $request = Yii::$app->request;
            $model = new GtAlkesView;
            $query = $model->find();
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

    public function actionPemeriksaanBedah()
    {
        try {
            $request = Yii::$app->request;
            $model = new GtOperasiView;
            $query = $model->find();
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

    public function actionPemeriksaanRadiologi()
    {
        try {
            $request = Yii::$app->request;
            $model = new GtPemeriksaanRadView;
            $query = $model->find();
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

    public function actionPemeriksaanLaboratorium()
    {
        try {
            $request = Yii::$app->request;
            $model = new GtPemeriksaanLabView;
            $query = $model->find();
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

    public function actionPoliklinik()
    {
        try {
            $request = Yii::$app->request;
            $model = new GtPoliklinikView;
            $query = $model->find();
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

    public function actionPasien()
    {
        try {
            $request = Yii::$app->request;
            $model = new GtPasienView;
            $query = $model->find();
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

    public function actionTarifTindakan()
    {
        try {
            $request = Yii::$app->request;
            $model = new GtTarifTindakanView;
            $query = $model->find();
            $query = DocoRestActiveFilter::advancedFilter(
                $model,
                DocoRestActiveFilter::filterMutation(
                    $model,
                    $query,
                    [
                        'tarif.kelas' => 'json_array_like',
                        'tarif.penjamin' => 'json_array_like',
                        'tarif.perda' => 'json_array_like',
                        'tarif.harga_tariftindakan' => 'json_array_like',
                        'tarif.persencyto_tindakan' => 'json_array_like',
                        'tarif.persen_penyulit' => 'json_array_like'
                    ]
                )
            );
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

    public function actionSatuanKonversi()
    {
        try {
            $request = Yii::$app->request;
            $model = new GtSatuanKonversiView;
            $query = $model->find();
            $query = DocoRestActiveFilter::advancedFilter(
                $model,
                DocoRestActiveFilter::filterMutation(
                    $model,
                    $query,
                    [
                        'data_konversi.satuanbesar_id' => 'json_array_like',
                        'data_konversi.satuan_besar' => 'json_array_like',
                        'data_konversi.satuankecil_id' => 'json_array_like',
                        'data_konversi.satuan_kecil' => 'json_array_like',
                        'data_konversi.nilai_konversi' => 'json_array_like'
                    ]
                )
            );
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
}