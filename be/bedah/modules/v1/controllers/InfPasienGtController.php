<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\GtLayananBedahView;

class InfPasienGtController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\GtLayananBedahView';

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

    public function actionIndex()
    {
        $model = new GtLayananBedahView();
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter(
            $model,
            DocoRestActiveFilter::filterMutation(
                $model,
                $query,
                [
                    'tgl_permintaan' => 'date',
                    'tgl_operasi' => 'date',
                    'jam_mulai' => 'time',
                    'jam_selesai' => 'time',
                    'data_intra_operasi.pasienmasukpenunjang_id' => 'json_array_like',
                    'data_intra_operasi.surgical_sefety_checklist' => 'json_array_like',
                    'data_intra_operasi.masuk_kamar' => 'json_array_time',
                    'data_intra_operasi.mulai_anastesi' => 'json_array_time',
                    'data_intra_operasi.selesai_anastesi' => 'json_array_time',
                    'data_intra_operasi.mulai_operasi' => 'json_array_time',
                    'data_intra_operasi.selesai_operasi' => 'json_array_time',
                    'data_tim_operasi.nama_pegawai' => 'json_array_like',
                    'data_tim_operasi.posisi' => 'json_array_like',
                    'data_tindakan_operasi.pasienmasukpenunjang_id' => 'json_array_like',
                    'data_tindakan_operasi.daftartindakan_nama' => 'json_array_like',
                    'data_penggunaan_bmhp.pasienmasukpenunjang_id' => 'json_array_like',
                    'data_penggunaan_bmhp.obatalkes_id' => 'json_array_like',
                    'data_penggunaan_bmhp.daftartindakan_nama' => 'json_array_like',
                    'data_tindakan_konsultasi.pasienmasukpenunjang_id' => 'json_array_like',
                    'data_tindakan_konsultasi.daftartindakan_nama' => 'json_array_like',
                    'data_post_operasi.jam_masuk_rec' => 'json_array_time',
                    'data_post_operasi.jam_keluar_rec' => 'json_array_time',
                    'data_post_operasi.cairan_infus' => 'json_array_like',
                    'data_post_operasi.kesadaran_umum' => 'json_array_like',
                    'data_post_operasi.tingkat_kesadaran' => 'json_array_like',
                    'data_post_operasi.kondisi_pasien' => 'json_array_like'
                ]
            )
        );
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
