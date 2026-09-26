<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 13:25
 * @Mod: Ardi Pratama Septiadi
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\components\BpjsController;
use app\modules\v1\components\Penomoran;

use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\KeluargaPasien;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\InfoDataPendaftaranView;
use app\modules\v1\models\TarifTotalRsFn;
use app\modules\v1\models\TarifKomponenRsFn;
use app\modules\v1\models\PenanggungBiayaView;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyAsuransiPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungJawabView;
use app\modules\v1\models\SyPenanggungBiayaView;
use app\modules\v1\models\SyPenjaminView;
use app\modules\v1\models\SyncsantoyusupR;

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
use Doco\components\DocoConstansId;
use Doco\Services\KasirService;
use Doco\Services\Vendors\PendaftaranService;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\LaporanKunjunganRawatDaruratView;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Traits\AutoPlafonTrait;
use app\modules\v1\models\CaraBayar;

class PendaftaranIgdController extends BpjsController
{
    use AutoPlafonTrait;
    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    static protected $_url = 'on/sinkronisasi/sync';
    static protected $_restSerconn;
    static protected $_headers;
    const PENOMORAN_RJRD = 193;
    const K_P_STYP = 69;
    const PENOMORAN_PASIEN = 190;

    public $messageBroker = [
        'save-pendaftaran' => [
            'services' => [
                'Mhg' => [
                    'CreateMasterPatient' => [
                        'result' => true,
                        'successProcess'=>true,
                        'state' => 'create',
                    ],
                ],
                'Satusehat' => [
                    'Encounter' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ],
                ],
            ]
        ],
    ];

    public function init()
    {
        self::$_restSerconn = Yii::$app->serconn->guzzle();
        self::$_headers = Yii::$app->request->headers;
    }

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

    public function actionSavePendaftaran()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $isBpjs = false;
        $pasienBaru = true;
        $isMultiPayer = false;

        /** Validasi Error */
        $errorParse = [];
        /** Tindakan Karcis */
        $listTindakan = $tmp = $sepNew = $komponen = [];
        $listTagihan = [];
        /** allow Bpjs */
        $allowBpjs = $request->post('allow_bpjs', false);
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
         * @var app\modules\v1\payload\AsuransiForm
         */
        $asuransi = [];

        /**
         * @var app\modules\v1\payload\BpjsNewForm
         */
        $bpjs = [];

        $payload = new TipePasien;
        $payloadKunjungan = new Kunjungan;
        $pasienPayLoad = new PayloadPasien;
        $payloadMultipayer = new PayloadMultiPayer;
        $pasienPayLoad->scenario = "pendaftaran-igd";

        $pasienPayLoad->attributes = !empty($post['pasien']) ? $post['pasien'] : [];
        $pasienPayLoad->namadepan = ArrayHelper::getValue($post, 'pasien.namadepan');
        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        $payloadMultipayer->isMultipayer = $isMultiPayer;

        if (!empty($post['asuransi'])) {
            $payloadAsuransi = new AsuransiForm;
            $payloadAsuransi->attributes = $post['asuransi'];
            $payloadAsuransi->tgl_konfirmasi = !empty($payloadAsuransi->tgl_konfirmasi)
                ? $payloadAsuransi->tgl_konfirmasi : null;
            $payloadAsuransi->penjamingrade_id = isset($payloadAsuransi['penjamingrade_id']) && !empty($payloadAsuransi['penjamingrade_id']) ? $payloadAsuransi['penjamingrade_id'] : null;
            $asuransi = $payloadAsuransi->attributes;
            if (!$payloadAsuransi->validate()) $errorParse['asuransi'] = $payloadAsuransi->errors;
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
                ? date('Y-m-d', strtotime($payloadPjPasien->pj_tanggal_lahir)) : null;
            $payloadPjPasien->scenario = 'pendaftaran-igd';
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
                $pasienBaru = false;
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
        $statusPasien = !empty($payload->no_rekam_medik) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B;

        $getTotalhari = DocoHelpers::convertToHari($pasienPayLoad->tanggal_lahir);
        $getGolongan = Cache::getGolonganUmur();
        $getGolUmurId = 1;

        foreach ($getGolongan as $value) {
            if (
                $value['golonganumur_minimal'] <= $getTotalhari
                && $value['golonganumur_maksimal'] >= $getTotalhari
            ) {
                $getGolUmurId = $value['golonganumur_id'];
                break;
            }
        }
        $pasienPayLoad->golonganumur_id = $getGolUmurId;

        $additionals = [
            'tarif' => $listTagihan,
            'rujukan' => $rujukan,
            'penanggung_jawab' => $pjPasien,
            'asuransi' => $asuransi,
            'additional_payer' => $payloadMultipayer
        ];

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
            $autoPlafon = $this->setAutoPlafon(DocoConstants::INST_ID_RD, $payloadKunjungan->kelaspelayanan_id, $payloadKunjungan->ruangan_id);
            if (!empty($autoPlafon)) {
                $limitTagihan = $autoPlafon;
            }
        }

        $dataPendaftaran = [
            'tgl_pendaftaran' => date('Y-m-d H:i:s'),
            'penjamin_id' => $payload->penjamin_id,
            'pasien_id' => $pasienPayLoad->pasien_id,
            'pegawai_id' => $payloadKunjungan->dokter_id,
            'instalasi_id' => DocoConstants::INST_ID_RD,
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
            if ($pendaftaran->save()) {
                $idPendaftaran = $pendaftaran->pendaftaran_id;
                $prosesBpjs = false;
                if ($isBpjs && !$allowBpjs) {
                    $prosesBpjs = true;
                    $bpjsRes = $this->saveBpjs($payloadBpjs, $idPendaftaran, $pasienBaru);
                    if (is_array($bpjsRes)) return $bpjsRes;
                } else if ($isBpjs && $allowBpjs) { //handle bpjs unauth
                    $bpjsRes = $this->saveBpjsWithouBridging($payloadBpjs, $idPendaftaran, $pasienBaru);
                    $prosesBpjs = false;
                }

                $transaction->commit();
                // Integrasi Akunting
                IntegrasiAkunting::integrateKarcisPasien($idPendaftaran);

                // Select no pendaftaran
                $noPendaftaran = Pendaftaran::findOne($idPendaftaran)->no_pendaftaran;
                $pendaftaran->no_pendaftaran = $noPendaftaran;

                if(Yii::$app->params['isRabbitMq']) {   
                    (new RabbitBgProcess())->send([
                        "penjamin_id" => $pendaftaran->penjamin_id,
                        "kodebenefit" => $request->post('refresh_asuransi'),
                        "data_peserta" => $dataPeserta,
                        "data_pendaftaran" => $pendaftaran->attributes
                    ], 'integrate_insurance', 'integrate_insurance');   
                }

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
                        return isset($tagihanTindakan['message']) ? $tagihanTindakan['message'] : 'Terjadi Kesalahan API';
                    }
                }

                $restPendaftaran = LaporanKunjunganRawatDaruratView::find()
                ->where(['pendaftaran_id' => $idPendaftaran])
                ->asArray()->one();
                
                return [
                    'message' => 'Proses Pendaftaran berhasil',
                    'id' => DocoHelpers::encrypt($idPendaftaran),
                    'is_bpjs' => $prosesBpjs,
                    'pendaftaran' => $restPendaftaran
                ];
            }
            return [
                'status' => 422,
                'data' => $pendaftaran->errors
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $exception = preg_match('/(?<=ERROR:  )(.*)/', $e->getMessage(), $out);
            $message = $e->getMessage();
            if (isset($out[1])) {
                $message = 'Query :' . $out[1];
            }
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $message
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $e->getMessage()
            ];
        }
    }

    public function actionSavePendaftaranIgd()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $isBpjs = false;
        $pasienBaru = true;
        $tmpPendaftaran = '';

        /** Validasi Error */
        $errorParse = [];
        /** Tindakan Karcis */
        $listTindakan = $tmp = $sepNew = $komponen = [];
        $listTagihan = [];
        /** allow Bpjs */
        $allowBpjs = $request->post('allow_bpjs', false);
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
        $pasienPayLoad = new PayloadPasien;
        $pasienPayLoad->scenario = "pendaftaran-igd";

        $pasienPayLoad->attributes = !empty($post['pasien']) ? $post['pasien'] : [];
        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        /* Default Jenis Kasus Penyakit Umum */
        $payloadKunjungan->jeniskasuspenyakit_id = 23;
        $payloadKunjungan->kelaspelayanan_id =self::K_P_STYP;

        if (!empty($post['asuransi'])) {
            $payloadAsuransi = new AsuransiForm;
            $payloadAsuransi->attributes = $post['asuransi'];
            $payloadAsuransi->tgl_konfirmasi = !empty($payloadAsuransi->tgl_konfirmasi)
                ? $payloadAsuransi->tgl_konfirmasi : null;
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
                ? date('Y-m-d', strtotime($payloadPjPasien->pj_tanggal_lahir)) : null;
            if ($payloadPjPasien->pj_pengantar == 990){  //Pasien
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
            } else if ($payloadPjPasien->pj_pengantar == 991) {  //Ortu/Wali
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
        $statusPasien = !empty($payload->no_rekam_medik) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B;

        $getTotalhari = DocoHelpers::convertToHari($pasienPayLoad->tanggal_lahir);
        $getGolongan = Cache::getGolonganUmur();
        $getGolUmurId = 1;

        foreach ($getGolongan as $value) {
            if (
                $value['golonganumur_minimal'] <= $getTotalhari
                && $value['golonganumur_maksimal'] >= $getTotalhari
            ) {
                $getGolUmurId = $value['golonganumur_id'];
                break;
            }
        }
        $pasienPayLoad->golonganumur_id = $getGolUmurId;

        $additionals = [
            'tarif' => $listTagihan,
            'rujukan' => $rujukan,
            'penanggung_jawab' => $pjPasien,
            'asuransi' => $asuransi,
            'penanggungbiaya' => $penanggungbiaya,
            'keluargapasien' => $keluargapasien,
        ];

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
            $autoPlafon = $this->setAutoPlafon(DocoConstants::INST_ID_RD, $payloadKunjungan->kelaspelayanan_id, $payloadKunjungan->ruangan_id);
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
            'instalasi_id' => DocoConstants::INST_ID_RD,
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
            $tmpPendaftaran = (new Penomoran)->setNoReg(DocoConstants::INST_ID_RD);

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

            if ($pendaftaran->save()) {
                $idPendaftaran = $pendaftaran->pendaftaran_id;
                $prosesBpjs = false;
                if ($isBpjs && !$allowBpjs) {
                    $prosesBpjs = true;
                    $bpjsRes = $this->saveBpjs($payloadBpjs, $idPendaftaran, $pasienBaru);
                    if (is_array($bpjsRes)) return $bpjsRes;
                } else if ($isBpjs && $allowBpjs) { //handle bpjs unauth
                    $bpjsRes = $this->saveBpjsWithouBridging($payloadBpjs, $idPendaftaran, $pasienBaru);
                    $prosesBpjs = false;
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

                $params['route'] = 'app/save-pendaftaran-igd';
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
            $exception = preg_match('/(?<=ERROR:  )(.*)/', $e->getMessage(), $out);
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
}
