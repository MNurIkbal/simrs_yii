<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\ObatAlkesDetailView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\SatuanKonversiObatView;

class SatuanKonversiController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\SatuanKonversi';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        $verbs["create"] = ["POST"];
        $verbs["delete"] = ["DELETE", "POST"];
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
        try {
            $request = Yii::$app->request;
            $model = new SatuanKonversiObatView;
            $query = $model->find();

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');

                if(isset($advancedFilter['satuan_besar'])) {
                    $satuanbesar_id = $advancedFilter['satuan_besar'];
                    $query->andWhere(['satuanbesar_id' => $satuanbesar_id]);
                    unset($_GET['advanced-filter']['satuan_besar']);
                }

                if(isset($advancedFilter['satuan_kecil'])) {
                    $satuankecil_id = $advancedFilter['satuan_kecil'];
                    $query->andWhere(['satuankecil_id' => $satuankecil_id]);
                    unset($_GET['advanced-filter']['satuan_kecil']);
                }
            }

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

    public function actionView($id)
    {
        $query = SatuanKonversiObatView::find()->where(['satuankonversi_id' => $id])->one();

        return $query;
    }

    public function actionCreate(){
        $request = Yii::$app->request;
        $model = new SatuanKonversi();
        try {
            $post = $request->post();
            $model->attributes = $post;
            if($model->validate()){
                if($model->save()){
                    \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                    return $this->updateSatuanMaster($post, false);
                } else {
                    return ['status'=> 422, 'data'=>$model->errors];
                }
            }else{
                return ['status'=> 422, 'data'=>$model->errors];
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdate() {
        try {
            $request = Yii::$app->request;
            $satuankonversi_id = $request->get('id');
            $is_active = $request->post('is_active');
            $model = SatuanKonversi::find()->where(['satuankonversi_id' => $satuankonversi_id])->one();
            $model->is_active = $is_active;
            if($model->save()){
                \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                $data = [
                    'skonversi' => $model,
                    'is_active' => $is_active
                ];
                return $this->updateSatuanMaster($data, true);
            }else{
                return ['status'=> 422, 'data'=>$model->errors, 'message' => 'Terjadi Kesalahan pada Server'];
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateSatuanMaster($data, $isUpdate = false) {
        $obatalkes_id = isset($data['obatalkes_id']) ? $data['obatalkes_id'] : $data['skonversi']['obatalkes_id'];
        $modelOA = ObatAlkes::find()->where(['obatalkes_id' => $obatalkes_id])->one();
        $satuanKonversi = SatuanKonversi::find()->where([
                                'obatalkes_id' => $obatalkes_id,
                                'is_active' => true
                            ])->asArray()->all();

        if($isUpdate || (!$isUpdate && $data['is_active'])) {
            $max_konversi = $this->searchMaxKonversi($satuanKonversi);
            $modelOA->kemasan_besar = $max_konversi['nilai_konversi'];
            $modelOA->satuanbesar_id = $max_konversi['satuanbesar_id'];
        }

        if($modelOA->save()) {
            $response = Yii::$app->docoRest->apotek->get('allow/reset-cache-konvert-satuan');
            $response = ['status' => 200, 'message' => 'Data berhasil disimpan'];
        } else {
            $response = ['status' => 422, 'data' => $modelOA->errors, 'message' => 'Terjadi Kesalahan pada Server'];
        }

        return $response;
    }

    private function searchMaxKonversi($listSatuan) {
        $max_nilai_konversi = 0;
        $max_satuan_besar = null;

        foreach ($listSatuan as $key => $value) {
            if($value['nilai_konversi'] > $max_nilai_konversi) {
                $max_nilai_konversi = $value['nilai_konversi'];
                $max_satuan_besar = $value['satuanbesar_id'];
            }
        }

        return [
            'nilai_konversi' => $max_nilai_konversi,
            'satuanbesar_id' => $max_satuan_besar
        ];
    }

    public function actionListObatAlkes()
    {
        try {
            $request = Yii::$app->request;
            $model = new ObatAlkes;
            $query = $model->find()
                ->select([
                    'obatalkes_m.obatalkes_id',
                    'obatalkes_m.obatalkes_kode',
                    'obatalkes_m.obatalkes_nama',
                    'obatalkes_m.satuankecil_id',
                    'kecil.satuanunit_nama as satuan_kecil'
                ])
                ->leftJoin('satuanunit_m kecil', 'obatalkes_m.satuankecil_id = kecil.satuanunit_id')
                ->where(['kecil.is_deleted' => false, 'kecil.is_active' => true]);

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');

                if(isset($advancedFilter['satuan_kecil'])) {
                    $satuankecil_id = $advancedFilter['satuan_kecil'];
                    $query->andWhere(['satuankecil_id' => $satuankecil_id]);
                    unset($_GET['advanced-filter']['satuan_kecil']);
                }

                if(isset($advancedFilter['obatalkes_id'])) {
                    $obatalkes_id = $advancedFilter['obatalkes_id'];
                    $query->andWhere(['obatalkes_id' => $obatalkes_id]);
                    unset($_GET['advanced-filter']['obatalkes_id']);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
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

    public function actionListKonversi()
    {
        try {
            $request = Yii::$app->request;

            $model = new SatuanKonversiObatView;
            $query = $model->find()
                ->where([
                    'obatalkes_id' => $request->get('obatalkes_id'),
                    'jenis' => 'obat'
                ]);

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
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