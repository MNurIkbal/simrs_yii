<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Sunarko
 * @Date:   2018-06-05 16:21:21
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 11:48:40
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;

use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PasienBatalPeriksa;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\MasukKamar;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\PasienDirujukKeluar;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\WarnaTempatTidur;
use app\modules\v1\models\KetTempatTidur;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\InstruksiTindakan;
use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\InfoKunjunganRi;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\InfoObatAlkesView;
use app\modules\v1\models\Barang;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\ObatAlkesFn;
use app\modules\v1\models\ValidateInjention;
use app\modules\v1\models\RincianPasienView;
use app\modules\v1\models\PemberianPiutang;
use app\modules\v1\models\PendaftaranPenjamin;
use app\modules\v1\models\Pembayaran;
use app\modules\v1\models\TindakanPelayananT;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\RincianPasienDetail2View;
use app\modules\v1\models\RincianPasienDetailView;
use app\modules\v1\models\RincianKelompokTindakanView;
use app\modules\v1\payload\ParamModel;
use app\modules\v1\models\BayarUangMuka;
use app\modules\v1\businessLogic\TindakanAkomodasi;
use app\modules\v1\models\KelahiranBayiView;
use app\modules\v1\models\CpptView;
use Doco\models\WorklistPasien;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\models\Pegawai;
use Doco\models\LoginForm;
use app\modules\v1\models\RujukanPulang;
use Doco\Services\RmService;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadForm;
use Doco\Services\InternalService;
use Doco\models\bpjs\Bpjs as BpjsRanap;
use app\modules\v1\actions\ProsesPulangPasien\ProgramFisioRanapAction;
use Doco\models\bpjs\BpjsAplicare;
use Doco\Services\UpdateEklaimService;

