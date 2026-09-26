<?php

/**
 * @Author: rizal
 * @Date:   2018-01-24 10:39:52
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanKunjunganRawatJalanView;
use Doco\components\DocoHelpers;
use app\modules\v1\models\GtLayananRawatJalanView;

class InfPasienRawatJalanController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\LaporanKunjunganRawatJalanView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
    * @author Rizal
    * @since 2018-01-24 10:41:50
    * @param 
    * @return json list data
    * @desc 
    */
    public function actionIndex()
    {
        $model = new LaporanKunjunganRawatJalanView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        // modify advanced filters
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionLayananGt()
    {
        $model = new GtLayananRawatJalanView();
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter(
            $model,
            DocoRestActiveFilter::filterMutation(
                $model,
                $query,
                [
                    'waktu_pendaftaran' => 'date',
                    'waktu_masuk' => 'date',
                    'waktu_keluar' => 'date',
                    'data_pemeriksaan.tgl_periksa' => 'json_array_date',
                    'data_pemeriksaan.dokter' => 'json_array_like',
                    'data_pemeriksaan.nik_dokter' => 'json_array_like',
                    'data_pemeriksaan.perawat' => 'json_array_like',
                    'data_pemeriksaan.nik_perawat' => 'json_array_like',
                    'diag_utama.id' => 'json_like',
                    'diag_utama.text' => 'json_like',
                    'diag_utama.kode' => 'json_like',
                    'diag_utama.nama' => 'json_like',
                    'diag_penunjang.id' => 'json_like',
                    'diag_penunjang.text' => 'json_like',
                    'diag_penunjang.kode' => 'json_like',
                    'diag_penunjang.nama' => 'json_like',
                    'data_tindakan.tindakanpelayanan_id' => 'json_array_like',
                    'data_tindakan.pendaftaran_id' => 'json_array_like',
                    'data_tindakan.daftartindakan_id' => 'json_array_like',
                    'data_tindakan.tgl_tindakan' => 'json_array_date',
                    'data_tindakan.qty_tindakan' => 'json_array_like',
                    'data_tindakan.tarif_satuan' => 'json_array_like',
                    'data_tindakan.daftartindakan_nama' => 'json_array_like',
                    'data_tindakan.kelompoktindakan_nama' => 'json_array_like',
                    'data_tindakan.dokterpenanggungjawab_id' => 'json_array_like',
                    'data_tindakan.perawat1_id' => 'json_array_like'
                ]
            )
        );
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
