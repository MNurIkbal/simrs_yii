<?php
//Author: Aris Munandar

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;

use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Html;

// Using model

use app\modules\v1\models\Pegawai;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\AsesmenMedisRD;
use app\modules\v1\models\AsesmenMedisIGD;
use app\modules\v1\models\PeriksaTubuh;
use app\modules\v1\models\BagianTubuh;
use app\modules\v1\models\BagianTubuhDetail;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\AsesmenKeperawatanRD;
use app\modules\v1\models\AsesmenPerawatRD;
use app\modules\v1\models\Triase;

use app\modules\v1\payload\AssessmentDokter;
use SirsCore\businessLogic\MonitoringTtvLogic;

// Class
class AsesmenMedisController extends DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\AsesmenMedisRD';

    private $triage_text_array = ['resusitasi' => 'Gawat Darurat',
                                   'emergent' => 'Membahayakan Jiwa',
                                   'urgent' => 'Berpotensi Membahayakan Jiwa',
                                   'non_urgent' => 'Dapat Menjadi Serius',
                                   'less_urgent' => 'Tidak Berbahaya'];
        // Verbs
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['save-asesmen-dokter'] = ["POST"];
        $verbs["cetak-asmed-rd"] = ["GET", "POST"];
        return $verbs;
    }

    // Acions
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(404, 'Data Tidak Ditemukan');
        }
        $riwayat = [];
        $reseptur = [];
        $obatPulang = [];
        $getAsesmenMedis = AsesmenMedisIGD::find()->select([
            'diagnosa_m.diagnosa_nama',
            'asesmenmedisrd_t.*'
        ])
            ->leftJoin('diagnosa_m', 'diagnosa_m.diagnosa_id = asesmenmedisrd_t.diagnosa_id')
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()
            ->one();

        if ($getAsesmenMedis) {
            $getSuggestData = Pendaftaran::find()->select([
                'asesmenperawatrd_t.tgl_datang',
                'asesmenperawatrd_t.tensi',
                'asesmenperawatrd_t.detak_nadi',
                'asesmenperawatrd_t.suhu_tubuh',
                'triase_t.tgl_triase as tgl_pasien_datang',
                'triase_t.tekanan_darah as tekanandarah',
                'triase_t.nadi',
                'triase_t.nafas as pernapasan',
                'triase_t.suhu',
                'triase_t.saturasi_oksigen as saturasi_o2',
                'asesmenperawatrd_t.tinggi_badan',
                'asesmenperawatrd_t.berat_badan',
                'asesmenperawatrd_t.gcseye_id',
                'asesmenperawatrd_t.gcsverbal_id',
                'asesmenperawatrd_t.gcsmotorik_id',
                'asesmenperawatrd_t.hasil_gcs',
                'asesmenperawatrd_t.is_kapitis',
                'asesmenperawatrd_t.r_penyakitsaatini as riwayat_penyakit_sekarang',
                'asesmenperawatrd_t.r_penyakitdahulu as riwayat_penyakit_dahulu',
                'asesmenperawatrd_t.r_pengobatan as riwayat_terapi_sebelumnya',
                'asesmenperawatrd_t.is_alergi',
                'asesmenperawatrd_t.alergi_obat',
                'asesmenperawatrd_t.alergi_lainnya',
                'asesmenperawatrd_t.kategori_triase_sehari as triage',
                'asesmenperawatrd_t.asesmen_auto',
                'asesmenperawatrd_t.asesmen_allo',
                'asesmenperawatrd_t.asesmen_auto_anamnesa',
                'asesmenperawatrd_t.asesmen_allo_text as asesmen_allo_anamnesa',
            ])
                ->leftJoin('triase_t', 'triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->leftJoin('asesmenperawatrd_t', 'asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where(['pendaftaran_t.pendaftaran_id' => $pendaftaran_id])->asArray()->one();

            if (empty($getAsesmenMedis['tgl_pasien_datang']) && isset($getSuggestData['tgl_datang'])) {
                $getAsesmenMedis['tgl_pasien_datang'] = $getSuggestData['tgl_datang'];
            }
            if (empty($getAsesmenMedis['tekanandarah']) && isset($getSuggestData['tensi'])) {
                $getAsesmenMedis['tekanandarah'] = $getSuggestData['tensi'];
            }
            if (empty($getAsesmenMedis['nadi']) && isset($getSuggestData['detak_nadi'])) {
                $getAsesmenMedis['nadi'] = $getSuggestData['detak_nadi'];
            }
            if (empty($getAsesmenMedis['suhu']) && isset($getSuggestData['suhu_tubuh'])) {
                $getAsesmenMedis['suhu'] = $getSuggestData['suhu_tubuh'];
            }
            foreach ($getSuggestData as $key => $value) {
                if (empty($getAsesmenMedis[$key])) {
                    $getAsesmenMedis[$key] = $value;
                }
            }

            if (empty($getAsesmenMedis['alergi'])) {
                $getAsesmenMedis['alergi'] = '';
            }
            if ($getSuggestData['is_alergi']) {
                if ($getAsesmenMedis['alergi'] == 'Tidak') {
                    $getAsesmenMedis['alergi'] = 'Ya';
                }
                if (empty($getAsesmenMedis['alergi_obat'])) {
                    if (!empty($getSuggestData['alergi_obat'])) {
                        $getAsesmenMedis['alergi'] .= ', Obat: ' . $getSuggestData['alergi_obat'];
                    }
                }
                if (empty($getAsesmenMedis['alergi_lainnya'])) {
                    if (!empty($getSuggestData['alergi_lainnya'])) {
                        $getAsesmenMedis['alergi'] .= ', Lainnya: ' . $getSuggestData['alergi_lainnya'];
                    }
                }
            }
        }

        if (empty($getAsesmenMedis)) {
            $getAsesmenMedis = Pendaftaran::find()->select([
                'pendaftaran_t.pasien_id',
                'asesmenperawatrd_t.tgl_datang',
                'asesmenperawatrd_t.tensi',
                'asesmenperawatrd_t.detak_nadi',
                'asesmenperawatrd_t.suhu_tubuh',
                'triase_t.tgl_triase as tgl_pasien_datang',
                'triase_t.tekanan_darah as tekanandarah',
                'triase_t.nadi',
                'triase_t.nafas as pernapasan',
                'triase_t.suhu',
                'triase_t.saturasi_oksigen as saturasi_o2',
                'asesmenperawatrd_t.tinggi_badan',
                'asesmenperawatrd_t.berat_badan',
                'COALESCE(asesmenperawatrd_t.gcseye_id, triase_t.gcseye_id) as gcseye_id',
                'COALESCE(asesmenperawatrd_t.gcsverbal_id, triase_t.gcsverbal_id) as gcsverbal_id',
                'COALESCE(asesmenperawatrd_t.gcsmotorik_id, triase_t.gcsmotorik_id) as gcsmotorik_id',
                'COALESCE(asesmenperawatrd_t.hasil_gcs, triase_t.hasil_gcs) as hasil_gcs',
                'asesmenperawatrd_t.is_kapitis',
                'asesmenperawatrd_t.r_penyakitsaatini as riwayat_penyakit_sekarang',
                'asesmenperawatrd_t.r_penyakitdahulu as riwayat_penyakit_dahulu',
                'asesmenperawatrd_t.r_pengobatan as riwayat_terapi_sebelumnya',
                'asesmenperawatrd_t.is_alergi',
                'asesmenperawatrd_t.alergi_obat',
                'asesmenperawatrd_t.alergi_lainnya',
                'COALESCE(asesmenperawatrd_t.kategori_triase_sehari, triase_t.hasil_triase) as triage',
                'asesmenperawatrd_t.asesmen_auto',
                'asesmenperawatrd_t.asesmen_allo',
                'asesmenperawatrd_t.asesmen_auto_anamnesa',
                'asesmenperawatrd_t.asesmen_allo_text as asesmen_allo_anamnesa',
            ])
                ->leftJoin('triase_t', 'triase_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->leftJoin('asesmenperawatrd_t', 'asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where(['pendaftaran_t.pendaftaran_id' => $pendaftaran_id])->asArray()->one();

            if (isset($getAsesmenMedis['tgl_datang'])) {
                $getAsesmenMedis['tgl_pasien_datang'] = $getAsesmenMedis['tgl_datang'];
            }
            if (isset($getAsesmenMedis['tensi'])) {
                $getAsesmenMedis['tekanandarah'] = $getAsesmenMedis['tensi'];
            }
            if (isset($getAsesmenMedis['detak_nadi'])) {
                $getAsesmenMedis['nadi'] = $getAsesmenMedis['detak_nadi'];
            }
            if (isset($getAsesmenMedis['suhu_tubuh'])) {
                $getAsesmenMedis['suhu'] = $getAsesmenMedis['suhu_tubuh'];
            }

            $getAsesmenMedis['alergi'] = '';
            if ($getAsesmenMedis['is_alergi']) {
                $getAsesmenMedis['alergi'] = 'Ya';
                if (!empty($getAsesmenMedis['alergi_obat'])) {
                    $getAsesmenMedis['alergi'] .= ', Obat: ' . $getAsesmenMedis['alergi_obat'];
                }
                if (!empty($getAsesmenMedis['alergi_lainnya'])) {
                    $getAsesmenMedis['alergi'] .= ', Lainnya: ' . $getAsesmenMedis['alergi_lainnya'];
                }
            }

            if ($getAsesmenMedis['is_alergi'] === false){
                $getAsesmenMedis['alergi'] = 'Tidak';
            }

            $riwayat = Pendaftaran::find()->select([
                'asesmenmedisrd_t.asesmenmedisrd_id',
                'asesmenmedisrd_t.riwayat_penyakit_sekarang as riwayat_penyakit_dahulu'
            ])
                ->leftJoin('asesmenmedisrd_t', 'asesmenmedisrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where(['pasien_id' => $getAsesmenMedis['pasien_id']])
                ->andWhere('pendaftaran_t.no_pendaftaran LIKE :query')
                ->addParams([':query' => 'RD%'])
                ->orderBy('pendaftaran_t.pendaftaran_id DESC')
                ->limit(2)
                ->offset(1)
                ->asArray()
                ->one();

            $reseptur = Pendaftaran::find()->select([
                'reseptur_t.reseptur_id',
                'pendaftaran_t.no_pendaftaran',
                'pendaftaran_t.pendaftaran_id'
            ])
                ->leftJoin('reseptur_t', 'reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                ->where(['pendaftaran_t.pasien_id' => $getAsesmenMedis['pasien_id']])
                ->andWhere('pendaftaran_t.no_pendaftaran LIKE :query')
                ->addParams([':query' => 'RD%'])
                ->orderBy('pendaftaran_t.pendaftaran_id DESC')
                ->limit(2)
                ->offset(1)
                ->asArray()
                ->one();

           if(!empty($reseptur)){
              $obatPulang = Pendaftaran::find()->select([
                  'reseptur_t.reseptur_id',
                  'resepturdetail_t.r',
                  'resepturdetail_t.rke',
                  'resepturdetail_t.qty_reseptur',
                  'resepturdetail_t.additional_data',
                  'obatalkes_m.obatalkes_namalain',
                  'signaobat_m.signa_nama'
              ])
                  ->leftJoin('reseptur_t', 'reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                  ->leftJoin('resepturdetail_t', 'resepturdetail_t.reseptur_id = reseptur_t.reseptur_id')
                  ->rightJoin('obatalkes_m', 'obatalkes_m.obatalkes_id = resepturdetail_t.obatalkes_id')
                  ->leftJoin('signaobat_m', 'signaobat_m.signa_id = resepturdetail_t.signa_id')
                  ->where(['pendaftaran_t.pendaftaran_id' => $reseptur['pendaftaran_id']])
                  ->asArray()
                  ->all();
            }
            $askepRecord = AsesmenPerawatRD::find()->select(['asesmenperawatrd_id', 'keluhan as keluhan_utama'])->andWhere(compact('pendaftaran_id'))->asArray()->one();
            if (!empty($askepRecord)) {
                $getAsesmenMedis['keluhan_utama'] = $askepRecord['keluhan_utama'];
            }
        }
        $getAnatomi = null;
        if (!is_null($getAsesmenMedis) && isset($getAsesmenMedis['asesmenmedisrd_id'])) {
            $getAnatomi = $this->getAnatomi($getAsesmenMedis['asesmenmedisrd_id']);
        }
        $getDiagnosa = Diagnosa::find()->select([
            'diagnosa_id as id',
            'diagnosa_nama as text',
        ])->asArray()->all();
        $getGcs = Gcs::find()->select([
            'gcs_id as id',
            'gcs_nama as text',
            'gcs_namalainnya',
            'gcs_nilaimin',
            'gcs_nilaimax',
            'is_kapitis',
        ])->orderBy([
            'gcs_nilaimin' => SORT_ASC
        ])->asArray()->all();
        $getMetodeGcs = MetodeGcs::find()->select([
            'metodegcs_id as id',
            'metodegcs_nama',
            'metodegcs_singkatan',
            'metodegcs_nilai',
        ])->asArray()->all();
        $metodeGcs = [];
        foreach ($getMetodeGcs as $item => $metode) {
            $metode['text'] = $metode['metodegcs_nama'] . ' - ' . $metode['metodegcs_nilai'];
            $metodeGcs[$metode['metodegcs_singkatan']][] = $metode;
        }
        $data_bagiantubuh = $this->getOrSetCache(DocoConstants::VAR_CACHE_BAGIANTUBUH, BagianTubuh::find());
        $data_getdetailbagiantubuh = $this->getOrSetCache(DocoConstants::VAR_CACHE_DETAILBAGIANTUBUH, BagianTubuhDetail::find());
        $data_detailbagiantubuh = [];
        foreach ($data_getdetailbagiantubuh as $k => $v) {
            $data_detailbagiantubuh[$v['bagiantubuh_id']][$v['bagiantubuhdetail_id']] = $v['nama_bagiantubuh'];
        }
        
        $enable_pulang = (new DocoConstansId)->actionGetAdditional('konfig_edit_form_pelayanan');

        return [
            'asesmenmedis'           => $getAsesmenMedis,
            'diagnosa'               => $getDiagnosa,
            'gcs'                    => $getGcs,
            'metodegcs'              => $metodeGcs,
            'data-anatomi'           => $getAnatomi,
            'data-bagiantubuh'       => $data_bagiantubuh,
            'data-detailbagiantubuh' => $data_detailbagiantubuh,
            'riwayat'                => $riwayat,
            'reseptur'               => $reseptur,
            'obatPulang'             => $obatPulang,
            'enable_pulang'          => $enable_pulang,
        ];
    }

    public function getAnatomi($asesmenmedisrd_id)
    {
        try {
            $anatomiTubuh = PeriksaTubuh::find()->select([
                'periksatubuh_t.bagiantubuh_id',
                'periksatubuh_t.bagiantubuhdetail_id',
                'periksatubuh_t.catatan_tubuh',
                'periksatubuh_t.koordinat_x',
                'periksatubuh_t.koordinat_y',
                'periksatubuh_t.counters',
                'periksatubuh_t.created_date',
                'bagian' => 'bagiantubuh_m.namabagtubuh',
                'bagianDetail' => 'bagiantubuhdetail_m.nama_bagiantubuh'
            ])->joinWith(
                [
                    'bagianTubuh' => function ($query) {
                        $query->select([
                            'bagiantubuh_m.bagiantubuh_id'
                        ]);
                    },
                    'bagianTubuhDetail' => function ($query) {
                        $query->select([
                            'bagiantubuhdetail_m.bagiantubuhdetail_id'
                        ]);
                    },
                ]
            )->where([
                'asesmenmedisrd_id' => $asesmenmedisrd_id
            ])->orderBy(['counters' => SORT_ASC])->asArray()->all();
            $result = $anatomiTubuh;
        } catch (\Exception $e) {
            $result = ['message' => $e->getMessage()];
        }
        return $result;
    }

    public function actionDiagnosaList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = Diagnosa::find()
            ->select([
                'diagnosa_id as id',
                'diagnosa_nama as text'
            ]);
        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'ilike',
                'diagnosa_nama',
                $term
            ]);
        }
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    public function actionSaveMedis()
    {
        $request = Yii::$app->request;
        $dataMedis = $request->post('formdata', []);
        $anatomi = isset($dataMedis['anatomi']) && !empty($dataMedis['anatomi']) ? $dataMedis['anatomi'] : [];
        $pendaftaran_id = !isset($dataMedis['pendaftaran_id']) || empty($dataMedis['pendaftaran_id']) ? null : $dataMedis['pendaftaran_id'];
        if (is_null($pendaftaran_id)) {
            return $this->responseJson(402, 'attribut pendaftaran_id tidak boleh kosong!');
        } else {
            $cekPendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            if (empty($cekPendaftaran)) {
                return $this->responseJson(404, 'Data Pendaftaran Tidak Ditemukan');
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        $model = AsesmenMedisIGD::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        if (empty($model)) {
            $model = new AsesmenMedisIGD;
        }
        $model->ket_imt = $dataMedis['imt_kategori'];
        $dataMedis['additional_data'] = (isset($dataMedis['additional_data']) ?
                                      (is_array($dataMedis['additional_data'])
                                      ? json_encode($dataMedis['additional_data'])
                                      : $dataMedis['additional_data'] ): '');
        $model->attributes = $dataMedis;

        if (!$model->save()) {
            $transaction->rollBack();
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }

        MonitoringTtvLogic::feedData($dataMedis, DocoConstants::ASESMEN_MEDIS, DocoConstants::INSTALASI_RAWAT_DARURAT);

        $asesmenmedisrd_id = $model->getPrimaryKey();
        PeriksaTubuh::deleteAll('asesmenmedisrd_id=:asesmenmedisrd_id', [':asesmenmedisrd_id' => $asesmenmedisrd_id]);
        if (isset($anatomi) && !empty($anatomi)) {
            $detailKeperawatan = [];
            foreach ($anatomi as $index => $item) {
                $anatomi[$index]['asesmenmedisrd_id'] = $asesmenmedisrd_id;
            }
            try {
                PeriksaTubuh::batchInsert($anatomi);
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                Yii::error([
                    'error-data' => $e->getMessage()
                ]);
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }
        }

        $transaction->commit();
        return $this->responseJson(200, 'Simpan Asesmen Medis Berhasil!');
    }

    /**
    * @controller actionCetakAsmedRd
    * @attribute #poliklinik# => Untuk Menampilkan nama Poliklinik
    * @attribute #no_pendaftaran# => Untuk Menampilkan No Pendaftaran
    * @attribute #no_rm# => Untuk Menampilkan No Rekam Medik
    * @attribute #nama_pasien# => Untuk Menampilkan nama pasien
    * @attribute #jenis_kelamin# => Untuk Menampilkan nama pasien
    * @attribute #tanggal_lahir# => Untuk Menampilkan Jenis Kelamin
    * @attribute #cara_bayar# => Untuk Menampilkan cara Bayar
    * @attribute #penjamin# => Untuk Menampilkan Penjamin
    * @attribute #dokter_pemeriksa# => Untuk Menampilkan Dokter pemeriksa
    * @attribute #perawat# => Untuk Menampilkan Nama Perawat
    * @attribute #tanggal_periksa# => Untuk Menampilkan Tanggal periksa
    * @attribute #keadaan_umum# => Untuk Menampilkan Keadaan Umum
    * @attribute #tekanan_darah# => Untuk Menampilkan Tekanan Darah
    * @attribute #klasifikasitekanandarah# => Untuk Menampilkan Klasifikasi Tekanan Darah
    * @attribute #mean_arteri_preassure# => Untuk Menampilkan Mean Arteri Preassure
    * @attribute #detak_nadi# => Untuk Menampilkan Detak Nadi
    * @attribute #denyut_jantung# => Untuk Menampilkan Denyut Jantung
    * @attribute #pernafasan# => Untuk Menampilkan Pernafasan
    * @attribute #suhu_tubuh# => Untuk Menampilkan Suhu Tubuh
    * @attribute #tinggi_badan# => Untuk Menampilkan Tinggi Badan
    * @attribute #berat_badan# => Untuk Menampilkan Berat Badan
    * @attribute #massa_index_tubuh# => Untuk Menampilkan massa index tubuh
    * @attribute #kelainan_tubuh# => Untuk Menampilkan Kelainan pada bagian tubuh
    * @attribute #inspeksi# => Untuk Menampilkan Inspeksi
    * @attribute #palpasi# => Untuk Menampilkan Palpasi
    * @attribute #perkusi# => Untuk Menampilkan Perkusi
    * @attribute #auskultasi# => Untuk Menampilkan Auskultasi
    * @attribute #gcs_eye# => Untuk Menampilkan GCS Eye
    * @attribute #metodegcs_eye# => Untuk Menampilkan metode GCS Eye
    * @attribute #nilaigcs_eye# => Untuk Menampilkan nilai GCS Eye
    * @attribute #gcs_verbal# => Untuk Menampilkan GCS Verbal
    * @attribute #metodegcs_verbal# => Untuk Menampilkan metode GCS Verbal
    * @attribute #nilaigcs_verbal# => Untuk Menampilkan nilai GCS Verbal
    * @attribute #gcs_motorik# => Untuk Menampilkan GCS Motorik
    * @attribute #nilaigcs_motorik# => Untuk Menampilkan nilai GCS Motorik
    * @attribute #gcs_is_kapitis# => Untuk Menampilkan is kapitis
    * @attribute #gcs_kategori# => Untuk Menampilkan gsc nama
    * @attribute #hasil_metode_gcs# => Untuk Menampilkan Hasil Metode GCS
    * @attribute #pernapasan_gerakan# => Untuk Menampilkan List PERNAPASAN GERAKAN DADA
    * @attribute #jalan_nafas# => Untuk Menampilkan List JALAN NAFAS DAN PERNAFASAN
    * @attribute #sirkulasi# => Untuk Menampilkan List SIRKULASI
    * @attribute #gambar_anatomi# => Untuk Menampilkan Gambar Anatomi Tubuh
    * @attribute #list_tabel_anatomi# => Untuk Menampilkan List bagian anatomi tubuh
    * @attribute #tanggal_pemeriksaan# => Untuk Menampilkan Tanggal Pemeriksaan
    * @attribute #tgl_cetak# => Untuk Menampilkan Tanggal saat ini dicetak
    * @attribute #imt_kategori# => Untuk Menampilkan imt kategori / bmi_definisi
    * @attribute #umur# => Untuk Menampilkan umur Pasien
    * @attribute #kelaspelayanan_nama# => Untuk Menampilkan kelas pelayanan  Pasien
    * @attribute #tgl_pendaftaran# => Untuk Menampilkan tgl pendaftaran  Pasien
    * @attribute #jeniskasuspenyakit_nama# => Untuk Menampilkan Jenis Penyakit  Pasien
    * @attribute #ttd_dokter_pemeriksa# => Tanda Tangan Dokter Pemeriksa
    * @attribute #status_periksa# => Untuk Menampilkan Status Periksa  Pasien
    **/
    public function actionCetakAsmedRd($id)
    {
        // Get pemeriksaan fisik
        $id = DocoHelpers::decrypt($id);
        $result = InfoPasienRdV::find()->where(['pendaftaran_id' => $id])->asArray()->one();
        $res = AsesmenMedisIGD::find()->where(['pendaftaran_id' => $id])->asArray()->one();
        $image = Yii::$app->urlManagerFrontend->createUrl('')."media/img/img-pemeriksaan/bagian_tubuh_medis.jpg";

        $resDiagnosa = AsesmenMedisIGD::find()->select([
            'diagnosa_m.diagnosa_nama',

        ])
        ->join('JOIN', 'diagnosa_m', 'diagnosa_m.diagnosa_id=asesmenmedisrd_t.diagnosa_id')
        ->where(['asesmenmedisrd_id' => $res['asesmenmedisrd_id']])
        ->asArray()->one();
        $diagResult = json_decode($res['diagnosis'], true);
        
        if($res['diagnosa_id'] != null && $res['diagnosis'] == null){
            $diagnosa_asesmen = $resDiagnosa;
        }
        if ($res['diagnosis'] != null) {
            if ($res['diagnosa_id'] != null) {
                $diagExist = [
                    'id' => '5',
                    'text' => $resDiagnosa['diagnosa_nama']
                ];
                $diagResult[] = $diagExist;
            }

            $resultString = "";
            foreach ($diagResult as $item) {
                $text = $item['text'];
                if(isset($item['nama'])){
                    $text = $item['nama'];
                }
                $resultString .= "- " . $text . "<br>";
            }
            $resultString = trim($resultString);
            $diagnosa_asesmen = $resultString;
        }

        $periksa_tubuh = PeriksaTubuh::find()->select([
            'periksatubuh_t.periksatubuh_id',
            'periksatubuh_t.created_date',
            'periksatubuh_t.catatan_tubuh',
            'periksatubuh_t.koordinat_x',
            'periksatubuh_t.koordinat_y',
            'bagiantubuh_m.namabagtubuh',
            'bagiantubuhdetail_m.nama_bagiantubuh',
        ])->join('JOIN', 'bagiantubuh_m', 'bagiantubuh_m.bagiantubuh_id=periksatubuh_t.bagiantubuh_id')
        ->join('JOIN', 'bagiantubuhdetail_m', 'bagiantubuhdetail_m.bagiantubuhdetail_id=periksatubuh_t.bagiantubuhdetail_id')
        ->where(['asesmenmedisrd_id' => $res['asesmenmedisrd_id']])
        ->orderBy('periksatubuh_t.periksatubuh_id ASC')->asArray()->all();
        $space = ' ';
        $gcs = [ $res['gcseye_id'],$res['gcsverbal_id'],$res['gcsmotorik_id']];
        $arrGcs = [];
        foreach ($gcs as $key => $value) {
             $sql = (new Query())->select([
            'metodegcs_nama',
            'metodegcs_nilai'
            ])
            ->from('metodegcs_m')->where([
                'metodegcs_id' => $value,
            ])->one();
            $arrGcs[$key] = $sql;
        }


        // Check model
        if (!empty($result) && !empty($res)) {
            $print = new DocoPrint();
            $beratBadan = (isset($res['berat_badan']) && $res['berat_badan'] != '')? $res['berat_badan']:0;
            $tinggiBadan = (isset($res['tinggi_badan']) && $res['tinggi_badan'] != '')? $res['tinggi_badan']:0;
            if(($tinggiBadan == 0) || ($beratBadan == 0)){
                $bmi = 0;
            }else{
                // Menghitung BMI
                $bmi = $beratBadan / (($tinggiBadan/100) * ($tinggiBadan/100));
                $bmi = number_format($bmi,2,",",".");
                // Menghitung BB Ideal
                $bb_ideal = ($tinggiBadan - 100) - (0.1 * ($tinggiBadan - 100));
                $bb_ideal = number_format($bb_ideal,2,",",".");
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $image);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // bypass SSL
            $data = curl_exec($ch);
            curl_close($ch);
            $imagecreate  = imagecreatefromstring($data);
            $white  = imagecolorallocate($imagecreate, 255, 255, 255);
            $red = imagecolorallocate($imagecreate, 0xFF, 0x00, 0x00);
            $orange = imagecolorallocate($imagecreate, 255, 112, 67);
            $fontImage = "fonts/arialbd.ttf";


            // $explode = explode("x", $res['resolusi_image']);
            // $width = isset($explode[0])? $explode[0]:400;
            // $height = isset($explode[1])? $explode[1]:400;

            // Generate tabel pemeriksaan anatomi
            // Variable
            $no = 1;
            $htmlTable = '<table border="1" cellpadding="1" cellspacing="0" style="width:400px">';
            $htmlTable .= '<tbody>';
            $htmlTable .= '<tr>';
            $htmlTable .= '<th>No</th>';
            $htmlTable .= '<th>Tanggal periksa</th>';
            $htmlTable .= '<th>Bagian Tubuh</th>';
            $htmlTable .= '<th>Bagian Tubuh Detail</th>';
            $htmlTable .= '<th>Catatan</th>';
            $htmlTable .= '</tr>';

            //Loop
            foreach ($periksa_tubuh as $value) {
                // Generate table
                $htmlTable .= '<tr><td>'.$no.'</td><td>'.date('d/m/Y H:i:s', strtotime($value['created_date'])).'</td><td>'.$value['namabagtubuh'].'</td><td>'.$value['nama_bagiantubuh'].'</td><td>'.$value['catatan_tubuh'].'</td></tr>';
                if(isset($value['koordinat_x']) && isset($value['koordinat_y'])) {
                    $notext = $no."";
                    imagefilledarc($imagecreate, $value['koordinat_x']+3, $value['koordinat_y']+12, 25, 25, 0, 360, $orange, IMG_ARC_PIE);
                    imagefttext($imagecreate, 15, 0, $value['koordinat_x']+2-(3.5*strlen($notext)), $value['koordinat_y']+18, $white, $fontImage,$notext );
                }
                $no++;
            }

            // Close tag
            $htmlTable .= '</tbody>';
            $htmlTable .= '</table>';
            $fileTempPath = Yii::getAlias("@webroot")."/assets/tmp_bagian_tubuh_medis.png";
            imagepng($imagecreate, $fileTempPath);
            $htmlImage = '
                    <div>
                    <img src="'.$fileTempPath.'" width="500" height="520">
                    </div>';
            // checkbox anamnesis
            $allo_text = !empty($res['asesmen_allo_anamnesa'])?$res['asesmen_allo_anamnesa']:'';
            $asesmen_auto = $res['asesmen_auto'] == 1 ? '<input checked="checked" type="checkbox" /> Auto Anamnesa' : '<input type="checkbox" /> Auto Anamnesa';
            $asesmen_allo = $res['asesmen_allo'] == 1 ? '<input checked="checked" type="checkbox" /> Allo Anamnesa <br>'.$allo_text : '<input type="checkbox" /> Allo Anamnesa';
            $anamnesis    = $asesmen_auto.'<br>'.$asesmen_allo;
            $dewasa = $res['skala_nyeri'];
            $anak = $res['skala_nyeri_anak'];
            // checkbox skrining nyeri
            if($res['skrining_nyeri'] == '1'){
                $skrining_nyeri = '<input type="checkbox" /> Tidak <input checked="checked" type="checkbox" /> Ya' ;
                $nol      = ($dewasa == 0 && $dewasa != null || $anak == 0 && $dewasa != null ) ? '<span style=" border: 5px solid #04C86B;border-radius: 50px;background-color:#04C86B; color:white;">O</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">O</span>';
                $satu     = ($dewasa == 1 || $anak == 1 ) ? '<span style=" border: 5px solid #4FC354;border-radius: 50px;background-color:#4FC354; color:white;">1</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">1</span>';
                $dua      = ($dewasa == 2 || $anak == 2 ) ? '<span style=" border: 5px solid #8DBD33;border-radius: 50px;background-color:#8DBD33; color:white;">2</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">2</span>';
                $tiga     = ($dewasa == 3 || $anak == 3 ) ? '<span style=" border: 5px solid #C5DA2C;border-radius: 50px;background-color:#C5DA2C; color:white;">3</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">3</span>';
                $empat    = ($dewasa == 4 || $anak == 4 ) ? '<span style=" border: 5px solid #F0F221;border-radius: 50px;background-color:#F0F221; color:white;">4</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">4</span>';
                $lima     = ($dewasa == 5 || $anak == 5 ) ? '<span style=" border: 5px solid #F2D51A;border-radius: 50px;background-color:#F2D51A; color:white;">5</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">5</span>';
                $enam     = ($dewasa == 6 || $anak == 6 ) ? '<span style=" border: 5px solid #F2B610;border-radius: 50px;background-color:#F2B610; color:white;">6</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">6</span>';
                $tujuh    = ($dewasa == 7 || $anak == 7 ) ? '<span style=" border: 5px solid #F09409;border-radius: 50px;background-color:#F09409; color:white;">7</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">7</span>';
                $delapan  = ($dewasa == 8 || $anak == 8 ) ? '<span style=" border: 5px solid #EF7800;border-radius: 50px;background-color:#EF7800; color:white;">8</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">8</span>';
                $sembilan = ($dewasa == 9 || $anak == 9 ) ? '<span style=" border: 5px solid #E54209;border-radius: 50px;background-color:#E54209; color:white;">9</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">9</span>';
                $sepuluh  = ($dewasa == 10 || $anak == 10 ) ? '<span style=" border: 5px solid #D61F01;border-radius: 50px;background-color:#D61F01; color:white;">10</span>':'<span style=" border: 5px solid grey;border-radius: 50px;background-color:grey; color:white;">10</span>';
                $skala_number = $nol.'&emsp;&emsp;'.$satu.'&emsp;&emsp;'.$dua.'&emsp;&emsp;'.$tiga.'&emsp;&emsp;'.$empat.'&emsp;&emsp;'.$lima.'&emsp;&emsp;'
                                .$enam.'&emsp;&emsp;'.$tujuh.'&emsp;&emsp;'.$delapan.'&emsp;&emsp;'.$sembilan.'&emsp;&emsp;'.$sepuluh.'&emsp;&emsp;';
                if($res['pilih_skala'] == 'dewasa'){
                    $skala_nyeri = 'Skala Nyeri  <br><br>
                    <span style="display: inline-flex;text-align=center;">'.$skala_number.
                    '</span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;';
                }
                else if($res['pilih_skala'] == 'anak'){
                    $skala_nyeri = 'Skala Nyeri Anak <br><br>
                    <span style="display: inline-flex;text-align=center;">'.$skala_number.
                    '</span>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;';
                }else{
                    $skala_nyeri = '';
                }
            }else if($res['skrining_nyeri'] == '0'){
                $skrining_nyeri = '<input checked="checked" type="checkbox" /> Tidak <input type="checkbox" /> Ya' ;
            }else{
                $skrining_nyeri = '<input type="checkbox" /> Tidak <input type="checkbox" /> Ya' ;
            }

                // checkbox kasus polisi
            $polisi_tidak = $res['kasus_polisi'] == 'tidak' ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
            $polisi_ya   = $res['kasus_polisi'] == 'ya' ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
            $kasus_polisi = $polisi_tidak.'&nbsp;'.$polisi_ya;
            $kecelakan_tidak = $res['kasus_kecelakaan'] == 'tidak' ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak';
            $kecelakan_ya   = $res['kasus_kecelakaan'] == 'ya' ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya';
            $kasus_kecelakaan = $kecelakan_tidak.'&nbsp;'.$kecelakan_ya;

                // checkbox cara datang
            $diantar_text =  !empty($res['cara_datang_diantar'])?$res['cara_datang_diantar']:'';
            $sendiri = $res['cara_datang'] == '0' ? '<input checked="checked" type="checkbox" /> Sendiri' : '<input type="checkbox" /> Sendiri';
            $diantar = $res['cara_datang'] == '1' ? '<input checked="checked" type="checkbox" /> Diantar Oleh<br>'.$diantar_text : '<input type="checkbox" /> Diantar Oleh';
            $cara_datang = $sendiri.'&nbsp;'.$diantar;

            //checkbox triage
            $resusitasi  = isset($res['triage']) && $res['triage'] == 'resusitasi' ? '<input checked="checked" type="checkbox" /> Resusitasi' : '<input type="checkbox" /> Resusitasi';
            $emergency   = isset($res['triage']) && $res['triage'] == 'emergent' ? '<input checked="checked" type="checkbox" /> Emergency' : '<input type="checkbox" /> Emergency';
            $urgent      = isset($res['triage']) && $res['triage'] == 'urgent' ? '<input checked="checked" type="checkbox" /> Urgent' : '<input type="checkbox" /> Urgent';
            $non_urgent  = isset($res['triage']) && $res['triage'] == 'non_urgent' ? '<input checked="checked" type="checkbox" /> Non Urgent' : '<input type="checkbox" /> Non Urgent';
            $less_urgent = isset($res['triage']) && $res['triage'] == 'less_urgent' ? '<input checked="checked" type="checkbox" /> Less Urgent' : '<input type="checkbox" /> Less Urgent';
            $triage = $resusitasi.'<br>'.$emergency.'<br>'.$urgent.'<br>'.$non_urgent.'<br>'.$less_urgent;
            
            $signaturePath = Pegawai::signatureEmployee($result['dokter_jaga_id']);

            /**prevent space */
            $pattern = '/\s\s+/m';
            $replacement = ' ';

            $keluhan_utama = isset($res['keluhan_utama']) ? $res['keluhan_utama'] : ' - ';
            $keluhan_utama = preg_replace($pattern, $replacement, $keluhan_utama);

            $riwayat_penyakit_sekarang = isset($res['riwayat_penyakit_sekarang']) ? $res['riwayat_penyakit_sekarang'] : ' - ';
            $riwayat_penyakit_sekarang = preg_replace($pattern, $replacement, $riwayat_penyakit_sekarang);

            $riwayat_penyakit_dahulu = isset($res['riwayat_penyakit_dahulu']) ? $res['riwayat_penyakit_dahulu'] : ' - ';
            $riwayat_penyakit_dahulu = preg_replace($pattern, $replacement, $riwayat_penyakit_dahulu);

            // Assign attributes
            $print->attributes = [
                // Asesmen Medis
                '#tgl_pasiendatang#' =>  isset($res['tgl_pasien_datang']) ? date('d/m/Y H:i:s', strtotime($res['tgl_pasien_datang'])) : ' - ',
                '#triage#' => isset($triage) ? $triage : ' - ',
                '#anemnesis#' => isset($anamnesis) ? $anamnesis : ' - ',
                '#keluhan_utama#' =>  $keluhan_utama,
                '#riwayat_penyakit_sekarang#' => $riwayat_penyakit_sekarang,
                '#tgl_asesmen#' => isset($res['tgl_asesmen']) ? date('d/m/Y H:i:s', strtotime($res['tgl_asesmen'])) : ' - ',
                '#dikirim_oleh#' => isset($res['dikirim_oleh']) ? $res['dikirim_oleh'] : ' - ',
                '#kasus_polisi#' => isset($kasus_polisi) ? $kasus_polisi : ' - ',
                '#kasus_kecelakaan#' => isset($kasus_kecelakaan) ? $kasus_kecelakaan : ' - ',
                '#cara_datang#' => isset($cara_datang) ? $cara_datang : ' - ',
                '#riwayat_penyakit_dahulu#' => $riwayat_penyakit_dahulu,
                '#riwayat_terapi_sebelumnya#' => isset($res['riwayat_terapi_sebelumnya']) ? $res['riwayat_terapi_sebelumnya'] : ' - ',
                '#alergi#' => isset($res['alergi']) ? $res['alergi'] : null,

                // Pemeriksaan Fisik
                '#tinggi_badan#' =>  isset($res['tinggi_badan']) ? $res['tinggi_badan'] : 0,
                '#berat_badan#' => isset($res['berat_badan']) ? $res['berat_badan'] : 0,
                '#berat_badan_ideal#' =>  isset($bb_ideal) ? $bb_ideal : 0,
                '#IMT#' => isset($bmi) ? $bmi : 0,
                '#klasifikasi_berat_badan#' => isset($res['ket_imt']) ? $res['ket_imt'] : ' - ',

                '#tekanan_darah#' => isset($res['tekanandarah']) ? $res['tekanandarah'] : ' - ',
                '#frekuensi_nadi#' => isset($res['nadi']) ? $res['nadi'] : ' - ',
                '#pernapasan#' => isset($res['pernapasan']) ? $res['pernapasan'] : " - ",
                '#suhu#' => isset($res['suhu']) ? $res['suhu'] : ' - ',
                '#saturasi_o2#' => isset($res['saturasi_o2']) ? $res['saturasi_o2'] : ' - ',
                '#skrining_nyeri#' => isset($skrining_nyeri) ? $skrining_nyeri : ' - ',
                '#skala_nyeri#' => isset($skala_nyeri) ? $skala_nyeri : null,
                '#skala_nyeri_anak#' => isset($skala_nyeri_anak) ? $skala_nyeri_anak : null,

                '#gcs_eye#' => isset($res['gcseye_id']) ? $arrGcs[0]['metodegcs_nama'].' - '.$arrGcs[0]['metodegcs_nilai']:' - ',
                '#gcs_verbal#' => isset($res['gcsverbal_id']) ? $arrGcs[1]['metodegcs_nama'].' - '.$arrGcs[1]['metodegcs_nilai']:' - ',
                '#gcs_motorik#' => isset($res['gcsmotorik_id']) ? $arrGcs[2]['metodegcs_nama'].' - '.$arrGcs[2]['metodegcs_nilai']:' - ',
                '#hasil_metode_gcs#' => isset($res['hasil_gcs']) ? $res['hasil_gcs'] : null,

                // secondary survey
                '#kepala#' => isset($kepala_form)?$kepala_form: null,
                '#mata#' => isset($eye_form)?$eye_form : null,
                '#mulut#' => isset($mulut_form)?$mulut_form : null,
                '#telinga#' => isset($telinga_form)?$telinga_form : null,
                '#leher#' => isset($leher_form)?$leher_form : null,
                '#extremitas#' => isset($extremitas_form)?$extremitas_form : null,
                '#dada#' => isset($dada_form)?$dada_form : null,
                '#abdomen#' => isset($abdomen_form)?$abdomen_form : null,
                '#pelvis#' => isset($pelvis_form)?$pelvis_form : null,
                '#medulla_spinalis#' => isset($medulla_spinalis_form)?$medulla_spinalis_form : null,
                '#kolumna_veterbalis#' => isset($kolumna_veterbalis_form)?$kolumna_veterbalis_form : null,
                '#secondary_survey#' => $this->renderPartial('cetak',[
                    'kepala' => isset($kepala_form)?$kepala_form: null,
                    'mata' => isset($eye_form)?$eye_form : null,
                    'mulut' => isset($mulut_form)?$mulut_form : null,
                    'telinga' => isset($telinga_form)?$telinga_form : null,
                    'leher' => isset($leher_form)?$leher_form : null,
                    'extremitas' => isset($extremitas_form)?$extremitas_form : null,
                    'dada' => isset($dada_form)?$dada_form : null,
                    'abdomen' => isset($abdomen_form)?$abdomen_form : null,
                    'pelvis' => isset($pelvis_form)?$pelvis_form : null,
                    'medulla_spinalis' => isset($medulla_spinalis_form)?$medulla_spinalis_form : null,
                    'kolumna_veterbalis' => isset($kolumna_veterbalis_form)?$kolumna_veterbalis_form : null,
                ]),
                // '#side_left#' => isset($side_left)?$side_left : null,
                // '#side_right#' => isset($side_right)?$side_right : null,

                // status lokalis
                '#gambar_anatomi#' => $htmlImage,
                '#list_tabel_anatomi#' => $htmlTable,

                // Pemeriksaan Penunjang
                '#laboratorium#' => isset($res['laboratorium']) ? $res['laboratorium'] : null,
                '#radiologi#' => isset($res['radiologi']) ? $res['radiologi'] : null,
                '#ekg#' => isset($res['ekg']) ? $res['ekg'] : null,
                '#lain_lain#' => isset($res['lain_lain']) ? $res['lain_lain'] : null,

                // Diagnosis dan Terapis Saat Meninggalkan IGD
                '#diagnosa#' => isset($diagnosa_asesmen) ? $diagnosa_asesmen : null,
                '#terapi#' => isset($res['terapi']) ? $res['terapi'] : null,

                // Tindak Lanjut
                '#tindak_lanjut#' => isset($tindakan_lanjut) ? $tindakan_lanjut : null,

                // Kondisi Saat Meninggalkan IGD
                '#KU#' => isset($res['ku_keluar']) ? $res['ku_keluar'] : null,
                '#tekanan_darah_keluar#' => isset($res['tekanandarah_keluar']) ? $res['tekanandarah_keluar'] : null,
                '#nadi_keluarr#' => isset($res['nadi_keluar']) ? $res['nadi_keluar'] : null,
                '#pernapasan_keluar#' => isset($res['pernapasan_keluar']) ? $res['pernapasan_keluar'] : null,
                '#saturasi_o2_keluar#' => isset($res['saturasi_o2_keluar']) ? $res['saturasi_o2_keluar'] : null,
                '#suhu_keluar#' => isset($res['suhu_keluar']) ? $res['suhu_keluar'] : null,

                '#status_periksa#' => isset($result['status_periksa']) ? $result['status_periksa'] : null,
                '#jeniskasuspenyakit_nama#' => isset($result['jeniskasuspenyakit_nama']) ? $result['jeniskasuspenyakit_nama'] : null,
                '#umur#' => isset($result['umur']) ? $result['umur'] : null,
                '#kelaspelayanan_nama#' => isset($result['kelaspelayanan_nama']) ? $result['kelaspelayanan_nama'] : null,
                '#tgl_pendaftaran#' => isset($result['tgl_pendaftaran']) ? date('d/m/Y H:i:s', strtotime($result['tgl_pendaftaran'])) : null,
                '#poliklinik#' => isset($result['ruangan_nama']) ? $result['ruangan_nama'] : null,
                '#no_pendaftaran#' => isset($result['no_pendaftaran']) ? $result['no_pendaftaran'] : null ,
                '#no_rm#' => isset($result['no_rekam_medik']) ? $result['no_rekam_medik'] : null,
                '#nama_pasien#' => isset($result['nama_pasien']) ? $result['nama_pasien'] : null,
                '#jenis_kelamin#' => isset($result['jenis_kelamin']) ? $result['jenis_kelamin'] : null,
                '#tanggal_lahir#' => isset($result['tanggal_lahir']) ? date('d/m/Y  ', strtotime($result['tanggal_lahir'])) : null,
                '#cara_bayar#' => isset($result['carabayar_nama']) ? $result['carabayar_nama'] : null,
                '#penjamin#' => isset($result['penjamin_nama']) ? $result['penjamin_nama'] : null,
                '#dokter_pemeriksa#' => isset($result['dokter_jaga']) ? $result['dokter_jaga'] : null,
                '#tanggal_periksa#' => isset($result['tglperiksafisik']) ? date('d F Y', strtotime($result['tglperiksafisik'])) : null,
                '#bb_ideal#' => isset($result['bb_ideal']) ? $result['bb_ideal'] : null,
                '#tgl_cetak#' => date('d F Y'),
                // Section  Secondary Survey
                '#pemeriksaan_kepala#'=> $this->getValueRadioButton('normal',$res['kepala'],$res['kepala_lainnya']),
                '#pemeriksaan_mata#'=> $this->getValueRadioButton('normal',$res['mata'],$res['mata_lainnya']),
                '#pemeriksaan_tht#'=> $this->getValueRadioButton('normal',$res['tht'],$res['tht_lainnya']),
                '#pemeriksaan_leher#'=> $this->getValueRadioButton('normal',$res['leher'],$res['leher_lainnya']),
                '#pemeriksaan_mulut#'=> $this->getValueRadioButton('normal',$res['mulut'],$res['mulut_lainnya']),
                '#pemeriksaan_thoraks#'=> $this->getValueRadioButton('normal',$res['toraks'],$res['thoraks_lainnya']),
                '#pemeriksaan_paruparu_pergerakan#' => $this->getValueRadioButton('asimetris',$res['pergerakan'],null),
                '#pemeriksaan_paruparu_perkusi#' => $this->getValueRadioButton('normal',$res['perkusi'],$res['perkusi_lainnya']),
                '#pemeriksaan_paruparu_pernapasan#' => $this->getValueRadioButton('normal',$res['pernapasan'],$res['pernapasan_lainnya']),
                '#pemeriksaan_paruparu_rochi#' => $this->getValueRadioButton('ada',$res['rochi'],null),
                '#pemeriksaan_paruparu_wheezing#' => $this->getValueRadioButton('ada',$res['wheezing'],null),
                '#pemeriksaan_jantung_irama#' => $this->getValueRadioButton('reguler',$res['irama'],null),
                '#pemeriksaan_jantung_bunyijantung#' => $this->getValueRadioButton('normal',$res['bunyi_jantung'],$res['bunyi_jantung_lainnya']),
                '#pemeriksaan_abdomen_kelainan#' => $this->getValueRadioButton('normal',$res['kelainan'],$res['kelaianan_lainnya']),
                '#pemeriksaan_abdomen_benjolan#' => $this->getValueRadioButton('ya',$res['benjolan'],$res['benjolan_lainnya']),
                '#pemeriksaan_abdomen_tekan#' => $this->getValueRadioButton('normal',$res['nyeri_tekan'],$res['nyeri_tekan_lainnya']),
                '#pemeriksaan_abdomen_hernia#' => $this->getValueRadioButton('normal',$res['hernia'],$res['hernia_lainnya']),
                '#pemeriksaan_abdomen_bisingusus#' => $this->getValueRadioButton('normal',$res['bising_usus'],$res['bising_usus_lainnya']),
                '#pemeriksaan_abdomen_distensi#' => $this->getValueRadioButton('normal',$res['distensi'],$res['distensi_lainnya']),
                '#pemeriksaan_tulangbelakang#' => $this->getValueRadioButton('normal',$res['tulang_belakang'],$res['tulang_belakang_lainnya']),
                '#pemeriksaan_sistem_saraf#' => $this->getValueRadioButton('normal',$res['sistem_saraf'],$res['sistem_saraf_lainnya']),
                '#pemeriksaan_genetalia#' => $this->getValueRadioButton('normal',$res['genetalia'],$res['genetalia_lainnya']),
                '#pemeriksaan_edema#' => $this->getValueRadioButton('ya',$res['edema'],$res['edema_lainnya']),
                '#pemeriksaan_crt#' => $this->getValueRadioButton('ya',$res['crt'],$res['crt_lainnya']),
                '#pemeriksaan_lain_lain#' => isset($res['pemeriksaan_fisik_lainnya']) ? $res['pemeriksaan_fisik_lainnya']  : '',
                '#ttd_dokter_pemeriksa#' => $signaturePath
            ];
            // Print output
            $print->Output();
            unlink($fileTempPath);
        }
    }

    

    /**
    * @controller actionCetakAsmedRdKramat
    * @attribute #no_rm# => Untuk Menampilkan nama Poliklinik
    * @attribute #tgl_pendaftaran# => Untuk Menampilkan No Pendaftaran
    * @attribute #no_pendaftaran# => Untuk Menampilkan No Rekam Medik
    * @attribute #nama_pasien# => Untuk Menampilkan nama pasien
    * @attribute #jenis_kelamin# => Untuk Menampilkan nama pasien
    * @attribute #tanggal_lahir# => Untuk Menampilkan Jenis Kelamin
    * @attribute #umur# => Untuk Menampilkan cara Bayar
    * @attribute #kelaspelayanan_nama# => Untuk Menampilkan Penjamin
    * @attribute #cara_bayar# => Untuk Menampilkan Dokter pemeriksa
    * @attribute #penjamin# => Untuk Menampilkan Nama Perawat
    * @attribute #poliklinik# => Untuk Menampilkan Tanggal periksa
    * @attribute #dokter_pemeriksa# => Untuk Menampilkan Keadaan Umum
    * @attribute #jeniskasuspenyakit_nama# => Untuk Menampilkan Tekanan Darah
    * @attribute #status_periksa# => Untuk Menampilkan Klasifikasi Tekanan Darah
    * @attribute #tgl_pasiendatang# => Untuk Menampilkan Mean Arteri Preassure
    * @attribute #triage# => Untuk Menampilkan Detak Nadi
    * @attribute #tgl_asesmen# => Untuk Menampilkan Denyut Jantung
    * @attribute #jenis_kelamin# => Untuk Menampilkan Pernafasan
    * @attribute #kasus_polisi# => Untuk Menampilkan Suhu Tubuh
    * @attribute #kasus_kecelakaan# => Untuk Menampilkan Tinggi Badan
    * @attribute #cara_datang# => Untuk Menampilkan Berat Badan
    * @attribute #gcs_eye# => Untuk Menampilkan massa index tubuh
    * @attribute #gcs_verbal# => Untuk Menampilkan Kelainan pada bagian tubuh
    * @attribute #gcs_motorik# => Untuk Menampilkan Inspeksi
    * @attribute #hasil_metode_gcs# => Untuk Menampilkan Palpasi
    * @attribute #tekanan_darah# => Untuk Menampilkan Perkusi
    * @attribute #frekuensi_nadi# => Untuk Menampilkan Auskultasi
    * @attribute #pernapasan# => Untuk Menampilkan GCS Eye
    * @attribute #suhu# => Untuk Menampilkan metode GCS Eye
    * @attribute #saturasi_o2# => Untuk Menampilkan nilai GCS Eye
    * @attribute #skala_nyeri# => Untuk Menampilkan GCS Verbal
    * @attribute #objective# => Untuk Menampilkan metode GCS Verbal
    * @attribute #gambar_anatomi# => Untuk Menampilkan nilai GCS Verbal
    * @attribute #list_tabel_anatomi# => Untuk Menampilkan GCS Motorik
    * @attribute #diagnosa_primary# => Untuk Menampilkan nilai GCS Motorik
    * @attribute #diagnosa_secondary# => Untuk Menampilkan is kapitis
    * @attribute #terapi_igd# => Untuk Menampilkan gsc nama
    * @attribute #tindak_lanjut# => Untuk Menampilkan Hasil Metode GCS
    * @attribute #KU# => Untuk Menampilkan List PERNAPASAN GERAKAN DADA
    * @attribute #tekanan_darah_keluar# => Untuk Menampilkan List JALAN NAFAS DAN PERNAFASAN
    * @attribute #nadi_keluarr# => Untuk Menampilkan List SIRKULASI
    * @attribute #pernapasan_keluar# => Untuk Menampilkan Gambar Anatomi Tubuh
    * @attribute #saturasi_o2_keluar# => Untuk Menampilkan List bagian anatomi tubuh
    * @attribute #suhu_keluar# => Untuk Menampilkan Tanggal Pemeriksaan
    * @attribute #tgl_cetak# => Untuk Menampilkan Tanggal saat ini dicetak
    * @attribute #dokter_pemeriksa# => Untuk Menampilkan imt kategori / bmi_definisi
    **/
    public function actionCetakAsmedRdKramat($id)
    {
        // Get pemeriksaan fisik
        $id = DocoHelpers::decrypt($id);
        $result = InfoPasienRdV::find()->where(['pendaftaran_id' => $id])->asArray()->one();
        $res = AsesmenMedisIGD::find()->where(['pendaftaran_id' => $id])->asArray()->one();
        $image = Yii::$app->urlManagerFrontend->createUrl('')."media/img/img-pemeriksaan/bagian_tubuh_medis.jpg";
        if($res['diagnosa_id'] != null){
            $diagnosa_asesmen = AsesmenMedisIGD::find()->select([
                'diagnosa_m.diagnosa_nama',

            ])
            ->join('JOIN', 'diagnosa_m', 'diagnosa_m.diagnosa_id=asesmenmedisrd_t.diagnosa_id')
            ->where(['asesmenmedisrd_id' => $res['asesmenmedisrd_id']])
            ->asArray()->one();
        }
        $periksa_tubuh = PeriksaTubuh::find()->select([
            'periksatubuh_t.periksatubuh_id',
            'periksatubuh_t.created_date',
            'periksatubuh_t.catatan_tubuh',
            'periksatubuh_t.koordinat_x',
            'periksatubuh_t.koordinat_y',
            'bagiantubuh_m.namabagtubuh',
            'bagiantubuhdetail_m.nama_bagiantubuh',
        ])->join('JOIN', 'bagiantubuh_m', 'bagiantubuh_m.bagiantubuh_id=periksatubuh_t.bagiantubuh_id')
        ->join('JOIN', 'bagiantubuhdetail_m', 'bagiantubuhdetail_m.bagiantubuhdetail_id=periksatubuh_t.bagiantubuhdetail_id')
        ->where(['asesmenmedisrd_id' => $res['asesmenmedisrd_id']])
        ->orderBy('periksatubuh_t.periksatubuh_id ASC')->asArray()->all();
        $space = ' ';
        $gcs = [ $res['gcseye_id'],$res['gcsverbal_id'],$res['gcsmotorik_id']];
        $arrGcs = [];
        foreach ($gcs as $key => $value) {
             $sql = (new Query())->select([
            'metodegcs_nama',
            'metodegcs_nilai'
            ])
            ->from('metodegcs_m')->where([
                'metodegcs_id' => $value,
            ])->one();
            $arrGcs[$key] = $sql;
        }


        // Check model
        if (!empty($result) && !empty($res)) {
            $print = new DocoPrint();
            $beratBadan = (isset($res['berat_badan']) && $res['berat_badan'] != '')? $res['berat_badan']:0;
            $tinggiBadan = (isset($res['tinggi_badan']) && $res['tinggi_badan'] != '')? $res['tinggi_badan']:0;
            if(($tinggiBadan == 0) || ($beratBadan == 0)){
                $bmi = 0;
            }else{
                // Menghitung BMI
                $bmi = $beratBadan / (($tinggiBadan/100) * ($tinggiBadan/100));
                $bmi = number_format($bmi,2,",",".");
                // Menghitung BB Ideal
                $bb_ideal = ($tinggiBadan - 100) - (0.1 * ($tinggiBadan - 100));
                $bb_ideal = number_format($bb_ideal,2,",",".");
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $image);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // good edit, thanks!
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1); // also, this seems wise considering output is image.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // bypass SSL
            $data = curl_exec($ch);
            curl_close($ch);
            $imagecreate  = imagecreatefromstring($data);
            $white  = imagecolorallocate($imagecreate, 255, 255, 255);
            $red = imagecolorallocate($imagecreate, 0xFF, 0x00, 0x00);
            $orange = imagecolorallocate($imagecreate, 255, 112, 67);
            $fontImage = "fonts/arialbd.ttf";


            // Generate tabel pemeriksaan anatomi
            // Variable
            if(!empty($periksa_tubuh)){
              $no = 1;
              $htmlTable = '<table border="1" cellpadding="0" cellspacing="0" style="width:100%;border-collapse: collapse;">';
              $htmlTable .= '<tbody>';
              $htmlTable .= '<tr>';
              $htmlTable .= '<th width="5%">No</th>';
              $htmlTable .= '<th width="15%">Tanggal periksa</th>';
              $htmlTable .= '<th width="15%">Bagian Tubuh</th>';
              $htmlTable .= '<th width="15%">Bagian Tubuh Detail</th>';
              $htmlTable .= '<th width="50%">Catatan</th>';
              $htmlTable .= '</tr>';

              //Loop
                foreach ($periksa_tubuh as $value) {
                  // Generate table
                  $htmlTable .= '<tr><td style="padding:4px;">'.$no.'</td><td style="padding:4px;">'.date('d/m/Y H:i:s', strtotime($value['created_date'])).'</td><td style="padding:4px;">'.$value['namabagtubuh'].'</td><td style="padding:4px;">'.$value['nama_bagiantubuh'].'</td><td style="padding:4px;">'.$value['catatan_tubuh'].'</td></tr>';
                  if(isset($value['koordinat_x']) && isset($value['koordinat_y'])) {
                    $notext = $no."";
                    imagefilledarc($imagecreate, $value['koordinat_x']+3, $value['koordinat_y']+12, 25, 25, 0, 360, $orange, IMG_ARC_PIE);
                    imagefttext($imagecreate, 15, 0, $value['koordinat_x']+2-(3.5*strlen($notext)), $value['koordinat_y']+18, $white, $fontImage,$notext );
                  }
                  $no++;
                }


              // Close tag
              $htmlTable .= '</tbody>';
              $htmlTable .= '</table>';

            }else{
              $htmlTable = '<div style="width:100%; border: 1px solid; padding: 20px;text-align:center;">Tidak terdapat informasi status lokalis</div>';
            }
            $fileTempPath = Yii::getAlias("@webroot")."/assets/tmp_bagian_tubuh_medis.png";
            imagepng($imagecreate, $fileTempPath);
            $htmlImage = '
                    <div style="display:table-cell; vertical-align:middle; text-align:center">
                    <img src="'.$fileTempPath.'" width="500" height="520">
                    </div>';

            // checkbox anamnesis
            $allo_text = !empty($res['asesmen_allo_anamnesa'])?$res['asesmen_allo_anamnesa']:'';
            $asesmen_auto = $res['asesmen_auto'] == 1 ? '&#9642; Auto Anamnesa' : '';
            $asesmen_allo = $res['asesmen_allo'] == 1 ? '&#9642; Allo Anamnesa, <br>'.$allo_text : '';
            $anamnesis    = $asesmen_auto.'<br>'.$asesmen_allo;
            $dewasa = $res['skala_nyeri'];
            $anak = $res['skala_nyeri_anak'];

            // checkbox skrining nyeri
            if($res['pilih_skala'] == 'dewasa'){
                $skala_nyeri = isset($res['skala_nyeri']) ? $res['skala_nyeri'] .' - '.$this->getSkalaNyeriText($res['pilih_skala'], $res['skala_nyeri']) : '';
                $skala_nyeri_title = 'Skala Nyeri Dewasa';
            }
            else if($res['pilih_skala'] == 'anak'){
                $skala_nyeri = isset($res['skala_nyeri_anak']) ? $res['skala_nyeri_anak'] .' - '.$this->getSkalaNyeriText($res['pilih_skala'], $res['skala_nyeri_anak']) : '';
                $skala_nyeri_title = 'Skala Nyeri Anak';

            }else{
                $skala_nyeri = ' - ';
                $skala_nyeri_title = 'Skala Nyeri';
            }


            // Asesmen Medis
            $kasus_polisi = isset($res['kasus_polisi']) ? ucfirst($res['kasus_polisi']) : ' - ';
            $kasus_kecelakaan = isset($res['kasus_kecelakaan']) ? ucfirst($res['kasus_kecelakaan']) : ' - ';


            // cara datang
            $diantar_text =  !empty($res['cara_datang_diantar'])?$res['cara_datang_diantar']:'';
            $cara_datang = isset($res['cara_datang']) ? ($res['cara_datang'] == '0' ? 'Sendiri' : 'Diantar Oleh: '.$diantar_text) : ' - ';

            $triage = isset($res['triage']) ? $this->triage_text_array[$res['triage']] : ' - ';
            $additional_data = isset($res['additional_data']) ? json_decode($res['additional_data'], true) : [];

            $objective = isset($additional_data['objective']) ? $additional_data['objective'] : '';
            $diagnosa_primary = isset($additional_data['diagnosa_primary']) ? json_decode($additional_data['diagnosa_primary'], true) : [];
            $diagnosa_secondary = isset($additional_data['diagnosa_secondary']) ? json_decode($additional_data['diagnosa_secondary'], true) : [];

            // Assign attributes
            $print->attributes = [
                // Asesmen Medis
                '#tgl_pasiendatang#' =>  isset($res['tgl_pasien_datang']) ? date('d/m/Y H:i:s', strtotime($res['tgl_pasien_datang'])) : ' - ',
                '#triage#' => isset($triage) ? $triage : ' - ',
                '#anemnesis#' => isset($anamnesis) ? $anamnesis : ' - ',
                '#keluhan_utama#' =>  isset($res['keluhan_utama']) ? $res['keluhan_utama'] : ' - ',
                '#riwayat_penyakit_sekarang#' => isset($res['riwayat_penyakit_sekarang']) ? $res['riwayat_penyakit_sekarang'] : ' - ',
                '#tgl_asesmen#' => isset($res['tgl_asesmen']) ? date('d/m/Y H:i:s', strtotime($res['tgl_asesmen'])) : ' - ',
                '#dikirim_oleh#' => isset($res['dikirim_oleh']) ? $res['dikirim_oleh'] : ' - ',
                '#kasus_polisi#' => isset($kasus_polisi) ? $kasus_polisi : ' - ',
                '#kasus_kecelakaan#' => isset($kasus_kecelakaan) ? $kasus_kecelakaan : ' - ',
                '#cara_datang#' => isset($cara_datang) ? $cara_datang : ' - ',
                '#riwayat_penyakit_dahulu#' => isset($res['riwayat_terapi_sebelumnya']) ? $res['riwayat_terapi_sebelumnya'] : ' - ',
                '#riwayat_terapi_sebelumnya#' => isset($res['riwayat_terapi_sebelumnya']) ? $res['riwayat_terapi_sebelumnya'] : ' - ',
                '#alergi#' => isset($res['alergi']) ? $res['alergi'] : null,

                '#tekanan_darah#' => isset($res['tekanandarah']) ? $res['tekanandarah'] : ' - ',
                '#frekuensi_nadi#' => isset($res['nadi']) ? $res['nadi'] : ' - ',
                '#pernapasan#' => isset($res['pernapasan']) ? $res['pernapasan'] : " - ",
                '#suhu#' => isset($res['suhu']) ? $res['suhu'] : ' - ',
                '#saturasi_o2#' => isset($res['saturasi_o2']) ? $res['saturasi_o2'] : ' - ',
                '#skrining_nyeri#' => isset($skrining_nyeri) ? $skrining_nyeri : ' - ',
                '#skala_nyeri#' => $skala_nyeri,
                '#skala_nyeri_title#' => $skala_nyeri_title,

                '#gcs_eye#' => isset($res['gcseye_id']) ? $arrGcs[0]['metodegcs_nama'].' - '.$arrGcs[0]['metodegcs_nilai']:' - ',
                '#gcs_verbal#' => isset($res['gcsverbal_id']) ? $arrGcs[1]['metodegcs_nama'].' - '.$arrGcs[1]['metodegcs_nilai']:' - ',
                '#gcs_motorik#' => isset($res['gcsmotorik_id']) ? $arrGcs[2]['metodegcs_nama'].' - '.$arrGcs[2]['metodegcs_nilai']:' - ',
                '#hasil_metode_gcs#' => isset($res['hasil_gcs']) ? $res['hasil_gcs'] : ' - ',

                // Objektif
                '#objective#' => $objective,

                // Diagnosa
                '#diagnosa_primary#' => !empty($diagnosa_primary) ? $diagnosa_primary['text'] : ' - ',
                '#diagnosa_secondary#' => $this->getDiagnosaMultipleAsText($diagnosa_secondary),

                // Terapi IGD
                '#terapi_igd#' => isset($res['terapi']) ? $res['terapi'] : '',

                // status lokalis
                '#gambar_anatomi#' => $htmlImage,
                '#list_tabel_anatomi#' => $htmlTable,

                // Pemeriksaan Penunjang
                '#laboratorium#' => isset($res['laboratorium']) ? $res['laboratorium'] : null,
                '#radiologi#' => isset($res['radiologi']) ? $res['radiologi'] : null,
                '#ekg#' => isset($res['ekg']) ? $res['ekg'] : null,
                '#lain_lain#' => isset($res['lain_lain']) ? $res['lain_lain'] : null,

                // Diagnosis dan Terapis Saat Meninggalkan IGD
                '#diagnosa#' => isset($diagnosa_asesmen) ? $diagnosa_asesmen : null,
                '#terapi#' => isset($res['terapi']) ? $res['terapi'] : null,

                // Tindak Lanjut
                '#tindak_lanjut#' => isset($res['tindak_lanjut']) ? ucfirst(str_replace('_', ' ', $res['tindak_lanjut'])) : '',

                // Kondisi Saat Meninggalkan IGD
                '#KU#' => isset($res['ku_keluar']) ? $res['ku_keluar'] : null,
                '#tekanan_darah_keluar#' => isset($res['tekanandarah_keluar']) ? $res['tekanandarah_keluar']. 'mmHg' : null,
                '#nadi_keluarr#' => isset($res['nadi_keluar']) ? $res['nadi_keluar']. 'x/menit' : null,
                '#pernapasan_keluar#' => isset($res['pernapasan_keluar']) ? $res['pernapasan_keluar']. 'x/menit' : null,
                '#saturasi_o2_keluar#' => isset($res['saturasi_o2_keluar']) ? $res['saturasi_o2_keluar']. '%' : null,
                '#suhu_keluar#' => isset($res['suhu_keluar']) ? $res['suhu_keluar']. '°C' : null,

                '#status_periksa#' => isset($result['status_periksa']) ? $result['status_periksa'] : null,
                '#jeniskasuspenyakit_nama#' => isset($result['jeniskasuspenyakit_nama']) ? $result['jeniskasuspenyakit_nama'] : null,
                '#umur#' => isset($result['umur']) ? $result['umur'] : null,
                '#kelaspelayanan_nama#' => isset($result['kelaspelayanan_nama']) ? $result['kelaspelayanan_nama'] : null,
                '#tgl_pendaftaran#' => isset($result['tgl_pendaftaran']) ? date('d/m/Y H:i:s', strtotime($result['tgl_pendaftaran'])) : null,
                '#poliklinik#' => isset($result['ruangan_nama']) ? $result['ruangan_nama'] : null,
                '#no_pendaftaran#' => isset($result['no_pendaftaran']) ? $result['no_pendaftaran'] : null ,
                '#no_rm#' => isset($result['no_rekam_medik']) ? $result['no_rekam_medik'] : null,
                '#nama_pasien#' => isset($result['nama_pasien']) ? $result['nama_pasien'] : null,
                '#jenis_kelamin#' => isset($result['jenis_kelamin']) ? $result['jenis_kelamin'] : null,
                '#tanggal_lahir#' => isset($result['tanggal_lahir']) ? date('d/m/Y  ', strtotime($result['tanggal_lahir'])) : null,
                '#cara_bayar#' => isset($result['carabayar_nama']) ? $result['carabayar_nama'] : null,
                '#penjamin#' => isset($result['penjamin_nama']) ? $result['penjamin_nama'] : null,
                '#dokter_pemeriksa#' => isset($result['dokter_jaga']) ? $result['dokter_jaga'] : null,
                '#tanggal_periksa#' => isset($result['tglperiksafisik']) ? date('d F Y', strtotime($result['tglperiksafisik'])) : null,
                '#tgl_cetak#' => date('d F Y'),

            ];
            // Print output
            $print->Output();
            unlink($fileTempPath);
        }
    }

    private function getSkalaNyeriText($jenis, $skala)
    {
        if($jenis){
            // Anak
            if ($skala == 1) {
                return 'Tidak Nyeri';
            }elseif($skala >= 2 && $skala < 4){
                return 'Sedikit Nyeri';
            }elseif($skala >= 4 && $skala < 6){
                return 'Agak Mengganggu';
            }elseif($skala >= 6 && $skala < 8){
                return 'Nyeri Mengganggu';
            }elseif($skala >= 8 && $skala < 10){
                return 'Sangat Mengganggu';
            }else{
                return 'Nyeri Berat';
            }
        }

        // Dewasa
        if ($skala == 1) {
            return 'Tidak Nyeri';
        }elseif($skala >= 2 && $skala < 5){
            return 'Nyeri Ringan';
        }elseif($skala >= 5 && $skala < 8){
            return 'Nyeri Sedang';
        }else{
            return 'Nyeri Berat';
        }
    }

    private function getDiagnosaMultipleAsText($diagnosa)
    {
        $html = '';
        if(!empty($diagnosa)){
          foreach ($diagnosa as $key => $value) {
              $html .= $value['text'].'<br>';
          }
        }else{
          $html = ' - ';
        }
        return $html;
    }

    private function getValueRadioButton($string, $val, $alasan = null)
    {
        
        $result = $yes = $no = '';
        if($string == 'normal'){
            $yes = 'Normal';
            $no = 'Tidak Normal';
        }else if($string == 'reguler') {
            $yes = 'Reguler';
            $no = 'Inreguler';
        }else if($string == 'ada') {
            $yes = 'Ada';
            $no = 'Tidak Ada';
        }else if($string == 'asimetris') {
            $yes = 'Normal';
            $no = 'Asimetris';
        } else {
            $yes = 'Tidak';
            $no = 'Ya';
        }

        $alasan = isset($alasan) ? ', '.$alasan : $alasan;
        if(isset($val)){
            $result = $val == 0 ? $no.''.$alasan  : $yes;
        }


        return $result;
    }


}
