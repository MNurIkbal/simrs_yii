<?php

/**
 * setup profil rumah sakit
 *
 * @author ali.padilah@docotel.com
 * @return session json
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\ProfilRumahSakit;
use app\modules\v1\models\Pendidikan;
use app\modules\v1\models\KonfigSystem;

class SetupController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ProfilRumahSakit';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    /**
     * cek data rumah sakit
     *
     * @return json
     */
    public function actionDatars()
    {
        // $model = new ProfilRumahSakit;
        // $query = $model::find();
        // $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // return new ActiveDataProvider([
        //     'query' => $query,
        // ]);


        //     // ->select([
        //     //         'pendidikan_m.pendidikan_id',
        //     //         'pendidikan_m.indexing_id',
        //     //         'pendidikan_m.pendidikan_urutan',
        //     //         'pendidikan_m.pendidikan_urutan',
        //     //         'pendidikan_m.pendidikan_nama',
        //     //         'pendidikan_m.pendidikan_namalainnya',
        //     //         'pendidikan_m.is_active',
        //     // ])->joinWith([
        //     // 'indexing' => function ($query) {
        //     //     $query->select(['indexing_m.indexing_nama','indexing_m.indexing_id']);
        //     // }]);

        // // return $profil;

        return $this->getData();
    }


    private function getData()
    {
        $profil = ProfilRumahSakit::find()
        ->select([
                'nama_rumahsakit',
                'kelas_rumahsakit',
                'namadirektur_rumahsakit',
                'alamatlokasi_rumahsakit',
                'nomor_suratizin',
                'no_faksimili',
                'logo_rumahsakit',
                'path_logorumahsakit',
                'npwp',
                'website',
                'email',
                'no_telp_profilrs',
                'negara',
                'ppkpelayanan',
                'profilrumahsakit_m.kabupaten_id',
                'profilrumahsakit_m.kecamatan_id',
                'profilrumahsakit_m.propinsi_id',
                'profilrumahsakit_m.kelurahan_id',
                'kodejenisrs_profilrs',
                'jenisrs_profilrs',
                'gambar_login',
                'background_login',
                'logo_header',
                'path_gambar_login',
                'path_background_login',
                'path_logo_header',
                'warna_header',
                'font_header'
        ])->joinWith([
        'kabupaten' => function ($query) {
            $query->select(['kabupaten_nama']);
        }])->joinWith([
        'kecamatan' => function ($query) {
            $query->select(['kecamatan_nama']);
        }])->joinWith([
        'kelurahan' => function ($query) {
            $query->select(['kelurahan_nama']);
        }])->joinWith([
        'propinsi' => function ($query) {
            $query->select(['propinsi_nama']);
        }]);

        $data = $profil->where(['profilrs_id' => 1])->asArray()->one();

        $konfig_layar = KonfigSystem::find()->asArray()->one();

        $data['header'] = $konfig_layar['header'];
        $data['header_detail'] = $konfig_layar['header_detail'];
        $data['footer'] = $konfig_layar['footer'];
        $data['path_logoheader'] = $konfig_layar['path_logoheader'];

        return $data;
    }
}