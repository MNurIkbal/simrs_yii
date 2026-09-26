<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-06 14:46:16
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\cache\Cache;

use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\Persalinan;
use app\modules\v1\models\KalaTigaForm;
use app\modules\v1\models\PersalinanDetail;
use app\modules\v1\models\KelahiranBayi;

class KeadaanUmumController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\Persalinan';

    /**
     * @todo Verbs function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    /**
     * @todo Actions function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /**
     * @todo Fungsi untuk mendapatkan kebutuhan data keadaan umum
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataKeadaanUmum()
    {
        $params = Yii::$app->request->get();
        $pendaftaranId = $params['id'];

        $persalinan = Persalinan::find()->where(['pendaftaran_id' => $pendaftaranId])->one();
        $getLookup = $this->getDataLookupKeperawatan([
            'rujuk_kala',
            'pendamping', 
            'jenis_persalinan',
            'masalah_persalinan'
        ]);
        return [
            'persalinan' => $persalinan,
            'rujukKala' => isset($getLookup['rujuk_kala']) ? $getLookup['rujuk_kala'] : $getLookup['rujuk_kala'],
            'pendamping' => isset($getLookup['pendamping']) ? $getLookup['pendamping'] : $getLookup['pendamping'],
            'masalahPersalinan' => isset($getLookup['masalah_persalinan']) ? $getLookup['masalah_persalinan'] : $getLookup['masalah_persalinan'],
            'jenisPersalinan' => isset($getLookup['jenis_persalinan']) ? $getLookup['jenis_persalinan'] : $getLookup['jenis_persalinan'],
        ];
    }

    /**
     * @todo Fungsi untuk melakukan proses simpan persalinan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionSimpanPersalinan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        Yii::error([
            'keadaanumum-data' => $post
        ]);
        try {
            if (isset($post['persalinan_id']) && $post['persalinan_id'] != '') {
                $model = Persalinan::findOne($post['persalinan_id']);
            } else {
                $model = new Persalinan;
            }

            if (!$model->load($post, '')) {
                $transaction->rollBack();
                throw new \Exception(json_encode($model->getErrors()), 1);
                $result = [
                    'message' => $model->getErrors(),
                    'status' => 500,
                ];
            }

            if (!$model->validate()) {
                $transaction->rollBack();
                throw new \Exception(json_encode($model->getErrors()), 1);
                $result = [
                    'message' => $model->getErrors(),
                    'status' => 500,
                ];
            }

            if ($model->save()) {
                $transaction->commit();
                $result = [
                    'message' => Yii::t('app', 'Data Berhasil di simpan'),
                    'status' => 200
                ];
            } else {
                $transaction->rollBack();
                throw new \Exception(json_encode($model->getErrors()), 1);
                // $result = [
                //     'message' => ,
                //     'status' => 500,
                // ];
            }
            return $result;
        } catch (\yii\db\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function actionSaveKalaTiga()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $pendaftaran_id = $post["pendaftaran_id"];
            $find = Persalinan::find()->where(["pendaftaran_id" => $pendaftaran_id])->one();
            if (is_null($find)) {
                $model = new Persalinan;
                $model->pendaftaran_id = $pendaftaran_id;
            }else{
                $model = $find;
            }

            $model->k3 = json_encode($post);

            if (!$model->save()) {
                return [
                    "status" => 422,
                    "text" => Yii::t('app', 'Kala 3 Gagal'),
                ];
            }else{
                $transaction->commit();
                return [
                    'text' => Yii::t('app', 'Kala 3 Berhasil di simpan'),
                    'status' => 200
                ];
            }

        } catch (Exception $e) {
            $transaction->rollBack();
            return [
                    "status" => 422,
                    "text" => $e->getMessage(),
                ];
        }
    }

    public function actionGetKalaTiga()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get("id", null);
        $persalinan_k3 = [];
        if (is_null($pendaftaran_id)) {
            return $persalinan_k3;
        }

        $persalinan_k3 = Persalinan::find()
            ->select("persalinan_id, k3")
            ->where(["pendaftaran_id" => $pendaftaran_id])->one();

        return $persalinan_k3;
    }


    public function actionSimpanKalaEmpat($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $find = Persalinan::find()->where(["pendaftaran_id" => $id])->one();
            if (is_null($find)) {
                $model = new Persalinan;
            } else {
                $model = $find;
            }
            
            $attributes = [
                'pasienadmisi_id' => $request->post('pasienadmisi_id'),
                'pendaftaran_id' => $id,
                'k4_keadaanumum' => $request->post('k4_keadaanumum'),
                'k4_td_systolic' => $request->post('k4_td_systolic'),
                'k4_td_diastolic' => $request->post('k4_td_diastolic'),
                'k4_detaknadi' => $request->post('k4_detaknadi'),
                'k4_pernapasan' => $request->post('k4_pernapasan'),
                'k4_masalah' => $request->post('k4_masalah'),
            ];

            $model->attributes = $attributes;
            if ($model->save()) {
                $transaction->commit();
                return [
                    'text' => Yii::t('app', 'Kala 4 Berhasil di simpan'),
                    'status' => 200
                ];
            }

            return [
                'status' => 422,
                'data' => $model->errors
            ];

        } catch (Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
    }

    /**
    * @controller actionCetakKeadaanUmum
    * @attribute #no_rekam_medik# => No Rekam Medik
    * @attribute #tgl_pendaftaran# => Tanggal Pendaftaran
    * @attribute #no_pendaftaran# => No Pendaftaran
    * @attribute #nama_pasien# => Nama Pasien
    * @attribute #jenis_kelamin# => Jenis Kelamin
    * @attribute #jeniskasuspenyakit_nama# => Jenis Kasus Penyakit
    * @attribute #tanggal_lahir# => Tanggal Lahir
    * @attribute #umur# => Umur
    * @attribute #dokter_admisi# => Dokter Admisi
    * @attribute #kelas_pelayanan# => Kelas Pelayanan
    * @attribute #kamarruangan_nokamar# => No Kamar
    * @attribute #no_tempattidur# => No Tempat Tidur
    * @attribute #tgl_persalinan# => Tanggal Persalinan
    * @attribute #penolong# => Penolong
    * @attribute #tempat_persalinan# => Tempat Persalinan
    * @attribute #jenis_persalinan# => Jenis Persalinan
    * @attribute #alasan_merujuk# => Alasan Merujuk
    * @attribute #tempat_rujukan# => Tempat Rujukan
    * @attribute #rujuk_kala# => Rujuk Kala
    * @attribute #pendamping# => Pendamping
    * @attribute #masalah_persalinan# => Masalah Persalinan
    * @attribute #k2_episitomi# => Kala 2 Episitomi
    * @attribute #k2_indikasi# => Kala 2 Indikasi
    * @attribute #k2_pendamping# => Kala 2 Pendamping
    * @attribute #k2_gawatjalan# => Kala 2 Gawat Jalan
    * @attribute #k2_tindakanjanin# => Kala 2 Tindakan Janin
    * @attribute #k2_hasil# => Kala 2 Hasil
    * @attribute #k2_distosiabahu# => Kala 2 Distosia Bahu
    * @attribute #k2_tindakandistosia# => Kala 2 Tindakan janin
    * @attribute #k2_masalah# => Kala 2 Masalah
    * @attribute #k1_gariswaspada# => Kala 1 Garis Waspada
    * @attribute #k1_masalahlain# => Kala 1 Masalah lain
    * @attribute #k1_pelaksanaanmasalah# => Kala 1 Penatalaksanaan masalah
    * @attribute #k1_hasil# => Kala 1 Hasil
    **/
    public function actionCetakKeadaanUmum() {
        $id = Yii::$app->request->get('id');
        $persalinan = (new \yii\db\Query())
        ->select([
            'persalinan.tgl_persalinan',
            'persalinan.penolong',
            'persalinan.tempat_persalinan',
            'persalinan.alasan_merujuk',
            'persalinan.tempat_rujukan',
            'persalinan.k1_gariswaspada',
            'persalinan.k1_masalah',
            'persalinan.k1_pelaksanaanmasalah',
            'persalinan.k1_hasil',
            'persalinan.k2_episitomi',
            'persalinan.k2_indikasi',
            'persalinan.k2_gawatjanin',
            'persalinan.k2_tindakanjanin',
            'persalinan.k2_hasil',
            'persalinan.k2_distosiabahu',
            'persalinan.k2_tindakandistosia',
            'persalinan.k2_masalah',
            'rujuk_kala.lookup_name AS rujuk_kala',
            'pendamping.lookup_name AS pendamping',
            'masalah_persalinan.lookup_name AS masalah_persalinan',
            'k2_pendamping.lookup_name AS k2_pendamping',
            'persalinan.k3',
            'persalinan.k4_keadaanumum',
            'persalinan.k4_td_systolic',
            'persalinan.k4_td_diastolic',
            'persalinan.k4_detaknadi',
            'persalinan.k4_pernapasan',
            'persalinan.k4_masalah',
            'pegawai_m.nama_pegawai',
            'jenis_persalinan.lookup_name AS jenis_persalinan'
        ])
        ->from(Persalinan::tableName().' persalinan')
        ->leftJoin(LookupKeperawatan::tableName().' rujuk_kala', 'rujuk_kala.lookupkeperawatan_id = persalinan.rujuk_kala')
        ->leftJoin(LookupKeperawatan::tableName().' pendamping', 'pendamping.lookupkeperawatan_id = persalinan.pendamping')
        ->leftJoin(LookupKeperawatan::tableName().' jenis_persalinan', 'jenis_persalinan.lookupkeperawatan_id = persalinan.jenis_persalinan')
        ->leftJoin(LookupKeperawatan::tableName().' masalah_persalinan', 'masalah_persalinan.lookupkeperawatan_id = persalinan.masalah_persalinan')
        ->leftJoin(LookupKeperawatan::tableName().' k2_pendamping', 'k2_pendamping.lookupkeperawatan_id = persalinan.k2_pendamping')
        ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = persalinan.penolong')
        ->where(['persalinan.pendaftaran_id' => $id])
        ->one();

        $persalinanDetail = PersalinanDetail::find()->where([
            'pendaftaran_id' => $id
        ])->asArray()->all();


        $cacheKeperawatan = Cache::getLookUpKeperawatan([
            'laserisasi',
            'bayinormal_tindakan',
            'asfiksia',
            'asfiksia_tindakan',
            'penilaian',
            'kondisi_bayi'
        ]);

        $listKonfig = ArrayHelper::map($cacheKeperawatan,'lookupkeperawatan_id','lookup_name','lookup_type');

        $dataPasien = InfoPasienRanap::find()->where(['pendaftaran_id' => $id])->one();
        $dataKelahiran = KelahiranBayi::find()->where(['pendaftaran_id' => $id])->asArray()->all();

        $k2_episitomi = '-';
        if ($persalinan['k2_episitomi'] != 1) {
            if ($persalinan['k2_episitomi'] === false) {
                $k2_episitomi = Yii::t('app', 'Tidak');
            }
        } else {
            $k2_episitomi = Yii::t('app', 'Ya');
        }

        $k2_gawatjanin = '-';
        if ($persalinan['k2_gawatjanin'] != 1) {
            if ($persalinan['k2_gawatjanin'] === false) {
                $k2_gawatjanin = Yii::t('app', 'Tidak');
            }
        } else {
            $k2_gawatjanin = Yii::t('app', 'Ya');
        }

        $k2_distosiabahu = '-';
        if ($persalinan['k2_distosiabahu'] != 1) {
            if ($persalinan['k2_distosiabahu'] === false) {
                $k2_distosiabahu = Yii::t('app', 'Tidak');
            }
        } else {
            $k2_distosiabahu = Yii::t('app', 'Ya');
        }
        $model = new KalaTigaForm;
        if (!empty($persalinan['k3'])) {
            $model->attributes = json_decode($persalinan['k3'], true);
        }

        /** Kondisi Untuk kala 3 */
        if (!is_null($model->inisiasi_menyusui)) {
            if (!$model->inisiasi_menyusui) {
                $model->inisiasi_menyusui = 'Tidak, ' . $model->inisiasi_menyusui_alasan;
            } else {
                $model->inisiasi_menyusui = 'Ya';
            }
        }

        if (!is_null($model->oksitosin_10uim)) {
            if (!$model->oksitosin_10uim) {
                $model->oksitosin_10uim = 'Tidak, ' . $model->oksitosin_10uim_alasan;
            } else {
                $model->oksitosin_10uim = 'Ya, ' . $model->oksitosin_10uim_menit . ' Menit';
            }
        }

        if (!is_null($model->ulang_oksitosin)) {
            if ($model->ulang_oksitosin) {
                $model->ulang_oksitosin = 'Ya, ' . $model->ulang_oksitosin_alasan;
            } else {
                $model->ulang_oksitosin = 'Tidak';
            }
        }

        if (!is_null($model->penegangan_tali_pusat)) {
            if (!$model->penegangan_tali_pusat) {
                $model->penegangan_tali_pusat = 'Tidak, ' . $model->penegangan_tali_pusat_alasan;
            } else {
                $model->penegangan_tali_pusat = 'Ya';
            }
        }

        if (!is_null($model->fundus_uteri)) {
            if (!$model->fundus_uteri) {
                $model->fundus_uteri = 'Tidak, ' . $model->fundus_uteri_alasan;
            } else {
                $model->fundus_uteri = 'Ya';
            }
        }

        if (!is_null($model->plasenta_lahir_lengkap)) {
            if (!$model->plasenta_lahir_lengkap) {
                $tindakan = implode(", ", $model->plasenta_lahir_lengkap_tindakan);
                $model->plasenta_lahir_lengkap = 'Tidak, Tindakan :' . $tindakan;
            } else {
                $model->plasenta_lahir_lengkap = 'Ya';
            }
        }

        if (!is_null($model->plasenta_lahir_tidak_lahir)) {
            if ($model->plasenta_lahir_tidak_lahir) {
                $tindakan = implode(", ", $model->plasenta_lahir_tidak_lahir_tindakan);
                $model->plasenta_lahir_tidak_lahir = 'Ya, Tindakan :' . $tindakan;
            } else {
                $model->plasenta_lahir_tidak_lahir = 'Tidak';
            }
        }

        if (!is_null($model->laserisasi)) {
            if ($model->laserisasi) {
                $model->laserisasi = 'Ya, ' . $model->laserisasi_tempat;
            } else {
                $model->laserisasi = 'Tidak';
            }
        }

        if (isset($listKonfig['laserisasi'][$model->laserisasi_perineum])) {
            $model->laserisasi_perineum = 'Ya, ' . $listKonfig['laserisasi'][$model->laserisasi_perineum];
        } else {
            $model->laserisasi_perineum = 'Tidak, ' . $model->laserisasi_perineum_alasan;
        }

        if (!is_null($model->laserisasi_perineum_penjahitan)) {
            if (!$model->laserisasi_perineum_penjahitan) {
               $model->laserisasi_perineum_penjahitan = 'Tidak, ' . $model->laserisasi_perineum_penjahitan_alasan;
            } else {
               $model->laserisasi_perineum_penjahitan = 'Ada Penjahitan Dengan /Tanpa Anastesi';
            }
        }

        if (!is_null($model->anoni_uteri)) {
            if (!$model->anoni_uteri) {
               $model->anoni_uteri = 'Tidak';
            } else {
               $model->anoni_uteri = 'Ya, ' . $model->anoni_uteri_tindakan;
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekam_medik#' => isset($dataPasien['no_rekam_medik']) ? $dataPasien['no_rekam_medik'] : '-',
            '#tgl_pendaftaran#' => isset($dataPasien['tgl_pendaftaran']) ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($dataPasien['tgl_pendaftaran'])), false, true) : '-',
            '#no_pendaftaran#' => isset($dataPasien['no_pendaftaran']) ? $dataPasien['no_pendaftaran'] : '-',
            '#nama_pasien#' => isset($dataPasien['nama_pasien']) ? $dataPasien['nama_pasien'] : '-',
            '#jenis_kelamin#' => isset($dataPasien['jenis_kelamin']) ? $dataPasien['jenis_kelamin'] : '-',
            '#jeniskasuspenyakit_nama#' => isset($dataPasien['jeniskasuspenyakit_nama']) ? $dataPasien['jeniskasuspenyakit_nama'] : '-',
            '#tanggal_lahir#' => isset($dataPasien['tanggal_lahir']) ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($dataPasien['tanggal_lahir'])), false, false) : '-',
            '#umur#' => isset($dataPasien['umur']) ? $dataPasien['umur'] : '-',
            '#dokter_admisi#' => isset($dataPasien['dokter_admisi']) ? $dataPasien['dokter_admisi'] : '-',
            '#kelas_pelayanan#' => isset($dataPasien['kelas_pelayanan']) ? $dataPasien['kelas_pelayanan'] : '-',
            '#kamarruangan_nokamar#' => isset($dataPasien['kamarruangan_nokamar']) ? $dataPasien['kamarruangan_nokamar'] : '-',
            '#no_tempattidur#' => isset($dataPasien['no_tempattidur']) ? $dataPasien['no_tempattidur'] : '-',
            '#tgl_persalinan#' => isset($persalinan['tgl_persalinan']) ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($persalinan['tgl_persalinan'])), false, true) : '-',
            '#penolong#' => isset($persalinan['nama_pegawai']) ? $persalinan['nama_pegawai'] : '-',
            '#tempat_persalinan#' => isset($persalinan['tempat_persalinan']) ? $persalinan['tempat_persalinan'] : '-',
            '#jenis_persalinan#' => isset($persalinan['jenis_persalinan']) ? $persalinan['jenis_persalinan'] : '-',
            '#alasan_merujuk#' => isset($persalinan['alasan_merujuk']) ? $persalinan['alasan_merujuk'] : '-',
            '#tempat_rujukan#' => isset($persalinan['tempat_rujukan']) ? $persalinan['tempat_rujukan'] : '-',
            '#rujuk_kala#' => isset($persalinan['rujuk_kala']) ? $persalinan['rujuk_kala'] : '-',
            '#pendamping#' => isset($persalinan['pendamping']) ? $persalinan['pendamping'] : '-',
            '#masalah_persalinan#' => isset($persalinan['masalah_persalinan']) ? $persalinan['masalah_persalinan'] : '-',
            '#k2_episitomi#' => $k2_episitomi,
            '#k2_indikasi#' => isset($persalinan['k2_indikasi']) ? $persalinan['k2_indikasi'] : '-',
            '#k2_pendamping#' => isset($persalinan['k2_pendamping']) ? $persalinan['k2_pendamping'] : '-',
            '#k2_gawatjanin#' => $k2_gawatjanin,
            '#k2_tindakanjanin#' => isset($persalinan['k2_tindakanjanin']) ? implode(", ", json_decode($persalinan['k2_tindakanjanin'])) : '-',
            '#k2_hasil#' => isset($persalinan['k2_hasil']) ? $persalinan['k2_hasil'] : '-',
            '#k2_distosiabahu#' => $k2_distosiabahu,
            '#k2_tindakandistosia#' => isset($persalinan['k2_tindakandistosia']) ? implode(", ", json_decode($persalinan['k2_tindakandistosia'])) : '-',
            '#k2_masalah#' => isset($persalinan['k2_masalah']) ? $persalinan['k2_masalah'] : '-',
            '#k1_gariswaspada#' => isset($persalinan['k1_gariswaspada']) ? ($persalinan['k1_gariswaspada'] != null) ? 'Ya' : 'Tidak' : '-',
            '#k1_masalahlain#' => isset($persalinan['k1_masalah']) ? $persalinan['k1_masalah'] : '-',
            '#k1_pelaksanaanmasalah#' => isset($persalinan['k1_pelaksanaanmasalah']) ? $persalinan['k1_pelaksanaanmasalah'] : '-',
            '#k1_hasil#' => isset($persalinan['k1_hasil']) ? $persalinan['k1_hasil'] : '-',
            '#inisiasi_menyusui#' => $model->inisiasi_menyusui,
            '#lama_kala#' => $model->lama_kala,
            '#oksitosin_10uim#' => $model->oksitosin_10uim,
            '#ulang_oksitosin#' => $model->ulang_oksitosin,
            '#penegangan_tali_pusat#' => $model->penegangan_tali_pusat,
            '#fundus_uteri#' => $model->fundus_uteri,
            '#plasenta_lahir_lengkap#' => $model->plasenta_lahir_lengkap,
            '#plasenta_lahir_tidak_lahir#' => $model->plasenta_lahir_tidak_lahir,
            '#laserisasi#' => $model->laserisasi,
            '#laserisasi_perineum#' => $model->laserisasi_perineum,
            '#laserisasi_perineum_penjahitan#' => $model->laserisasi_perineum_penjahitan,
            '#anoni_uteri#' => $model->anoni_uteri,
            '#jumlah_darah_keluar#' => $model->jumlah_darah_keluar,
            '#masalah_penatalaksanaan#' => $model->masalah_penatalaksanaan,
            '#hasil#' => $model->hasil,
            '#k4_keadaanumum#' => $persalinan['k4_keadaanumum'],
            '#tekanan_darah#' => !empty($persalinan['k4_td_systolic']) ? $persalinan['k4_td_systolic'] . '/' . $persalinan['k4_td_diastolic'] : null,
            '#k4_detaknadi#' => $persalinan['k4_detaknadi'],
            '#k4_pernapasan#' => $persalinan['k4_pernapasan'],
            '#k4_masalah#' => $persalinan['k4_masalah'],
            '#tabel_pemantauan#' => $this->renderPartial('tabel-kala-pemantauan',[
                'detail' => $persalinanDetail
            ]),
            '#bayi_baru_lahir#' => $this->renderPartial('bayi-baru-lahir',[
                'detail' => $dataKelahiran,
                'cache' => $listKonfig
            ]),
            '#jenis_persalinan#' => $persalinan['jenis_persalinan']
        ];
        // return $listKonfig;
        $print->Output();
    }

    /**
     * @todo Fungsi untuk mendapatkan data lookup
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function getDataLookupKeperawatan($lookupType)
    {
        $model = LookupKeperawatan::find()->where([
            'lookup_type' => $lookupType
        ])->andWhere([
            'is_active' => true,
            'is_deleted' => false
        ])->all();

        if( is_array($lookupType) ){
            $result = [];
            foreach($model as $index => $item) {
                $result[$item['lookup_type']][] = $item;
            }
            return $result;
        }
        return $model;
    }
}