class InfPasienRanapController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienRanap';
    protected $_title = 'Informasi Pasien';
    public $messageBroker = [
        // 'proses-pulang-pasien' => [
        //     'services' => [
        //         Dipindahkan menjadi endpoint fisio per sprint 27 Mei 2022
        //         Dicomment bila diperlukan tinggal diaktifkan.
        //         'Fisioterapi' => [
        //             'UpdateStatusProgramClose' => [
        //                 'payload' => [
        //                     'pasien_id' => 'PasienPulangForm.pasien_id',
        //                     'pendaftaran_id' => 'InfoPasienRanapForm.pendaftaran_id',
        //                     'carakeluar_id' => 'PasienPulangForm.carakeluar_id'
        //                 ]
        //             ]
        //         ]
        //     ]
        // ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        //$verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        //unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;

        $model = new InfoPasienRanap;
        $query = $model::find();

        $query->joinWith([
            'permintaanKonsul' => function ($query) {
                $query->onCondition("(
                    permintaankonsul_t.jenis_konsul::varchar = '" . DocoConstants::JNS_KNSL_1X . "' AND 
                    permintaankonsul_t.status_konsul = " . DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU . " AND
                    permintaankonsul_t.jawaban_konsul is NULL
                ) OR (
                    permintaankonsul_t.jenis_konsul::varchar = '" . DocoConstants::JNS_KNSL_RB . "' AND
                    permintaankonsul_t.status_konsul = " . DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU . "
                )");
            }
        ]);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
         **/
        $ruangan_id = $_GET['idruangan'];
        //default tgl admisi hari ini
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $stop_akomodasi = null;
        if (isset($_GET['advanced-filter'])) {
            if ( isset($_GET['advanced-filter']['tgl_admisi']) ) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_admisi']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
            }
            if ( isset($_GET['advanced-filter']['is_stopakomodasi']) ) {
                $stop_akomodasi = $_GET['advanced-filter']['is_stopakomodasi'];
                unset($_GET['advanced-filter']['is_stopakomodasi']);
            }
        }
        // $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        // $dokter = DokterView::find()->andWhere(['pegawai_id'=>$pegawai_id])->one();

        if ($ruangan_id) {
            $query->andWhere(['infopasienri_v.ruangan_id' => $ruangan_id]);

            // kalau loginan itu dokter, yg dimunculin hanya DPJP (dokter_admisi_id) = id dokter loginan
            // if ($dokter) {
            //     // kalau dia punya jadwal dokter, pasien dimunculin semua, kalau tidak punya baru difilter sesuai dpjp nya loginan tersebut
            //     /** Di kommen karena kebutuh menampilkan semua pasien untuk dokter jaga */
            //     // if (!$this->getHasJadwalDokter($ruangan_id, $pegawai_id)) {
            //     //     $query->andWhere(['infopasienri_v.dokter_admisi_id'=>$pegawai_id]);
            //     // }
            // }
        } else {
            // kalau loginan itu dokter, yg dimunculin DPJP atau konsul = id dokter loginan
            // if ($dokter) {
            //     /** Di kommen karena kebutuh menampilkan semua pasien untuk dokter jaga */
            //     // $query->andWhere('(
            //     //     infopasienri_v.dokter_admisi_id = ' . $pegawai_id . ' OR
            //     //     permintaankonsul_t.dokter_id = ' . $pegawai_id . ')'
            //     // );
            // }
        }
        switch ($stop_akomodasi) {
            case 1:
                $query->andWhere(['in', 'infopasienri_v.jenis_konsul', [DocoConstants::JNS_KNSL_1X, DocoConstants::JNS_KNSL_RB]]);
                $query->andWhere(['infopasienri_v.status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU]);
                break;
            case 2:
                $query->andWhere(['or', ['infopasienri_v.jenis_konsul' => null], ['infopasienri_v.jenis_konsul' => DocoConstants::JNS_KNSL_AR]]);
                $query->orWhere(['and', ['IN', 'infopasienri_v.jenis_konsul', [DocoConstants::JNS_KNSL_1X, DocoConstants::JNS_KNSL_RB]], ['not', ['infopasienri_v.status_konsul' => DocoConstants::STATUS_PERMINTAAN_KONSUL_SETUJU]]]);
                break;
            case 3:
                $query->andWhere(['or', ['infopasienri_v.is_pasientitipan_pk' => true], ['infopasienri_v.is_pasientitipan' => true]]);
                $query->andWhere(['infopasienri_v.is_stoppasientitipan' => false]);
                break;
            case 4:
                $query->andWhere(['is_stopakomodasi' => true]);
                break;
            default:
        }

        $query->andWhere(['not in', 'infopasienri_v.status_ranap', [DocoConstants::STATUS_RANAP_BATAL_RAWAT]]);
        $query->andWhere([
            'pasienpulang_id' => NULL,
            // 'is_stopakomodasi' => false
        ]);

        if ($start && $end) {
            $query->andWhere(['between', 'infopasienri_v.tgl_admisi', $start, $end]);
        }
        /**
         * End Special Condition date range
         **/

        // return $query->createCommand()->getRawSql();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        // return $query->createCommand()->getRawSql();

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }



    /*
    * author: Rizqi Febian
    * date: 24-04-2018
    * get data dokter by jam skrg
    * params needed: ruangan_id, type: 1 || 0 || 2, 1:Rajal 2:Penunjang 0:IGD
    */
    public function getHasJadwalDokter($ruangan_id, $pegawai_id)
    {
        $where = "";
        $now = date('H:i');
        $where .= " where j.ruangan_id = {$ruangan_id} ";
        $where .= " and d.pegawai_id = {$pegawai_id} ";
        $where .= " and j.jam_mulai <= time '{$now}' and j.jam_tutup >=time '{$now}' and d.jadwaldokter_mulai <= time '{$now}' and d.jadwaldokter_tutup >= time '{$now}'";
        $where .= " and d.is_active = true and d.is_deleted = false";
        $query = "select p.pegawai_id,p.nama_pegawai from jadwalbukapoli_m j RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id " . $where;
        $data = Yii::$app->db->createCommand($query)->queryAll();
        return $data;
    }

    public function actionDataPendaftaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $idRuangan = $post['idR'];
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if ($post['date']) {
            $newData = explode(' - ', $post['date']);
            if (count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "select pendaftaran_id, no_pendaftaran from infopasienri_v where ruangan_id={$idRuangan} and no_pendaftaran LIKE '%{$term}%'
            group by pendaftaran_id, no_pendaftaran
            order by no_pendaftaran asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataRekamMedik()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $idRuangan = $post['idR'];
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if ($post['date']) {
            $newData = explode(' - ', $post['date']);
            if (count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "select no_rekam_medik from infopasienri_v where ruangan_id={$idRuangan} and no_rekam_medik LIKE '%{$term}%'
            group by no_rekam_medik
            order by no_rekam_medik asc limit 50
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataNamaPasien()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataNamaDokter()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('dokter_admisi')
            ->groupBy(["dokter_admisi"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataKasusPenyakit()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('jeniskasuspenyakit_nama')
            ->groupBy(["jeniskasuspenyakit_nama"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataHakKelas()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('hak_kelas')
            ->groupBy(["hak_kelas"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataKelasSaatIni()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('kelas_pelayanan')
            ->groupBy(["kelas_pelayanan"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDataNamaRuangan()
    {
        $model = new InfoPasienRanap;
        $query = $model::find(true)->select('ruangan_nama')
            ->groupBy(["ruangan_nama"]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @controller actionExportPdf
     * @attribute #table_pasien# => Untuk Menampilkan Tabel pasien ranap
     * @attribute #periode# => untuk menampilkan periode data
     * @attribute #tanggal# => untuk menampilkan tanggal sekarang
     * @attribute #tanggal_cetak# => untuk menampilkan tanggal cetak
     * @attribute #jenis# => untuk menampilkan title
     * @attribute #cetak_oleh# => untuk menampilkan pencetak
     * @attribute #kepala# => untuk menampilkan nama kepala ruangan
     * @attribute #kepalanip# => untuk menampilkan nip kepala ruangan
     **/

    public function actionExportPdf($jenis, $ruangan)
    {
        $data = [];
        $model = new InfoPasienRanap;
        $jenis_title = 'Rawat Inap';
        $ruangan_id = $_GET['idruangan'];

        $query = $model::find(
            'tgl_admisi',
            'no_rekam_medik',
            'no_pendaftaran',
            'nama_pasien',
            'jenis_kelamin',
            'dokter_admisi',
            'carabayar_nama',
            'penjamin_nama',
            'jeniskasuspenyakit_nama',
            'kelas_pelayanan',
            'hak_kelas',
            'ruangan_nama',
            'kamarruangan_nokamar',
            'no_tempattidur',
            'tgl_pindahkamar',
            'rencana_pulang',
            'pasienpulang_id',
            'is_pasientitipan',
            'is_stoppasientitipan',
            'kelas_ditagihkan_id',
            'kelas_ditagihkan_nama',
            'is_pasientitipan_pk'
        )->where(['ruangan_id' => $ruangan]);

        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $dokter = DokterView::find()->andWhere(['pegawai_id' => $pegawai_id])->one();
        if ($ruangan_id) {
            $query->andWhere(['infopasienri_v.ruangan_id' => $ruangan_id]);

            // kalau loginan itu dokter, yg dimunculin hanya DPJP (dokter_admisi_id) = id dokter loginan
            if ($dokter) {
                // kalau dia punya jadwal dokter, pasien dimunculin semua, kalau tidak punya baru difilter sesuai dpjp nya loginan tersebut
                if (!$this->getHasJadwalDokter($ruangan_id, $pegawai_id)) {
                    $query->andWhere(['infopasienri_v.dokter_admisi_id' => $pegawai_id]);
                }
            }
        } else {
            // kalau loginan itu dokter, yg dimunculin DPJP atau konsul = id dokter loginan
            if ($dokter) {
                $query->andWhere(
                    '(
                    infopasienri_v.dokter_admisi_id = ' . $pegawai_id . ' OR
                    permintaankonsul_t.dokter_id = ' . $pegawai_id . ')'
                );
            }
        }

        $query->andWhere(['not in', 'status_ranap', ['453']]);
        $query->andWhere(['pasienpulang_id' => NULL]);
        // $query->andWhere(['is', 'pasienpulang_id', NULL]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // modify advanced filters
        $request = Yii::$app->request;
        $tgl_awal = '';
        $tgl_akhir = '';
        $dataKepala = (PegawaiView::find()->where(['ruangan_id' => $ruangan, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one()) ? PegawaiView::find()->where(['ruangan_id' => $ruangan, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one() : '';
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_admisi_awal']) && isset($advancedFilters['tgl_admisi_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_admisi_awal'];
            $tgl_akhir = $advancedFilters['tgl_admisi_akhir'];
            $query->andWhere(['between', 'tgl_admisi', $tgl_awal, $tgl_akhir]);
        }
        $data = $query->asArray()->all();
        $print = new DocoPrint();

        $print->attributes = [
            '#table_pasien#' => $this->renderPartial('index', [
                'detail' => $data
            ]),
            '#periode#' => $tgl_awal . '-' . $tgl_akhir,
            '#tanggal#' => date('d F Y'),
            '#tanggal_cetak#' => date('d F Y H:i:s'),
            '#jenis#' => $jenis_title,
            '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
            '#kepala#' => ($dataKepala) ? $dataKepala['nama_pegawai'] : '-',
            '#kepalanip#' => ($dataKepala) ? $dataKepala['nomorindukpegawai'] : '-',
        ];

        $print->Output();
    }


    /**
     * @controller actionExportRincianTagihanPdf
     * @attribute #tanggal# => Tanggal Pendaftaran 
     * @attribute #no_pendaftaran# => Nomor Pendaftaran
     * @attribute #no_rm# => Nomor Rekam Medik 
     * @attribute #nama# => Nama pasien 
     * @attribute #jenis_kasus_penyakit# => Jenis Kasus Penyakit 
     * @attribute #ruangan# => Nama Ruangan 
     * @attribute #kamar# => Nama Kamar 
     * @attribute #tempat_tidur# => Nama Tempat Tidur 
     * @attribute #dokter# => Nama dokter 
     * @attribute #kelas_pelayanan# => Kelas Pelayanan 
     * @attribute #penjamin# => Penjamin 
     * @attribute #cara_bayar# => Cara Bayar 
     * @attribute #status_bayar# => Status Bayar 
     * @attribute #total_tagihan# => Menampilkan detail tindakan
     * @attribute #riwayat_pembayaran# => Menampilkan Tabel tagihan riwayat pasien 
     * @attribute #detail_tindakan# => Menampilkan detail tindakan
     * @attribute #total_uang_muka# => Menampilkan detail tindakan
     * @attribute #total_dibayar# => Menampilkan detail tindakan
     * @attribute #sisa_tagihan# => Menampilkan detail tindakan
     * @attribute #biaya_admin# => Menampilkan biaya Admin
     * @attribute #pembulatann# => Menampilkan biaya Pembulatan
     * @attribute #subsidi_asuranasi# => Menampilkan biaya subsidi asuransi
     * @attribute #detail_tindakan# => Tindakan Laboratorium
     * @attribute #total_akomodasi# => Menampilkan total akomodasi
     * 
     **/
    public function actionExportRincianTagihanPdf($pendaftaran_id)
    {
        $modelVal = new ValidateInjention;
        $modelVal->pendaftaran_id = $pendaftaran_id;

        if (!$modelVal->validate()) {
            return [
                'status' => 422,
                'data' => $modelVal->errors
            ];
        }

        $header = RincianPasienView::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $detail = RincianPasienDetailView::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->all();


        $ruangan = '';
        $totalTagihan = 0;
        $arrPemeriksaan = [];
        $kelompok_visite = $this->constans->actionGetId('visite');

        foreach ($detail as $value) {
            $is_obat = isset($value['is_obat']) ? $value['is_obat'] : null;
            $instalasi = isset($value['instalasi_id']) ? $value['instalasi_id'] : null;
            if (!empty($value['instalasi_pelayanan']) && $value['instalasi_pelayanan'] != "") {
                $ruangan = isset($value['instalasi_pelayanan']) ? $value['instalasi_pelayanan'] : null;
            }
            if (!isset($listData[$value['pendaftaran_id']])) {
                $listData[$value['pendaftaran_id']] = [
                    'obat' => [],
                    'tindakan' => [],
                    'pemeriksaan' => [],
                    'penunjang' => [],
                    'visite' => [],
                ];
            }

            if ($is_obat) {
                $listData[$value['pendaftaran_id']]['obat'][] = $value;
            } else {
                if (!empty($value['ruangan_pelayanan'])) {
                    $arrPemeriksaan[$value['ruangan_pelayanan_id']][$value['ruangan_pelayanan']][] = $value;
                }

                if (in_array($instalasi, DocoConstants::$exceptPenunjang)) {
                } else {
                    $listData[$value['pendaftaran_id']]['penunjang'][$instalasi]['data'][] = $value;
                    $listData[$value['pendaftaran_id']]['penunjang'][$instalasi]['title'] = $ruangan;
                }

                if ($value['kelompoktindakan_id'] == $kelompok_visite) {
                    $listData[$value['pendaftaran_id']]['visite'][$instalasi]['data'][] = $value;
                    $listData[$value['pendaftaran_id']]['visite'][$instalasi]['title'] = 'Visite Dokter';
                }
            }
            $totalTagihan += $value['jumlah_tarif'];
        }

        foreach ($arrPemeriksaan as $key => $value) {
            if (in_array($key, DocoConstants::$exceptPenunjang)) {
                foreach ($value as $kt => $vt) {
                    $pendaftaran_id = isset($vt[0]['pendaftaran_id']) ? $vt[0]['pendaftaran_id'] : null;
                    $instalasi_id = isset($vt[0]['instalasi_id']) ? $vt[0]['instalasi_id'] : null;

                    $listData[$pendaftaran_id]['tindakan'][$instalasi_id]['data'][] = $vt;
                    $listData[$pendaftaran_id]['tindakan'][$instalasi_id]['title'] = $kt;
                }
            } else {
                foreach ($value as $kp => $vp) {
                    $pendaftaran_id = isset($vp[0]['pendaftaran_id']) ? $vp[0]['pendaftaran_id'] : null;
                    $instalasi_pelayanan = isset($vp[0]['instalasi_pelayanan']) ? $vp[0]['instalasi_pelayanan'] : null;

                    if ($kp == 'Rawat Darurat') {
                        $kp = isset($vp[0]['ruangan_admisi']) ? $vp[0]['ruangan_admisi'] : '-';
                    }

                    $listData[$pendaftaran_id]['pemeriksaan'][$instalasi_pelayanan]['data'][] = $vp;
                    $listData[$pendaftaran_id]['pemeriksaan'][$instalasi_pelayanan]['title'] = $kp;
                }
            }
        }

        $tgl_pendaftaran = isset($header['tgl_pendaftaran']) ? date('d M Y H:i:s', strtotime($header['tgl_pendaftaran'])) : '-';
        $ruangan_pendaftaran_id = isset($header['ruangan_pendaftaran_id']) ? $header['ruangan_pendaftaran_id'] : '-';
        $ruangan_pendaftaran = ($ruangan_pendaftaran_id == DocoConstants::RUANGAN_PENDAFTARAN_IGD) ? $header['ruangan_admisi'] : $header['ruangan_pendaftaran'];

        $kamar = isset($header['kamar']) ? $header['kamar'] : '-';
        $tempat_tidur = isset($header['tempat_tidur']) ? $header['tempat_tidur'] : '-';
        $no_pendaftaran = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-';
        $no_rekam_medik = isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : null;
        $nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '-';
        $jeniskasuspenyakit_nama = isset($header['jeniskasuspenyakit_nama']) ? $header['jeniskasuspenyakit_nama'] : '-';
        $dok_pendaftaran = isset($header['dok_pendaftaran']) ? $header['dok_pendaftaran'] : '-';
        $carabayar_nama = isset($header['carabayar_admisi']) ? $header['carabayar_admisi'] : '-';
        $kelas_admisi = isset($header['kelas_admisi']) ? $header['kelas_admisi'] : '-';
        $penjamin_admisi = isset($header['penjamin_admisi']) ? $header['penjamin_admisi'] : '-';
        $status = isset($header['status_lunas']) ? $header['status_lunas'] : '-';

        //get header total tagihan
        $dataTotal = $this->getTotalHeaderPembayaran($pendaftaran_id, $header);
        $total_tagihan = isset($dataTotal['total_tagihan']) ? $dataTotal['total_tagihan'] : 0;
        $total_asuransi = isset($dataTotal['total_asuransi']) ? $dataTotal['total_asuransi'] : 0;
        $uang_masuk = isset($dataTotal['uang_masuk']) ? $dataTotal['uang_masuk'] : 0;
        $sisa_tagihan = isset($dataTotal['sisa_tagihan']) ? $dataTotal['sisa_tagihan'] : 0;
        $total_administrasi = isset($dataTotal['total_admin']) ? $dataTotal['total_admin'] : 0;
        $total_akomodasi = isset($dataTotal['total_akomodasi']) ? $dataTotal['total_akomodasi'] : 0;

        $listHeader = [
            'total_tagihan' => DocoHelpers::rupiahDisplay($total_tagihan + $total_administrasi),
            'total_asuransi' => DocoHelpers::rupiahDisplay($total_asuransi),
            'uang_masuk' => DocoHelpers::rupiahDisplay($uang_masuk),
            'sisa_tagihan' => DocoHelpers::rupiahDisplay($sisa_tagihan),
            'total_akomodasi' => DocoHelpers::rupiahDisplay($total_akomodasi)
        ];

        if (!empty($header)) {
            $countData = count($header);
            $print = new DocoPrint();
            $print->attributes = [
                '#tanggal#' => $tgl_pendaftaran,
                '#no_rm#' => $no_rekam_medik,
                '#no_pendaftaran#' => $no_pendaftaran,
                '#nama#' => $nama_pasien,
                '#jenis_kasus_penyakit#' => $jeniskasuspenyakit_nama,
                '#dokter#' => $dok_pendaftaran,
                '#ruangan#' => $ruangan_pendaftaran,
                '#kamar#' => $kamar,
                '#tempat_tidur#' => $tempat_tidur,
                '#kelas_pelayanan#' => $kelas_admisi,
                '#penjamin#' => $penjamin_admisi,
                '#cara_bayar#' => $carabayar_nama,
                '#status_bayar#' => $status,
                '#detail_tindakan#' => $this->renderPartial('riwayat_pemeriksaan', [
                    'listHeader' => $listHeader,
                    'kelompok_visite' => $kelompok_visite,
                    'detail' => !empty($listData[$pendaftaran_id]) ? $listData[$pendaftaran_id] : [],
                ]),
            ];
        }

        $print->Output();
    }

    /**
     * @controller actionExpRincianTagihanRanap
     * @attribute #tanggal# => Tanggal Pendaftaran 
     * @attribute #no_pendaftaran# => Nomor Pendaftaran
     * @attribute #no_rm# => Nomor Rekam Medik 
     * @attribute #nama# => Nama pasien 
     * @attribute #jenis_kasus_penyakit# => Jenis Kasus Penyakit 
     * @attribute #ruangan# => Nama Ruangan 
     * @attribute #kamar# => Nama Kamar 
     * @attribute #tempat_tidur# => Nama Tempat Tidur 
     * @attribute #dokter# => Nama dokter 
     * @attribute #kelas_pelayanan# => Kelas Pelayanan 
     * @attribute #penjamin# => Penjamin 
     * @attribute #cara_bayar# => Cara Bayar 
     * @attribute #status_bayar# => Status Bayar 
     * @attribute #total_tagihan# => Menampilkan detail tindakan
     * @attribute #riwayat_pembayaran# => Menampilkan Tabel tagihan riwayat pasien 
     * @attribute #detail_tindakan# => Menampilkan detail tindakan
     * @attribute #total_uang_muka# => Menampilkan detail tindakan
     * @attribute #total_dibayar# => Menampilkan detail tindakan
     * @attribute #sisa_tagihan# => Menampilkan detail tindakan
     * @attribute #biaya_admin# => Menampilkan biaya Admin
     * @attribute #pembulatann# => Menampilkan biaya Pembulatan
     * @attribute #subsidi_asuranasi# => Menampilkan biaya subsidi asuransi
     * @attribute #detail_tindakan# => Tindakan Laboratorium
     * @attribute #total_akomodasi# => Menampilkan total akomodasi
     * @attribute #masuk# => Menampilkan Jam Masuk
     * @attribute #keluar# => Menampilkan Jam Keluar
     * @attribute #kamar_# => Menampilkan Kamar
     * @attribute #kelas# => Menampilkan Kelas
     * @attribute #tt_dari# => Menampilkan TT Dari
     * @attribute #sampai# => Menampilkan Sampai
     * @attribute #hari# => Menampilkan Hari
     * @attribute #tarif# => Menampilkan Tarif
     * @attribute #biaya# => Menampilkan Biaya
     * @attribute #kelompok_tindakan# => Menampilkan Kelompok Tindakan
     * @attribute #tabel_akomodasi# => Menampilkan Kelompok Tindakan
     * @attribute #uang_masuk# => Menampilkan Uang Masuk
     * @attribute #total_administrasi# => Menampilkan Administrasi
     * 
     **/
    public function actionExpRincianTagihanRanap($pendaftaran_id)
    {

        $modelVal = new ValidateInjention;
        $modelVal->pendaftaran_id = $pendaftaran_id;

        if (!$modelVal->validate()) {
            return [
                'status' => 422,
                'data' => $modelVal->errors
            ];
        }

        $header = RincianPasienView::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $detailTindakan = RincianKelompokTindakanView::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->all();

        if (!empty($header['is_pasientitipan_pk'])) {
            if ($header['is_pasientitipan_pk'] == true && $header['is_stoppasientitipan'] == false) {
                $header['kelas_admisi'] = $header['kelas_ditagihkan_nama'];
            }
        } else if (empty($header['is_pasientitipan_pk'])) {
            if ($header['is_pasientitipan'] == true && $header['is_stoppasientitipan'] == false) {
                $header['kelas_admisi'] = $header['kelas_ditagihkan_nama'];
            }
        }

        $tgl_pendaftaran = isset($header['tgl_pendaftaran']) ? date('d M Y H:i:s', strtotime($header['tgl_pendaftaran'])) : '-';
        $ruangan_pendaftaran_id = isset($header['ruangan_pendaftaran_id']) ? $header['ruangan_pendaftaran_id'] : '-';
        $ruangan_id = (new DocoConstansId)->actionGetId(DocoConstants::RUANGAN_IGD);
        $ruangan_pendaftaran = ($ruangan_pendaftaran_id == $ruangan_id) ? $header['ruangan_admisi'] : $header['ruangan_pendaftaran'];

        $kamar = isset($header['kamar']) ? $header['kamar'] : '-';
        $tempat_tidur = isset($header['tempat_tidur']) ? $header['tempat_tidur'] : '-';
        $no_pendaftaran = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-';
        $no_rekam_medik = isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : null;
        $nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '-';
        $jeniskasuspenyakit_nama = isset($header['jeniskasuspenyakit_nama']) ? $header['jeniskasuspenyakit_nama'] : '-';
        // $dok_pendaftaran = isset($header['dok_pendaftaran']) ? $header['dok_pendaftaran'] : '-';
        $dok_pendaftaran = isset($header['dok_admisi']) ? $header['dok_admisi'] : '-';
        $carabayar_nama = isset($header['carabayar_admisi']) ? $header['carabayar_admisi'] : '-';
        $kelas_admisi = isset($header['kelas_admisi']) ? $header['kelas_admisi'] : '-';
        $penjamin_admisi = isset($header['penjamin_admisi']) ? $header['penjamin_admisi'] : '-';
        $status = isset($header['status_lunas']) ? $header['status_lunas'] : '-';
        $tgl_masuk = isset($header['tgl_masuk']) ? date('d M Y', strtotime($header['tgl_masuk'])) : '-';
        $jam_masuk = isset($header['jam_masuk']) ? $header['jam_masuk'] : '-';
        $tgl_keluar = isset($header['tgl_keluar']) ? date('d M Y', strtotime($header['tgl_keluar'])) : '-';
        $jam_keluar = isset($header['jam_keluar']) ? $header['jam_keluar'] : '-';

        //get header total tagihan
        $dataTotal = $this->getTotalHeaderPembayaran($pendaftaran_id, $header);
        $total_tagihan = isset($dataTotal['total_tagihan']) ? $dataTotal['total_tagihan'] : 0;
        $total_asuransi = isset($dataTotal['total_asuransi']) ? $dataTotal['total_asuransi'] : 0;
        $uang_masuk = isset($dataTotal['uang_masuk']) ? $dataTotal['uang_masuk'] : 0;
        $sisa_tagihan = isset($dataTotal['sisa_tagihan']) ? $dataTotal['sisa_tagihan'] : 0;
        $total_administrasi = $this->generateTotalAdministrasi($pendaftaran_id, $dataTotal['total_tagihan']);
        $total_akomodasi = isset($dataTotal['total_akomodasi']) ? $dataTotal['total_akomodasi'] : 0;

        $listHeader = [
            // 'total_tagihan' => DocoHelpers::rupiahDisplay($total_tagihan + $total_administrasi),
            'total_tagihan' => DocoHelpers::rupiahDisplay($total_tagihan),
            'total_administrasi' => DocoHelpers::rupiahDisplay($total_administrasi),
            'total_asuransi' => DocoHelpers::rupiahDisplay($total_asuransi),
            'uang_masuk' => DocoHelpers::rupiahDisplay($uang_masuk),
            'sisa_tagihan' => DocoHelpers::rupiahDisplay($sisa_tagihan + $total_administrasi),
            'total_akomodasi' => DocoHelpers::rupiahDisplay($total_akomodasi)
        ];

        if (!empty($header)) {
            $countData = count($header);
            $print = new DocoPrint();
            $print->attributes = [
                '#tanggal#' => $tgl_pendaftaran,
                '#no_rm#' => $no_rekam_medik,
                '#no_pendaftaran#' => $no_pendaftaran,
                '#nama#' => $nama_pasien,
                '#jenis_kasus_penyakit#' => $jeniskasuspenyakit_nama,
                '#dokter#' => $dok_pendaftaran,
                '#ruangan#' => $ruangan_pendaftaran,
                '#kamar#' => $kamar,
                '#tempat_tidur#' => $tempat_tidur,
                '#kelas_pelayanan#' => $kelas_admisi,
                '#penjamin#' => $penjamin_admisi,
                '#cara_bayar#' => $carabayar_nama,
                '#status_bayar#' => $status,
                '#kelompok_tindakan#' => $this->renderPartial('kelompok_tindakan', [
                    'detailTindakan' => !empty($detailTindakan) ? $detailTindakan : [],
                ]),
                '#tabel_akomodasi#' => $this->renderPartial('tabel_akomodasi'),
                '#total_tagihan#' => $listHeader['total_tagihan'],
                '#total_administrasi#' => $listHeader['total_administrasi'],
                '#subsidi_asuranasi#' => $listHeader['total_asuransi'],
                '#uang_masuk#' => $listHeader['uang_masuk'],
                '#total_akomodasi#' => $listHeader['total_akomodasi'],
                '#sisa_tagihan#' => $listHeader['sisa_tagihan'],
                '#pegawai#' => '',
                '#kamar_#' => '',
                '#kelas#' => '',
                '#tt_dari#' => '',
                '#sampai#' => '',
                '#hari#' => '',
                '#tarif#' => '',
                '#biaya#' => '',
                '#masuk#' => $tgl_masuk == '-' ? '-' : $tgl_masuk . ' ' . $jam_masuk,
                '#keluar#' => $tgl_keluar == '-' ? '-' : $tgl_keluar . ' ' . $jam_keluar,
            ];
        }

        $print->Output();
    }

    public function actionExportExcel($jenis, $ruangan)
    {
        $data = [];
        $data_baru = [];
        $model = new InfoPasienRanap;
        $ruangan_id = $_GET['idruangan'];
        $query = $model::find(
            'tgl_admisi',
            'no_rekam_medik',
            'no_pendaftaran',
            'nama_pasien',
            'jenis_kelamin',
            'dokter_admisi',
            'carabayar_nama',
            'penjamin_nama',
            'jeniskasuspenyakit_nama',
            'kelas_pelayanan',
            'hak_kelas',
            'ruangan_nama',
            'kamarruangan_nokamar',
            'no_tempattidur',
            'tgl_pindahkamar',
            'rencana_pulang',
            'is_pasientitipan',
            'is_stoppasientitipan',
            'kelas_ditagihkan_id',
            'kelas_ditagihkan_nama',
            'is_pasientitipan_pk',
            'is_stopakomodasi',
            'dokter_admisi_id',
            'status_konsul',
            'jenis_konsul'
        )->where(['ruangan_id' => $ruangan]);

        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $groupEmployee = Pegawai::find()->select(['kelompokpegawai_id'])->andWhere(['pegawai_id' => $pegawai_id])->asArray()->one();
        $kelompokpegawai_id = !empty($groupEmployee) ? $groupEmployee['kelompokpegawai_id'] : 0;

        $dokter = DokterView::find()->andWhere(['pegawai_id' => $pegawai_id])->one();
        if ($ruangan_id) {
            $query->andWhere(['infopasienri_v.ruangan_id' => $ruangan_id]);

            // kalau loginan itu dokter, yg dimunculin hanya DPJP (dokter_admisi_id) = id dokter loginan
            if ($dokter) {
                // kalau dia punya jadwal dokter, pasien dimunculin semua, kalau tidak punya baru difilter sesuai dpjp nya loginan tersebut
                // if (!$this->getHasJadwalDokter($ruangan_id, $pegawai_id)) {
                //     $query->andWhere(['infopasienri_v.dokter_admisi_id'=>$pegawai_id]);
                // }
            }
        } else {
            // kalau loginan itu dokter, yg dimunculin DPJP atau konsul = id dokter loginan
            // if ($dokter) {
            //     $query->andWhere('(
            //         infopasienri_v.dokter_admisi_id = ' . $pegawai_id . ' OR
            //         permintaankonsul_t.dokter_id = ' . $pegawai_id . ')'
            //     );
            // }
        }
        $query->andWhere(['not in', 'infopasienri_v.status_ranap', [DocoConstants::STATUS_RANAP_BATAL_RAWAT]]);
        // $query->andWhere(['pasienpulang_id' => NULL, 'is_stopakomodasi' => false]);
        $query->andWhere(['pasienpulang_id' => NULL]);

        $request = Yii::$app->request;
        $stop_akomodasi = 1;
        $advancedFilters = $request->get('advanced-filter', []);
        if ( isset($advancedFilters['is_stopakomodasi']) ) {
            if($advancedFilters['is_stopakomodasi'] == 2) {
                $stop_akomodasi = 2;
            }
            unset($_GET['advanced-filter']['is_stopakomodasi']);
        }
        if ($stop_akomodasi == 2) {
            $query->andWhere([
                'is_stopakomodasi' => true
            ]);
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // modify advanced filters
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        if (isset($advancedFilters['tgl_admisi_awal']) && isset($advancedFilters['tgl_admisi_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_admisi_awal'];
            $tgl_akhir = $advancedFilters['tgl_admisi_akhir'];
        }
        if($tgl_awal && $tgl_akhir) {
            $query->andWhere(['between', 'tgl_admisi', $tgl_awal, $tgl_akhir]);
        }
        $data = $query->asArray()->all();
        $counter = 0;
        foreach ($data as $key => $value) {
            $date1 = date_create(date('Y-m-d', strtotime($value['tgl_admisi'])));
            $date2 = date_create(date('Y-m-d'));
            $diff = date_diff($date1, $date2);
            $dataHariRawat = $diff->format('%a') + 1;
            $statusTitipan = '-';
            $isTitip = false;
            $isKonsul = false;
            $keterangan_pasien = "PASIEN NON KONSUL";

            if ($value['carabayar_id'] == 6) {
                $statusTitipan = $statusTitipan;
            } else if (!empty($value['is_pasientitipan_pk'])) {
                if ($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false) {
                    $statusTitipan = $value['kelas_ditagihkan_nama'];
                    $isTitip = true;
                }
            } else if (empty($value['is_pasientitipan_pk'])) {
                if ($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false) {
                    $statusTitipan = $value['kelas_ditagihkan_nama'];
                    $isTitip = true;
                }
            }

            // $isKonsul = $value['dokter_admisi_id'] != $pegawai_id &&
            // $kelompokpegawai_id == DocoConstants::KELOMPOK_PEGAWAI_DOKTER &&
            // $ruangan_id == null
            // ? true
            // : false;

            if( ($value['jenis_konsul'] == 434 || $value['jenis_konsul'] == 435) && $value['status_konsul'] == 437 ) {
                $isKonsul = true;
            }

            if($isKonsul == true){
                $keterangan_pasien = "PASIEN KONSUL";
            } else if ($isTitip == true){
                $keterangan_pasien = "PASIEN TITIPAN";
            } else if ($value['is_stopakomodasi'] == true) {
                $keterangan_pasien = "PASIEN STOP AKOMODASI";
            }

            $data_baru[$counter]['Tanggal Admisi'] = date('d M Y H:i:s', strtotime($value['tgl_admisi']));
            $data_baru[$counter]['No.RM / No Pendaftaran'] = $value['no_rekam_medik'] . " / " . $value['no_pendaftaran'];
            $data_baru[$counter]['Nama Pasien'] = $value['nama_pasien'];
            $data_baru[$counter]['Jenis Kelamin'] = $value['jenis_kelamin'];
            $data_baru[$counter]['Dokter DPJP'] = $value['dokter_admisi'];
            $data_baru[$counter]['Cara Bayar / Penjamin'] = $value['carabayar_nama'] . ' / ' . $value['penjamin_nama'];
            $data_baru[$counter]['Hak Kelas / Kelas Saat Ini / Kelas Tagihan'] = $value['hak_kelas'] . " / " . $value['kelas_pelayanan'] . " / " . $statusTitipan;
            $data_baru[$counter]['Kasus Penyakit'] = $value['jeniskasuspenyakit_nama'];
            $data_baru[$counter]['Nama Ruangan / No.Kamar-No.Bed'] = $value['ruangan_nama'] . " / " . $value['kamarruangan_nokamar'] . " - " . $value['no_tempattidur'];
            $data_baru[$counter]['Hari Rawat'] = ($dataHariRawat == 0) ? 1 : $dataHariRawat;
            $data_baru[$counter]['Tanggal Pindah'] =  !empty($value['tgl_pindahkamar']) ?
                date('d M Y H:i:s', strtotime($value['tgl_pindahkamar'])) : ' - ';
            $data_baru[$counter]['Rencana Pulang'] = !empty($value['rencana_pulang']) ?
                date('d M Y H:i:s', strtotime($value['rencana_pulang'])) : ' - ';
            $data_baru[$counter]['Status Pasien'] = $keterangan_pasien;
            $counter++;
        }
        $header = ['Periode' => date("d F Y", strtotime($tgl_awal)) . ' - ' . date("d F Y", strtotime($tgl_akhir))];
        $filePath = DocoHelpers::exportExcel("Informasi Pasien Rawat Inap", $data_baru, $header, array("uploadPath" => "./uploads"), [], [], true);

        $filePath->save('php://output');
        die;
    }

    public function actionViewData($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = "")
    {
        $pasienranap = InfoPasienRanap::find();
        if ($id) {
            $pasienranap->where(['pendaftaran_id' => $id]);
        }

        return $pasienranap;
    }

    public function actionDataInstruksiTindakan($pendaftaran_id)
    {
        return $this->getDataInstruksiTindakan($pendaftaran_id);
    }

    private function getDataInstruksiTindakan($pendaftaran_id = "")
    {
        $query = InfoInstruksiView::find();
        $query->where([
            'not in', 'status_implementasi', [
                DocoConstants::ST_P_PEN_SDH_OPRS,            //483
                DocoConstants::RESEPTUR_SUDAH_DIPROSES,        //347 
                DocoConstants::IMPLEMENTASI_SUDAH_IMPLEMENTASI, //455
                DocoConstants::DISETUJUI,                      //471
                DocoConstants::ST_SELESAI,            //475

                DocoConstants::BTL_APPROVE,        //472
                DocoConstants::BTL_PERIKSA_LAB,    //476
                DocoConstants::VAR_B_R,            //432
                DocoConstants::DI_TOLAK,            //451

                DocoConstants::LAB_BELUM_PERIKSA,  //477
                DocoConstants::ST_P_PEN_PRKS,  //473
                DocoConstants::ST_P_PEN_AMB_SAMP,  //474

                DocoConstants::RESEPTUR_DISERAHKAN, //660
            ]
        ]);

        if ($pendaftaran_id) {
            $query->andWhere(['pendaftaran_id' => $pendaftaran_id]);
        }
        $result = $query->all();

        return $result;
    }

    public function actionGetBundleData($id = "")
    {

        $dataranap = $this->getData($id)->asArray()->one();
        return [
            'list-ruangan' => $this->getListRuangan($dataranap['jeniskasuspenyakit_id']),
            'list-pegawai' => $this->getDataPegawai(),
            'list-rujukan' => $this->getDataRujukan(),
            'list-carakeluar' => $this->getDataCaraKeluar(),
            'list-kondisikeluar' => $this->getDataKondisiKeluar(),
            'list-jeniskelamin' => ArrayHelper::map($this->getLookupByType('jenis_kelamin')->asArray()->all(), 'lookup_id', 'lookup_name'),
            'list-hubungankeluarga' => ArrayHelper::map($this->getLookupByType('hubungan_keluarga')->asArray()->all(), 'lookup_id', 'lookup_name')
        ];
    }

    private function getListRuangan($jeniskasuspenyakit_id)
    {
        $data = KasusPenyakitRuangan::find()
            ->where(['jeniskasuspenyakit_id' => $jeniskasuspenyakit_id])
            ->orderBy('jeniskasuspenyakit_id');
        $items = ArrayHelper::map($data->all(), 'ruangan_id', 'ruangan_nama');

        return $items;
    }

    private function getDataPegawai()
    {
        $sql = "select pegawai_id, nama_pegawai from pegawai_m where is_deleted = false and is_active = true
            order by pegawai_id asc 
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $items = ArrayHelper::map($data, 'pegawai_id', 'nama_pegawai');
        return $items;
    }

    private function getDataRujukan()
    {
        $sql = "select rujukankeluar_id, rumahsakit_rujukan from rujukankeluar_m where is_deleted = false and is_active = true
            order by rujukankeluar_id asc 
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $items = ArrayHelper::map($data, 'rujukankeluar_id', 'rumahsakit_rujukan');
        return $items;
    }

    private function getDataCaraKeluar()
    {
        $sql = "select carakeluar_id, carakeluar_nama from carakeluar_m where is_deleted = false and is_active = true
            order by carakeluar_id asc 
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $items = ArrayHelper::map($data, 'carakeluar_id', 'carakeluar_nama');
        return $items;
    }

    private function getDataKondisiKeluar()
    {
        $sql = "select kondisikeluar_id, kondisikeluar_nama from kondisikeluar_m where is_deleted = false and is_active = true
        group by kondisikeluar_id
        order by kondisikeluar_id asc 
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $items = ArrayHelper::map($data, 'kondisikeluar_id', 'kondisikeluar_nama');
        return $items;
    }

    public function actionDataKondisiKeluar($carakeluar_id)
    {
        $sql = "select kondisikeluar_id, kondisikeluar_nama from kondisikeluar_m where carakeluar_id={$carakeluar_id} and is_deleted = false and is_active = true
            order by kondisikeluar_id asc 
        ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }

    public function actionBatalPeriksa()
    {
        try {
            $request = Yii::$app->request;

            if ($request->post()) {
                $modelBatalPeriksa = new PasienBatalPeriksa;
                $modelBatalPeriksa->attributes = $request->post();
                $id = $request->post('pasienadmisi_id');

                if ($modelBatalPeriksa->save()) {
                    $modelPendaftaran = PasienAdmisi::findOne($id);
                    $modelPendaftaran['status_ranap'] = DocoConstants::ST_P_RNP_BTL; //453;
                    $modelPendaftaran['pasienbatalperiksa_id'] = $modelBatalPeriksa->pasienbatalperiksa_id;

                    // //get kamar tempat tidur
                    // $kamartempattidur_id = $modelPendaftaran['kamartempattidur_id'];
                    // $datakamar = KamarTempatTidur::findOne($kamartempattidur_id);
                    // //get kamar ruangan
                    // $kamarruangan_id = $datakamar['kamarruangan_id'];
                    // $dataruangan = KamarRuangan::findOne($kamarruangan_id);
                    // //get kamar ruangan jenis
                    // $ruanganjenis = $dataruangan['kamarruangan_jenis'];
                    // //$datalookup = Lookup::findOne($ruanganjenis);
                    // //set data update ruangan kosong
                    // $datakamar['status_isi'] = false;
                    // //$datakamar['kettempattidur_id'] = $datalookup['lookup_urutan'];
                    // switch ($ruanganjenis) {
                    //     case '340':
                    //         $datakamarlama['kettempattidur_id'] = 2;//jenis kamar laki-laki
                    //         break;
                    //     case '341':
                    //         $datakamarlama['kettempattidur_id'] = 1;//jenis kamar perempuan
                    //         break;
                    //     default:
                    //         $datakamarlama['kettempattidur_id'] = 7;//jenis kamar campur
                    //         break;
                    // }
                    // //update ruangan kosong 
                    // $datakamar->save();


                    $id = $modelPendaftaran['kamartempattidur_id'];
                    $modelTt = KamarTempatTidur::findOne($id);
                    $modelTt->status_isi = false;
                    $ketTt = $this->getKetTt($modelTt->kamarruangan_id);
                    $modelTt->kettempattidur_id = $ketTt ? $ketTt['kettempattidur_id'] : $modelTt->kettempattidur_id;
                    $modelTt->save();

                    $getStatusIsi = $this->cekStatusIsi($id);

                    if ($getStatusIsi == true) {
                        if ($modelPendaftaran->save()) {
                            return [
                                'message' => 'Data Berhasil di simpan',
                            ];
                        } else {
                            $errors = DocoHelpers::parseError($modelPasienPulang->errors, 'PasienBatalPeriksa');
                            return [
                                'data' => $errors,
                                'status' => 422
                            ];
                        };
                    }

                    //
                    
                } else {
                    $errors = DocoHelpers::parseError($modelPasienPulang->errors, 'PasienBatalPeriksa');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function getKetTt($kamarruangan_id)
    {
        $kamar = KamarRuangan::findOne($kamarruangan_id);
        $jnskamar = $kamar->kamarruangan_jenis;

        $ketTt = KetTempatTidur::find()
            ->andWhere([
                'kamarruangan_jenis' => $jnskamar,
                'is_kosong' => true
            ])
            ->asArray()->one();
        return $ketTt ?: null;
    }

    public function actionGetDataKamar()
    {
        $request = Yii::$app->request;

        $model = new KamarRuanganView;
        $query = $model::find();
        if ($request->get('jeniskasuspenyakit_id')) {
            $query->andWhere(['jkpkamar_id' => $request->get('jeniskasuspenyakit_id')]);
        }
        if ($request->get('kelaspelayanan_id')) {
            $query->andWhere(['kelaspelayanan_id' => $request->get('kelaspelayanan_id')]);
        }
        if ($request->get('ruangan_id')) {
            $query->andWhere(['ruangan_id' => $request->get('ruangan_id')]);
        }
        return [
            'data' => $query->asArray()->all(),
            'list-ruangan' => $query->select(['ruangan_id', 'ruangan_nama', 'kamarruangan_nokamar'])->distinct()->orderBy(['ruangan_nama' => SORT_ASC, 'kamarruangan_nokamar' => SORT_ASC])->all()
        ];
    }

    public function actionProsesPindahKamar()
    {
        try {
            $tglStopAkomodasi = date('Y-m-d H:i:s');
            $connection = Yii::$app->db;
            $request = Yii::$app->request;

            if ($request->post()) {
                $dataPost = $request->post();
                $transaction = $connection->beginTransaction();

                $idAdmisi = $dataPost['pasienadmisi_id'];
                $idPendaftaran = $dataPost['pendaftaran_id'];

                $modelAdmisi = PasienAdmisi::findOne($dataPost['pasienadmisi_id']);
                $modelPasien = Pasien::findOne($modelAdmisi['pasien_id']);
                $kamartempattidur_id_lama = $modelAdmisi['kamartempattidur_id'];
                $kamartempattidur_id_baru = $dataPost['kamartempattidur_id'];

                if (isset($modelAdmisi['kamarruangan_id']) && isset($modelAdmisi['kelaspelayanan_id']) && 
                    isset($dataPost['kamarruangan_id']) && isset($dataPost['kelaspelayanan_id']) && 
                    $dataPost['kamarruangan_id'] != $modelAdmisi['kamarruangan_id'] && $dataPost['kelaspelayanan_id'] != $modelAdmisi['kelaspelayanan_id']) {
                    //1. Stop akomodasi kamar sebelumnya kalau kelas pelayanan atau tempat tidurnya beda
                    TindakanAkomodasi::execute(
                        DocoConstants::AKOMODASI_MUTASI,
                        $idAdmisi,
                        $tglStopAkomodasi,
                        false,
                        false,
                        null,
                        false,
                        true
                    );
                }

                //2. update pendaftaran & pasien admisi
                $modelPendaftaran = Pendaftaran::findOne($dataPost['pendaftaran_id']);
                if ($modelPendaftaran['instalasi_id'] != DocoConstants::INST_ID_RD) {
                    $modelPendaftaran['jeniskasuspenyakit_id'] = $dataPost['jeniskasuspenyakit_id'];
                    $modelPendaftaran['kelaspelayanan_id'] = $dataPost['kelaspelayanan_id'];
                    $modelPendaftaran['ruangan_id'] = $dataPost['ruangan_id'];
                    $modelPendaftaran->save(false);
                }


                $modelAdmisi['jeniskasuspenyakit_id'] = $dataPost['jeniskasuspenyakit_id'];
                $modelAdmisi['kelaspelayanan_id'] = $dataPost['kelaspelayanan_id'];
                $modelAdmisi['kamarruangan_id'] = $dataPost['kamarruangan_id'];
                $modelAdmisi['ruangan_id'] = $dataPost['ruangan_id'];
                $modelAdmisi['kamartempattidur_id'] = $dataPost['kamartempattidur_id'];
                $modelAdmisi['tgl_pindahkamar'] = $dataPost['tgl_pindahkamar'];
                $modelAdmisi['is_pasientitipan'] = isset($dataPost['is_pasientitipan']) ? $dataPost['is_pasientitipan'] : false;
                $modelAdmisi['ruangan_titipan_id'] = isset($dataPost['ruangan_titipan_id']) ? $dataPost['ruangan_titipan_id'] : null;
                $modelAdmisi['kelas_ditagihkan_id'] = isset($dataPost['kelas_ditagihkan_id']) ? $dataPost['kelas_ditagihkan_id'] : null;
                $modelAdmisi['kamar_titipan_id'] = isset($dataPost['kamar_titipan_id']) ? $dataPost['kamar_titipan_id'] : null;

                if ($modelAdmisi->save(false)) {
                    //3. set data dan update ruangan lama kosong
                    $datakamarlama = KamarTempatTidur::find(true)->where(['kamartempattidur_id'=>$kamartempattidur_id_lama])->one();
                    $kamarruangan_id = $datakamarlama['kamarruangan_id'];
                    $dataruangan = KamarRuangan::findOne($kamarruangan_id);
                    $ruanganjenis = $dataruangan['kamarruangan_jenis'];
                    $datakamarlama['status_isi'] = false;
                    switch ($ruanganjenis) {
                        case '340':
                            $kettempattidur_id = DocoConstants::KET_TT_KSG_L; //jenis kamar laki-laki
                            break;
                        case '341':
                            $kettempattidur_id = DocoConstants::KET_TT_KSG_P; //jenis kamar perempuan
                            break;
                        case '342':
                            $kettempattidur_id = DocoConstants::KET_TT_KSG; //jenis kamar flexibel
                            break;
                        case '431':
                            $kettempattidur_id = DocoConstants::KET_TT_KSG_CMPR; //jenis kamar campur
                            break;
                        default:
                            $kettempattidur_id = DocoConstants::KET_TT_KSG_CMPR; //default jenis kosong campur
                            break;
                    }
                    $datakamarlama['kettempattidur_id'] = $kettempattidur_id;
                    $getStatusIsi = $this->cekStatusIsi($kamartempattidur_id_lama);
                    if ($getStatusIsi == true) {
                        if (!$datakamarlama->save(false)) {
                            return [
                                'message' => 'Data Gagal di simpan',
                                'status' => 422,
                                'statusCode' => 422
                            ];
                        }
                    }
                
                    //4. set data dan update ruangan baru terisi
                    $datakamarbaru = KamarTempatTidur::findOne($kamartempattidur_id_baru);
                    $datakamarbaru['status_isi'] = true;
                    if ($modelPasien['jeniskelamin'] == DocoConstants::VAR_PR) { // jenis kelamin perempuan
                        $datakamarbaru['kettempattidur_id'] = DocoConstants::KET_TT_ISI_P; //isi perempuan
                    } else {
                        $datakamarbaru['kettempattidur_id'] = DocoConstants::KET_TT_ISI_L; //isi laki-laki
                    }
                    $datakamarbaru->save(false);

                    //5. data insert pindahkamar_t
                    $pegawai_id = Yii::$app->jwt->user->pegawai_id;
                    $modelPindah = new PindahKamar;
                    $modelPindah['kamartempattidur_id'] = $dataPost['kamartempattidur_id'];
                    $modelPindah['pendaftaran_id'] = $dataPost['pendaftaran_id'];
                    $modelPindah['kamarruangan_id'] = $dataPost['kamarruangan_id'];
                    $modelPindah['pegawai_id'] = $pegawai_id;
                    $modelPindah['carabayar_id'] = $modelAdmisi['carabayar_id'];
                    $modelPindah['ruangan_id'] = $dataPost['ruangan_id'];
                    $modelPindah['penjamin_id'] = $modelAdmisi['penjamin_id'];
                    $modelPindah['pasienadmisi_id'] = $modelAdmisi['pasienadmisi_id'];
                    $modelPindah['kelaspelayanan_id'] = $modelAdmisi['kelaspelayanan_id'];
                    $modelPindah['pasien_id'] = $modelAdmisi['pasien_id'];
                    $modelPindah['tgl_pindahkamar'] = date('Y-m-d H:i:s');
                    $modelPindah['jam_pindahkamar'] = date('H:i:s');
                    $modelPindah['is_pasientitipan'] = isset($dataPost['is_pasientitipan']) ? $dataPost['is_pasientitipan'] : false;
                    $modelPindah['ruangan_titipan_id'] = isset($dataPost['ruangan_titipan_id']) ? $dataPost['ruangan_titipan_id'] : null;
                    $modelPindah['kelas_ditagihkan_id'] = isset($dataPost['kelas_ditagihkan_id']) ? $dataPost['kelas_ditagihkan_id'] : null;
                    $modelPindah['kamar_titipan_id'] = isset($dataPost['kamar_titipan_id']) ? $dataPost['kamar_titipan_id'] : null;
                    $modelPindah->save();

                    //5. update masuk kamar lama
                    $tglStopAkomodasi = date('Y-m-d H:i:s');
                    $modelMKupdate = MasukKamar::find()
                        ->andWhere(['pasienadmisi_id' => $modelAdmisi->pasienadmisi_id])
                        ->andWhere(['is', 'pindahkamar_id', new \yii\db\Expression('null')])
                        ->one();
                    $modelMKupdate['pindahkamar_id'] = $modelPindah['pindahkamar_id'];
                    $modelMKupdate['tgl_keluarkamar'] = $tglStopAkomodasi;
                    $modelMKupdate['jam_keluarkamar'] = date('H:i:s');
                    $modelMKupdate['tgl_last_akomodasi'] = $tglStopAkomodasi;
                    $d1 = date_create(date('Y-m-d'));
                    $d2 = date_create(date('Y-m-d', strtotime($modelMKupdate['tgl_masukkamar'])));
                    $diff =  date_diff($d1, $d2);
                    $modelMKupdate['lamadirawat_kamar'] = $diff->format('%a') + 1;
                    $modelMKupdate->save(false);

                    //7. insert masukkamar_t
                    $modelMKinsert = new MasukKamar;
                    $modelMKinsert['ruangan_id'] = $dataPost['ruangan_id'];
                    $modelMKinsert['carabayar_id'] = $modelAdmisi['carabayar_id'];
                    $modelMKinsert['pasienadmisi_id'] = $modelAdmisi['pasienadmisi_id'];
                    $modelMKinsert['penjamin_id'] = $modelAdmisi['penjamin_id'];
                    $modelMKinsert['pegawai_id'] = $pegawai_id;
                    $modelMKinsert['kelaspelayanan_id'] = $modelAdmisi['kelaspelayanan_id'];
                    $modelMKinsert['kamartempattidur_id'] = $dataPost['kamartempattidur_id'];
                    $modelMKinsert['kamarruangan_id'] = $dataPost['kamarruangan_id'];
                    $modelMKinsert['tgl_masukkamar'] = date('Y-m-d H:i:s');
                    $modelMKinsert['jam_masukkamar'] = date('H:i:s');
                    $modelMKinsert->save();

                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                        'status' => 200,
                        'statusCode' => 200
                    ];
                } else {
                    $errors = DocoHelpers::parseError($modelAdmisi->errors, 'PasienAdmisi');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
        }
    }

    public function actionProsesPulangPasien()
    {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $request = Yii::$app->request;
            $dataPost = $request->post();

            if ($request->post()) {
                $modelPulang = new PasienPulang;
                $modelRujukan = new PasienDirujukKeluar;
                $rujukanPulang = new RujukanPulang;
                $returnMsg = 'Data Berhasil di simpan';

                $pasienadmisi_id = $dataPost['InfoPasienRanapForm']['pasienadmisi_id'];
                $pendaftaran_id = $dataPost['InfoPasienRanapForm']['pendaftaran_id'];

                /* ada rujukan*/
                if (!empty($dataPost['PasienDirujukKeluarForm'])) {
                    $modelRujukan->attributes = $dataPost['PasienDirujukKeluarForm'];

                    if ($modelPulang->pasiendirujukkeluar_id) {
                        $modelRujukan->pendaftaran_id   = $pendaftaran_id;
                        $modelRujukan->pasienadmisi_id  = $pasienadmisi_id;
                        $modelRujukan->pasien_id        = $modelPulang->pasien_id;
                        $modelRujukan->tgldirujuk       = date('Y-m-d', strtotime($modelRujukan->tgldirujuk));
                        $modelRujukan->save();
                    }
                }

                if (!empty($dataPost['RujukanPulangForm'])) {
                    $postRujukan = $dataPost['RujukanPulangForm'];
                    $rujukanPulang = $this->saveRujukanPasien($postRujukan);
                }

                $modelPulang->attributes = $dataPost['PasienPulangForm'];
                $modelPulang->waktu_pemeriksaan_jenazah = ($modelPulang->waktu_pemeriksaan_jenazah) ? date('Y-m-d H:i:s', strtotime($modelPulang->waktu_pemeriksaan_jenazah)) : date('Y-m-d H:i:s');
                $modelPulang->tglpasienpulang = ($modelPulang->tglpasienpulang) ? date('Y-m-d H:i:s', strtotime($modelPulang->tglpasienpulang)) : date('Y-m-d H:i:s');
                $modelPulang->tgl_meninggal = ($modelPulang->tgl_meninggal) ? date('Y-m-d H:i:s', strtotime($modelPulang->tgl_meninggal)) : '';
                $modelPulang->tgl_rencanakontrol = ($modelPulang->tgl_rencanakontrol) ? date('Y-m-d H:i:s', strtotime($modelPulang->tgl_rencanakontrol)) : '';
                $modelPulang->pendaftaran_id = $pendaftaran_id;
                $modelPulang->pasienadmisi_id = $pasienadmisi_id;
                $modelPulang->pasiendirujukkeluar_id = $modelRujukan->pasiendirujukkeluar_id;
                $modelPulang->ruanganakhir_id = $dataPost['PasienPulangForm']['ruanganasal_id'];

                if (!$modelPulang->save()) {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($modelPulang->errors, 'PasienPulang');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                } else {
                    $modelAdmisi = PasienAdmisi::findOne($pasienadmisi_id);
                    $modelAdmisi->pasienpulang_id = $modelPulang->pasienpulang_id;
                    $modelAdmisi->tgl_pulang = $modelPulang->tglpasienpulang;
                    $modelAdmisi->status_ranap = DocoConstants::STATUS_RANAP_PULANG;
                    $modelAdmisi->save();
                    
                    /*kamar ruangan*/
                    $modelKamarRuangan = KamarRuangan::findOne($modelAdmisi->kamarruangan_id);
                    if(!empty($modelKamarRuangan)){
                        $arr = $modelKamarRuangan->kamarruangan_jenis;
                        $getWarnaTempatTidur = WarnaTempatTidur::find()
                            ->where(['kamarruangan_jenis' => $arr])
                            ->orderby(['kettempattidur_nama' => SORT_ASC])
                            ->andWhere([
                                'is_active' => true,
                                'is_deleted' => false,
                                'is_kosong' => true,
                            ])
                            ->one();
                        if(empty($getWarnaTempatTidur)){
                            return [
                                'message' => 'Warna Tempat Tidur Tidak Tersedia.',
                                'status' => 422
                            ];
                        }
                    }else{
                        return [
                            'message' => 'Kamar Ruangan Tidak Tersedia.',
                            'status' => 422
                        ];
                    }

                    /*kamar tempat tidur*/
                    $modelKamarTempatTidur = KamarTempatTidur::findOne($modelAdmisi->kamartempattidur_id);
                    $getStatusIsi = $this->cekStatusIsi($modelAdmisi->kamartempattidur_id);
                    if ($getStatusIsi == true) {
                        if(!empty($modelKamarTempatTidur)){
                            $modelKamarTempatTidur->status_isi = false;
                            $modelKamarTempatTidur->kettempattidur_id = $getWarnaTempatTidur->kettempattidur_id;
                            if (!$modelKamarTempatTidur->save()) {
                                return [
                                    'message' => 'Kamar Tempat Tidur Tidak Terupdate.',
                                    'status' => 422
                                ];
                            }
                        }else{
                            return [
                                'message' => 'Kamar Tempat Tidur Tidak Tersedia.',
                                'status' => 422
                            ];
                        }
                    }
                    /**
                     * update tanggal pulang SEP untuk pasien BPJS versi terbaru
                     */
                    $t_sep_new = [];
                    $resKontrol = [];
                    if($dataPost['PasienPulangForm']['nosep'] != null) {
                            $carakeluarBpjs = (new DocoConstansId)->actionGetAdditional('cara_pulang_bpjs',true);
                            $caraPulangBpjs = isset($carakeluarBpjs[$dataPost['PasienPulangForm']['carakeluar_id']]) ? $carakeluarBpjs[$dataPost['PasienPulangForm']['carakeluar_id']] : 5;
                            $model = new BpjsRanap();
            
                            $t_sep_new['noSep'] = $dataPost['PasienPulangForm']['nosep'];
                            $t_sep_new['statusPulang'] = $caraPulangBpjs;
                            $t_sep_new['noSuratMeninggal'] = $dataPost['PasienPulangForm']['carakeluar_id'] == 4 ? $dataPost['PasienPulangForm']['no_surat_kematian'] : '';
                            $t_sep_new['tglMeninggal'] = $dataPost['PasienPulangForm']['carakeluar_id'] == 4 ?  date('Y-m-d', strtotime($dataPost['PasienPulangForm']['tgl_meninggal'])) : '';
                            $t_sep_new['tglPulang'] = date('Y-m-d', strtotime($dataPost['tglpasienpulang_x']));
                            $t_sep_new['noLPManual'] = '';
                            $t_sep_new['user'] = $dataPost['PasienPulangForm']['user'];
                            $model->t_sep = $t_sep_new;
            
                            if ($t_sep_new['noSep'] != null && $t_sep_new['tglPulang'] != null) {
                                $result = $model->updateTanggalPulangSepNew();
                            }

                            if (!empty($dataPost['PasienPulangForm']['is_rencanakontrol'])) {
                                $nosep = $dataPost['PasienPulangForm']['nosep'];
                                $bpjs_id = "SELECT bpjs_id FROM bpjs_t WHERE nosep = '{$nosep}'";
                                $bpjs_id = $connection->createCommand($bpjs_id)->queryOne();
                                $bpjs_id = ArrayHelper::getValue($bpjs_id, 'bpjs_id');
                                $payload_create_kontrol = [
                                    'jenis_rencana' => 1, // set default rencana kontrol rawat jalan
                                    'user' => $dataPost['PasienPulangForm']['user'],
                                    'RencanaKontrolForm' => [
                                        'no_sep' => $dataPost['PasienPulangForm']['nosep'],
                                        'bpjs_id' => $bpjs_id,
                                        'pendaftaran_id' => $dataPost['InfoPasienRanapForm']['pendaftaran_id'],
                                        'no_kartu' => $dataPost['PasienPulangForm']['no_kartu'],
                                        'tgl_rencanakontrol' => $dataPost['PasienPulangForm']['tgl_rencanakontrol'],
                                        'jenis_pelayanan' => 2,
                                        'nama_spesialis' => $dataPost['PasienPulangForm']['nama_spesialis'],
                                        'kode_poli' => $dataPost['PasienPulangForm']['kode_poli'],
                                        'dokterdpjp_kode' => $dataPost['PasienPulangForm']['dokterdpjp_kode'],
                                        'dokterdpjp_nama' => $dataPost['PasienPulangForm']['dokterdpjp_nama'],
                                    ]
                                ];
                                
                                $res_create_rencana_kontrol = Yii::$app->docoRest->pendaftaran->post('rencana-kontrol-inap/create', [
                                    'form_params' => $payload_create_kontrol
                                ]);
                                $res_create_rencana_kontrol = json_decode($res_create_rencana_kontrol->getBody(), true);
                                if (ArrayHelper::getValue($res_create_rencana_kontrol, 'metadata.status') == 200) {
                                    if (ArrayHelper::getValue($res_create_rencana_kontrol, 'response.result.metaData.code') != 200) {
                                        array_push($resKontrol, ArrayHelper::getValue($res_create_rencana_kontrol, 'response.result.metaData.message'));
                                    }
                                } else {
                                    array_push($resKontrol, ArrayHelper::getValue($res_create_rencana_kontrol, 'response.result.metaData.message'));
                                }
                            }
                    }

                    /**
                     * update tanggal pulang SEP untuk pasien BPJS
                     * per 01/04/22 ada request untuk non aktifkan update karena sudah dibackup dari pendaftaran
                     */
                    // if (isset($modelAdmisi->carabayar->groupcarabayar_id) && $modelAdmisi->carabayar->groupcarabayar_id == DocoConstants::GROUP_BPJS) {
                    //     if (isset($modelAdmisi->pendaftaran->bpjs_id)) {
                    //         $mBpjs = Bpjs::findOne($modelAdmisi->pendaftaran->bpjs_id);
                    //         $mBpjs->tglpulang = date('Y-m-d');
                    //         if (!$mBpjs->save()) {
                    //             $transaction->rollBack();
                    //             $errors = DocoHelpers::parseError($modelPulang->errors, 'PasienPulang');
                    //             return [
                    //                 'data' => $errors,
                    //                 'status' => 422
                    //             ];
                    //         }

                    //         $mUpdateTanggalPulangSep = new Bpjs;
                    //         $statPulangINACBG = Lookup::find()->select(['lookup_value'])->innerJoin('carakeluar_m', 'lookup_m.lookup_id = carakeluar_m.carakeluarinacbg_id')->where(['carakeluar_m.carakeluar_id' => $modelPulang->carakeluar_id])->limit(1)->scalar();
                    //         $mUpdateTanggalPulangSep->t_sep = [
                    //             'noSep' => $mBpjs->nosep,
                    //             'statusPulang' => $statPulangINACBG,
                    //             'noSuratMeninggal' => !empty($mBpjs->no_surat_meninggal) ? $mBpjs->no_surat_meninggal : '',
                    //             'tglMeninggal' => !empty($mBpjs->tgl_meninggal_bpjs) ? $mBpjs->tgl_meninggal_bpjs : '',
                    //             'tglPulang' => date('Y-m-d'),
                    //             'noLPManual' => !empty($mBpjs->no_lp_manual) ? $mBpjs->no_lp_manual : '',
                    //             'user' => Yii::$app->jwt->user->nama_pemakai
                    //         ];
                    //         $resultUpdateTanggalPulang = $mUpdateTanggalPulangSep->updateTanggalPulangSep();
                    //         if (!$resultUpdateTanggalPulang) {
                    //             return [
                    //                 'status' => 422,
                    //                 'message' => 'Gagal Update Tanggal Pulang'
                    //             ];
                    //         } else if (($resultUpdateTanggalPulang['metaData']['code'] != 200) || ($resultUpdateTanggalPulang['metaData']['code'] != '200')) {
                    //             $returnMsg = $resultUpdateTanggalPulang['metaData']['message'];
                    //         }
                    //     }
                    // }

                    /* update masukkamar_t */
                    $masukkamar = MasukKamar::find()
                        ->andWhere(['pasienadmisi_id' => $pasienadmisi_id])
                        ->andWhere(['is', 'pindahkamar_id', new \yii\db\Expression('null')])
                        ->one();
                    if ($masukkamar) {
                        $tglpulang = $modelPulang->tglpasienpulang;
                        $masukkamar->tgl_keluarkamar = $tglpulang;

                        $d1 = date_create(date('Y-m-d', strtotime($tglpulang)));
                        $d2 = date_create(date('Y-m-d', strtotime($masukkamar['tgl_masukkamar'])));
                        $diff =  date_diff($d1, $d2);
                        $masukkamar->lamadirawat_kamar = $diff->format('%a') + 1;
                        $masukkamar->save();
                    }

                    /*added condition Form Pelayanan Jenazah*/
                    if (isset($dataPost['PelayananJenazahForm'])) {
                        $dataPelayananJenazah = [];
                        $postPelayananJenazah = $dataPost['PelayananJenazahForm'];
                        $dataPelayananJenazah['kondisi'] = isset($postPelayananJenazah['kondisi_pasien']) ? $postPelayananJenazah['kondisi_pasien'] : '';
                        $dataPelayananJenazah['hubungan_keluarga'] = isset($postPelayananJenazah['hub_keluarga']) ? $postPelayananJenazah['hub_keluarga'] : '';
                        $dataPelayananJenazah['nama_pj'] = isset($postPelayananJenazah['nama_pj']) ? $postPelayananJenazah['nama_pj'] : '';
                        $dataPelayananJenazah['jeniskelamin_id'] = isset($postPelayananJenazah['jenis_kelamin']) ? $postPelayananJenazah['jenis_kelamin'] : '';
                        $dataPelayananJenazah['umur'] = isset($postPelayananJenazah['umur']) ? $postPelayananJenazah['umur'] : '';
                        $dataPelayananJenazah['no_kontak'] = isset($postPelayananJenazah['no_telp']) ? $postPelayananJenazah['no_telp'] : '';
                        $dataPelayananJenazah['alamat'] = isset($postPelayananJenazah['alamat']) ? $postPelayananJenazah['alamat'] : '';
                        if (isset($dataPost['list_jenazah_order'])) {
                            $listOrder = [];
                            if (isset($dataPost['list_jenazah_order']['tindakan'])) {
                                if (is_array($dataPost['list_jenazah_order']['tindakan'])) {
                                    foreach ($dataPost['list_jenazah_order']['tindakan'] as $list_order_tindakan) {
                                        $list_order_tindakan['additional_data'] = json_decode($list_order_tindakan['additional_data'], true);
                                        $listOrder['tindakan'][] = $list_order_tindakan;
                                    }
                                }
                            }
                            if (isset($dataPost['list_jenazah_order']['obat'])) {
                                if (is_array($dataPost['list_jenazah_order']['obat'])) {
                                    foreach ($dataPost['list_jenazah_order']['obat'] as $list_order_obat) {
                                        $listOrder['obat'][] = $list_order_obat;
                                    }
                                }
                            }
                            $dataPelayananJenazah['list_order'] = json_encode($listOrder);
                        }
                        if (isset($dataPost['list_jenazah_alat'])) {
                            $dataPelayananJenazah['list_linen'] = json_encode($dataPost['list_jenazah_alat']);
                        }
                        $res = $this->orderPelayananJenazah($pendaftaran_id, $dataPelayananJenazah);
                        if (!$res) {
                            throw new Exception("Gagal Simpan Pelayanan Jenazah", 1);
                        }
                    }


                    $transaction->commit();
                    $kamarRuanganId = ArrayHelper::getValue($modelKamarRuangan, 'kamarruangan_id');
                    (new BpjsAplicare())->createOrUpdateAplicare($kamarRuanganId, DocoConstants::TYPE_UPDATE_APLICARE);

                    /* update tgl pulang sync eklaim BPJS - Pemulangan Rawat Inap */
                    $dataPendaftaran = Pendaftaran::find()
                                        ->select(['pendaftaran_id', 'no_pendaftaran'])
                                        ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                                        ->asArray()->one();

                    if(!empty($dataPendaftaran)) {
                        $registration = [
                            'no_pendaftaran' => $dataPendaftaran['no_pendaftaran'],
                            'instalasi_kode' => [DocoConstants::INSTALASI_RAWAT_INAP, DocoConstants::INSTALASI_RAWAT_DARURAT]
                        ];

                        (new UpdateEklaimService)->updateTglPulang($registration);
                    }

                    Yii::$app->cache->delete('gizi-pendaftaran-id-'. DocoHelpers::encrypt($pendaftaran_id));

                    return [
                        'message'   => $returnMsg,
                        'data' => [
                            'modelPulang' => $modelPulang,
                            'modelKamarTempatTidur' => $modelKamarTempatTidur,
                            'rujukanPulang' => $rujukanPulang,
                        ],
                        'status'    => 200,
                        'resKontrol' => $resKontrol
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function cekStatusIsi($id) {
        try {
            $model = KamarTempatTidur::find()->select(['status_isi', 'kettempattidur_id'])->where(['kamartempattidur_id' => $id])->one();
            $status_isi = ArrayHelper::getValue($model, 'status_isi');
            $kettempattidur_id = ArrayHelper::getValue($model, 'kettempattidur_id');

            $status = false;
            if ($status_isi == true && $kettempattidur_id != DocoConstants::KET_TT_KSG_CMPR) {
                $status = true;
            }
            
            return $status;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdateStatusPeriksa($id)
    {
        try {
            $pendaftaran_id = json_decode(DocoHelpers::decrypt($id));
            $modelAdmisi = PasienAdmisi::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            if ($modelAdmisi && $modelAdmisi->status_ranap == DocoConstants::STATUS_RANAP_BELUM_PERIKSA) {
                $modelAdmisi['status_ranap'] = DocoConstants::STATUS_RANAP_PERIKSA;
                if ($modelAdmisi->save(false)) {
                    $rmService = new RmService;
                    $respn = $rmService->periksa([
                        'pendaftaran_id' => [$pendaftaran_id],
                        'pasienadmisi_id' => [$modelAdmisi->pasienadmisi_id],
                        'status' => DocoConstants::MONITORING_RM_ISSUE,
                    ]);
                }
            }
            return [
                'status_ranap' => DocoConstants::STATUS_RANAP_PERIKSA,
                'stat_ranap' => 'Periksa'
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCekKamarFleksibel($kamarruangan_id)
    {
        $db = Yii::$app->db;
        $jenis_fleksibel = DocoConstants::VAR_JKF;
        $status_approve = DocoConstants::VAR_STJ;
        $status_dipesan = DocoConstants::VAR_BK;
        $sql = "SELECT jeniskelamin
            FROM infopemesanankamar_v
            WHERE 
                kamarruangan_id = {$kamarruangan_id}
            AND kamarruangan_jenis = {$jenis_fleksibel}
            AND (
                statusbooking = '{$status_approve}' OR statusbooking = '{$status_dipesan}'
            )
        ";
        $data = $db->createCommand($sql)->queryOne();

        return !empty($data) ? $data : false;
    }

    /**
     * @see Fungsi get data lookup_m
     * @return array, activeQueryRecords
     *
     */
    public function getLookupByType($type = null)
    {
        $result = Lookup::find();

        if ($type) {
            $result->where(['lookup_type' => $type, 'is_active' => 't', 'is_deleted' => 'f']);
        }

        return $result;
    }

    public function actionCariTindakanJenazah($ruangan_id = null)
    {
        $params = Yii::$app->request;
        $term = $params->get('term', '');
        $pendaftaran_id = $params->get('pendaftaran_id', 0);

        $page = $params->get('page', 0);
        $limit = $params->get('limit', 5);
        $offset = $params->get('offset', 0);

        $dataKunjungan = InfoKunjunganRi::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        if (is_null($dataKunjungan)) {
            throw new Exception("Terjadi Kesalahan Pada ID Pendaftaran", 1);
        }

        // $dataTindakan = InfoTarifRs::find()
        //             ->where('tipepaket_id IS NULL')
        //             ->andWhere(['komponentarif_id'=>DocoConstants::KOMPONEN_TARIF])
        //             ->andWhere(['kelaspelayanan_id' => $dataKunjungan->kelaspelayanan_id])
        //             ->andWhere(['penjamin_id' => $dataKunjungan->penjamin_id]);

        $dataTindakan = InfoTarifRs::find()
            ->select("
                        infotarifrs_v.*,
                        array_to_json(ARRAY(
                            SELECT 
                                (
                                    SELECT row_to_json(d) 
                                    FROM (SELECT ins.*) d
                                )::text as item_list 
                            FROM infotarifrs_v ins 
                            WHERE
                            ins.daftartindakan_id = infotarifrs_v.daftartindakan_id AND
                            ins.ruangan_id = '{$ruangan_id}' AND
                            ins.tipepaket_id IS NULL AND
                            ins.komponentarif_id <> 6 AND
                            ins.kelaspelayanan_id = '{$dataKunjungan->kelaspelayanan_id}' AND
                            ins.penjamin_id = '{$dataKunjungan->penjamin_id}'
                        )) as list_komponen
                        ")
            ->where('tipepaket_id IS NULL')
            ->andWhere(['komponentarif_id' => DocoConstants::KOMPONEN_TARIF])
            ->andWhere(['kelaspelayanan_id' => $dataKunjungan->kelaspelayanan_id])
            ->andWhere(['penjamin_id' => $dataKunjungan->penjamin_id]);

        if ($ruangan_id) {
            $dataTindakan->andWhere(['ruangan_id' => $ruangan_id]);
        }

        if ($term) {
            $dataTindakan->andWhere("LOWER(infotarifrs_v.daftartindakan_nama) LIKE LOWER('%" . $term . "%')");
        }

        return $dataTindakan->offset($offset)->limit($limit)->asArray()->all();
    }

    public function actionCariObatJenazah()
    {
        try {
            $params = Yii::$app->request;
            $term = $params->get('term', '');
            $ruangan_id = $params->get('ruangan_id', null);

            $page = $params->get('page', 0);
            $limit = $params->get('limit', 5);
            $offset = $params->get('offset', 0);
            // $data = InfoStokObatAlkesView::find()->where(['ruangan_id' => $ruangan_id ]);
            $data = ObatAlkesFn::find()->select(['obatalkes_nama', 'obatalkes_id', 'ruangan_id', 'obatalkes_kode', 'qty_tersedia', 'satuankecil_id', 'satuankecil as satuankecil_nama', 'jml_hargajual as hargajual', 'jml_harganetto as harganetto', 'jml_margin as jmlmargin', 'jml_discount as jmldiscount', 'jml_ppn as jmlppn', 'persen_ppn as persenppn', 'persen_disc as persendiscount', 'persen_margin as persenmargin'])->where(['ruangan_id' => $ruangan_id]);
            if ($term) {
                $data->andWhere("LOWER(infostokobatalkes_v.obatalkes_nama) LIKE LOWER('%" . $term . "%')");
            }

            $items = $data->offset($offset)->limit($limit)->asArray()->all();

            return $items;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCariLinenJenazah()
    {
        $params = Yii::$app->request;
        $term = $params->get('term', '');
        $page = $params->get('page', 0);
        $limit = $params->get('limit', 5);
        $offset = $params->get('offset', 0);
        try {
            $getData = Barang::find()->where(['is_active' => TRUE, 'is_deleted' => FALSE]);
            if ($term) {
                $getData->andWhere(['ILIKE', 'barang_nama', $term]);
            }
            return $getData->offset($offset)->limit($limit)->asArray()->all();
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionCariAlatJenazah()
    {
        $params = Yii::$app->request;
        $term = $params->get('term', '');
        $page = $params->get('page', 0);
        $limit = $params->get('limit', 5);
        $offset = $params->get('offset', 0);
        try {
            $getData = ObatAlkes::find()->where(['is_active' => TRUE, 'is_deleted' => FALSE]);
            if ($term) {
                $getData->andWhere(['ILIKE', 'obatalkes_nama', $term]);
            }
            return $getData->offset($offset)->limit($limit)->asArray()->all();
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionGetSatuanObatJenazah($obat_id = null)
    {
        try {
            $params = Yii::$app->request;
            $obat_id = $params->get('obat_id', null);

            $dataObat = InfoObatAlkesView::find()->where(['obatalkes_id' => $obat_id])->one();
            if (!empty($dataObat)) {
                return [
                    'data_obat' => [
                        'satuankecil_id' => $dataObat->satuankecil_id,
                        'satuan_kecil' => $dataObat->satuan_kecil
                    ]
                ];
            }
            return [
                'data_obat' => null
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'data_obat' => null
            ];
        } catch (\Exception $e) {
            return [
                'data_obat' => null
            ];
        }
    }

    // public function actionCobaOrderPelayananJenazah($pendaftaran_id)
    // {
    //     $params = Yii::$app->request;
    //     $post = $params->post();
    //     return $this->orderPelayananJenazah($pendaftaran_id,$post);
    // }

    /*
        Trigger Order Pelayanan Jenazah Ketika Transaksi Pulang
    *   return: boolean
    */
    protected function orderPelayananJenazah($pendaftaran_id, $form_attribute)
    {
        try {
            $restJenazah = Yii::$app->docoRest->jenazah;
            $request = $restJenazah->post('order/create', [
                'query' => ['id' => $pendaftaran_id],
                'form_params' => $form_attribute
            ]);
            $response = json_decode($request->getBody(), true);
            return $response;
        } catch (RequestException $e) {
            return false;
        } catch (\yii\base\Exception $e) {
            return false;
        }
    }

    public function actionStopAkomodasi($pendaftaran_id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $payload = new ParamModel;
            $payload->pendaftaran_id = $pendaftaran_id;

            if ($payload->validate()) {
                $dataPendaftaran = Pendaftaran::findOne($pendaftaran_id);
                if ($dataPendaftaran) {
                    $idAdmisi = !empty($dataPendaftaran->pasienadmisi_id) ? $dataPendaftaran->pasienadmisi_id : null;
                    $tglStopAkomodasi = date('Y-m-d H:i:s');
                    $dataPendaftaran->is_stopakomodasi = true;
                    $dataPendaftaran->tgl_stopakomodasi = $tglStopAkomodasi;
                    if (!$dataPendaftaran->validate()) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                            'data' => $dataPendaftaran->errors
                        ]);
                    }
                    // kondisi ketika stop akomodasi ditagihkan atau tidak
                    // merujuk dari perubahan ketika batal stop akomodasi ada pilihan ditagihkan atau tidak, **default true
                    if($dataPendaftaran->is_ditagihkan){
                        $getConfigAkomodasi = (new DocoConstansId)->actionGetAdditional('konfig_stop_akomodasi', true);
                        $gracePeriod = $getConfigAkomodasi[1];

                        // get latest generate accomodation
                        $getLatestTindakan = (new TindakanAkomodasi)->getTodayAkomodasi($idAdmisi, date('Y-m-d'));
                        $executeTindakanAkomodasi = true;

                        /* 
                        * Check if the interval from latest generate accomodation is lower than the grace period range
                        */
                        if (is_null($getLatestTindakan)) {
                            $executeTindakanAkomodasi = true; // execute tindakan akomodasi when there is no accomodation today
                        } else {
                            $dateNow = date('Y-m-d H:i:00');
                            $tglTindakan = date('Y-m-d H:i:00', strtotime($getLatestTindakan['tgl_tindakan']));
                            $interval = round((strtotime($dateNow) - strtotime($tglTindakan)) / 3600, 1);

                            if ($gracePeriod != 0 && $interval < $gracePeriod) {
                                // dont execute tindakan akomodasi when hour interval between today last accomodation and current date is lower than grace periode
                                $executeTindakanAkomodasi = false; 
                            }
                        }

                        if ($executeTindakanAkomodasi) {
                            TindakanAkomodasi::execute(DocoConstants::STOP_AKOMODASI, $idAdmisi, $tglStopAkomodasi);
                        }
                    }
                    $dataPendaftaran->save();
                    $transaction->commit();
                    Yii::$app->cache->delete('pasien-pendaftaran-id-'. DocoHelpers::encrypt($pendaftaran_id));
                    return DocoHelpers::callBack(DocoMessages::KEY_UPDATED, ['text' => 'Akomodasi Berhasil di Stop']);
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, ['text' => DocoMessages::ERR_MESSAGE_PENDAFTARAN_NOT_EXIST]);
                }
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $payload->errors
                ]);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, ['text' => DocoMessages::ERR_MESSAGE]);
        }
    }

    public function actionBatalStopAkomodasi()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $post = $request->post();
        try {

            $modelLogin = new LoginForm();
            $modelLogin->username = Yii::$app->jwt->user->nama_pemakai;
            $modelLogin->password = isset($post['password']) ? $post['password'] : '';
            $payload = new ParamModel;
            $payload->pendaftaran_id = $post['pendaftaran_id'];

            if (!$modelLogin->validate()) {
                return [
                    'status' => 422,
                    'text' => 'Password Salah',
                ];
            }

            if ($payload->validate()) {
                $dataPendaftaran = Pendaftaran::findOne($post['pendaftaran_id']);
                if ($dataPendaftaran) {
                    $dataPendaftaran->is_stopakomodasi = false;
                    $dataPendaftaran->is_ditagihkan = $post['is_ditagihkan'];
                    if ($post['is_ditagihkan'] == 1) {
                        $dataPendaftaran->tgl_stopakomodasi = null; // variable is_ditagihkan adalah variable alasan pembatan stop akomodasi. jika melanjutkan perawatan maka akan diset null untuk tgl stop akomodasi
                    }
                    $dataPendaftaran->alasan_batalstop = $post['alasan_batalstop'];

                    if($post['is_ditagihkan'] && $dataPendaftaran->status_bayar == DocoConstants::LUNAS){
                        return [
                            'status' => 422,
                            'text' => 'Pasien Sudah Melakukan Pembayaran, silahkan Batal Bayar Terlebih dahulu'
                        ];
                    }
                    elseif($post['is_ditagihkan'] && $dataPendaftaran->is_close_bill) {
                        return [
                            'status' => 422,
                            'text' => 'Pasien sudah dilakukan proses Lock Bill.'
                        ];
                    }

                    if (!$dataPendaftaran->validate()) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                            'data' => $dataPendaftaran->errors
                        ]);
                    }
                    $dataPendaftaran->save();
                    $transaction->commit();
                    Yii::$app->cache->delete('pasien-pendaftaran-id-'. DocoHelpers::encrypt($post['pendaftaran_id']));
                    return DocoHelpers::callBack(DocoMessages::KEY_UPDATED, ['text' => 'Batal Stop Akomodasi Berhasil']);
                } else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, ['text' => DocoMessages::ERR_MESSAGE_PENDAFTARAN_NOT_EXIST]);
                }
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $payload->errors
                ]);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, ['text' => DocoMessages::ERR_MESSAGE]);
        }
    }

    private function getHeaderTotal($pendaftaran_id)
    {
        $kelompok_karcis = DocoConstants::VAR_KEL_KRCS;
        $total_tagihan = Yii::$app->db->createCommand("
             SELECT
                SUM(sub_total) AS total_tagihan
            FROM
                infotagihandetail_v
            WHERE
                pendaftaran_id = {$pendaftaran_id}
        ")->queryOne();

        $biaya_admin = Yii::$app->db->createCommand("
             SELECT
                SUM(sub_total) AS biaya_admin
            FROM
                infotagihandetail_v
            WHERE
                pendaftaran_id = {$pendaftaran_id} AND 
                kelompoktindakan_id = {$kelompok_karcis}
        ")->queryOne();


        return [
            'total_tagihan' => $total_tagihan['total_tagihan'],
            'biaya_admin' => $biaya_admin['biaya_admin']
        ];
    }

    private function getTotalHeaderPembayaran($pendaftaran_id, $header)
    {
        $result = [];
        $totalAkomodasi = 0;
        if ($pendaftaran_id) {
            $pasienadmisi_id = $header['pasienadmisi_id'];
            $rincianDetail = RincianPasienDetail2View::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();

            $total_tagihan = $rincianDetail['total_tagihan'];
            $modelPembayaran = Yii::$app->db->createCommand("SELECT 
                COALESCE(SUM(total_administrasi), 0) AS total_administrasi,
                COALESCE(SUM(total_tagihan), 0) AS total_tagihan,
                COALESCE(SUM(total_dijamin), 0) AS total_dijamin,
                COALESCE(SUM(penggunaan_uangmuka), 0) AS penggunaan_uangmuka

                FROM pembayaran_t
                WHERE pendaftaran_id = {$pendaftaran_id}")->queryOne();

            // uang muka
            $uang_muka = BayarUangMuka::find()->where(['pendaftaran_id' => $pendaftaran_id])->sum('jumlah_uangmuka');

            // piutang 
            $piutang = PemberianPiutang::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();

            $total_piutang = ($piutang) ? $piutang->total_piutang : 0;
            $total_administrasi = $modelPembayaran['total_administrasi'];
            $total_terbayar = $modelPembayaran['total_tagihan'];
            $total_asuransi = $modelPembayaran['total_dijamin'];
            $total_uang_muka = ($header['is_pulang']) ? $modelPembayaran['penggunaan_uangmuka'] : $uang_muka;

            $total_asuransi = ($total_asuransi > $total_tagihan) ? $total_tagihan : $total_asuransi;

            $pendaftaranPenjamin = PendaftaranPenjamin::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            $nominal_dijamin = ($pendaftaranPenjamin) ? $pendaftaranPenjamin['nominal_dijamin'] : 0;
            $total_asuransi = ($header['status_bayar'] == DocoConstants::BELUM_LUNAS) ? $nominal_dijamin : $total_asuransi;

            $endDate = date('Y-m-d H:i:s');
            if (!$header['is_stopakomodasi'] && !$header['is_pulang']) {
                $cekAkomodasi = TindakanAkomodasi::execute(DocoConstants::STOP_AKOMODASI, $pasienadmisi_id, $endDate, true);
                $totalAkomodasi = ($cekAkomodasi['total_akomodasi'] != 0)
                    ? $cekAkomodasi['total_akomodasi'] : 0;
            }

            $total_uang_masuk = $total_uang_muka + $total_terbayar + $total_administrasi + $total_piutang;

            $sisa_tagihan = ($total_tagihan - $total_asuransi - $total_uang_masuk) + $totalAkomodasi;

            $sisa_tagihan = ($sisa_tagihan < 0) ? 0 : $sisa_tagihan;

            $result = [
                'total_tagihan' => $total_tagihan,
                'total_asuransi' => $total_asuransi,
                'uang_masuk' => $total_uang_masuk,
                'sisa_tagihan' => $sisa_tagihan,
                'total_akomodasi' => $totalAkomodasi,
                'total_admin' => $total_administrasi
            ];
        }

        return $result;
    }

    /**
     * @controller actionExportDetailRincian
     * @attribute #no_pendaftaran# => Nomor Pendaftaran
     * @attribute #no_rm# => Nomor Rekam Medik 
     * @attribute #nama_pasien# => Nama Pasien
     * @attribute #ruangan# => Nama Ruangan 
     * @attribute #kamar# => Nama Kamar 
     * @attribute #kelas_pelayanan# => kelas pelayanan
     * @attribute #tanggal_masuk# => tanggal masuk
     * @attribute #tabel# => content detail tagihan
     * 
     **/
    public function actionExportDetailRincian($pendaftaran_id)
    {

        $modelVal = new ValidateInjention;
        $modelVal->pendaftaran_id = $pendaftaran_id;
        if (!$modelVal->validate()) {
            return [
                'status' => 422,
                'data' => $modelVal->errors
            ];
        }

        $header = RincianPasienView::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $detail = RincianPasienDetailView::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            // ->andWhere(['<>', 'kelompoktindakan_id', 18])
            ->asArray()->all();

        $grandTotal = 0;
        foreach ($detail as $key => $value) {
            $grandTotal += $value['jumlah_tarif'];
        }

        if (!empty($header['is_pasientitipan_pk'])) {
            if ($header['is_pasientitipan_pk'] == true && $header['is_stoppasientitipan'] == false) {
                $header['kelaspelayanan_nama'] = $header['kelas_ditagihkan_nama'];
            }
        } else if (empty($header['is_pasientitipan_pk'])) {
            if ($header['is_pasientitipan'] == true && $header['is_stoppasientitipan'] == false) {
                $header['kelaspelayanan_nama'] = $header['kelas_ditagihkan_nama'];
            }
        }

        // $grandTotal = round($grandTotal);
        // kebutuhan header
        $no_pendaftaran = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-';
        $no_rekam_medik = isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-';
        $nama_pasien = isset($header['nama_pasien']) ? $header['nama_pasien'] : '-';
        $ruangan_pendaftaran_id = isset($header['ruangan_pendaftaran_id']) ? $header['ruangan_pendaftaran_id'] : '-';
        $ruangan_pendaftaran = ($ruangan_pendaftaran_id == DocoConstants::RUANGAN_PENDAFTARAN_IGD) ? $header['ruangan_admisi'] : $header['ruangan_pendaftaran'];

        $kamar = isset($header['kamar']) ? $header['kamar'] : '-';
        $tempat_tidur = isset($header['tempat_tidur']) ? $header['tempat_tidur'] : '-';
        $kelas = isset($header['kelaspelayanan_nama']) ? $header['kelaspelayanan_nama'] : '-';
        $ruangan = $kamar . '/' . $tempat_tidur . '/' . $kelas;
        $kelas_admisi = isset($header['kelas_admisi']) ? $header['kelas_admisi'] : '-';
        $tgl_masuk = isset($header['tgl_masuk']) ? date('d M Y', strtotime($header['tgl_masuk'])) : '-';
        $jam_masuk = isset($header['jam_masuk']) ? date('H:i', strtotime($header['jam_masuk'])) : '';

        $tgl_keluar = isset($header['tgl_keluar']) ? date('d M Y', strtotime($header['tgl_keluar'])) : '-';
        $jam_keluar = isset($header['jam_keluar']) ? date('H:i', strtotime($header['jam_keluar'])) : '';

        $tgl_admisi = $tgl_masuk . ' ' . $jam_masuk . ' s/d ' . $tgl_keluar . ' ' . $jam_keluar;
        $prefix = isset($header['nama_depan']) ? $header['nama_depan'] : '';

        $nama_lengkap = $prefix . ' ' . $nama_pasien;

        $groupedData = $this->group_by("kelompoktindakan_nama", $detail);


        if (!empty($header)) {
            $countData = count($header);
            $print = new DocoPrint();
            $print->attributes = [
                '#tanggal_masuk#' => $tgl_admisi,
                '#no_rm#' => $no_rekam_medik,
                '#nama_pasien#' => $nama_lengkap,
                '#no_pendaftaran#' => $no_pendaftaran,
                '#ruangan#' => $ruangan,
                '#kelas_pelayanan#' => $kelas_admisi,
                '#tabel#' => $this->renderPartial('detail_rincian', [
                    'header' => $header,
                    'data' => $groupedData,
                    'grandTotal' => $grandTotal
                ]),
            ];
        }

        $print->Output();
    }

    /**
     * @controller actionExportSuratKeteranganKelahiran
     * @attribute #no_pendaftaran# => No Pendaftaran
     * @attribute #nama_depan# => Nama_Depan
     * @attribute #nama_pasien# => Nama_Pasien
     * @attribute #nama_ibu# => Nama Ibu
     * @attribute #nama_ayah# => Nama Ayah
     * @attribute #alamat_pasien# => Alamat Pasien
     * @attribute #tanggal_lahir# => Tanggal Lahir
     * @attribute #gelar_depan_dokter# => Gelar Depan Dokter
     * @attribute #dokter_dpjp# => Dokter Dpjp
     * @attribute #nama_lengkap# => Nama Lengkap
     * @attribute #nama_dokter# => Nama Dokter
     * @attribute #hari_lahir# => Hari Lahir
     * @attribute #jam_lahir# => Jam Lahir
     * @attribute #berat_badan# => Berat Badan
     * @attribute #tinggi_badan# => Tinggi Badan
     * @attribute #tanggal_skr# => Tanggal Skr 
     * @attribute #hari_skr# => Hari Skr 
     * @attribute #jenis_kelamin# => Jenis Kelamin 
     * @attribute #jenis_kelamin_e# => Jenis Kelamin E
     * @attribute #dpjp_ibu_nama# => DPJP Ibu Nama
     * 
     **/
    public function actionExportSuratKeteranganKelahiran($pendaftaran_id)
    {
        $modelVal = new ValidateInjention;
        $modelVal->pendaftaran_id = $pendaftaran_id;
        if (!$modelVal->validate()) {
            return [
                'status' => 422,
                'data' => $modelVal->errors
            ];
        }

        $header = KelahiranBayiView::find()->where(['pendaftaranbaru_id' => $pendaftaran_id])->one();

        // kebutuhan header
        $no_pendaftaran     = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-';
        $nama_depan         = isset($header['nama_depan']) ? $header['nama_depan'] : '';
        $nama_pasien        = isset($header['nama_pasien']) ? $header['nama_pasien'] : '-';
        $nama_ibu           = isset($header['nama_ibu']) ? $header['nama_ibu'] : '-';
        $nama_ayah          = isset($header['nama_ayah']) ? $header['nama_ayah'] : '-';
        $alamat_pasien      = isset($header['alamat_pasien']) ? $header['alamat_pasien'] : '-';
        $tanggal_lahir      = isset($header['tanggal_lahir']) ? date('d M Y', strtotime($header['tanggal_lahir'])) : '-';
        $jam_lahir          = isset($header['tanggal_lahir']) ? date('H:i', strtotime($header['tanggal_lahir'])) : '';
        $berat_badan        = isset($header['berat_badan']) ? $header['berat_badan'] : '-';
        $tinggi_badan       = isset($header['tinggi_badan']) ? $header['tinggi_badan'] : '-';
        $gelar_depan_dokter = isset($header['gelar_depan_dokter']) ? $header['gelar_depan_dokter'] : '';
        $dokter_dpjp        = isset($header['dokter_dpjp']) ? $header['dokter_dpjp'] : '-';
        $dpjp_ibu_nama      = isset($header['dpjp_ibu_nama']) ? $header['dpjp_ibu_nama'] : '-';
        $jenis_kelamin      = isset($header['jenis_kelamin']) ? $header['jenis_kelamin'] : '-';
        $tanggal_skr        = date('d M Y');

        if ($jenis_kelamin == 'Laki - Laki') {
            $jenis_kelamin_e = 'Male Infant';
        } else if ($jenis_kelamin == 'Perempuan') {
            $jenis_kelamin_e = 'Female Infant';
        } else {
            $jenis_kelamin_e    = '-';
        }

        $daftar_hari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => "Jum'at",
            'Saturday'  => 'Sabtu'
        ];
        $nama_hari    = date('l', strtotime($header['tanggal_lahir']));
        $hari_lahir   = $daftar_hari[$nama_hari];

        $nama_hari    = date('l', strtotime($tanggal_skr));
        $hari_skr     = $daftar_hari[$nama_hari];

        $nama_lengkap = $nama_depan . ' ' . $nama_pasien;
        $nama_dokter  = $gelar_depan_dokter . ' ' . $dokter_dpjp;

        if (!empty($header)) {
            $countData = count($header);
            $print = new DocoPrint();
            $print->attributes = [
                '#no#' => (empty($header['no_peneng']) ? '-' : $header['no_peneng']) . '/' . DocoHelpers::integerToRoman(date('m')) . '/' . date('Y'),
                '#no_pendaftaran#'     => $no_pendaftaran,
                '#nama_depan#'         => $nama_depan,
                '#nama_pasien#'        => $nama_pasien,
                '#nama_ibu#'           => $nama_ibu,
                '#nama_ayah#'          => $nama_ayah,
                '#alamat_pasien#'      => $alamat_pasien,
                '#tanggal_lahir#'      => $tanggal_lahir,
                '#gelar_depan_dokter#' => $gelar_depan_dokter,
                '#dokter_dpjp#'        => $dokter_dpjp,
                '#nama_lengkap#'       => '',
                '#nama_dokter#'        => $nama_dokter,
                '#hari_lahir#'         => $hari_lahir,
                '#jam_lahir#'          => $jam_lahir,
                '#berat_badan#'        => $berat_badan,
                '#tinggi_badan#'       => $tinggi_badan,
                '#tanggal_skr#'        => $tanggal_skr,
                '#hari_skr#'           => $hari_skr,
                '#jenis_kelamin#'      => $jenis_kelamin,
                '#jenis_kelamin_e#'    => $jenis_kelamin_e,
                '#dpjp_ibu_nama#'      => $dpjp_ibu_nama,
                '#ttd_dokter#'         => Pegawai::signatureEmployee($header->dokter_dpjp_id),
            ];
        }

        $print->Output();
    }

    /**
     * @controller actionExportSuratR2bbl
     * @attribute #no_rm# => no_rm
     * @attribute #no_registrasi# => no_registrasi
     * @attribute #tgl_registrasi_jam# => tgl_registrasi_jam
     * @attribute #nama_lengkap# => nama_lengkap
     * @attribute #nama_keluarga# => nama_keluarga
     * @attribute #tempat_lahir# => tempat_lahir
     * @attribute #tanggal_lahir# => tanggal_lahir
     * @attribute #kebangsaan# => kebangsaan
     * @attribute #no_peneng# => no_peneng
     * @attribute #umur# => umur
     * @attribute #alamat_tetap# => alamat_tetap
     * @attribute #telp# => telp
     * @attribute #nama_dan_alamat_pengirim# => nama_dan_alamat_pengirim
     * @attribute #jenis_kelamin# => jenis_kelamin
     * @attribute #suku# => suku
     * @attribute #agama# => agama
     * @attribute #warna_kulit# => warna_kulit
     * @attribute #berat_badan# => berat_badan
     * @attribute #panjang_badan# => panjang_badan
     * @attribute #gol_darah# => gol_darah
     * @attribute #diet# => diet
     * @attribute #alergi# => alergi
     * 
     **/
    public function actionExportSuratR2bbl($pendaftaran_id)
    {

        $modelVal = new ValidateInjention;
        $modelVal->pendaftaran_id = $pendaftaran_id;
        if (!$modelVal->validate()) {
            return [
                'status' => 422,
                'data' => $modelVal->errors
            ];
        }

        $header = KelahiranBayiView::find()->where(['pendaftaranbaru_id' => $pendaftaran_id])->one();

        // kebutuhan header
        $no_rekam_medik   = isset($header['no_rekam_medik']) ? $header['no_rekam_medik'] : '-';
        $no_pendaftaran       = isset($header['no_pendaftaran']) ? $header['no_pendaftaran'] : '-';
        $tgl_pendaftaran      = isset($header['tgl_pendaftaran']) ? date('d M Y - H:i', strtotime($header['tgl_pendaftaran'])) : '-';
        $nama_depan           = isset($header['nama_depan']) ? $header['nama_depan'] : '';
        $nama_pasien          = isset($header['nama_pasien']) ? $header['nama_pasien'] : '-';
        $nama_ayah            = isset($header['nama_ayah']) ? $header['nama_ayah'] : '-';
        $tempat_lahir         = isset($header['tempat_lahir']) ? $header['tempat_lahir'] : '-';
        $tanggal_lahir        = isset($header['tanggal_lahir']) ? date('d M Y', strtotime($header['tanggal_lahir'])) : '-';
        $warga_negara         = isset($header['warga_negara']) ? $header['warga_negara'] : '-';
        $no_peneng            = isset($header['no_peneng']) ? $header['no_peneng'] : '-';
        $umur                 = isset($header['umur']) ? $header['umur'] : '-';
        $alamat_pasien        = isset($header['alamat_pasien']) ? $header['alamat_pasien'] : '-';
        $no_telepon_pasien    = isset($header['no_telepon_pasien']) ? $header['no_telepon_pasien'] : '-';
        $nama_alamat_pengirim = isset($header['nama_alamat_pengirim']) ? $header['nama_alamat_pengirim'] : '-';
        $jenis_kelamin        = isset($header['jenis_kelamin']) ? $header['jenis_kelamin'] : '-';
        $nama_suku            = isset($header['nama_suku']) ? $header['nama_suku'] : '-';
        $agama                = isset($header['agama']) ? $header['agama'] : '-';
        $warna_kulit          = isset($header['warna_kulit']) ? $header['warna_kulit'] : '-';
        $berat_badan          = isset($header['berat_badan']) ? $header['berat_badan'] : '-';
        $tinggi_badan         = isset($header['tinggi_badan']) ? $header['tinggi_badan'] : '-';
        $golongandarah        = isset($header['golongandarah']) ? $header['golongandarah'] : '-';
        $diet                 = isset($header['diet']) ? $header['diet'] : '-';
        $alergi               = isset($header['alergi']) ? $header['alergi'] : '-';
        $gelar_depan_dokter = isset($header['gelar_depan_dokter']) ? $header['gelar_depan_dokter'] : '';
        $dokter_dpjp        = isset($header['dokter_dpjp']) ? $header['dokter_dpjp'] : '-';

        $nama_lengkap = $nama_depan . ' ' . $nama_pasien;
        $nama_dokter  = $gelar_depan_dokter . ' ' . $dokter_dpjp;

        if (!empty($header)) {
            $countData = count($header);
            $print = new DocoPrint();
            $print->attributes = [
                '#no_rm#'                    => $no_rekam_medik,
                '#no_registrasi#'            => $no_pendaftaran,
                '#tgl_registrasi_jam#'       => $tgl_pendaftaran,
                '#nama_lengkap#'             => $nama_lengkap,
                '#nama_keluarga#'            => $nama_ayah,
                '#tempat_lahir#'             => $tempat_lahir,
                '#tanggal_lahir#'            => $tanggal_lahir,
                '#kebangsaan#'               => $warga_negara,
                '#no_peneng#'                => $no_peneng,
                '#umur#'                     => $umur,
                '#alamat_tetap#'             => $alamat_pasien,
                '#telp#'                     => $no_telepon_pasien,
                '#nama_dan_alamat_pengirim#' => $nama_alamat_pengirim,
                '#jenis_kelamin#'            => $jenis_kelamin,
                '#suku#'                     => $nama_suku,
                '#agama#'                    => $agama,
                '#warna_kulit#'              => $warna_kulit,
                '#berat_badan#'              => $berat_badan,
                '#panjang_badan#'            => $tinggi_badan,
                '#gol_darah#'                => $golongandarah,
                '#diet#'                     => $diet,
                '#alergi#'                   => $alergi,
                '#nama_dokter#'              => $nama_dokter,
            ];
        }

        $print->Output();
    }

    private function group_by($key, $data)
    {
        $result = array();

        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        return $result;
    }

    public function actionStopPasienTitipan($pendaftaran_id)
    {
        $titipan = true;
        $sudahStop = false;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $payload = new ParamModel;
            $payload->pendaftaran_id = $pendaftaran_id;

            if ($payload->validate()) {
                $pindahKamar = PindahKamar::find()
                    ->where(['pendaftaran_id' => $pendaftaran_id])
                    ->orderBy(['pindahkamar_id' => SORT_DESC])
                    ->one();

                $pasienAdmisi = PasienAdmisi::find()
                    ->where(['pendaftaran_id' => $pendaftaran_id])
                    ->orderBy(['pasienadmisi_id' => SORT_DESC])
                    ->one();

                if ($pindahKamar && $pasienAdmisi) {
                    if ($pindahKamar->is_pasientitipan == true && $pasienAdmisi->is_pasientitipan == true) {
                        if ($pindahKamar->is_stoptitipan == false) {
                            TindakanAkomodasi::execute(
                                DocoConstants::STOP_TITIPAN,
                                $pindahKamar->pasienadmisi_id,
                                date('Y-m-d H:i:s', strtotime('NOW'))
                            );

                            $pindahKamar->is_stoptitipan = true;
                            $pindahKamar->save(false);
                            $pasienAdmisi->is_pasientitipan = false;
                            $pasienAdmisi->is_stoptitipan = true;
                            $pasienAdmisi->save(false);
                            $transaction->commit();

                            return DocoHelpers::callBack(
                                DocoMessages::KEY_UPDATED,
                                [
                                    'text' => 'Kelas Titipan Berhasil Dihentikan.'
                                ]
                            );
                        } else {
                            $sudahStop = true;
                        }
                    }
                } else {
                    $pasienAdmisi = PasienAdmisi::find()
                        ->where(['pendaftaran_id' => $pendaftaran_id])
                            ->andWhere(['pasienbatalperiksa_id' => null])
                            ->orderBy(['pasienadmisi_id' => SORT_DESC])->one();
                    if ($pasienAdmisi) {
                        if ($pasienAdmisi->is_pasientitipan == true) {
                            $pasienAdmisi->is_pasientitipan = false;
                            if ($pasienAdmisi->is_stoptitipan == false) {
                                TindakanAkomodasi::execute(
                                    DocoConstants::STOP_TITIPAN,
                                    $pasienAdmisi->pasienadmisi_id,
                                    date('Y-m-d H:i:s', strtotime('NOW'))
                                );

                                $pasienAdmisi->is_stoptitipan = true;
                                $pasienAdmisi->save(false);
                                $transaction->commit();

                                return DocoHelpers::callBack(
                                    DocoMessages::KEY_UPDATED,
                                    [
                                        'text' => 'Kelas Titipan Berhasil Dihentikan.'
                                    ]
                                );
                            } else {
                                $sudahStop = true;
                            }
                        }
                    }
                }

                if ($sudahStop == true) {
                    return DocoHelpers::callBack(
                        DocoMessages::KEY_ERR_VALIDATION,
                        [
                            'text' => Yii::t('app', 'Kelas titipan pasien sudah di stop.')
                        ]
                    );
                }

                return DocoHelpers::callBack(
                    DocoMessages::KEY_ERR_VALIDATION,
                    [
                        'text' => Yii::t('app', 'Pasien tidak menggunakan kelas titipan.')
                    ]
                );
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $payload->errors
                ]);
            }
        } catch (Exception $e) {
            Yii::warning($e->getMessage());
            Yii::warning($e->getFile());
            Yii::warning($e->getLine());
            $transaction->rollBack();

            return DocoHelpers::callBack(
                DocoMessages::KEY_ERR_CUSTOM,
                [
                    'text' => DocoMessages::ERR_MESSAGE
                ]
            );
        }
    }

    /**
     * @todo Fungsi untuk generate total administrasi tagihan ranap
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    private function generateTotalAdministrasi($idPendaftaran, $totalTagihan)
    {
        $persenAdmin = 0;
        $maxAdmin = 0;
        $penjualanResepAdmin = 0;
        $totalAdmin = 0;
        $type = 'pelayanan';

        $penjualanResep = (new \yii\db\Query())
            ->from('penjualanresep_t')
            ->where([
                'pendaftaran_id' => $idPendaftaran,
                'is_active' => true,
                'is_deleted' => false
            ]);
        $penjualanResepAdmin = $penjualanResep->sum('biayaadministrasi');
        $penjualanResepAdmin = $penjualanResepAdmin ? $penjualanResepAdmin : 0;

        $konfigAdmin = (new \yii\db\Query())
            ->from('konfigsystem_k')
            ->select([
                'adm_persen',
                'adm_tindakan_id'
            ])
            ->one();

        if ($penjualanResepAdmin === 0 && $konfigAdmin['adm_persen'] === 0) {
            return $totalAdmin;
        }

        $pasien = (new \yii\db\Query())
            ->from('infopasienri_v')
            ->select([
                'penjamin_id',
                'is_pasientitipan',
                'is_stoptitipan',
                'pindahkamar_id',
                'is_pasientitipan_pk',
                'is_stoppasientitipan',
                'kelaspelayanan_id',
                'kelas_ditagihkan_id'
            ])
            ->where([
                'pendaftaran_id' => $idPendaftaran
            ])
            ->one();

        if (empty($pasien)) {
            return $totalAdmin;
        }

        $idPenjamin = $pasien['penjamin_id'];
        $idKelas = $pasien['kelaspelayanan_id'];

        if ($pasien['pindahkamar_id']) {
            if ($pasien['is_stoppasientitipan'] == false) {
                if ($pasien['is_pasientitipan_pk'] == true) {
                    $idKelas = $pasien['kelas_ditagihkan_id'];
                }
            }
        } else {
            if ($pasien['is_stoptitipan'] == false) {
                if ($pasien['is_pasientitipan'] == true) {
                    $idKelas = $pasien['kelas_ditagihkan_id'];
                }
            }
        }

        $tarifAdmin = Yii::$app->db->createCommand('
            SELECT 
                daftartindakan_id,
                komponentarif_id,
                penjamin_id,
                kelaspelayanan_id,
                daftartindakan_nama,
                harga_tariftindakan
            FROM totaltarifnaikkelas_fn(:idPenjamin,:idKelas,:type) 
            WHERE daftartindakan_id = :idTindakan
        ')
            ->bindParam(':idPenjamin', $idPenjamin)
            ->bindParam(':idKelas', $idKelas)
            ->bindParam(':type', $type)
            ->bindParam(':idTindakan', $konfigAdmin['adm_tindakan_id'])
            ->queryOne();

        if (!empty($tarifAdmin)) {
            $maxAdmin = $tarifAdmin['harga_tariftindakan'];
        }

        // Perhitugan total administrasi
        // Maksimum total tagihan admin = total tagihan * persen admin / 100 (maksimum tarif admin)
        // Total admin = maksimum total tagihan admin + total penjualan resep admin
        $persenAdmin = $konfigAdmin['adm_persen'];

        if ($persenAdmin > 0) {
            $totalAdmin = $totalTagihan * $persenAdmin / 100;

            if ($totalAdmin > $maxAdmin) {
                $totalAdmin = $maxAdmin;
            }
        }

        return $totalAdmin + $penjualanResepAdmin;
    }

    public function saveRujukanPasien($dataPost)
    {
        $pendaftaran_id = !empty($dataPost['pendaftaran_id']) ? $dataPost['pendaftaran_id'] : null;
        $model = RujukanPulang::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        if (is_null($model)) {
            $model = new RujukanPulang();
        }
        $model->attributes = $dataPost;
        if (!$model->validate()) {
            throw new \yii\db\Exception('Gagal Validasi Rujukan Pasien', $model->getErrors(), 422);
        }
        if (!$model->save()) {
            throw new \yii\db\Exception('Gagal Simpan Rujukan Pasien', $model->getErrors(), 422);
        }
        return $model;
    }

    public function actionSetDokter()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id');
            $dokter_id = $request->post('dokter_id');
            $tgl_masukperiksa = $request->post('tgl_masukperiksa');

            $pasien_admisi = PasienAdmisi::find()->where(compact('pendaftaran_id'))->one();
            if ($pasien_admisi) {
                $pasien_admisi->pegawai_id = $dokter_id;
                $pasien_admisi->tgl_masukperiksa = $tgl_masukperiksa;
                $pasien_admisi->status_ranap = DocoConstants::STATUS_RANAP_PERIKSA;
                if ($pasien_admisi->save(false)) {
                    $rmService = new RmService;
                    $respn = $rmService->periksa([
                        'pendaftaran_id' => [$pendaftaran_id],
                        'pasienadmisi_id' => [$pasien_admisi->pasienadmisi_id],
                        'status' => DocoConstants::MONITORING_RM_ISSUE,
                    ]);
                }
                return [
                    'message' => 'Data Berhasil di simpan',
                    'data' => $pasien_admisi
                ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /* excel bg-proc */
    public function actionExportExcelBgprocess()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        (new InternalService)->sendTo([
            'Sirs' => [
                'ListDataPasienRawatInapExport' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'ListPasienRawatInapExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'ext' => '.xlsx',
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [
                'ListPasienRawatInapUpload' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filename' => 'informasi-pasien-rawat-inap',
                    'ext' => '.xlsx',
                ]
            ]
        ], true);
        return [
            'randString' => $randString
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        $filePath = $request->get('filePath', null);
        if ($request->isPost)
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;

            $path = "uploads/";

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

    public function actionGetHistoryVisit($pendaftaran_id)
    {
        $model = new CpptView;
        $getCpptDokter = (new CpptView)->find()->select([
            "DATE(tgl_cppt) as tgl_cppt",
            "string_agg(distinct(nama_pegawai), ', ') as dokter_cppt"
        ])->where([
            'kelompokpegawai_id' => DocoConstants::KELOMPOK_MEDIS,
            'pendaftaran_id' => $pendaftaran_id,
        ])->andWhere([
            'not', [
                'pasienadmisi_id' => null,
            ],
        ])->groupBy([
            'DATE(tgl_cppt)',
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $getCpptDokter);
        $totalRecord = $query->count();

        return (new DocoHelpers)->response([
            'recordsFiltered' => $totalRecord,
            'recordsTotal' => $totalRecord,
            'data' => $getCpptDokter->asArray()->all(),
            'message' => 'Success Retrieve History Visit',
        ]);
    }

    public function actionGetHistoryVisitHeader($pendaftaran_id) {
        $getPatientInformation = (new WorklistPasien)->find()->select([
            'no_rekam_medik',
            'nama_pasien',
            'jk as jenis_kelamin',
            'no_pendaftaran',
            'tgl_pendaftaran',
            'nama_pegawai as dokter_dpjp',
        ])->where(['pendaftaran_id' => $pendaftaran_id, 'jenis' => 'RI'])->asArray()->one();

        return (new DocoHelpers)->response([
            'data' => $getPatientInformation,
            'message' => 'Success Retrieve Patient Admission Information',
        ]);
    }
}
