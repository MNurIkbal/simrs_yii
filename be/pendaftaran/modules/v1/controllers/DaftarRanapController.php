<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\widgets\ActiveForm;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Propinsi;
use app\modules\v1\models\Suku;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PenanggungJawab;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\PasienPulangRdRjView;
use yii\helpers\ArrayHelper;

class DaftarRanapController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    const JENIS_IDENTITAS = 'jenis_identitas';
    const NAMA_DEPAN = 'nama_depan';
    const JENIS_KELAMIN = 'jenis_kelamin';
    const STATUS_PERKAWINAN = 'status_perkawinan';
    const WARGA_NEGARA = 'warga_negara';
    const AGAMA = 'agama';
    const PENGANTAR = 'pengantar';
    const HUBUNGAN = 'hubungan_keluarga';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionGenerateApi()
    {
        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();
        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
        ]);

        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find();
        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
        ]);

        // cara bayar
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

        // dokter PJ
        $modelDokter = new Pegawai;
        $queryDokter = $modelDokter::find()->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER]);
        $queryDokter = DocoRestActiveFilter::advancedFilter($modelDokter, $queryDokter);
        $queryDokter = new ActiveDataProvider([
            'query' => $queryDokter,
        ]);

        // jenis identitas
        $modelJenisIdentitas = new Lookup;
        $queryJenisIdentitas = $modelJenisIdentitas::find()->where(['lookup_type' => self::JENIS_IDENTITAS]);
        $queryJenisIdentitas = DocoRestActiveFilter::advancedFilter($modelJenisIdentitas, $queryJenisIdentitas);
        $queryJenisIdentitas = new ActiveDataProvider([
            'query' => $queryJenisIdentitas,
        ]);

        // nama depan
        $modelNamaDepan = new Lookup;
        $queryNamaDepan = $modelNamaDepan::find()->where(['lookup_type' => self::NAMA_DEPAN]);
        $queryNamaDepan = DocoRestActiveFilter::advancedFilter($modelNamaDepan, $queryNamaDepan);
        $queryNamaDepan = new ActiveDataProvider([
            'query' => $queryNamaDepan,
        ]);

        // jenis kelamin
        $modelJenisKelamin = new Lookup;
        $queryJenisKelamin = $modelJenisKelamin::find()->where(['lookup_type' => self::JENIS_KELAMIN]);
        $queryJenisKelamin = DocoRestActiveFilter::advancedFilter($modelJenisKelamin, $queryJenisKelamin);
        $queryJenisKelamin = new ActiveDataProvider([
            'query' => $queryJenisKelamin,
        ]);

        // status perkawinan
        $modelStatusPerkawinan = new Lookup;
        $queryStatusPerkawinan = $modelStatusPerkawinan::find()->where(['lookup_type' => self::STATUS_PERKAWINAN]);
        $queryStatusPerkawinan = DocoRestActiveFilter::advancedFilter($modelStatusPerkawinan, $queryStatusPerkawinan);
        $queryStatusPerkawinan = new ActiveDataProvider([
            'query' => $queryStatusPerkawinan,
        ]);

        // warga negara
        $modelWargaNegara = new Lookup;
        $queryWargaNegara = $modelWargaNegara::find()->where(['lookup_type' => self::WARGA_NEGARA]);
        $queryWargaNegara = DocoRestActiveFilter::advancedFilter($modelWargaNegara, $queryWargaNegara);
        $queryWargaNegara = new ActiveDataProvider([
            'query' => $queryWargaNegara,
        ]);

        // agama
        $modelAgama = new Lookup;
        $queryAgama = $modelAgama::find()->where(['lookup_type' => self::AGAMA]);
        $queryAgama = DocoRestActiveFilter::advancedFilter($modelAgama, $queryAgama);
        $queryAgama = new ActiveDataProvider([
            'query' => $queryAgama,
        ]);

        // pengantar
        $modelPengantar = new Lookup;
        $queryPengantar = $modelPengantar::find()->where(['lookup_type' => self::PENGANTAR]);
        $queryPengantar = DocoRestActiveFilter::advancedFilter($modelPengantar, $queryPengantar);
        $queryPengantar = new ActiveDataProvider([
            'query' => $queryPengantar,
        ]);

        // hubungan
        $modelHubungan = new Lookup;
        $queryHubungan = $modelHubungan::find()->where(['lookup_type' => self::HUBUNGAN]);
        $queryHubungan = DocoRestActiveFilter::advancedFilter($modelHubungan, $queryHubungan);
        $queryHubungan = new ActiveDataProvider([
            'query' => $queryHubungan,
        ]);

        // pekerjaan
        $modelPekerjaan = new Pekerjaan;
        $queryPekerjaan = $modelPekerjaan::find();
        $queryPekerjaan = DocoRestActiveFilter::advancedFilter($modelPekerjaan, $queryPekerjaan);
        $queryPekerjaan = new ActiveDataProvider([
            'query' => $queryPekerjaan,
        ]);

        // provinsi
        $modelPropinsi = new Propinsi;
        $queryPropinsi = $modelPropinsi::find();
        $queryPropinsi = DocoRestActiveFilter::advancedFilter($modelPropinsi, $queryPropinsi);
        $queryPropinsi = new ActiveDataProvider([
            'query' => $queryPropinsi,
        ]);

        // suku
        $modelSuku = new Suku;
        $querySuku = $modelSuku::find();
        $querySuku = DocoRestActiveFilter::advancedFilter($modelSuku, $querySuku);
        $querySuku = new ActiveDataProvider([
            'query' => $querySuku,
        ]);

        // pasien
        $modelPasien = new Pasien;
        $queryPasien = $modelPasien::find();
        $queryPasien = DocoRestActiveFilter::advancedFilter($modelPasien, $queryPasien);
        $queryPasien = new ActiveDataProvider([
            'query' => $queryPasien,
        ]);

        // jenis kasus penyakit
        $modelJenisKasus = new JenisKasusPenyakit;
        $queryJenisKasus = $modelJenisKasus::find();
        $queryJenisKasus = DocoRestActiveFilter::advancedFilter($modelJenisKasus, $queryJenisKasus);
        $queryJenisKasus = new ActiveDataProvider([
            'query' => $queryJenisKasus,
        ]);

        // kelas pelayanan
        $modelKelasPelayanan = new KelasPelayanan;
        $queryKelasPelayanan = $modelKelasPelayanan::find();
        $queryKelasPelayanan = DocoRestActiveFilter::advancedFilter($modelKelasPelayanan, $queryKelasPelayanan);
        $queryKelasPelayanan = new ActiveDataProvider([
            'query' => $queryKelasPelayanan,
        ]);

        return [
            'carabayar' => $queryCaraBayar->getModels(),
            'instalasi' => $queryInstalasi->getModels(),
            'penjamin' => $queryPenjamin->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'dokter' => $queryDokter->getModels(),
            'jenisidentitas' => $queryJenisIdentitas->getModels(),
            'nama_depan' => $queryNamaDepan->getModels(),
            'jenis_kelamin' => $queryJenisKelamin->getModels(),
            'status_perkawinan' => $queryStatusPerkawinan->getModels(),
            'warga_negara' => $queryWargaNegara->getModels(),
            'agama' => $queryAgama->getModels(),
            'pengantar' => $queryPengantar->getModels(),
            'hubungan' => $queryHubungan->getModels(),
            'pekerjaan' => $queryPekerjaan->getModels(),
            'propinsi' => $queryPropinsi->getModels(),
            'suku' => $querySuku->getModels(),
            'pasien' => $queryPasien->getModels(),
            'jeniskasus' => $queryJenisKasus->getModels(),
            'kelaspelayanan' => $queryKelasPelayanan->getModels(),
        ];
    }

    public function actionGetPasien($no_rekam_medik)
    {
        $modelPasien = Pasien::find()
            ->joinWith(['penanggungJawab'])
            ->where(['no_rekam_medik' => $no_rekam_medik]);
        
        return $modelPasien->asArray()->one();
    }

    public function actionListRuangan($jeniskasuspenyakit_id) {
        $data = KasusPenyakitRuangan::find()
            ->joinWith(['ruangan'])
            ->where(['kasuspenyakitruangan_mp.is_active' => 't', 'kasuspenyakitruangan_mp.is_deleted' => 'f', 
                'kasuspenyakitruangan_mp.jeniskasuspenyakit_id' => $jeniskasuspenyakit_id ]);

        $items = ArrayHelper::map($data->all(), 'ruangan.ruangan_id', 'ruangan.ruangan_nama');

        return $items;
    }
}