<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\SatuanKonversiBarang;
use app\modules\v1\models\SatuanKonversiObatView;
use app\modules\v1\models\Barang;

class SatuanKonversiBarangController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\SatuanKonversiBarang';
    
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE", "POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
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
                if(isset($advancedFilter['obatalkes_nama'])) {
                    $obatalkes_id = $advancedFilter['obatalkes_nama'];
                    $query->andWhere(['obatalkes_id' => $obatalkes_id]);
                    unset($_GET['advanced-filter']['obatalkes_nama']);
                }

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

    public function actionListBarang()
    {
        try {
            $request = Yii::$app->request;
            $model = new Barang;
            $query = $model->find()
                ->select([
                    'barang_m.barang_id', 
                    'barang_m.barang_nama', 
                    'barang_m.satuankecil_id', 
                    'kecil.satuanunit_nama as satuan_kecil'
                ])
                ->leftJoin('satuanunit_m kecil', 'barang_m.satuankecil_id = kecil.satuanunit_id')
                ->where(['kecil.is_deleted' => false, 'kecil.is_active' => true]);

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['barang_nama'])) {
                    $barang_id = $advancedFilter['barang_nama'];
                    $query->andWhere(['barang_id' => $barang_id]);
                    unset($_GET['advanced-filter']['barang_nama']);
                }

                if(isset($advancedFilter['satuan_kecil'])) {
                    $satuankecil_id = $advancedFilter['satuan_kecil'];
                    $query->andWhere(['satuankecil_id' => $satuankecil_id]);
                    unset($_GET['advanced-filter']['satuan_kecil']);
                }

                if(isset($advancedFilter['barang_id'])) {
                    $barang_id = $advancedFilter['barang_id'];
                    $query->andWhere(['barang_id' => $barang_id]);
                    unset($_GET['advanced-filter']['barang_id']);
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
                    'jenis' => 'barang',
                    'obatalkes_id' => $request->get('barang_id')
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