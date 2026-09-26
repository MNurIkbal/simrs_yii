<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoKunjungan;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Jabatan;

class InfKunjunganController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\InfoKunjungan';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        // unset($actions['view']);
        // unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        
        $model = new InfoKunjungan;
        $query = $model::find();

        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
                $between = true;
            }            
        }
        if($between) {
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGenerateApi()
    {
        // cara bayar
        $modelCaraBayar = new CaraBayar;
        $queryCaraBayar = $modelCaraBayar::find();

        $queryCaraBayar = DocoRestActiveFilter::advancedFilter($modelCaraBayar, $queryCaraBayar);
        $queryCaraBayar = $queryCaraBayar->asArray()->all();

        
        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find();

        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = $queryInstalasi->asArray()->all();

        // jenis kasus penyakit
        $modelKasusPenyakit = new JenisKasusPenyakit;
        $queryKasusPenyakit = $modelKasusPenyakit::find();

        $queryKasusPenyakit = DocoRestActiveFilter::advancedFilter($modelKasusPenyakit, $queryKasusPenyakit);
        $queryKasusPenyakit = $queryKasusPenyakit->asArray()->all();
        
        return [
            'cara_bayar' => $queryCaraBayar,            
            'instalasi' => $queryInstalasi,
            'kasus_penyakit' => $queryKasusPenyakit,
        ];
    }

    public function actionListPegawai()
    {
        $model = new Pegawai;
        $query = $model::find()->joinWith(['jabatan']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListJabatan()
    {
        $model = new Jabatan;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}