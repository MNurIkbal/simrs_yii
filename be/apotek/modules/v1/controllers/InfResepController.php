<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoPenjualanResep;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Lookup;

class InfResepController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfoPenjualanResep';

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
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        $model = new InfoPenjualanResep;
        $query = $model::find()->select(['penjualanresep_id','tglresep', 'noresep', 'nama_pasien', 'carabayar_nama', 
            'penjamin_nama', 'SUM(totalhargajual) as total']);

        $between = false;
        $start = date('Y-m-01');
        $end = date('Y-m-d');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglresep'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglresep']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglresep']);
                $between = true;
            }
        }
        if($between) {
            $query->andWhere(['between', 'tglresep', $start, $end]);
        }

        $query->groupBy(['penjualanresep_id','tglresep', 'noresep', 'nama_pasien', 'carabayar_nama', 
            'penjamin_nama', 'totalhargajual']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionPenjualan()
    {
        $request = Yii::$app->request;
        
        $model = new PenjualanResep;
        $query = $model::find()
        ->joinWith(['pasien'])
        ->leftJoin('lookup_m ON to_number(penjualanresep_t.jenispenjualan::text, \'999\'::text) = lookup_m.lookup_id')
        ->select(['penjualanresep_t.*', 'lookup_m.lookup_name', 'pasien_m.nama_pasien']);

        $between = false;
        $start = date('Y-m-01');
        $end = date('Y-m-d');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpenjualan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpenjualan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpenjualan']);
                $between = true;
            }
        }
        if($between) {
            $query->andWhere(['between', 'tglpenjualan', $start, $end]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGenerateApi()
    {
        $modelCaraBayar = new CaraBayar;
        $queryCaraBayar = $modelCaraBayar::find();

        $queryCaraBayar = DocoRestActiveFilter::advancedFilter($modelCaraBayar, $queryCaraBayar);
        $queryCaraBayar = new ActiveDataProvider([
            'query' => $queryCaraBayar,
        ]);

        // penjamin
        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find();

        $queryPenjamin = DocoRestActiveFilter::advancedFilter($modelPenjamin, $queryPenjamin);
        $queryPenjamin = new ActiveDataProvider([
            'query' => $queryPenjamin,
        ]);

        // resep
        $modelResep = new PenjualanResep;
        $queryResep = $modelResep::find();

        $queryResep = DocoRestActiveFilter::advancedFilter($modelResep, $queryResep);
        $queryResep = new ActiveDataProvider([
            'query' => $queryResep,
        ]);

        return [
            'cara_bayar' => $queryCaraBayar->getModels(),
            'penjamin' => $queryPenjamin->getModels(),
            'resep' => $queryResep->getModels(),
        ];
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;

        $modelMaster = new PenjualanResep();
        $queryMaster = $modelMaster->find()->joinWith(['pasien', 'carabayar', 'penjamin', 'ruangan.instalasi', 'pendaftaran', 
                       'pegawai'])
                        ->where(['penjualanresep_id' => $id]);

        $model = new InfoPenjualanResep;
        $query = $model->find()
            ->where(['penjualanresep_id' => $id]);

        return [
            'master' => $queryMaster->asArray()->one(),
            'detail' => $query->asArray()->all()
        ];
    }

    public function actionDeletePenjualan($id)
    {
        try {
            $model = PenjualanResep::delete($id);
            return $model;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}