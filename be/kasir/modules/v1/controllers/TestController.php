<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DokterView;

class TestController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\DokterView';

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
        return $actions;
    }

    public function actionIndex()
    {
        $header = array(
            "Tanggal" => "tanggal satu",
            "Bagian" => "bagian satu",
        );

        // $result = DokterView::find()->select('nama_pegawai')->all();
        $result = Yii::$app->db->createCommand("
            select 
                nama_pegawai,
                ruangan_nama as ruangan,
                instalasi_nama as instalasi,
                jabatan_nama as jabatan
            from 
                dokter_v
        ")->queryAll();

        return \Doco\components\DocoHelpers::exportExcel("Laporan Data Dokter", $result, $header, array(
            "filePrefix" => "Kasir", // Nama Modul
            "uploadPath" => "", // Optional, default folder uploads di root app & root advanced app
        ));
    }
}