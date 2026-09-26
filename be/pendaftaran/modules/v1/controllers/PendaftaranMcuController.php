<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\components\BpjsController;
use app\modules\v1\controllers\AllowController;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\MasterPaketMcuView;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\TarifTotalRsFn;
use app\modules\v1\models\TarifKomponenRsFn;
use app\modules\v1\models\InfPasienPenunjang;
use app\modules\v1\models\KonfigSystem;

use app\modules\v1\payload\TipePasien;
use app\modules\v1\payload\Kunjungan;
use app\modules\v1\payload\Rujukan as PayloadRujukan;
use app\modules\v1\payload\Pasien as PayloadPasien;
use app\modules\v1\payload\Antrian as PayloadAntrian;
use app\modules\v1\payload\MultiCarabayar as PayloadMultiPayer;
use app\modules\v1\payload\PjPasien;
use app\modules\v1\payload\AsuransiForm;
use app\modules\v1\payload\BpjsNewForm;

use app\modules\v1\cache\Cache;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use SirsCore\features\IntegrasiAkunting;
use Doco\Services\KasirService;
use Doco\models\Antrian as AntrianDcms;
use Doco\components\DocoConstansId;
use app\modules\v1\models\LaporanKunjunganMcuView;
use Doco\Traits\AutoPlafonTrait;
use app\modules\v1\models\CaraBayar;

