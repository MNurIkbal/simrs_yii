<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 13:25
 * @Mod: Ardi Pratama Septiadi
 * @Modif: Setyabudi
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\components\BpjsController;
use app\modules\v1\components\Penomoran;

use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\KeluargaPasien;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\InfRencanaKontrol;
use app\modules\v1\models\KonsulPoli;
use app\modules\v1\models\InfoDataPendaftaranView;
use app\modules\v1\models\TarifTotalRsFn;
use app\modules\v1\models\TarifKomponenRsFn;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\PenanggungBiayaView;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyAsuransiPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungBiayaView;
use app\modules\v1\models\SyPenanggungJawabView;
use app\modules\v1\models\SyPenjaminView;
use app\modules\v1\models\SyncsantoyusupR;
use app\modules\v1\models\LaporanKunjunganRawatJalanView;
use app\modules\v1\models\PenomoranK;
use app\modules\v1\models\InfoJadwalDokterView;
use app\modules\v1\models\Antrian;

use app\modules\v1\payload\TipePasien;
use app\modules\v1\payload\Kunjungan;
use app\modules\v1\payload\Rujukan as PayloadRujukan;
use app\modules\v1\payload\Pasien as PayloadPasien;
use app\modules\v1\payload\Antrian as PayloadAntrian;
use app\modules\v1\payload\MultiCarabayar as PayloadMultiPayer;
use app\modules\v1\payload\PjPasien;
use app\modules\v1\payload\AsuransiForm;
use app\modules\v1\payload\BpjsNewForm;
use app\modules\v1\payload\KeluargaPasienForm;
use app\modules\v1\payload\PenanggungBiayaForm;

use app\modules\v1\cache\Cache;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use SirsCore\features\IntegrasiAkunting;
use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;
use Doco\components\DocoConstansId;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Traits\AutoPlafonTrait;
use app\modules\v1\models\CaraBayar;

class PendaftaranRajalController extends BpjsController
{
    use AutoPlafonTrait;

    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    static protected $_url = 'on/sinkronisasi/sync';
    static protected $_restSerconn;
    static protected $_headers;
    const PENOMORAN_RJRD = 193;
    const K_P_STYP = 69;
    const PENOMORAN_PASIEN = 190;

    public function init()
    {
        self::$_restSerconn = Yii::$app->serconn->guzzle();
        self::$_headers = Yii::$app->request->headers;
    }

    public $messageBroker = [
        'save-pendaftaran' => [
            'services' => [
                'Mhg' => [
                    'CreateMasterPatient' => [
                        'result' => true,
                        'successProcess'=>true,
                        'state' => 'create',
                    ],
                    'UpdateAppointment' => [
                        'result' => true,
                        'successProcess'=>true,
                    ]
                ],
                'Sirs' => [
                    'AddAntrianOffJkn' => [
                         'result' => true,
                         'successProcess'=>true,
                         'taskid' => [],
                    ],
                    'StatusUpdateJkn' => [
                        'result' => true,
                        'successProcess'=>true,
                        'taskid' => ['1' ,'2', '3'],
                        'update_from' => 'save_pendaftaran'
                    ],
                    'AddAntrianOnJkn' => [
                         'result' => true,
                         'successProcess'=>true,
                         'taskid' => ['1', '2', '3'],
                         'setujui' => false
                    ],
                    'StatusUpdatePembantaran' => [
                        'result' => true,
                        'successProcess' => true,
                        'update_from' => 'save_pendaftaran'
                    ]
                ],
                'Satusehat' => [
                    'Encounter' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],

    ];

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

    public function actionSavePendaftaran()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $isBpjs = false;
        $pasienBaru = true;
        $isKonsul = false;
        $isMultiPayer = false;

        /** Validasi Error */
        $errorParse = [];
        /** Tindakan Karcis */
        $listTindakan = $tmp = $sepNew = $komponen = [];
        $listTagihan = [];
        /** allow Bpjs */
        $allowBpjs = $request->post('allow_bpjs',false);
        /**
         * @var app\modules\v1\payload\PayloadRujukan
         */
        $rujukan = [];

        /**
         * @var app\modules\v1\payload\PjPasien
         */
        $pjPasien = [];

        /**
         *  @var app\modules\v1\payload\AsuransiForm
        */
        $pjPasien = [];

        /**
         * @var app\modules\v1\payload\PayloadAntrian
         */
        $dataAntrian = [];

        /**
         * @var app\modules\v1\payload\AsuransiForm
         */
        $asuransi = [];

        /**
         * @var app\modules\v1\payload\BpjsNewForm
         */
        $bpjs = [];

        /** response Pendaftaran */
        $resPasien = [];

        /** response Pasien */
        $resPendaftaran = [];

        /** Antrian JKN for insert to antrianjkn_r */
        $antrianJkn = [];

        $payload = new TipePasien;
        $payloadKunjungan = new Kunjungan;
        $payloadAntrian = new PayloadAntrian;
        $pasienPayLoad = new PayloadPasien;
        $payloadMultipayer = new PayloadMultiPayer;
        $pasienPayLoad->scenario = "pendaftaran-rajal";
        $payloadKunjungan->scenario = "pendaftaran-rajal";

        $pasienPayLoad->attributes = !empty($post['pasien']) ? $post['pasien'] : [];
        $pasienPayLoad->namadepan = ArrayHelper::getValue($post, 'pasien.namadepan');
        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        $payloadMultipayer->isMultipayer = $isMultiPayer;

        if(!empty($post['buatjanjipoli_id'])){
           $janjiId = $post['buatjanjipoli_id'];
           $isKonsul = true;
           $dataKonsul['buatjanjipoli_id'] = $janjiId;
           /** Status Janji == true (Daftar BPJS Hari sama) */
           if($payload->carabayar_id == DocoConstants::CARA_BAYAR_BPJS && $post['status_janji'] == true) {
               return $this->actionSetKunjunganPoli($dataKonsul, false);
           }
        }

        if (!empty($post['asuransi'])) {
            $payloadAsuransi = new AsuransiForm;
            $payloadAsuransi->attributes = $post['asuransi'];
            $payloadAsuransi->tgl_konfirmasi = !empty($payloadAsuransi->tgl_konfirmasi) ? date('Y-m-d', strtotime($payloadAsuransi->tgl_konfirmasi)) : null;
            $payloadAsuransi->penjamingrade_id = isset($payloadAsuransi['penjamingrade_id']) && !empty($payloadAsuransi['penjamingrade_id']) ? $payloadAsuransi['penjamingrade_id'] : null;
            $asuransi = $payloadAsuransi->attributes;
            if (!$payloadAsuransi->validate()) $errorParse['asuransi'] = $payloadAsuransi->errors;
        }

        $payloadBpjs = [];
        if (!empty($post['bpjs'])) {
            $payloadBpjs = new BpjsNewForm;
            $payloadBpjs->attributes = $post['bpjs'];
            $payload->no_rekam_medik = $payloadBpjs->no_rekam_medik;
            $pasienPayLoad->nopeserta_bpjs = $payloadBpjs->no_kartu;
            $isBpjs = true;
            /** Statis Rujukan WIP */
            $diagnosa = Diagnosa::find()->select([
                'diagnosa_id'
            ])->where([
                'diagnosa_kode' => $payloadBpjs->diagnosa_awal
            ])->one();
            $payload->asalrujukan_id = 2;
            $post['rujukan'] = [
                'rujukandari_id' => 1,
                'no_rujukan' => $payloadBpjs->no_rujukan ? $payloadBpjs->no_rujukan : $payloadBpjs->no_kartu,
                'nama_perujuk' => 'BPJS',
                'tanggal_rujukan' => date('Y-m-d', strtotime($payloadBpjs->tanggal_rujukan)),
                'kodediagnosa_rujukan' => $payloadBpjs->diagnosa_awal,
                'diagnosa_id' => !empty($diagnosa->diagnosa_id) ? $diagnosa->diagnosa_id : null,
            ];
            if($allowBpjs) {
                $payloadBpjs->scenario = 'unauth';
            }
            $jeniskunjungan = $payloadBpjs->asal_rujukan == 1 ? DocoConstants::RUJUK_FKTP_JKN : DocoConstants::RUJUK_RS_JKN;

            if (!$payloadBpjs->validate()) $errorParse['bpjs'] = $payloadBpjs->errors;
        }

        /** Set Rujukan */
        if (!empty($post['rujukan'])) {
            $payloadRujukan = new PayloadRujukan;
            $payloadRujukan->attributes = $post['rujukan'];
            $payloadRujukan->asalrujukan_id = $payload->asalrujukan_id;
            $payloadRujukan->tanggal_rujukan = date('Y-m-d', strtotime($payloadRujukan->tanggal_rujukan));
            if (!$payloadRujukan->validate()) $errorParse['rujukan'] = $payloadRujukan->errors;
            $rujukan = $payloadRujukan->attributes;
        }

        if (!empty($post['pj_pasien'])) {
            $payloadPjPasien = new PjPasien;
            $payloadPjPasien->attributes = $post['pj_pasien'];
            $payloadPjPasien->pj_tanggal_lahir = !empty($payloadPjPasien->pj_tanggal_lahir)
                                        ? date('Y-m-d',strtotime($payloadPjPasien->pj_tanggal_lahir)) : null;
            $pjPasien = $payloadPjPasien->attributes;
            if (!$payloadPjPasien->validate()) $errorParse['pj_pasien'] = $payloadPjPasien->errors;
        }

        if (!empty($payload->no_rekam_medik) || isset($post['pasien']['no_rekam_medik'])) {
            $noRm = !empty($payload->no_rekam_medik) ? $payload->no_rekam_medik : $post['pasien']['no_rekam_medik'];
            $pasien = Pasien::find()->where([
                'no_rekam_medik' => $noRm
            ])->asArray()->one();
            $pasienPayLoad->attributes = $pasien;
            if($payload->carabayar_id != DocoConstants::VAR_ID_CARABAYAR_BPJS) {
                if (empty($pasien)) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Pasien tidak ditemukan'
                    ];
                }
                $pasienPayLoad->scenario = "pendaftaran-pasien-lama";
                $pasienBaru = false;
                /** cek status kunjungan sebelumnya */
                if(!$pasienBaru && !isset($janjiId)){
                    $cekPulang = Pendaftaran::getKunjunganPasienOne(
                        $pasienPayLoad->no_rekam_medik,
                        $payloadKunjungan->ruangan_id,
                        [DocoConstants::STATUS_PERIKSA_PULANG, DocoConstants::STATUS_PERIKSA_BTL_PERIKSA,DocoConstants::STATUS_PERIKSA_BTL_KONSUL, DocoConstants::STATUS_PERIKSA_BTL_KUNJ] // [4,402,411,628]
                     );
                    if($cekPulang && $cekPulang['pasienpulang_id'] == null) {
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Pasien masih dalam pelayanan di unit ' . $cekPulang['rua_nama']
                        ];
                    }
                }
            } else if ($payload->carabayar_id == DocoConstants::VAR_ID_CARABAYAR_BPJS) {
                if (!empty($pasien)) {
                    $pasienPayLoad->scenario = "pendaftaran-pasien-lama";
                    $pasienBaru = false;
                    // update no_telp
                    if(isset($post['bpjs']['no_telp']) && !empty($post['bpjs']['no_telp']) && $pasien['no_telepon_pasien'] != $post['bpjs']['no_telp']) {
                        Pasien::updateAll([
                            'no_telepon_pasien' => $post['bpjs']['no_telp']
                        ], [
                            'pasien_id' => $pasien['pasien_id'],
                        ]);
                    }
                }
            }
        }

        //** MultiPayer */
        if($payload->is_multi_payer == true && !empty($post['multi_payer'])) {
            $dataMultiPayer = $post['multi_payer'];
            $payloadMultipayer->attributes = $dataMultiPayer;
            $payloadMultipayer->isMultipayer = true;
            $payloadMultipayer->add_tgl_konfirmasi_1 = !empty($payloadMultipayer->add_tgl_konfirmasi_1) ? date('Y-m-d', strtotime($payloadMultipayer->add_tgl_konfirmasi_1)) : null;
            $payloadMultipayer->add_namapemilikasuransi_1 = isset($dataMultiPayer['add_namapemilikasuransi_1']) && !empty($dataMultiPayer['add_namapemilikasuransi_1']) ? $dataMultiPayer['add_namapemilikasuransi_1'] : null;
            $payloadMultipayer->add_nomorpokokperusahaan_1 = isset($dataMultiPayer['add_nomorpokokperusahaan_1']) && !empty($dataMultiPayer['add_nomorpokokperusahaan_1']) ? $dataMultiPayer['add_nomorpokokperusahaan_1'] : null;
            $payloadMultipayer->add_penjamingrade_id_1 = isset($dataMultiPayer['add_penjamingrade_id_1']) && !empty($dataMultiPayer['add_penjamingrade_id_1']) ? $dataMultiPayer['add_penjamingrade_id_1'] : null;
            if(isset($payloadMultipayer->is_add_payer) && $payloadMultipayer->is_add_payer == 'true') {
                $payloadMultipayer->add_tgl_konfirmasi_2 = !empty($payloadMultipayer->add_tgl_konfirmasi_2) ? date('Y-m-d', strtotime($payloadMultipayer->add_tgl_konfirmasi_2)) : null;
                $payloadMultipayer->add_namapemilikasuransi_2 = isset($dataMultiPayer['add_namapemilikasuransi_2']) && !empty($dataMultiPayer['add_namapemilikasuransi_2']) ? $dataMultiPayer['add_namapemilikasuransi_2'] : null;
                $payloadMultipayer->add_nomorpokokperusahaan_2 = isset($dataMultiPayer['add_nomorpokokperusahaan_2']) && !empty($dataMultiPayer['add_nomorpokokperusahaan_2']) ? $dataMultiPayer['add_nomorpokokperusahaan_2'] : null;
                $payloadMultipayer->add_penjamingrade_id_2 = isset($dataMultiPayer['add_penjamingrade_id_2']) && !empty($dataMultiPayer['add_penjamingrade_id_2']) ? $dataMultiPayer['add_penjamingrade_id_2'] : null;
            } else {
                $payloadMultipayer->add_carabayar_id_2 = null;
                $payloadMultipayer->add_penjamin_id_2 = null;
                $payloadMultipayer->add_asalrujukan_id_2 = null;
                $payloadMultipayer->add_penjamingrade_id_2 = null;
            }
            if (!$payloadMultipayer->validate()) $errorParse['multi_payer'] = $payloadMultipayer->errors;
            $isMultiPayer = true;
        } else {
            $payloadMultipayer->add_carabayar_id_1 = null;
            $payloadMultipayer->add_penjamin_id_1 = null;
            $payloadMultipayer->add_asalrujukan_id_1 = null;
            $payloadMultipayer->add_carabayar_id_2 = null;
            $payloadMultipayer->add_penjamin_id_2 = null;
            $payloadMultipayer->add_asalrujukan_id_2 = null;
            $payloadMultipayer->add_penjamingrade_id_1 = null;
            $payloadMultipayer->add_penjamingrade_id_2 = null;
        }

        $is_nourut = KonfigSystem::find()->one()->is_nourut;

        if ($is_nourut) {
            $payloadKunjungan->scenario = 'pendaftaran-rajal-nomor-urut';
        }

        /** Get Golongan Umur Id */
        $pasienPayLoad->tgl_rekam_medik = date('Y-m-d');
        $pasienPayLoad->is_aps = false;
        $pasienPayLoad->tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir)
                            ?  date('Y-m-d', strtotime($pasienPayLoad->tanggal_lahir)) : null;
        $pasienPayLoad->jenisidentitas = ($pasienPayLoad->jenisidentitas) ? $pasienPayLoad->jenisidentitas : null;
        $pasienPayLoad->statusperkawinan = ($pasienPayLoad->statusperkawinan) ? $pasienPayLoad->statusperkawinan : null;
        $pasienPayLoad->namadepan = ($pasienPayLoad->namadepan) ? $pasienPayLoad->namadepan : null;
        if ($pasienBaru) {
            if (!$pasienPayLoad->validate()) $errorParse['pasien'] = $pasienPayLoad->errors;
        }

        /** Validate Form */
        if (!$payload->validate()) $errorParse['tipe_pasien'] = $payload->errors;
        if (!$payloadKunjungan->validate()) $errorParse['kunjungan'] = $payloadKunjungan->errors;

        if (!empty($errorParse)) {
            return [
                'status' => 422,
                'data' => $errorParse
            ];
        }

        $tindakanKarcis = json_decode($payloadKunjungan->tindakan_karcis);
        $type = 'pelayanan';
        $detailTindakan = array();

        if (!empty($tindakanKarcis)) {
            $tipeTarif = 'pelayanan';
            $tarifTotal = (new TarifTotalRsFn([
                'extParam' => [
                    $payloadKunjungan->ruangan_id,
                    $payload->penjamin_id,
                    $payloadKunjungan->kelaspelayanan_id,
                    $tipeTarif
                ]
            ]));
            $docoContantsId = new DocoConstansId();
            $kelompok_tindakan_ids = $docoContantsId->actionGetAdditional(DocoConstants::KELOMPOK_TINDAKAN_ID, true);
            $modelTarifTotal = $tarifTotal::find()
            ->andWhere([
                'daftartindakan_id' => $tindakanKarcis
            ]);
            if(!empty($kelompok_tindakan_ids)){
                foreach ($kelompok_tindakan_ids as $value){
                    if(isset($value['operand']) || !empty($value['operand'])){
                        $modelTarifTotal->andWhere([$value['operand'], $value['column'], $value['value']]);
                    }else{
                        $modelTarifTotal->andWhere([$value['column'] => $value['value']]);
                    }
                }
            }
            $modelTarifTotal = $modelTarifTotal->all();
            if (!empty($modelTarifTotal)) {
                foreach ($modelTarifTotal as $value) {
                    if($value['dokter_id'] == $payloadKunjungan->dokter_id) {
                        $detailTindakan[$value['daftartindakan_id']] = [
                            'dokter_id' => $payloadKunjungan->dokter_id,
                            'perawat_id' => null,
                            'perawat2_id' => null,
                            'tipepaket_id' => null,
                            'is_cyto' => false,
                            'is_penyulit' => false,
                            'qty' => 1,
                            'daftartindakan_id' => $value->daftartindakan_id,
                            'implementasi_id' => null,
                            'instruksitindakan_id' => null
                        ];
                    } else {
                        if(!isset($detailTindakan[$value['daftartindakan_id']]) && $value['dokter_id'] == null) {
                            $detailTindakan[$value['daftartindakan_id']] = [
                                'dokter_id' => $payloadKunjungan->dokter_id,
                                'perawat_id' => null,
                                'perawat2_id' => null,
                                'tipepaket_id' => null,
                                'is_cyto' => false,
                                'is_penyulit' => false,
                                'qty' => 1,
                                'daftartindakan_id' => $value->daftartindakan_id,
                                'implementasi_id' => null,
                                'instruksitindakan_id' => null
                            ];
                        }
                    }
                }
            }
        }

        $umur = ucwords(DocoHelpers::getUmur($pasienPayLoad->tanggal_lahir));

        $statusPeriksa = DocoConstants::VAR_SP_AP;
        $isKarcis = false;
        $konfig = Cache::getKonfigSystem();
        $tipe_pembayaran = $konfig['pembayaran_langsung'];

        if (!empty($tmp) && $payload->carabayar_id == DocoConstants::CB_PEN_UMUM && $tipe_pembayaran) {
            $statusPeriksa = DocoConstants::VAR_SP_AK;
            $isKarcis = true;
        }
        $statusPasien = isset($pasien) && !empty($pasien) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B; 

        $jadwal_dokter = InfoJadwalDokterView::find()
            ->select(['jadwaldokter_id', 'jadwalbukapoli_id']);
        if(!empty($payloadKunjungan->jadwaldokter_id)){
            $jadwal_dokter->andWhere(['=', 'jadwaldokter_id', $payloadKunjungan->jadwaldokter_id]);
        }else{
            $jadwal_dokter->andWhere(['=', 'pegawai_id', !empty($payloadKunjungan->dokter_id) ? $payloadKunjungan->dokter_id : null]);
            $jadwal_dokter->andWhere(['=', 'hari_jadwalbuka', DocoHelpers::getIdHariIni()]);
            // ->andWhere(['>', 'kuota_tersedia', 0]);
        }
            
        $jadwal_dokter = $jadwal_dokter->asArray()->all();
        $jadwaldokter_id = ArrayHelper::getValue($jadwal_dokter, '0.jadwaldokter_id');
        $current_date = date('Y-m-d H:i:s');
        $payload_pendaftaranol_id = null;
        if(!empty($payload->pendaftaranol_id)){
            $payload_pendaftaranol_id = $payload->pendaftaranol_id;
        }
        $estimasidilayani = (new InfoJadwalDokterView)->getEstimasiByJadwal($jadwaldokter_id,$current_date,$isBpjs,true,$payload_pendaftaranol_id);

        if (empty($payload->antrian_id)) {
            $dataAntrian = [
                'pasien_id' => $pasienPayLoad->pasien_id,
                'ruangan_id' => $payloadKunjungan->ruangan_id,
                'carabayar_id' => $payload->carabayar_id,
                'pendaftaran_id' => null, // Di Set Di Trigger
                'tgl_antrian' => date("Y-m-d H:i:s"),
                'no_antrian' => array_key_exists('nomor_urut', $post['kunjungan']) ? $post['kunjungan']['nomor_urut'] : null,
                'penjamin_id' => $payload->penjamin_id,
                'pegawai_id' => !empty($payloadKunjungan->dokter_id) ? $payloadKunjungan->dokter_id : null,
                'status_pasien' => $statusPasien,
                'jenisantrian_id' => DocoConstants::VAR_JA_P,
                'jadwaldokter_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwaldokter_id'),
                'jadwalbukapoli_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwalbukapoli_id'),
                'estimasidilayani' => $estimasidilayani,
                'groupcarabayar_id' => $isBpjs ? DocoConstants::GROUP_BPJS : (!empty($post['asuransi']) ? DocoConstants::GROUP_JAMINAN : DocoConstants::GROUP_UMUM),
                'is_active' => false,
            ];
        } else {
            /*
            * Buat Antrian Poli jika belum buat
            * dihandle di pendaftaran_t()
            */
            $cekAntrianPoli = Antrian::find()
                            ->where(['jenisantrian_id' => DocoConstants::VAR_JA_P])
                            ->andWhere(['antrianasal_id' => $payload->antrian_id])
                            ->one();
            $jadwal_dokter_id = ArrayHelper::getValue($jadwal_dokter, '0.jadwaldokter_id');
            if(is_null($cekAntrianPoli)){
                $dataAntrian = [
                    'pasien_id' => $pasienPayLoad->pasien_id,
                    'ruangan_id' => $payloadKunjungan->ruangan_id,
                    'carabayar_id' => $payload->carabayar_id,
                    'pendaftaran_id' => null, // Di Set Di Trigger
                    'tgl_antrian' => date("Y-m-d H:i:s"),
                    'no_antrian' => array_key_exists('nomor_urut', $post['kunjungan']) ? $post['kunjungan']['nomor_urut'] : null,
                    'penjamin_id' => $payload->penjamin_id,
                    'pegawai_id' => !empty($payloadKunjungan->dokter_id) ? $payloadKunjungan->dokter_id : null,
                    'status_pasien' => $statusPasien,
                    'jenisantrian_id' => DocoConstants::VAR_JA_P,
                    'jadwaldokter_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwaldokter_id'),
                    'jadwalbukapoli_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwalbukapoli_id'),
                    'estimasidilayani' => $estimasidilayani,
                    'groupcarabayar_id' => $isBpjs ? DocoConstants::GROUP_BPJS : (!empty($post['asuransi']) ? DocoConstants::GROUP_JAMINAN : DocoConstants::GROUP_UMUM),
                    'is_active' => false,
                ];

                if (!$isBpjs && !empty($payload->pendaftaranol_id)) {
                    $sequenceAntrian = Antrian::find()
                        ->select([
                            'slot_sequence',
                        ])
                        ->where(['antrian_id' => $payload->antrian_id])->one();
                    if (!empty($sequenceAntrian)) {
                        $dataAntrian['slot_sequence'] = $sequenceAntrian->slot_sequence;
                    }
                }
            }elseif(!is_null($cekAntrianPoli) && !empty($jadwal_dokter_id) && !empty($cekAntrianPoli->jadwaldokter_id) && $jadwal_dokter_id <> $cekAntrianPoli->jadwaldokter_id) {
                /**
                 * kondisi ini khusus antrian poli yg sudah tergenerate tetapi dokternya berubah
                 */
                $dataAntrian = [
                    'pasien_id' => $pasienPayLoad->pasien_id,
                    'ruangan_id' => $payloadKunjungan->ruangan_id,
                    'carabayar_id' => $payload->carabayar_id,
                    'pendaftaran_id' => null, // Di Set Di Trigger
                    'tgl_antrian' => date("Y-m-d H:i:s"),
                    'no_antrian' => array_key_exists('nomor_urut', $post['kunjungan']) ? $post['kunjungan']['nomor_urut'] : null,
                    'penjamin_id' => $payload->penjamin_id,
                    'pegawai_id' => !empty($payloadKunjungan->dokter_id) ? $payloadKunjungan->dokter_id : null,
                    'status_pasien' => $statusPasien,
                    'jenisantrian_id' => DocoConstants::VAR_JA_P,
                    'jadwaldokter_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwaldokter_id'),
                    'jadwalbukapoli_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwalbukapoli_id'),
                    'estimasidilayani' => $estimasidilayani,
                    'groupcarabayar_id' => $isBpjs ? DocoConstants::GROUP_BPJS : (!empty($post['asuransi']) ? DocoConstants::GROUP_JAMINAN : DocoConstants::GROUP_UMUM),
                    'is_active' => false,
                ];
            }elseif (isset($is_nourut) && $is_nourut) {
                $dataAntrian = [
                    'no_antrian' => array_key_exists('nomor_urut', $post['kunjungan']) ? $post['kunjungan']['nomor_urut'] : null,
                    'jadwaldokter_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwaldokter_id'),
                    'jadwalbukapoli_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwalbukapoli_id'),
                    'estimasidilayani' => $estimasidilayani,
                    'groupcarabayar_id' => $isBpjs ? DocoConstants::GROUP_BPJS : (!empty($post['asuransi']) ? DocoConstants::GROUP_JAMINAN : DocoConstants::GROUP_UMUM),
                ];
            } else {
                $dataAntrian = [
                    'jadwaldokter_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwaldokter_id'),
                    'jadwalbukapoli_id' => ArrayHelper::getValue($jadwal_dokter, '0.jadwalbukapoli_id'),
                    'estimasidilayani' => $estimasidilayani,
                    'groupcarabayar_id' => $isBpjs ? DocoConstants::GROUP_BPJS : (!empty($post['asuransi']) ? DocoConstants::GROUP_JAMINAN : DocoConstants::GROUP_UMUM),
                ];
            }
        }

        $payloadAntrian->attributes = $dataAntrian;

        $getTotalhari = DocoHelpers::convertToHari($pasienPayLoad->tanggal_lahir);
        $getGolongan = Cache::getGolonganUmur();
        $getGolUmurId = 1;

        foreach ($getGolongan as $value) {
            if ($value['golonganumur_minimal'] <= $getTotalhari
                    && $value['golonganumur_maksimal'] >= $getTotalhari) {
                $getGolUmurId = $value['golonganumur_id'];
                break;
            }
        }
        $pasienPayLoad->golonganumur_id = $getGolUmurId;

        $antrianJkn = [
            'nomorkartu' => !empty($post['bpjs']) ? $payloadBpjs->no_kartu: '',
            'jenis_cara_bayar' => $payload->carabayar_id == DocoConstants::CARA_BAYAR_BPJS ? DocoConstants::J_P_JKN : DocoConstants::J_P_NONJKN,
            'nomorreferensi' => !empty($post['bpjs']) ? $payloadBpjs->no_rujukan : '',
            'jeniskunjungan' => !empty($post['bpjs']) ? $jeniskunjungan : DocoConstants::RUJUK_FKTP_JKN,
        ];

        $additionals = [
            'tarif' => $listTagihan,
            'rujukan' => $rujukan,
            'antrian' => $payloadAntrian,
            'penanggung_jawab' => $pjPasien,
            'asuransi' => $asuransi,
            'additional_payer' => $payloadMultipayer,
            'antrian_jkn' => $antrianJkn
        ];

        if (!empty($payload->pendaftaranol_id)) {
            $additionals['pendaftaranol_id'] = $payload->pendaftaranol_id;
        }

        if ($pasienBaru) {
            $additionals['pasien'] = $pasienPayLoad->attributes;
        }

        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $petugas_id = $jwt->loginpemakai_id;
        $petugas_tgl_pembuat = date('Y-m-d H:i:s');
        $dataCaraBayar = CaraBayar::findOne($payload->carabayar_id);
        $groupCaraBayarId = ArrayHelper::getValue($dataCaraBayar, 'groupcarabayar_id');
        $limitTagihan = $payloadKunjungan->limit_tagihan ? $payloadKunjungan->limit_tagihan : 0;
        if ($isBpjs || $groupCaraBayarId == DocoConstants::GROUP_BPJS) {
            $autoPlafon = $this->setAutoPlafon(DocoConstants::INST_ID_RJ, $payloadKunjungan->kelaspelayanan_id, $payloadKunjungan->ruangan_id);
            if (!empty($autoPlafon)) {
                $limitTagihan = $autoPlafon;
            }
        }
        
        $dataPendaftaran = [
            'tgl_pendaftaran' => date('Y-m-d H:i:s'),
            'penjamin_id' => $payload->penjamin_id,
            'pasien_id' => $pasienPayLoad->pasien_id,
            'pegawai_id' => $payloadKunjungan->dokter_id,
            'instalasi_id' => DocoConstants::INST_ID_RJ,
            'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
            'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
            'carabayar_id' => $payload->carabayar_id,
            'golonganumur_id' => $getGolUmurId,
            'umur' => $umur,
            'rujukan_id' => null,
            'antrian_id' => $payload->antrian_id,
            'kunjungan' => !empty($payload->no_rekam_medik) ? DocoConstants::VAR_K_L : DocoConstants::VAR_K_B,
            'ruangan_id' => $payloadKunjungan->ruangan_id,
            'transportasi' => ($payloadKunjungan->transportasi) ? $payloadKunjungan->transportasi : null,
            'keadaan_masuk' => $payloadKunjungan->keadaan_masuk,
            'status_periksa' => $statusPeriksa,
            'status_pasien' => $statusPasien,
            'status_masuk' => !empty($rujukan) ? DocoConstants::VAR_SM_R : DocoConstants::VAR_SM_NR,
            'keterangan_pendaftaran' => $payloadKunjungan->keterangan,
            'is_karcis' => $isKarcis,
            'additional_data' => json_encode($additionals),
            'is_aps' => false,
            'limit_tagihan' => $limitTagihan,
            'is_multipayer' => $isMultiPayer,
            'petugas_id' => $petugas_id,
            'petugas_tgl_pembuat' => $petugas_tgl_pembuat,
            'referal_pegawai_id' => (int) $payloadKunjungan->referal > 0 ? $payloadKunjungan->referal : null,
            'referal_luar' => (int) $payloadKunjungan->referal == 0 ? strtoupper($payloadKunjungan->referal) : null,
        ];

        $eligiblePasien = $request->post('eligible_pasien', false);
        $eligiblePasien = json_decode($eligiblePasien, true);
        $dataPeserta = ArrayHelper::getValue($eligiblePasien, 'dataPeserta', []);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $pendaftaran = new Pendaftaran;
            $pendaftaran->attributes = $dataPendaftaran;

            /*
            * penambahan validasi untuk membatasi satu pasien yg sama mendaftaran dalam internal 55 detik
            * conditions: jika hasil dari fungsi ini adalah true, maka akan mengeluarkan status error 422 atau pasien tidak dapat melanjutkan pendaftaran
            */
            if ( $this->registrationLimiterValidator($pasienPayLoad->pasien_id)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Pasien Sedang Diproses Dalam Proses Pendaftaran Yang Lain, Silahkan Tunggu Beberapa Saat Untuk Melakukan Pendaftaran!'
                ];
            }

            if ($pendaftaran->validate() && $pendaftaran->save(false)) {
                $idPendaftaran = $pendaftaran->pendaftaran_id;
                /*
                Yii::warning([
                    'Config Antrol Pendaftaran' => (new DocoConstansId)->actionGetId('is_antrolpendaftaran'),
                    'Config Skip Return Antrian Pendaftaran' => (new DocoConstansId)->actionGetId('skip_returnantrian'),
                ],'Config Antrian');
                */
                if($isBpjs && !$allowBpjs) {
                    if((new DocoConstansId)->actionGetId('is_antrolpendaftaran') == 1){
                        $antrolRes = $this->saveAntrol($payloadBpjs, $idPendaftaran, $pasienBaru);
                        if(is_array($antrolRes)){
                            if((new DocoConstansId)->actionGetId('skip_returnantrian') == 1){
                                $antrolBpjs =$request->post('allow_antrol',false); 
                                if(!$antrolBpjs){
                                    Yii::$app->cache->delete('pendaftaran-pasien-validator-'.$pasienPayLoad->pasien_id); // delete cache ketika pendaftaran berhasil
                                    return [
                                        'status' => 422,
                                        'title' => $antrolRes['title'],
                                        'text' => $antrolRes['text'],
                                        'flag' => 'antrol_error'
                                    ];
                                }else{
                                    Yii::warning([
                                        'method' => 'Proses Antrian Online',
                                        'timestamp' => date('Y-m-d H:i:s'),
                                        'payload' => $payloadBpjs,
                                        'data' => $antrolRes,
                                    ],'Antrian Online');  
                                }
                            }else{
                                Yii::warning([
                                    'method' => 'Proses Antrian Online',
                                    'timestamp' => date('Y-m-d H:i:s'),
                                    // 'payload' => $payloadBpjs,
                                    'data' => $antrolRes,
                                ],'Antrian Online');
                            }
                        }
                    }
                }

                $prosesBpjs = false;
                if ($isBpjs && !$allowBpjs) {
                    $bpjsRes = $this->saveBpjs($payloadBpjs, $idPendaftaran, $pasienBaru);
                    $prosesBpjs = true;
                    if (is_array($bpjsRes)) {
                        Yii::$app->cache->delete('pendaftaran-pasien-validator-'.$pasienPayLoad->pasien_id); // delete cache ketika pendaftaran berhasil
                        if((new DocoConstansId)->actionGetId('is_antrolpendaftaran') == 1){
                                $is_reservasi_jkn = PendaftaranOnline::find()->select([
                                        'pendaftaranol_id',
                                    ])
                                    ->andWhere(['pendaftaran_id' => $idPendaftaran])
                                    ->andWhere(['in', 'jenis_reservasi', [DocoConstants::JENIS_RESERVASI_JKN, DocoConstants::JENIS_RESERVASI_SIRS]])
                                    ->one();

                                if (!$is_reservasi_jkn) {
                                    $keterangan = isset($bpjsRes['text']) ? $bpjsRes['text'] : '';
                                    $batalAntrolRes = $this->cancelAntrol($idPendaftaran, $keterangan);
                                    if(is_array($batalAntrolRes)){
                                        if((new DocoConstansId)->actionGetId('skip_returnantrian') == 1){
                                            return $batalAntrolRes;
                                        }else{
                                            Yii::warning([
                                                'method' => 'Batal Antrian Online',
                                                'timestamp' => date('Y-m-d H:i:s'),
                                                // 'payload' => $payloadBpjs,
                                                'data' => $batalAntrolRes,
                                            ],'Antrian Online');
                                        }
                                    }
                                }
                            }

                        return $bpjsRes;
                    }
                } else if ($isBpjs && $allowBpjs) { //handle bpjs unauth
                    if((new DocoConstansId)->actionGetId('is_antrolpendaftaran') == 1){
                        $antrolRes = $this->saveAntrol($payloadBpjs, $idPendaftaran, $pasienBaru);
                        if(is_array($antrolRes)){
                            if((new DocoConstansId)->actionGetId('skip_returnantrian') == 1){
                                return $antrolRes;
                            }else{
                                Yii::warning([
                                    'method' => 'Proses Antrian Online',
                                    'timestamp' => date('Y-m-d H:i:s'),
                                    // 'payload' => $payloadBpjs,
                                    'data' => $antrolRes,
                                ],'Antrian Online');
                            }
                        }
                    }
                    $bpjsRes = $this->saveBpjsWithouBridging($payloadBpjs, $idPendaftaran, $pasienBaru);
                    $prosesBpjs = false;
                }

                // Update waktu antrian pasien JKN
                // if (!empty($payload->pendaftaranol_id)) {
                //     $pendaftaranol = PendaftaranOnline::find()->select([
                //         'pendaftaranol_id',
                //     ])->where(['jenis_reservasi' => DocoConstants::JENIS_RSV_JKN])
                //     ->andWhere(['pendaftaranol_id' => $payload->pendaftaranol_id])
                //     ->asArray()->one();

                //     if (!empty($pendaftaranol)){
                //         $updateAntrianJkn = $this->updateAntrianJkn($payload->pendaftaranol_id, null, 3);
                //         if (is_array($updateAntrianJkn)) return $updateAntrianJkn;
                //     }
                // }

                if($isKonsul) {
                    $dataKonsul['pendaftaran_id'] = $idPendaftaran;
                    $setKonsul = $this->actionSetKunjunganPoli($dataKonsul);
                }
                $transaction->commit();
                // Integrasi Akunting
                //IntegrasiAkunting::integrateKarcisPasien($idPendaftaran);

                // Select no pendaftaran
                $dataPendaftaran = Pendaftaran::findOne($idPendaftaran);
                $noPendaftaran = $dataPendaftaran->no_pendaftaran;

                //set slot sequence data antrian
                $setSequenceAntrian = $this->setSlotSequenceAntrian($dataPendaftaran);

                if (!empty($detailTindakan)) {
                    // Integrasi kasir tagihan pelayanan
                    $tagihanTindakan = (new KasirService)->tagihan([
                        'pendaftaran_id' => $idPendaftaran,
                        'no_pendaftaran' => $noPendaftaran,
                        'instalasi_id' => $pendaftaran->instalasi_id,
                        'ruangan_id' => $pendaftaran->ruangan_id,
                        'penjamin_id' => $pendaftaran->penjamin_id,
                        'kelas_pelayanan_id' => $pendaftaran->kelaspelayanan_id
                    ], $detailTindakan);

                    if (isset($tagihanTindakan['meta']['result']) && $tagihanTindakan['meta']['result'] == 'failed') {
                        $transaction->rollBack();
                        Yii::$app->cache->delete('pendaftaran-pasien-validator-'.$pasienPayLoad->pasien_id); // delete cache ketika pendaftaran berhasil
                        return isset($tagihanTindakan['message']) ? $tagihanTindakan['message'] : 'Terjadi Kesalahan API';
                    }
                }

                if(Yii::$app->params['isRabbitMq']) {   
                    (new RabbitBgProcess())->send([
                        "penjamin_id" => $dataPendaftaran['penjamin_id'],
                        "kodebenefit" => $request->post('refresh_asuransi'),
                        "data_peserta" => $dataPeserta,
                        "data_pendaftaran" => $dataPendaftaran->attributes,
                        'pendaftaranol_id' => $payload->pendaftaranol_id,
                    ], 'integrate_insurance', 'integrate_insurance');   
                }

                $resPendaftaran = LaporanKunjunganRawatJalanView::find()
                ->where(['no_pendaftaran' => $noPendaftaran])
                ->asArray()
                ->one();

                // Yii::$app->cache->delete('pendaftaran-pasien-validator-'.$pasienPayLoad->pasien_id); // delete cache ketika pendaftaran berhasil

                // PENANDA
                return [
                    'message' => 'Proses Pendaftaran berhasil rajal',
                    'id' => DocoHelpers::encrypt($idPendaftaran),
                    'is_bpjs' => $prosesBpjs,
                    'pendaftaran' => $resPendaftaran,
                    'pendaftaranol_id' => $payload->pendaftaranol_id,
                    'pendaftaran_id' => $idPendaftaran,
                ];
            }

            Yii::$app->cache->delete('pendaftaran-pasien-validator-'.$pasienPayLoad->pasien_id); // delete cache ketika pendaftaran gagal melewati validasi
            return [
                'status' => 422,
                'data' => $pendaftaran->errors
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            // $exception = preg_match('/(?<=ERROR:  )(.*)/',$e->getMessage(),$out);
            $message = $e->getMessage();
            // if (isset($out[1])) {
            //     $message = 'Query :' . $out[1];
            // }
            $this->logError($e);
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $message
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $e->getMessage()
            ];
        }
    }

    public function actionSetKunjunganPoli($data = [], $new = true)
    {
        $dataJanji = InfRencanaKontrol::find()->where(['buatjanjipoli_id' => $data['buatjanjipoli_id']])->one();
        if($dataJanji->transaksi_konsul == DocoConstants::T_K_KONSUL_POLI){
            $model = KonsulPoli::find()->where(['konsulpoli_id' => $data['buatjanjipoli_id']])->one();
            if($new){
                $model->pendaftaranbaru_id = $data['pendaftaran_id'];
            }
            $model->status_approve = DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI;
            $model->save();
            return true;
        }
    }

    public function actionGetDataPendaftaranUmum($q = null, $pendaftaran_id = null)
    {
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $query = InfoDataPendaftaranView::find()->select([
            'pendaftaran_id',
            'pasien_id',
            'tgl_pendaftaran',
            'no_pendaftaran',
            'no_rekam_medik',
            'nama_pasien',
            'namadepan_penanggung',
            'nama_pasien_penanggung',
            'propinsi_id_penanggung',
            'kabupaten_id_penanggung',
            'kecamatan_id_penanggung',
            'kelurahan_id_penanggung',
            'rt_penanggung',
            'rw_penanggung',
            'kode_pos_penanggung',
            'alamat_pasien_penanggung',
            'no_telepon_pasien_penanggung',
            'pekerjaan_id_penanggung',
            'pt_penanggung',
            'instalasi_id',
        ]);

        if ($q != null) {
            $query->andWhere([
                'no_rekam_medik' => $q
            ]);
        }

        if ($pendaftaran_id != null) {
            $query->andWhere([
                'pendaftaran_id' => $pendaftaran_id
            ]);
        }

        if (isset($advancedFilters['tgl_pendaftaran_awal'])
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
        }

        return [
            'data' => $query->orderBy(['pendaftaran_id' => SORT_DESC])
            ->one()
        ];
    }

    public function actionGetPenanggungBiaya($q, $carabayar_id)
    {
        $query = PenanggungBiayaView::find()
        ->where([
            'pasien_id' => $q
        ])
        ->andWhere([
            'carabayar_id' => $carabayar_id
        ]);

        return [
            'data' => $query->orderBy(['penanggungbiaya_id' => SORT_DESC])
            ->one()
        ];
    }

    public function actionSavePendaftaranRajal()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $isBpjs = false;
        $pasienBaru = true;
        $isKonsul = false;
        $tmpPendaftaran = '';

        /** Validasi Error */
        $errorParse = [];
        /** Tindakan Karcis */
        $listTindakan = $tmp = $sepNew = $komponen = [];
        $listTagihan = [];
        /** allow Bpjs */
        $allowBpjs = $request->post('allow_bpjs',false);
        /**
         * @var app\modules\v1\payload\PayloadRujukan
         */
        $rujukan = [];

        /**
         * @var app\modules\v1\payload\PjPasien
         */
        $pjPasien = [];

        /**
         *  @var app\modules\v1\payload\AsuransiForm
        */
        $pjPasien = [];

        /**
         * @var app\modules\v1\payload\PayloadAntrian
         */
        $dataAntrian = [];

        /**
         * @var app\modules\v1\payload\AsuransiForm
         */
        $asuransi = [];

        /**
         * @var app\modules\v1\payload\BpjsNewForm
         */
        $bpjs = [];

        /**
         * @var app\modules\v1\payload\PenanggungBiayaForm
         */
        $penanggungbiaya = [];

        /**
         * @var app\modules\v1\payload\KeluargaPasienForm
         */
        $keluargapasien = [];
        $keluargapasienOld = [];

        $payload = new TipePasien;
        $payloadKunjungan = new Kunjungan;
        $payloadAntrian = new PayloadAntrian;
        $pasienPayLoad = new PayloadPasien;
        $pasienPayLoad->scenario = "pendaftaran-rajal";
        $pasienPayLoad->attributes = !empty($post['pasien']) ? $post['pasien'] : [];
        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];

        /* Default Jenis Kasus Penyakit Umum */
        $payloadKunjungan->jeniskasuspenyakit_id = 23;
        $payloadKunjungan->kelaspelayanan_id =self::K_P_STYP;
        if(!empty($post['buatjanjipoli_id'])){
           $janjiId = $post['buatjanjipoli_id'];
           $isKonsul = true;
           $dataKonsul['buatjanjipoli_id'] = $janjiId;
           /** Status Janji == true (Daftar BPJS Hari sama) */
           if($payload->carabayar_id == DocoConstants::CARA_BAYAR_BPJS && $post['status_janji'] == true) {
               return $this->actionSetKunjunganPoli($dataKonsul, false);
           }
        }

        if (!empty($post['asuransi'])) {
            $payloadAsuransi = new AsuransiForm;
            $payloadAsuransi->attributes = $post['asuransi'];
            $payloadAsuransi->tgl_konfirmasi = !empty($payloadAsuransi->tgl_konfirmasi) ? date('Y-m-d', strtotime($payloadAsuransi->tgl_konfirmasi)) : null;
            $payloadAsuransi->masaberlakukartu = !empty($payloadAsuransi->masaberlakukartu) ? date('Y-m-d', strtotime($payloadAsuransi->masaberlakukartu)) : null;
            $asuransi = $payloadAsuransi->attributes;
            if (!$payloadAsuransi->validate()) $errorParse['asuransi'] = $payloadAsuransi->errors;
        }

        if (!empty($post['penanggungbiaya'])) {
            $payloadPenanggungBiaya = new PenanggungBiayaForm;
            $payloadPenanggungBiaya->attributes = $post['penanggungbiaya'];
            $penanggungbiaya = $payloadPenanggungBiaya->attributes;
            if (!$payloadPenanggungBiaya->validate()) $errorParse['penanggungbiaya'] = $payloadPenanggungBiaya->errors;
        }

        if (!empty($post['bpjs'])) {
            $payloadBpjs = new BpjsNewForm;
            $payloadBpjs->attributes = $post['bpjs'];
            $payload->no_rekam_medik = $payloadBpjs->no_rekam_medik;
            $pasienPayLoad->nopeserta_bpjs = $payloadBpjs->no_kartu;
            $isBpjs = true;
            /** Statis Rujukan WIP */
            $diagnosa = Diagnosa::find()->select([
                'diagnosa_id'
            ])->where([
                'diagnosa_kode' => $payloadBpjs->diagnosa_awal
            ])->one();
            $payload->asalrujukan_id = 33;
            $post['rujukan'] = [
                'rujukandari_id' => 1,
                'no_rujukan' => $payloadBpjs->no_rujukan ? $payloadBpjs->no_rujukan : $payloadBpjs->no_kartu,
                'nama_perujuk' => 'BPJS',
                'tanggal_rujukan' => date('Y-m-d', strtotime($payloadBpjs->tanggal_rujukan)),
                'kodediagnosa_rujukan' => $payloadBpjs->diagnosa_awal,
                'diagnosa_id' => !empty($diagnosa->diagnosa_id) ? $diagnosa->diagnosa_id : null,
            ];
            //** Override Value Penjamin */
            $payload->penjamin_id = !empty($payloadBpjs->jenis_peserta) ? $payloadBpjs->jenis_peserta : $payload->penjamin_id;
            if (!$payloadBpjs->validate()) $errorParse['bpjs'] = $payloadBpjs->errors;
        }

        /** Set Rujukan */
        if (!empty($post['rujukan'])) {
            $payloadRujukan = new PayloadRujukan;
            $payloadRujukan->attributes = $post['rujukan'];
            $payloadRujukan->asalrujukan_id = $payload->asalrujukan_id;
            $payloadRujukan->rujukandari_id = 1; //dtg sendiri
            $payloadRujukan->tanggal_rujukan = date('Y-m-d', strtotime($payloadRujukan->tanggal_rujukan));
            $rujukan = $payloadRujukan->attributes;
            if (!$payloadRujukan->validate()) $errorParse['rujukan'] = $payloadRujukan->errors;
        }

        if (!empty($payload->no_rekam_medik) && !isset($post['pasien']['nama_pasien'])) {
            $pasien = Pasien::find()->where([
                'no_rekam_medik' => $payload->no_rekam_medik
            ])->asArray()->one();
            if (empty($pasien)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Pasien tidak ditemukan'
                ];
            }
            $pasienPayLoad->attributes = $pasien;
            $pasienBaru = false;
            /** cek status kunjungan sebelumnya */
            if(!$pasienBaru && !isset($janjiId)){
                $cekPulang = Pendaftaran::getKunjunganPasienOne(
                    $pasienPayLoad->no_rekam_medik,
                    $payloadKunjungan->ruangan_id,
                    [DocoConstants::STATUS_PERIKSA_PULANG, DocoConstants::STATUS_PERIKSA_BTL_PERIKSA,DocoConstants::STATUS_PERIKSA_BTL_KONSUL, DocoConstants::STATUS_PERIKSA_BTL_KUNJ] // [4,402,411,628]
                );
                if($cekPulang && $cekPulang['pasienpulang_id'] == null) {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Pasien masih dalam pelayanan di unit ' . $cekPulang['rua_nama']
                    ];
                }
            }
            $keluargapasienOld = KeluargaPasien::find()->where([
                'pasien_id' => $pasienPayLoad->pasien_id
            ])->one();
        }

        /** Get Golongan Umur Id */
        $pasienPayLoad->tgl_rekam_medik = date('Y-m-d');
        $pasienPayLoad->is_aps = false;
        $pasienPayLoad->tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir)
                            ?  date('Y-m-d', strtotime($pasienPayLoad->tanggal_lahir)) : null;
        $pasienPayLoad->jenisidentitas = ($pasienPayLoad->jenisidentitas) ? $pasienPayLoad->jenisidentitas : null;
        $pasienPayLoad->statusperkawinan = ($pasienPayLoad->statusperkawinan) ? $pasienPayLoad->statusperkawinan : null;
        $pasienPayLoad->namadepan = ($pasienPayLoad->namadepan) ? $pasienPayLoad->namadepan : null;
        $pasienPayLoad->catatanpenting_pasien = $payloadKunjungan->catatanpenting_pasien;
        if (!$pasienPayLoad->validate()) $errorParse['pasien'] = $pasienPayLoad->errors;

        if (!empty($post['keluarga_pasien'])) {
            $payloadKp = new KeluargaPasienForm;
            $payloadKp->attributes = $post['keluarga_pasien'];
            $keluargapasien = $payloadKp->attributes;
            if (!$payloadKp->validate()) $errorParse['keluarga_pasien'] = $payloadKp->errors;
        }

        if (!empty($post['pj_pasien'])) {
            $payloadPjPasien = new PjPasien;
            $payloadPjPasien->attributes = $post['pj_pasien'];
            $payloadPjPasien->pj_tanggal_lahir = !empty($payloadPjPasien->pj_tanggal_lahir)
                                        ? date('Y-m-d',strtotime($payloadPjPasien->pj_tanggal_lahir)) : null;
            if ($payloadPjPasien->pj_pengantar == 990){
                $payloadPjPasien->pj_nama = $pasienPayLoad->nama_pasien;
                $payloadPjPasien->pj_jk = $pasienPayLoad->jeniskelamin;
                $payloadPjPasien->pj_jenis_identitas = $pasienPayLoad->jenisidentitas;
                $payloadPjPasien->pj_no_identitas = $pasienPayLoad->no_identitas_pasien;
                $payloadPjPasien->pj_tempat_lahir = $pasienPayLoad->tempat_lahir;
                $payloadPjPasien->pj_tanggal_lahir = $pasienPayLoad->tanggal_lahir;
                $payloadPjPasien->pj_umur = $pasienPayLoad->umur;
                $payloadPjPasien->pj_alamat = $pasienPayLoad->alamat_pasien;
                $payloadPjPasien->pj_no_telepon = $pasienPayLoad->no_telepon_pasien;
                $payloadPjPasien->pj_namadepan = $pasienPayLoad->namadepan;
                $payloadPjPasien->pj_propinsi_id = $pasienPayLoad->propinsi_id;
                $payloadPjPasien->pj_kabupaten_id = $pasienPayLoad->kabupaten_id;
                $payloadPjPasien->pj_kecamatan_id = $pasienPayLoad->kecamatan_id;
                $payloadPjPasien->pj_kelurahan_id = $pasienPayLoad->kelurahan_id;
                $payloadPjPasien->pj_pekerjaan_id = $pasienPayLoad->pekerjaan_id;
                $payloadPjPasien->pj_rt = $pasienPayLoad->rt;
                $payloadPjPasien->pj_rw = $pasienPayLoad->rw;
            } else if ($payloadPjPasien->pj_pengantar == 991) {
                if (!empty($post['keluarga_pasien'])){
                    $payloadPjPasien->pj_nama = $payloadKp->keluarga_nama;
                    $payloadPjPasien->pj_jk = $payloadKp->keluarga_jk;
                    $payloadPjPasien->pj_hubungan = $payloadKp->keluarga_hubungan;
                    $payloadPjPasien->pj_alamat = $payloadKp->keluarga_alamat;
                    $payloadPjPasien->pj_no_telepon = $payloadKp->keluarga_no_telepon;
                    $payloadPjPasien->pj_namadepan = $payloadKp->keluarga_namadepan;
                    $payloadPjPasien->pj_propinsi_id = $payloadKp->keluarga_propinsi_id;
                    $payloadPjPasien->pj_kabupaten_id = $payloadKp->keluarga_kabupaten_id;
                    $payloadPjPasien->pj_kecamatan_id = $payloadKp->keluarga_kecamatan_id;
                    $payloadPjPasien->pj_kelurahan_id = $payloadKp->keluarga_kelurahan_id;
                    $payloadPjPasien->pj_pekerjaan_id = $payloadKp->keluarga_pekerjaan_id;
                    $payloadPjPasien->pj_rt = $payloadKp->keluarga_rt;
                    $payloadPjPasien->pj_rw = $payloadKp->keluarga_rw;
                } else if (!empty($keluargapasienOld)) {
                    $payloadPjPasien->pj_nama = $keluargapasienOld->keluarga_nama;
                    $payloadPjPasien->pj_jk = $keluargapasienOld->keluarga_jk;
                    $payloadPjPasien->pj_hubungan = $keluargapasienOld->keluarga_hubungan;
                    $payloadPjPasien->pj_alamat = $keluargapasienOld->keluarga_alamat;
                    $payloadPjPasien->pj_no_telepon = $keluargapasienOld->keluarga_no_telepon;
                    $payloadPjPasien->pj_namadepan = $keluargapasienOld->keluarga_namadepan;
                    $payloadPjPasien->pj_propinsi_id = $keluargapasienOld->keluarga_propinsi_id;
                    $payloadPjPasien->pj_kabupaten_id = $keluargapasienOld->keluarga_kabupaten_id;
                    $payloadPjPasien->pj_kecamatan_id = $keluargapasienOld->keluarga_kecamatan_id;
                    $payloadPjPasien->pj_kelurahan_id = $keluargapasienOld->keluarga_kelurahan_id;
                    $payloadPjPasien->pj_pekerjaan_id = $keluargapasienOld->keluarga_pekerjaan_id;
                    $payloadPjPasien->pj_rt = $keluargapasienOld->keluarga_rt;
                    $payloadPjPasien->pj_rw = $keluargapasienOld->keluarga_rw;
                }
            }
            $pjPasien = $payloadPjPasien->attributes;
            if (!$payloadPjPasien->validate()) $errorParse['pj_pasien'] = $payloadPjPasien->errors;
        }

        /** Validate Form */
        if (!$payload->validate()) $errorParse['tipe_pasien'] = $payload->errors;
        if (!$payloadKunjungan->validate()) $errorParse['kunjungan'] = $payloadKunjungan->errors;

        if (!empty($errorParse)) {
            return [
                'status' => 422,
                'data' => $errorParse
            ];
        }

        $tindakanKarcis = json_decode($payloadKunjungan->tindakan_karcis);
        $type = 'pelayanan';
        $detailTindakan = array();

        if (!empty($tindakanKarcis)) {
            $tipeTarif = 'pelayanan';
            $tarifTotal = (new TarifTotalRsFn([
                'extParam' => [
                    $payloadKunjungan->ruangan_id,
                    $payload->penjamin_id,
                    $payloadKunjungan->kelaspelayanan_id,
                    $tipeTarif
                ]
            ]));
            $modelTarifTotal = $tarifTotal::find()
            ->andWhere([
                'kelompoktindakan_id' => DocoConstants::VAR_KEL_KRCS,
                'daftartindakan_id' => $tindakanKarcis
            ])->all();

            if (!empty($modelTarifTotal)) {
                foreach ($modelTarifTotal as $value) {
                    $detailTindakan[] = [
                        'dokter_id' => $payloadKunjungan->dokter_id,
                        'perawat_id' => null,
                        'perawat2_id' => null,
                        'tipepaket_id' => null,
                        'is_cyto' => false,
                        'is_penyulit' => false,
                        'qty' => 1,
                        'daftartindakan_id' => $value->daftartindakan_id,
                        'implementasi_id' => null,
                        'instruksitindakan_id' => null
                    ];
                }
            }
        }

        $umur = ucwords(DocoHelpers::getUmur($pasienPayLoad->tanggal_lahir));

        $statusPeriksa = DocoConstants::VAR_SP_AP;
        $isKarcis = false;
        $konfig = Cache::getKonfigSystem();
        $tipe_pembayaran = $konfig['pembayaran_langsung'];

        if (!empty($tmp) && $payload->carabayar_id == DocoConstants::CB_PEN_UMUM && $tipe_pembayaran) {
            $statusPeriksa = DocoConstants::VAR_SP_AK;
            $isKarcis = true;
        }
        $statusPasien = isset($pasien) && !empty($pasien) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B; 

        if (empty($payload->antrian_id)) {
            $dataAntrian = [
                'pasien_id' => $pasienPayLoad->pasien_id,
                'ruangan_id' => $payloadKunjungan->ruangan_id,
                'carabayar_id' => $payload->carabayar_id,
                'pendaftaran_id' => null, // Di Set Di Trigger
                'tgl_antrian' => date("Y-m-d H:i:s"),
                'no_antrian' => array_key_exists('nomor_urut', $post['kunjungan']) ? $post['kunjungan']['nomor_urut'] : null,
                'penjamin_id' => $payload->penjamin_id,
                'pegawai_id' => !empty($payloadKunjungan->dokter_id) ? $payloadKunjungan->dokter_id : null,
                'status_pasien' => $statusPasien,
                'jenisantrian_id' => DocoConstants::VAR_JA_P,
                'is_active' => false,
            ];
        } else {
            $is_nourut = KonfigSystem::find()->one()->is_nourout;

            if ($is_nourut) {
                $dataAntrian = [
                    'no_antrian' => array_key_exists('nomor_urut', $post['kunjungan']) ? $post['kunjungan']['nomor_urut'] : null
                ];
            }
        }

        $payloadAntrian->attributes = $dataAntrian;

        $getTotalhari = DocoHelpers::convertToHari($pasienPayLoad->tanggal_lahir);
        $getGolongan = Cache::getGolonganUmur();
        $getGolUmurId = 1;

        foreach ($getGolongan as $value) {
            if ($value['golonganumur_minimal'] <= $getTotalhari
                    && $value['golonganumur_maksimal'] >= $getTotalhari) {
                $getGolUmurId = $value['golonganumur_id'];
                break;
            }
        }
        $pasienPayLoad->golonganumur_id = $getGolUmurId;

        $additionals = [
            'tarif' => $listTagihan,
            'rujukan' => $rujukan,
            'antrian' => $payloadAntrian,
            'penanggung_jawab' => $pjPasien,
            'asuransi' => $asuransi,
            'penanggungbiaya' => $penanggungbiaya,
            'keluargapasien' => $keluargapasien,
        ];

        if (!empty($payload->pendaftaranol_id)) {
            $additionals['pendaftaranol_id'] = $payload->pendaftaranol_id;
        }

        if ($pasienBaru) {
            $additionals['pasien'] = $pasienPayLoad->attributes;
        }

        if (!$pasienBaru) {
            $pasien = Pasien::find()->where([
                'no_rekam_medik' => $payload->no_rekam_medik
            ])->one();
            $pasien->scenario = "fix-error-update";
            $pasien->catatanpenting_pasien = $payloadKunjungan->catatanpenting_pasien;
            if (!$pasien->save()) {
                return [
                    'status' => 422,
                    'text' => 'Gagal menambahkan catatan penting'
                ];
            }
        }
        $dataCaraBayar = CaraBayar::findOne($payload->carabayar_id);
        $groupCaraBayarId = ArrayHelper::getValue($dataCaraBayar, 'groupcarabayar_id');
        $limitTagihan = $payloadKunjungan->limit_tagihan ? $payloadKunjungan->limit_tagihan : 0;
        if ($isBpjs || $groupCaraBayarId == DocoConstants::GROUP_BPJS) {
            $autoPlafon = $this->setAutoPlafon(DocoConstants::INST_ID_RJ, $payloadKunjungan->kelaspelayanan_id, $payloadKunjungan->ruangan_id);
            if (!empty($autoPlafon)) {
                $limitTagihan = $autoPlafon;
            }
        }
        
        $dataPendaftaran = [
            'caramasuk_id' => $payload->asalrujukan_id,
            'tgl_pendaftaran' => date('Y-m-d H:i:s', strtotime($payloadKunjungan->tgl_pendaftaran)),
            'penjamin_id' => $payload->penjamin_id,
            'pasien_id' => $pasienPayLoad->pasien_id,
            'pegawai_id' => $payloadKunjungan->dokter_id,
            'instalasi_id' => DocoConstants::INST_ID_RJ,
            'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
            'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
            'carabayar_id' => $payload->carabayar_id,
            'golonganumur_id' => $getGolUmurId,
            'umur' => $umur,
            'rujukan_id' => null,
            'antrian_id' => $payload->antrian_id,
            'kunjungan' => !empty($payload->no_rekam_medik) ? DocoConstants::VAR_K_L : DocoConstants::VAR_K_B,
            'ruangan_id' => $payloadKunjungan->ruangan_id,
            'transportasi' => ($payloadKunjungan->transportasi) ? $payloadKunjungan->transportasi : null,
            'keadaan_masuk' => $payloadKunjungan->keadaan_masuk,
            'status_periksa' => $statusPeriksa,
            'status_pasien' => $statusPasien,
            'status_masuk' => !empty($rujukan) ? DocoConstants::VAR_SM_R : DocoConstants::VAR_SM_NR,
            'keterangan_pendaftaran' => $payloadKunjungan->keterangan,
            'is_karcis' => $isKarcis,
            'additional_data' => json_encode($additionals),
            'is_aps' => false,
            'limit_tagihan' => $limitTagihan,
            'namadepan' => $payloadKunjungan->namadepan,
            'nama_pasien' => $payloadKunjungan->nama_pasien,
            'propinsi_id' => $payloadKunjungan->propinsi_id,
            'kabupaten_id' => $payloadKunjungan->kabupaten_id,
            'kecamatan_id' => $payloadKunjungan->kecamatan_id,
            'kelurahan_id' => $payloadKunjungan->kelurahan_id,
            'rt' => $payloadKunjungan->rt,
            'rw' => $payloadKunjungan->rw,
            'kode_pos' => $payloadKunjungan->kode_pos,
            'alamat_pasien' => $payloadKunjungan->alamat_pasien,
            'no_telepon_pasien' => $payloadKunjungan->no_telepon_pasien,
            'pekerjaan_id' => $payloadKunjungan->pekerjaan_id,
            'pt' => $payloadKunjungan->pt,
            'dokterpengganti_id' => $payloadKunjungan->dokter_pengganti_id
        ];

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $pendaftaran = new Pendaftaran;
            $pendaftaran->attributes = $dataPendaftaran;
            $tmpPendaftaran = (new Penomoran)->setNoReg(DocoConstants::INST_ID_RJ);

            if(empty($tmpPendaftaran)) return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'No pendaftaran tidak tersedia.'
            ];

            $getLastRm = (new Penomoran)->getSetNoRm(null, self::PENOMORAN_PASIEN);
            if(empty($getLastRm)) return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Gagal mendapatkan No Rekam Medik.'
            ];

            $pendaftaran->no_pendaftaran = $tmpPendaftaran['no_reg'];

            if ($pendaftaran->validate() && $pendaftaran->save(false)) {
                $idPendaftaran = $pendaftaran->pendaftaran_id;
                $prosesBpjs = false;
                if ($isBpjs && !$allowBpjs) {
                    $bpjsRes = $this->saveBpjs($payloadBpjs, $idPendaftaran, $pasienBaru);
                    $prosesBpjs = true;
                    if (is_array($bpjsRes)) return $bpjsRes;
                } else if ($isBpjs && $allowBpjs) { //handle bpjs unauth
                    $bpjsRes = $this->saveBpjsWithouBridging($payloadBpjs, $idPendaftaran, $pasienBaru);
                    $prosesBpjs = false;
                }

                if($isKonsul) {
                    $dataKonsul['pendaftaran_id'] = $idPendaftaran;
                    $setKonsul = $this->actionSetKunjunganPoli($dataKonsul);
                }
                $transaction->commit();
                // Integrasi Akunting
                IntegrasiAkunting::integrateKarcisPasien($idPendaftaran);

                if (!empty($detailTindakan)) {
                    // Select no pendaftaran
                    $noPendaftaran = Pendaftaran::findOne($idPendaftaran)->no_pendaftaran;

                    // Integrasi kasir tagihan pelayanan
                    $tagihanTindakan = (new KasirService)->tagihan([
                        'pendaftaran_id' => $idPendaftaran,
                        'no_pendaftaran' => $noPendaftaran,
                        'instalasi_id' => $pendaftaran->instalasi_id,
                        'ruangan_id' => $pendaftaran->ruangan_id,
                        'penjamin_id' => $pendaftaran->penjamin_id,
                        'kelas_pelayanan_id' => $pendaftaran->kelaspelayanan_id
                    ], $detailTindakan);

                    if (isset($tagihanTindakan['meta']['result']) && $tagihanTindakan['meta']['result'] == 'failed') {
                        $transaction->rollBack();
                        return isset($tagihanTindakan['message']) ? $tagihanTindakan['message'] : 'Terjadi Kesalahan API';
                    }
                }

                $params['route'] = 'app/save-pendaftaran-rajal';
                $params['data'] = $this->syncPendaftaran($idPendaftaran);
                $sendData = (new PendaftaranService)->syncPendaftaranSty($params, function($data, $result) {
                    return $result;
                });
                $payload = $params['data'];
                $this->setLog($idPendaftaran, $payload, $sendData);
                (new Penomoran)->save($tmpPendaftaran['konfig_id'], 1, $tmpPendaftaran['no_reg'], $tmpPendaftaran['no_reg']);
                (new Penomoran)->getSetNoRm(null, self::PENOMORAN_PASIEN, 'save');

                return [
                    'message' => 'Proses Pendaftaran berhasil',
                    'id' => DocoHelpers::encrypt($idPendaftaran),
                    'is_bpjs' => $prosesBpjs
                ];
            }
            return [
                'status' => 422,
                'data' => $pendaftaran->errors
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $exception = preg_match('/(?<=ERROR:  )(.*)/',$e->getMessage(),$out);
            $message = $e->getMessage();
            if (isset($out[1])) {
                $message = 'Query :' . $out[1];
            }
            (new Penomoran)->save($tmpPendaftaran['konfig_id'], 0, $tmpPendaftaran['no_reg'], $tmpPendaftaran['no_reg']);
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $message
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            (new Penomoran)->save($tmpPendaftaran['konfig_id'], 0, $tmpPendaftaran['no_reg'], $tmpPendaftaran['no_reg']);
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $e->getMessage()
            ];
        }
    }

    /**
     * This function will get data pendaftaran
     * and send it to serconn for
     * Sync Santo Yusup
     *
     * @return array
     * @author : Erlangga (erlangga@docotel.com)
     */
    private function syncPendaftaran($idPendaftaran)
    {
        $result = $asuransi = $keluarga = $penanggung = $pasien = $penjamin = $penanggungJawab = [];
        $pdftrn = SyPendaftaranView::find()
        ->where(['pendaftaran_id' => $idPendaftaran])
        ->asArray()
        ->one();

        if(!empty($pdftrn)) {
            $add = json_decode($pdftrn['additional_data'], true);

            if(!empty($pdftrn['asuransipasien_id'])) {
                $asuransi = SyAsuransiPasienView::find()
                ->where(['asuransipasien_id' => $pdftrn['asuransipasien_id']])
                ->asArray()
                ->one();
            }

            //** keluarga harus selalu di kirim */
            // if(isset($add['keluargapasien']) && !empty($add['keluargapasien'])) {
                $keluarga = SyKeluargaPasienView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['keluargapasien_id' => SORT_DESC])
                ->asArray()
                ->one();
            // }

            if(isset($add['penanggungbiaya']) && !empty($add['penanggungbiaya'])) {
                $penanggung = SyPenanggungBiayaView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['penanggungbiaya_id' => SORT_DESC])
                ->asArray()
                ->one();
            }

            if(isset($add['penanggung_jawab']) && !empty($add['penanggung_jawab'])) {
                $penanggungJawab = SyPenanggungJawabView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
                ->orderBy(['penanggungjawab_id' => SORT_DESC])
                ->asArray()
                ->one();
            }

            $pasien = SyPasienView::find()
            ->where(['pasien_id' => $pdftrn['pasien_id']])
            ->asArray()
            ->one();

            if(isset($pdftrn['umur']) && !empty($pdftrn['umur'])) {
                $umurExp = explode(" ",$pdftrn['umur']);
                $pdftrn['umur_hari'] = $umurExp[4];
                $pdftrn['umur_bulan'] = $umurExp[2];
                $pdftrn['umur_tahun'] = $umurExp[0];
            }

            if (isset($pasien['additional_pasien']) && !empty($pasien['additional_pasien'])) {
                $additionalPasien = json_decode($pasien['additional_pasien']);
                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                            $ktp = $value->no_identitas_pasien;
                        }
                    }
                    if(isset($ktp)) {
                        $pasien['nik'] = $ktp;
                    }
                }
            }

            $penjamin = SyPenjaminView::find()
            ->where(['penjamin_id' => $pdftrn['penjamin_id']])
            ->asArray()
            ->one();
        }

        $result = [
            'pendaftaran' => $pdftrn,
            'pasien' => $pasien,
            'asuransi' => $asuransi,
            'keluarga' => $keluarga,
            'penanggung' => $penanggung,
            'penjamin' => $penjamin,
            'penanggungJawab' => $penanggungJawab,
        ];

        // $modelSync = new SyncsantoyusupR;
        // $modelSync->pendaftaran_id = $pdftrn['pendaftaran_id'];
        // $modelSync->pasien_id = $pdftrn['pasien_id'];
        // $modelSync->created_date = date("Y-m-d H:i:s");
        // $modelSync->count_sync = 1;
        // $modelSync->payload = json_encode($result);
        // $modelSync->save(false);

        return $result;
    }

    private function setLog($idPendaftaran, $result, $sendData)
    {
        $modelSync = new SyncsantoyusupR;
        $modelSync->pendaftaran_id = $idPendaftaran;
        $modelSync->pasien_id = null;
        $modelSync->created_date = date("Y-m-d H:i:s");
        $modelSync->count_sync = 1;
        $modelSync->payload = json_encode($result);
        $modelSync->uid = json_encode($sendData);
        $modelSync->save(false);
    }

    private function setSlotSequenceAntrian($dataPendaftaran)
    {
        $jadwaldokter = (new InfoJadwalDokterView)->getJadwalDokterIdByWaktu($dataPendaftaran->pegawai_id, $dataPendaftaran->ruangan_id);
        $jadwaldokterId = ArrayHelper::getValue($jadwaldokter, 'jadwaldokter_id');
        $slot = (new Pendaftaran)->getLastSequencePendaftaran($jadwaldokterId, date('Y-m-d'));

        $model = Antrian::findOne($dataPendaftaran->antrian_id);
        $model->jadwaldokter_id = empty($model->jadwaldokter_id) ? $jadwaldokterId : $model->jadwaldokter_id;
        $model->slot_sequence = empty($model->slot_sequence) ? ArrayHelper::getValue($slot, 'slot_sequence') : $model->slot_sequence;
        if($model->save(false)) {
            return true;
        } else {
            return $model;
        }
    }

    public function registrationLimiterValidator($pasien_id = null, $pasienAttributes = [])
    {
        $cache = Yii::$app->cache;
        if ( is_null($pasien_id) && empty($pasienAttributes) ) {
            return false;
        }
        $cacheName = 'pendaftaran-pasien-validator-'.$pasien_id;
Yii::warning($cache->get($cacheName));
        if ( !empty($cache->get($cacheName)) ) {
            return true;
        } else {
            $cache->set($cacheName, 1, 55);
        }

        return false;
    }
}