class PendaftaranMcuController extends BpjsController
{
    use AutoPlafonTrait;
    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    const PENUNJANG = 'penunjang';
    const KONSUL = 'konsul';
    const COMPLETE = 'complete';
    const MULTIPLE_RM = 'multiple_rm';
    const JK_LAKI = 'L';
    const LAINNYA = 'lainnya';
    const KAWIN = 'K';
    const ID_LAINNYA = 100;
    const ID_KAWIN = 302;
    const ID_BLM_KAWIN = 303;
    const ID_GOL_DRH_TIDAK_TAHU = 600;

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
                'Lis' => [
                    'BridgingLis' => [
                        'result' => true,
                        'successProcess'=>true,
                        'is_aps' => true
                    ]
                ],
                'Ris' => [
                    'RisBroker' => [
                        'result' => true,
                        'successProcess'=>true,
                        'is_aps' => true
                    ]
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

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["save-pendaftaran"] = ["POST"];
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

        if($post['tipe_pasien']['is_kolektif'] == true){
            if(isset($post['kunjungan']['list_pasien_mcu']) && !empty($post['kunjungan']['list_pasien_mcu'])){
                return $this->actionSavePendaftaranMultiple();
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Data pasien tidak di temukan'
                ]);
            }
        }
        $isBpjs = false;
        $pasienBaru = true;
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

        $payload = new TipePasien;
        $payloadKunjungan = new Kunjungan;
        $payloadAntrian = new PayloadAntrian;
        $pasienPayLoad = new PayloadPasien;
        $payloadMultipayer = new PayloadMultiPayer;
        $payloadKunjungan->scenario = "pendaftaran-rajal";

        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        $payloadMultipayer->isMultipayer = $isMultiPayer;

        if (!empty($post['pasien']) && empty($payload->no_rekam_medik)) {
            $postPasien = $post['pasien'];
            $pasienPayLoad->nama_pasien = $postPasien['nama_pasien'] ? $postPasien['nama_pasien'] : null;
            $pasienPayLoad->jeniskelamin = $postPasien['jeniskelamin'] ? $postPasien['jeniskelamin'] : null;
            $pasienPayLoad->tanggal_lahir = $postPasien['tanggal_lahir'] ? $postPasien['tanggal_lahir'] : null;
            $pasienPayLoad->alamat_pasien = $postPasien['alamat_pasien'] ? $postPasien['alamat_pasien'] : null;
            $pasienPayLoad->statusperkawinan = $postPasien['statusperkawinan'] ? $postPasien['statusperkawinan'] : null;
            $pasienPayLoad->golongandarah = $postPasien['golongandarah'] ? $postPasien['golongandarah'] : null;
            $pasienPayLoad->tempat_lahir = $postPasien['tempat_lahir'] ? $postPasien['tempat_lahir'] : null;
            $pasienPayLoad->umur = $postPasien['umur'] ? $postPasien['umur'] : null;
            $pasienPayLoad->propinsi_id = $postPasien['propinsi_id'] ? $postPasien['propinsi_id'] : null;
            $pasienPayLoad->pendidikan_id = $postPasien['pendidikan_id'] ? $postPasien['pendidikan_id'] : null;
            $pasienPayLoad->pekerjaan_id = $postPasien['pekerjaan_id'] ? $postPasien['pekerjaan_id'] : null;
            $pasienPayLoad->namadepan = $postPasien['namadepan'] ? $postPasien['namadepan'] : null;
            $pasienPayLoad->nama_ibu = $postPasien['nama_ibu'] ? $postPasien['nama_ibu'] : null;
            $pasienPayLoad->nama_ayah = $postPasien['nama_ayah'] ? $postPasien['nama_ayah'] : null;
            $pasienPayLoad->anakke = $postPasien['anakke'] ? $postPasien['anakke'] : null;
            $pasienPayLoad->jumlah_bersaudara = $postPasien['jumlah_bersaudara'] ? $postPasien['jumlah_bersaudara'] : null;
            $pasienPayLoad->rt = $postPasien['rt'] ? $postPasien['rt'] : null;
            $pasienPayLoad->rw = $postPasien['rw'] ? $postPasien['rw'] : null;
            $pasienPayLoad->no_telepon_pasien = $postPasien['no_telepon_pasien'] ? $postPasien['no_telepon_pasien'] : null;
            $pasienPayLoad->alamatemail = $postPasien['alamatemail'] ? $postPasien['alamatemail'] : null;
            $pasienPayLoad->warga_negara = $postPasien['warga_negara'] ? $postPasien['warga_negara'] : null;
            $pasienPayLoad->suku_id = $postPasien['suku_id'] ? $postPasien['suku_id'] : null;
            $pasienPayLoad->agama = $postPasien['agama'] ? $postPasien['agama'] : null;
            $pasienPayLoad->additional_identitas = isset($postPasien['additional_identitas']) ? $postPasien['additional_identitas'] : null;
            $pasienPayLoad->kabupaten_id = ArrayHelper::getValue($postPasien, 'kabupaten_id', null);
            $pasienPayLoad->kecamatan_id = ArrayHelper::getValue($postPasien, 'kecamatan_id', null);
            $pasienPayLoad->kelurahan_id = ArrayHelper::getValue($postPasien, 'kelurahan_id', null);
        }

        if (!empty($post['asuransi'])) {
            $payloadAsuransi = new AsuransiForm;
            $payloadAsuransi->attributes = $post['asuransi'];
            $payloadAsuransi->tgl_konfirmasi = !empty($payloadAsuransi->tgl_konfirmasi) ? date('Y-m-d', strtotime($payloadAsuransi->tgl_konfirmasi)) : null;
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

        if (!empty($payload->no_rekam_medik)) {
            $pasien = $this->getPasien()->where([
                'no_rekam_medik' => $payload->no_rekam_medik
            ])->asArray()->one();
            if (empty($pasien)) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Pasien tidak ditemukan'
                ]);
            }
            $pasienPayLoad->scenario = "pendaftaran-pasien-lama";
            $pasienPayLoad->attributes = $pasien;
            $pasienBaru = false;
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

        // if ($is_nourut) {
        //     $payloadKunjungan->scenario = 'pendaftaran-rajal-nomor-urut';
        // }

        /** Get Golongan Umur Id */
        $pasienPayLoad->tgl_rekam_medik = date('Y-m-d');
        $pasienPayLoad->is_aps = false;
        $pasienPayLoad->tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir) 
                            ?  date('Y-m-d', strtotime($pasienPayLoad->tanggal_lahir)) : null;
        if ($pasienBaru) {
            if (!$pasienPayLoad->validate()) $errorParse['pasien'] = $pasienPayLoad->errors;
        }

        /** Validate Form */
        if (!$payload->validate()) $errorParse['tipe_pasien'] = $payload->errors;
        if (!$payloadKunjungan->validate()) $errorParse['kunjungan'] = $payloadKunjungan->errors;
        
        if (!empty($errorParse)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $errorParse
            ]);
        }

        $umur = ucwords(DocoHelpers::getUmur($pasienPayLoad->tanggal_lahir));

        $statusPeriksa = DocoConstants::VAR_SP_AP;
        $isKarcis = false;
        
        if ($payload->carabayar_id == DocoConstants::CB_PEN_UMUM) {
            $statusPeriksa = DocoConstants::VAR_SP_AK;
            $isKarcis = true;
        }

        $statusPasien = !empty($payload->no_rekam_medik) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B;

        $listOrderMcu = [
            self::PENUNJANG => [],
            self::KONSUL => []
        ];

        $tmpOrderRuangan = [];
        $caraBayarId = Cache::getCaraBayarPenjamin($payload->penjamin_id);
        $attrCaraBayar = Cache::getAttrCaraBayar($caraBayarId);
        $groupCaraBayar = !empty($attrCaraBayar['groupcarabayar_id']) ? $attrCaraBayar['groupcarabayar_id'] : null;
        
        $listPaket = json_decode($payloadKunjungan->list_paket);
        $tindakanKarcis = json_decode($payloadKunjungan->tindakan_karcis,true);
        if (!empty($listPaket) && is_array($listPaket)) {
           $generateTindakanKarcis = $this->generateTindakanKarcis($payloadKunjungan, $payload, $listPaket, $tindakanKarcis, $isKarcis, $statusPeriksa);
           if(isset($generateTindakanKarcis['listTagihan']) && isset($generateTindakanKarcis['listOrderMcu'])){
                $listTagihan = $generateTindakanKarcis['listTagihan'];
                $listOrderMcu = $generateTindakanKarcis['listOrderMcu'];
           } else {
               return $generateTindakanKarcis;
           }
        } else {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Paket tidak boleh kosong'
            ]);
        }

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
            if (isset($is_nourut) && $is_nourut) {
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
            'tarif' => [],
            'rujukan' => $rujukan,
            'antrian' => $payloadAntrian,
            'penanggung_jawab' => $pjPasien,
            'asuransi' => $asuransi,
            'order_mcu' => $listOrderMcu,
            'additional_payer' => $payloadMultipayer
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
        if (!empty($post['bpjs']) || $groupCaraBayarId == DocoConstants::GROUP_BPJS) {
            $autoPlafon = $this->setAutoPlafon($payloadKunjungan->instalasi_id, $payloadKunjungan->kelaspelayanan_id, $payloadKunjungan->ruangan_id);
            if (!empty($autoPlafon)) {
                $limitTagihan = $autoPlafon;
            }
        }
        
        $dataPendaftaran = [
            'tgl_pendaftaran' => date('Y-m-d H:i:s'),
            'penjamin_id' => $payload->penjamin_id,
            'pasien_id' => $pasienPayLoad->pasien_id,
            'pegawai_id' => $payloadKunjungan->dokter_id,
            'instalasi_id' => $payloadKunjungan->instalasi_id,
            'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
            'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
            'carabayar_id' => $caraBayarId,
            'golonganumur_id' => $getGolUmurId,
            'umur' => $umur,
            'rujukan_id' => null,
            'antrian_id' => $payload->antrian_id,
            'kunjungan' => !empty($payload->no_rekam_medik) ? DocoConstants::VAR_K_L : DocoConstants::VAR_K_B,
            'ruangan_id' => $payloadKunjungan->ruangan_id,
            'transportasi' => $payloadKunjungan->transportasi,
            'keadaan_masuk' => $payloadKunjungan->keadaan_masuk,
            'status_periksa' => $statusPeriksa,
            'status_pasien' => $statusPasien,
            'status_masuk' => !empty($rujukan) ? DocoConstants::VAR_SM_R : DocoConstants::VAR_SM_NR,
            'keterangan_pendaftaran' => $payloadKunjungan->keterangan,
            'is_karcis' => $isKarcis,
            'additional_data' => json_encode($additionals),
            'is_aps' => true,
            'limit_tagihan' => $limitTagihan,
            'is_multipayer' => $isMultiPayer,
            'petugas_id' => $petugas_id,
            'petugas_tgl_pembuat' => $petugas_tgl_pembuat,
            'referal_pegawai_id' => (int) $payloadKunjungan->referal > 0 ? $payloadKunjungan->referal : null,
            'referal_luar' => (int) $payloadKunjungan->referal == 0 ? strtoupper($payloadKunjungan->referal) : null,
        ];

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $pendaftaran = new Pendaftaran;
            $pendaftaran->attributes = $dataPendaftaran;
            
            if ($pendaftaran->validate() && $pendaftaran->save(false)) {
                $idPendaftaran = $pendaftaran->pendaftaran_id;

                $prosesBpjs = false;
                if ($isBpjs && !$allowBpjs) {
                    $bpjsRes = $this->saveBpjs($payloadBpjs, $idPendaftaran, $pasienBaru);
                    $prosesBpjs = true;
                    if (is_array($bpjsRes)) return $bpjsRes;
                }
                $transaction->commit();
                // Integrasi Akunting
                IntegrasiAkunting::integrateKarcisPasien($idPendaftaran);

                if (!empty($listTagihan)) {
                    // Select no pendaftaran
                    $noPendaftaran = Pendaftaran::findOne($idPendaftaran)->no_pendaftaran;

                    // Integrasi kasir tagihan pelayanan
                    $tagihan = (new KasirService)->tagihan([
                        'pendaftaran_id' => $idPendaftaran,
                        'no_pendaftaran' => $noPendaftaran,
                        'instalasi_id' => $pendaftaran->instalasi_id,
                        'ruangan_id' => $pendaftaran->ruangan_id,
                        'penjamin_id' => $pendaftaran->penjamin_id,
                        'kelas_pelayanan_id' => $pendaftaran->kelaspelayanan_id
                    ], $listTagihan);

                    if (isset($tagihan['meta']['result']) && $tagihan['meta']['result'] == 'failed') {
                        $transaction->rollBack();
                        return isset($tagihan['message']) ? $tagihan['message'] : 'Terjadi Kesalahan API';
                    }
                }

                $resPendaftaran = Pendaftaran::find()
                ->select(
                    'pendaftaran_t.pendaftaran_id, 
                    pendaftaran_t.pasien_id, 
                    pendaftaran_t.status_pasien as status_pasien_id, 
                    look_stat_pasien.lookup_name as status_pasien'
                )
                ->leftJoin('lookup_m AS look_stat_pasien', 'look_stat_pasien.lookup_id::TEXT = pendaftaran_t.status_pasien::TEXT')
                ->where(['pendaftaran_id' => $idPendaftaran])
                ->asArray()->one();

                return [
                    'message' => 'Proses Pendaftaran berhasil',
                    'id' => DocoHelpers::encrypt($idPendaftaran),
                    'is_bpjs' => $prosesBpjs,
                    'pendaftaran' => $resPendaftaran,
                ];
            }
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $pendaftaran->errors
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $exception = preg_match('/(?<=ERROR:  )(.*)/',$e->getMessage(),$out);
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
            Yii::$app->response->statusCode = 500;
            $transaction->rollBack();
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function actionDownloadExcel()
    {
        $result = $header = $footer = $toggle = $custHeader = [];
        $custHeader = [
            [
                [
                    'label'=>'No',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'No Asuransi',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Nama Pemilik Asuransi',
                    'rowspan'=>1,
                ],
                [
                    'label'=>'Jenis Identitas (KTP/SIM/PASPOR/LAINNYA)',
                    'colspan'=>1,
                ],
                [
                    'label'=>'No Identitas',
                    'colspan'=>1,
                ],
                [
                    'label'=>'No Rekam Medik',
                    'colspan'=>1,
                ],
                [
                    'label'=>'Nama Pasien',
                    'colspan'=>1,
                ],
                [
                    'label'=>'Tempat Lahir',
                    'colspan'=>1,
                ],
                [
                    'label'=>'Tanggal Lahir (DD-MM-YYYY)',
                    'colspan'=>1,
                ],
                [
                    'label'=>'Jenis Kelamin (L/P)',
                    'colspan'=>1,
                ],
                [
                    'label'=>'Golongan Darah (A/B/O/AB/-)',
                    'colspan'=>1,
                ],
                [
                    'label'=>'Status Perkawinan (K/BK)',
                    'colspan'=>1,
                ],
                [
                    'label'=>'Alamat',
                    'colspan'=>1,
                ],
                [
                    'label'=>'No Telepon',
                    'colspan'=>1,
                ],
                [
                    'label'=>'No Rujukan',
                    'colspan'=>1,
                ],
                [
                    'label'=>'Rujukan Dari (RS)',
                    'colspan'=>1,
                ],
                [
                    'label'=>'Nama Perujuk',
                    'colspan'=>1,
                ]
            ]
        ];
        $filePath = DocoHelpers::exportExcel('Pendaftaran MCU', $result, $header,  array(
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ),$footer,[], true);
        $filePath->save('php://output');
        die;
    }

    public function actionSavePendaftaranMultiple()
    {
        $post =  Yii::$app->request->post();

        $data = [];
        $listPaket = [];

        $payloadKunjungan = new Kunjungan;
        $payload = new TipePasien;
        $payloadAntrian = new PayloadAntrian;

        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];

        $data = json_decode($post['kunjungan']['list_pasien_mcu'], true);
        $listPaket = json_decode($payloadKunjungan->list_paket);
        
        if(empty($data)){
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Data pasien tidak di temukan'
            ]);
        }

        if(empty($listPaket)){
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Paket tidak boleh kosong'
            ]);
        }

        $restSerconn = Yii::$app->serconn->guzzle();
        $headers = Yii::$app->request->headers;
        $post['authorization'] = $headers['authorization'];
        $post['x-owner'] = $headers['x-owner'];
        $request = $restSerconn->post('on/pendaftaranmcu/simpanmcukolektif', [
            'body' => json_encode($post)
        ]);

        return [
            'message' => 'Proses Pendaftaran berhasil',
        ];
    }

    /** kebutuhan data ke pasienmasukpenunjang_t by trigger */
    private function generateTindakanKarcis($payloadKunjungan, $payload, $listPaket, $tindakanKarcis, $isKarcis, $statusPeriksa)
    {
        $tmpOrderRuangan = $listTagihan = [];
        $caraBayarId = Cache::getCaraBayarPenjamin($payload->penjamin_id);
        $attrCaraBayar = Cache::getAttrCaraBayar($caraBayarId);
        $groupCaraBayar = !empty($attrCaraBayar['groupcarabayar_id']) ? $attrCaraBayar['groupcarabayar_id'] : null;
        $statusPasien = !empty($payload->no_rekam_medik) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B;

        $listOrderMcu = [
            self::PENUNJANG => [],
            self::KONSUL => []
        ];

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
        ->where([
            'tipepaket_id' => $listPaket
        ])->orWhere([
            'kelompoktindakan_id' => DocoConstants::VAR_KEL_KRCS,
            'daftartindakan_id' => $tindakanKarcis
        ])->all();

        $tarifKomponen = (new TarifKomponenRsFn([
            'extParam' => [
                $payloadKunjungan->ruangan_id,
                $payload->penjamin_id,
                $payloadKunjungan->kelaspelayanan_id, 
                $tipeTarif
            ]
        ]));

        $modelTarifKomponen = $tarifKomponen::find()
        ->where([
            'tipepaket_id' => $listPaket
        ])->orWhere([
            'kelompoktindakan_id' => DocoConstants::VAR_KEL_KRCS,
            'daftartindakan_id' => $tindakanKarcis
        ])->all();

        $modelTindakan = array_merge($modelTarifTotal, $modelTarifKomponen);

        if (!empty($modelTindakan)) {
            foreach ($modelTindakan as $value) {
                /** Parent Tindakan */
                if ($value['komponentarif_id'] == DocoConstants::KOMPONEN_TARIF) {
                    $payloadKunjungan->instalasi_id = $value['instalasi_id'];
                    $tmp[$value['tariftindakan_id']] = [
                        'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                        'pasien_id' => null,
                        'daftartindakan_id' => $value['daftartindakan_id'],
                        'tipepaket_id' => $value['tipepaket_id'],
                        'carabayar_id' => $payload->carabayar_id,
                        'pendaftaran_id' => null,
                        'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
                        'instalasi_id' => $value['instalasi_id'],
                        'ruangan_id' => $value['ruangan_id'],
                        'penjamin_id' => $value['penjamin_id'],
                        'tgl_tindakan' => date('Y-m-d H:i:s'),
                        'dokter_id' => !empty($payloadKunjungan->dokter_id) ? $payloadKunjungan->dokter_id : null,
                        'tarif_satuan' => $value['harga_tariftindakan'],
                        'qty' => 1,
                        'tarif_tindakan' => $value['harga_tariftindakan'],
                        'tarifcyto_tindakan' => 0,
                        'is_cyto' => false,
                        'discount_tindakan' => 0,
                        'tariftindakan_id' => $value['tariftindakan_id'],
                        'additional_data' => [
                            'list_komponen' => []
                        ]
                    ];
                } else {
                    /** Generate Komponen Tindakan */
                    $komponen[$value['tipepaket_id']][] = [
                        'komponentarif_id' => $value['komponentarif_id'],
                        'tindakanpelayanan_id' => null,
                        'tarif_kompsatuan' => $value['harga_tariftindakan'],
                        'tarif_tindakankomp' => $value['harga_tariftindakan'],
                        'tarifcyto_tindakankomp' => 0,
                        'subsidiasuransikomp' => 0,
                        'subsidipemerintahkomp' => 0,
                        'subsidirumahsakitkomp' => 0,
                        'iurbiayakomp' => 0,
                    ];
                }
            }
            foreach ($tmp as $key => $value) {
                $tmp[$key]['additional_data']['list_komponen'] = isset($komponen[$key]) ? $komponen[$key] : null;
                $listTagihan[] = $tmp[$key];
            }
        } else {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Paket tidak di temukan'
            ]);
        }

        $getOrder = MasterPaketMcuView::find()->andWhere([
            'tipepaket_id' => $listPaket
        ])->asArray()->all();

        if (!empty($getOrder)) {
            $pegawai_id = Yii::$app->jwt->user->pegawai_id;
            foreach ($getOrder as $value) {
                $detailOrders = !empty($value['detail']) ? $value['detail'] : null;
                if (!empty($detailOrders)) {
                    $detailOrders = json_decode($detailOrders, true);
                    if (is_array($detailOrders)) {
                        foreach ($detailOrders as $order) {
                            $ruanganOrder = !empty($order['ruangan_id']) ? $order['ruangan_id'] : null;
                            $instalasiOrder = !empty($order['instalasi_id']) ? $order['instalasi_id'] : null;
                            if (empty($ruanganOrder)) continue;
                            $typeOrder = !empty($order['is_penunjang']) ? self::PENUNJANG : self::KONSUL;
                            $jenisAntrian = !empty($order['is_penunjang']) ? DocoConstants::VAR_JA_PEN : DocoConstants::VAR_JA_P;
                            if (!isset($tmpOrderRuangan[$ruanganOrder])) {
                                $noAntrian = null;
                                $tmpOrderRuangan[$ruanganOrder] = true;
                                if (!$isKarcis) {
                                    $qAntrian = new Antrian;
                                    $qAntrian->attributes = [
                                        'ruangan_id' => $ruanganOrder,
                                        'carabayar_id' => $caraBayarId,
                                        'pendaftaran_id' => null,
                                        'tgl_antrian' => date('Y-m-d H:i:s'),
                                        'pasien_id' => null,
                                        'penjamin_id' => $payload->penjamin_id,
                                        'pegawai_id' => $pegawai_id,
                                        'status_pasien' => $statusPasien,
                                        'groupcarabayar_id' => $groupCaraBayar,
                                        'no_antrian' => '-',
                                        'jenisantrian_id' => $jenisAntrian,
                                    ];
                                    if ($qAntrian->save()) {
                                        $antrian_id = $qAntrian->antrian_id;
                                        $getNoAntrian = Antrian::find()->select([
                                            'no_antrian'
                                        ])->andWhere([
                                            'antrian_id' => $antrian_id
                                        ])->asArray()->one();
                                        $noAntrian = !empty($getNoAntrian['no_antrian']) ? $getNoAntrian['no_antrian'] : null;
                                    } else {
                                        throw new \Exception("Terjadi kesalahan pada antrian");
                                    }
                                }
                                if ($typeOrder === self::PENUNJANG) {
                                    $listOrderMcu[$typeOrder][] = [
                                        'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
                                        'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
                                        'pasienadmisi_id' => null,
                                        'pegawai_id' => null,
                                        'ruangan_id' => $ruanganOrder,
                                        'pasien_id' => null,
                                        'pendaftaran_id' => null,
                                        'ruanganasal_id' => $ruanganOrder,
                                        'tglmasukpenunjang' => date('Y-m-d H:i:s'),
                                        'kunjungan' => $statusPasien,
                                        'status_periksa' => DocoConstants::LAB_BELUM_PERIKSA,
                                        'is_bayar' => $isKarcis ? false : true,
                                        'instalasiasal_id' => $instalasiOrder,
                                        'no_antrian' => $noAntrian,
                                    ];
                                } else {
                                    $listOrderMcu[$typeOrder][] = [
                                        'ruangan_id' => $ruanganOrder,
                                        'pegawai_id' => $payloadKunjungan->dokter_id,
                                        'tindakanpelayanan_id' => null,
                                        'pendaftaran_id' => null,
                                        'pasien_id' => null,
                                        'tgl_konsulpoli' => date('Y-m-d H:i:s'),
                                        'asalpoliklinikkonsul_id' => $payloadKunjungan->ruangan_id,
                                        'status_periksa' => $statusPeriksa,
                                        'no_antriankonsul' => $noAntrian,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        return [
            'listTagihan' => $listTagihan,
            'listOrderMcu' => $listOrderMcu
        ];
    }

    /* 
    * Pendaftaran Kolektif MCU dengan Sercon non-block
    * @return true
    */
    public function actionSave()
    {
        $post = Yii::$app->request->post();
        $data = $dataPasien = $listOrderMcu = $listTagihan = $dataAntrian = [];
        $listPaket = [];
        $tindakanKarcis = [];
        $tmp = [];

        $payloadKunjungan = new Kunjungan;
        $payload = new TipePasien;
        $payloadAntrian = new PayloadAntrian;

        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];

        $data = json_decode($post['kunjungan']['list_pasien_mcu'], true);
        $listPaket = json_decode($payloadKunjungan->list_paket);
        $tindakanKarcis = json_decode($payloadKunjungan->tindakan_karcis,true);
        if($data){
            foreach($data as $k => $v){
                if(strtolower($v['status']) == self::COMPLETE){
                    $v['is_pasien_baru'] = true;
                    if(!empty($v['no_rekam_medik'])){
                        $v['is_pasien_baru'] = false;
                    }
                    
                    if(strtolower($v['jenisidentitas']) != self::LAINNYA) {
                        $lookup = AllowController::getLookupByType('jenis_identitas',ucwords($v['jenisidentitas']))->one();
                        if(!empty($lookup)){
                            $v['jenisidentitas'] =  $lookup['lookup_id'];
                        }else{
                            $v['jenisidentitas'] = self::ID_LAINNYA;
                        }
                    } else {
                        $v['jenisidentitas'] = self::ID_LAINNYA;
                    }

                    $v['tanggal_lahir'] = date('Y-m-d', strtotime($v['tanggal_lahir']));
                    $v['umur'] = ucwords(DocoHelpers::getUmur($v['tanggal_lahir']));
                    $getTotalhari = DocoHelpers::convertToHari($v['tanggal_lahir']);
                    $getGolongan = Cache::getGolonganUmur();
                    $getGolUmurId = 1;
            
                    foreach ($getGolongan as $value) {
                        if ($value['golonganumur_minimal'] <= $getTotalhari 
                                && $value['golonganumur_maksimal'] >= $getTotalhari) {
                            $getGolUmurId = $value['golonganumur_id'];
                            break;
                        }
                    }
                    $v['golonganumur_id'] = $getGolUmurId;

                    if(strtoupper($v['jenis_kelamin']) == self::JK_LAKI){
                        $v['jenis_kelamin'] = DocoConstants::VAR_LK;
                    }else{
                        $v['jenis_kelamin'] = DocoConstants::VAR_PR;
                    }

                    if(strtoupper($v['golongandarah']) != '-') {
                        $lookup = AllowController::getLookupByType('golongan_darah',strtoupper($v['golongandarah']))->one();
                        if(!empty($lookup)){
                            $v['golongandarah'] =  $lookup['lookup_id'];
                        }else{
                            $v['golongandarah'] = self::ID_GOL_DRH_TIDAK_TAHU;
                        }
                    } else {
                        $v['golongandarah'] = self::ID_GOL_DRH_TIDAK_TAHU;
                    }

                    if(strtoupper($v['statusperkawinan']) != self::KAWIN) {
                        $v['statusperkawinan'] = self::ID_KAWIN;
                    } else {
                        $v['statusperkawinan'] = self::ID_BLM_KAWIN;
                    }

                    $dataPasien[] = $v;
                }
            }
        }
        if($dataPasien) {
            try {                
                foreach($dataPasien as $k => $v) {
                    $pasienPayLoad = new PayloadPasien;
                    $pasienPayLoad->scenario = "pendaftaran-mcu-multiple";
                    $identitas = array();

                    /** Asuransi */
                    if (!empty($v['namapemilikasuransi'] && !empty($v['nokartuasuransi']))) {
                        $payloadAsuransi = new AsuransiForm;
                        $payloadAsuransi->nokartuasuransi = $v['nokartuasuransi'];
                        $payloadAsuransi->namapemilikasuransi = $v['namapemilikasuransi'];
                        $payloadAsuransi->tgl_konfirmasi = null;
                        $asuransi = $payloadAsuransi->attributes;
                    }

                    /** Rujukan */
                    if (!empty($v['no_rujukan'])) {
                        $payloadRujukan = new PayloadRujukan;
                        $payloadRujukan->rujukandari_id = 1;// harcode dulu
                        $payloadRujukan->asalrujukan_id = 1;// harcode dulu
                        $payloadRujukan->no_rujukan = $v['no_rujukan']; 
                        $payloadRujukan->nama_perujuk = $v['nama_perujuk']; 
                        $rujukan = $payloadRujukan->attributes;
                    }

                    /** Pasien Lama / baru */
                    $pasienBaru = true;
                    if ($v['is_pasien_baru'] == false) {
                        $pasien = Pasien::find()->where([
                            'no_rekam_medik' => $v['no_rekam_medik']
                        ])->asArray()->one();
                        if (empty($pasien)) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                                'text' => 'Pasien tidak ditemukan'
                            ]);
                        }
                        $pasienPayLoad->scenario = "pendaftaran-pasien-lama";
                        $pasienPayLoad->attributes = $pasien;
                        $pasienBaru = false;
                    } else {
                        $pasienPayLoad->tanggal_lahir = $v['tanggal_lahir'];
                        $pasienPayLoad->nama_pasien = $v['nama_pasien'];
                        $pasienPayLoad->jeniskelamin = $v['jenis_kelamin'];
                        $pasienPayLoad->jenisidentitas = $v['jenisidentitas'];
                        $pasienPayLoad->no_identitas_pasien = $v['no_identitas_pasien'];
                        $pasienPayLoad->no_telepon_pasien = $v['no_telepon_pasien'];
                        $pasienPayLoad->tempat_lahir = $v['tempat_lahir'];
                        $pasienPayLoad->statusperkawinan = $v['statusperkawinan'];
                        $pasienPayLoad->golonganumur_id = $v['golonganumur_id'];
                        $pasienPayLoad->golongandarah = $v['golongandarah'];
                        $pasienPayLoad->alamat_pasien = $v['alamat_pasien'];
                        $pasienPayLoad->propinsi_id = DocoConstants::ALAMAT_LAINNYA;
                        $pasienPayLoad->kabupaten_id = DocoConstants::ALAMAT_LAINNYA;
                        $pasienPayLoad->kecamatan_id = DocoConstants::ALAMAT_LAINNYA;
                        $pasienPayLoad->kelurahan_id = DocoConstants::ALAMAT_LAINNYA;

                        if ($pasienPayLoad->jenisidentitas && $pasienPayLoad->no_identitas_pasien) {
                            $identitas[] = [
                                'jenisidentitas' => $v['jenisidentitas'],
                                'no_identitas_pasien' => $v['no_identitas_pasien']
                            ];

                            $pasienPayLoad->additional_identitas = json_encode($identitas);
                        }
                    }

                    $statusPeriksa = DocoConstants::VAR_SP_AP;
                    $isKarcis = false;
                    
                    if ($payload->carabayar_id == DocoConstants::CB_PEN_UMUM) {
                        $statusPeriksa = DocoConstants::VAR_SP_AK;
                        $isKarcis = true;
                    }
            
                    $statusPasien = DocoConstants::VAR_PAS_B;

                    if ($pasienBaru) {
                        $statusPasien = DocoConstants::VAR_PAS_L;
                    }

                    $listOrderMcu = [
                        self::PENUNJANG => [],
                        self::KONSUL => []
                    ];
            
                    $caraBayarId = Cache::getCaraBayarPenjamin($payload->penjamin_id);

                    /** generate Paket dan karcis */
                    if (!empty($listPaket) && is_array($listPaket)) {
                        $generateTindakanKarcis = $this->generateTindakanKarcis($payloadKunjungan, $payload, $listPaket, $tindakanKarcis, $isKarcis, $statusPeriksa);
                        if(isset($generateTindakanKarcis['listTagihan']) && isset($generateTindakanKarcis['listOrderMcu'])){
                             $listTagihan = $generateTindakanKarcis['listTagihan'];
                             $listOrderMcu = $generateTindakanKarcis['listOrderMcu'];
                        } else {
                            return $generateTindakanKarcis;
                        }
                     }

                     if (empty($payload->antrian_id)) {
                        $dataAntrian = [
                            'pasien_id' => $pasienPayLoad->pasien_id,
                            'ruangan_id' => $payloadKunjungan->ruangan_id,
                            'carabayar_id' => $payload->carabayar_id,
                            'pendaftaran_id' => null, // Di Set Di Trigger
                            'tgl_antrian' => date("Y-m-d H:i:s"),
                            'no_antrian' => 'otomatis',
                            'penjamin_id' => $payload->penjamin_id,
                            'pegawai_id' => !empty($payloadKunjungan->dokter_id) ? $payloadKunjungan->dokter_id : null,
                            'status_pasien' => $statusPasien,
                            'jenisantrian_id' => DocoConstants::VAR_JA_P,
                            'is_active' => false,
                        ];
                    }
            
                    $payloadAntrian->attributes = $dataAntrian;

                    $additionals = [
                        'tarif' => [],
                        'rujukan' => $rujukan,
                        'antrian' => $payloadAntrian,
                        'penanggung_jawab' => [],
                        'asuransi' => $asuransi,
                        'order_mcu' => $listOrderMcu
                    ];

                    if ($pasienBaru) {
                        $additionals['pasien'] = $pasienPayLoad->attributes;
                    }

                    $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
                    $petugas_id = $jwt->loginpemakai_id;
                    $petugas_tgl_pembuat = date('Y-m-d H:i:s');
                    
                    $dataPendaftaran = [
                        'tgl_pendaftaran' => date('Y-m-d H:i:s'),
                        'penjamin_id' => $payload->penjamin_id,
                        'pasien_id' => $pasienPayLoad->pasien_id,
                        'pegawai_id' => $payloadKunjungan->dokter_id,
                        'instalasi_id' => $payloadKunjungan->instalasi_id,
                        'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
                        'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
                        'carabayar_id' => $caraBayarId,
                        'golonganumur_id' =>$v['golonganumur_id'],
                        'umur' => $v['umur'],
                        'rujukan_id' => null,
                        'antrian_id' => $payload->antrian_id,
                        'kunjungan' => ($pasienBaru == true) ? DocoConstants::VAR_K_B : DocoConstants::VAR_K_L,
                        'ruangan_id' => $payloadKunjungan->ruangan_id,
                        'transportasi' => $payloadKunjungan->transportasi,
                        'keadaan_masuk' => $payloadKunjungan->keadaan_masuk,
                        'status_periksa' => $statusPeriksa,
                        'status_pasien' => $statusPasien,
                        'status_masuk' => !empty($rujukan) ? DocoConstants::VAR_SM_R : DocoConstants::VAR_SM_NR,
                        'keterangan_pendaftaran' => $payloadKunjungan->keterangan,
                        'is_karcis' => $isKarcis,
                        'additional_data' => json_encode($additionals),
                        'is_aps' => true,
                        'no_exportexcel' => $payloadKunjungan->no_exportexcel,
                        'petugas_id' => $petugas_id,
                        'petugas_tgl_pembuat' => $petugas_tgl_pembuat
                    ];

                    $pendaftaran = new Pendaftaran;
                    $pendaftaran->attributes = $dataPendaftaran;
                    if ($pendaftaran->validate() && $pendaftaran->save(false)) {
                        $idPendaftaran = $pendaftaran->pendaftaran_id;
                        $prosesBpjs = false;
                        // Integrasi Akunting
                        IntegrasiAkunting::integrateKarcisPasien($idPendaftaran);
        
                        if (!empty($listTagihan)) {
                            // Select no pendaftaran
                            $noPendaftaran = Pendaftaran::findOne($idPendaftaran)->no_pendaftaran;
        
                            // Integrasi kasir tagihan pelayanan
                            $tagihan = (new KasirService)->tagihan([
                                'pendaftaran_id' => $idPendaftaran,
                                'no_pendaftaran' => $noPendaftaran,
                                'instalasi_id' => $pendaftaran->instalasi_id,
                                'ruangan_id' => $pendaftaran->ruangan_id,
                                'penjamin_id' => $pendaftaran->penjamin_id,
                                'kelas_pelayanan_id' => $pendaftaran->kelaspelayanan_id
                            ], $listTagihan);
        
                            if (isset($tagihan['meta']['result']) && $tagihan['meta']['result'] == 'failed') {
                                // $transaction->rollBack();
                                return isset($tagihan['message']) ? $tagihan['message'] : 'Terjadi Kesalahan API';
                            }
                        }
                    }
                }
            } catch (\yii\db\Exception $e) {
                Yii::$app->response->statusCode = 500;
                // $transaction->rollBack();
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                    'text' => $e->getMessage(),
                ]);
            } catch (\Exception $e) {
                Yii::$app->response->statusCode = 500;
                // $transaction->rollBack();
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                    'text' => $e->getMessage(),
                ]);
            }
        }
    }

    public function actionValidasiRekamMedik()
    {
        $post =  Yii::$app->request->post();
        $response = [];

        if(!empty($post)){
            foreach($post as $k => $v){
                $pasien = $this->getPasien()->where([
                    'no_rekam_medik' => $v
                ])->asArray()->one();
                if(!empty($pasien)){
                    $response[$v] = true;
                } else {
                    $response[$v] = false;
                }
            }
        }

        return $response;
    }

    public function actionValidasiIdentitasPasien()
    {
        $post =  Yii::$app->request->post();
        $response = [];
        $countMultipleRM = 0;

        if(!empty($post)){
            foreach($post as $k => $v){
                $v['multiple_rm'] = false;
                if(strtoupper($v['jenis_kelamin']) == self::JK_LAKI){
                    $jenis_kelamin = DocoConstants::VAR_LK;
                }else{
                    $jenis_kelamin = DocoConstants::VAR_PR;
                }

                if (empty($v['no_rekam_medik'])) {
                    $v['no_rekam_medik'] = null;
                    $pasien = $this->getPasien()->where([
                        'LOWER(nama_pasien)' => strtolower($v['nama_pasien']),
                        'tanggal_lahir' => date('Y-m-d',strtotime($v['tanggal_lahir'])),
                        'jeniskelamin' => $jenis_kelamin
                    ])->asArray()->all();
                    if(!empty($pasien)){
                        $v['no_rekam_medik'] = $pasien[0]['no_rekam_medik'];
                    }
                    if (count($pasien) > 1) {
                        $v['no_rekam_medik'] = '';
                        foreach($pasien as $key => $value) {
                            $v['no_rekam_medik'] = $v['no_rekam_medik'].$value['no_rekam_medik'].'<br/>';
                        }
                        $v['multiple_rm'] = true;
                        $v['status'] = self::MULTIPLE_RM;
                        $v['group'] = 1;
                        $countMultipleRM++;
                    }
                }

                $response[$k] = $v;
            }
        }

        return [
            'data' => $response,
            'count' => $countMultipleRM
        ];
    }

    private function getPasien()
    {
        $result = Pasien::find();
        return $result;
    }
}