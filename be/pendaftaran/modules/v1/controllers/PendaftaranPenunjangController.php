<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 13:25
 * @Mod: Ardi Pratama Septiadi
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\controllers\AllowController;
use app\modules\v1\components\Penomoran;
use app\modules\v1\components\BpjsController;
use Doco\Services\KasirService;

use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PenanggungJawab;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\PendaftaranOnline;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\PasienMasukPenunjang;
use app\modules\v1\models\TarifTotalRsFn;
use app\modules\v1\models\TarifKomponenRsFn;
use app\modules\v1\models\InfoDataPendaftaranView;
use app\modules\v1\models\InfPasienPenunjang;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\PenanggungBiayaView;
use app\modules\v1\models\KeluargaPasien;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyAsuransiPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungBiayaView;
use app\modules\v1\models\SyPenjaminView;
use app\modules\v1\models\SyncsantoyusupR;
use app\modules\v1\models\SyPasienMasukPenunjangView;
use app\modules\v1\models\SyPenanggungJawabView;
use app\modules\v1\models\PenomoranK;
use app\modules\v1\models\PasienKirimUnitLain;

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
use app\modules\v1\models\InfoKunjunganRsView;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use SirsCore\features\IntegrasiAkunting;
use Doco\Repositories\LookUpTransaksiRepositories;
use GuzzleHttp\Exception\RequestException;
use Doco\Services\Vendors\PendaftaranService;
use Doco\Services\InternalService;
use Doco\components\DocoConstansId;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Traits\AutoPlafonTrait;
use app\modules\v1\models\CaraBayar;

class PendaftaranPenunjangController extends BpjsController
{
    use AutoPlafonTrait;
    
    public $modelClass = 'app\modules\v1\models\Pendaftaran';
    static protected $_url = 'on/sinkronisasi/sync';
    static protected $_restSerconn;
    static protected $_headers;
    const C_B_PERSONIL = 45;
    const PENOMORAN_RAD = 195;
    const PENOMORAN_USG = 196;
    const PENOMORAN_LAB = 197;
    const PENOMORAN_FA = 198;
    const PENOMORAN_CT = 199;
    const INS_USG = 72;
    const INS_FA = 71;
    public static $LIST_REG_CT = [
        75 //instalasi_id
    ];
    public static $LIST_REG_LAB = [
        85,86,87,88 //instalasi_id
    ];
    public static $LIST_REG_FAR = [
        71,77,78,79,80,81,82 //instalasi_id
    ];
    public static $LIST_REG_RAD = [
        89 //instalasi_id
    ];
    public static $LIST_REG_US = [
        72 //instalasi_id
    ];
    const K_P_STYP = 69;
    const PENOMORAN_PASIEN = 188;
    const PREFIX_PASIEN = 'X';

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
                'Ris' => [
                    'RisBroker' => [
                        'result' => true,
                        'successProcess'=>true,
                        'is_aps' => true
                    ]
                ],
                'InaBroker' => [
                    'RisBroker' => [
                        'result' => true,
                        'successProcess'=>true,
                        'is_aps' => true
                    ]
                ],
                'Lis' => [
                    'BridgingLis' => [
                        'result' => true,
                        'successProcess'=>true,
                        'is_aps' => true,
                        'state' => true
                    ]
                ],
                'Roche' => [
                    'Order' => [
                        'result' => true,
                        'successProcess'=>true,
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

    public function actionSendRis(){
        $request = Yii::$app->request;
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        (new InternalService)->sendTo([
            'InaBroker' => [
                'RisBroker' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'result' => true,
                    'successProcess'=>true,
                    'is_aps' => true
                ]
            ]
        ], true);
        return true;
    }

    public function actionSendLis(){
        $request = Yii::$app->request;
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');


        (new InternalService)->sendTo([
            'Lis' => [
                'BridgingLis' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'result' => true,
                    'successProcess'=>true,
                    'is_aps' => true
                ]
            ]
        ], true);
        return true;
    }

    public function init()
    {
        self::$_restSerconn = Yii::$app->serconn->guzzle();
        self::$_headers = Yii::$app->request->headers;
    }

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
        $allowBpjs = $request->post('allow_bpjs', false);
        $isBpjs = false;

        if (!empty($post['bpjs'])) {
            $isBpjs = true;
        }

        if(isset($post['tipe_pasien']['pendaftaran_id']) && !empty($post['tipe_pasien']['pendaftaran_id'])){
            return $this->actionSavePendaftaranPasienRs();
        }

        /** Validasi Program Fisioterapi */
        $instalasiId = ArrayHelper::getValue($post, 'kunjungan.instalasi_id');

        if($isBpjs == false){
            $noRekamMedik = ArrayHelper::getValue($post, 'tipe_pasien.no_rekam_medik');
        }else{
            $noRekamMedik = ArrayHelper::getValue($post, 'bpjs.no_rekam_medik');
        }
        
        $pendaftaranId = ArrayHelper::getValue($post, 'tipe_pasien.pendaftaran_id');
        if (isset($instalasiId)) {
            $lookUpTransaksi = new LookUpTransaksiRepositories;
            $instalasiFisioId = $lookUpTransaksi->getInstalasiIdFisio();
            if ($instalasiId == $instalasiFisioId) {
                if (!empty($noRekamMedik)) {
                    $restFisio = Yii::$app->docoRest->fisioterapi;
                    $request = $restFisio->post('program-fisioterapi/check-program-pasien', [
                        'form_params' => [
                            'pendaftaran_id' => $pendaftaranId,
                            'no_rekam_medik' => $noRekamMedik
                        ]
                    ]);
                    $response = json_decode($request->getBody(), true);
                    $responseStatus = ArrayHelper::getValue($response, 'metadata.status');
                    if ($responseStatus != 200) {
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Pasien tidak mempunyai program fisioterapi'
                        ];
                    }
                } else {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Pasien tidak mempunyai program fisioterapi'
                    ];
                }
            }
        }

        $pasienBaru = true;
        $tagihanKarcis = $tagihanPenunjang = false;
        $isMultiPayer = false;

        /** Validasi Error */
        $errorParse = [];
        /** Tindakan Karcis */
        $listTindakan = $tmp = $sepNew = $komponen = [];
        $tmpPenunjang = $listTagihan = [];

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

        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        $payloadMultipayer->isMultipayer = $isMultiPayer;

        $eligiblePasien = ArrayHelper::getValue($post, 'eligible_pasien');
        $eligiblePasien = json_decode($eligiblePasien, true);
        $dataPeserta = ArrayHelper::getValue($eligiblePasien, 'dataPeserta', []);

        if (!empty($post['pasien']) && empty($payload->no_rekam_medik)) {
            $postPasien = $post['pasien'];
            $pasienPayLoad->nama_pasien = ArrayHelper::getValue($postPasien, 'nama_pasien');
            $pasienPayLoad->jeniskelamin = ArrayHelper::getValue($postPasien, 'jeniskelamin');
            $pasienPayLoad->tanggal_lahir = ArrayHelper::getValue($postPasien, 'tanggal_lahir');
            $pasienPayLoad->alamat_pasien = ArrayHelper::getValue($postPasien, 'alamat_pasien');
            $pasienPayLoad->statusperkawinan = ArrayHelper::getValue($postPasien, 'status_perkawinan');
            $pasienPayLoad->golongandarah = ArrayHelper::getValue($postPasien, 'golongan_darah');
            $pasienPayLoad->tempat_lahir = ArrayHelper::getValue($postPasien, 'tempat_lahir');
            $pasienPayLoad->umur = ArrayHelper::getValue($postPasien, 'umur');
            $pasienPayLoad->propinsi_id = ArrayHelper::getValue($postPasien, 'propinsi_id');
            $pasienPayLoad->pendidikan_id = ArrayHelper::getValue($postPasien, 'pendidikan_id');
            $pasienPayLoad->pekerjaan_id = ArrayHelper::getValue($postPasien, 'pekerjaan_id');;
            $pasienPayLoad->namadepan = ArrayHelper::getValue($postPasien, 'nama_depan');
            $pasienPayLoad->nama_ibu = ArrayHelper::getValue($postPasien, 'nama_ibu');
            $pasienPayLoad->nama_ayah = ArrayHelper::getValue($postPasien, 'nama_ayah');
            $pasienPayLoad->anakke = ArrayHelper::getValue($postPasien, 'anakke');
            $pasienPayLoad->jumlah_bersaudara = ArrayHelper::getValue($postPasien, 'jumlah_bersaudara');
            $pasienPayLoad->rt = ArrayHelper::getValue($postPasien, 'rt');
            $pasienPayLoad->rw = ArrayHelper::getValue($postPasien, 'rw');
            $pasienPayLoad->no_telepon_pasien = ArrayHelper::getValue($postPasien, 'no_telepon_pasien');
            $pasienPayLoad->alamatemail = ArrayHelper::getValue($postPasien, 'alamatemail');
            $pasienPayLoad->warga_negara = ArrayHelper::getValue($postPasien, 'warga_negara');
            $pasienPayLoad->suku_id = ArrayHelper::getValue($postPasien, 'suku_id');
            $pasienPayLoad->agama = ArrayHelper::getValue($postPasien, 'agama');
            $pasienPayLoad->additional_identitas = ArrayHelper::getValue($postPasien, 'additional_identitas');
            $pasienPayLoad->kabupaten_id = ArrayHelper::getValue($postPasien, 'kabupaten_id', null);
            $pasienPayLoad->kecamatan_id = ArrayHelper::getValue($postPasien, 'kecamatan_id', null);
            $pasienPayLoad->kelurahan_id = ArrayHelper::getValue($postPasien, 'kelurahan_id', null);
        }

        if (!empty($post['asuransi'])) {
            $payloadAsuransi = new AsuransiForm;
            $payloadAsuransi->attributes = $post['asuransi'];
            $payloadAsuransi->tgl_konfirmasi = !empty($payloadAsuransi->tgl_konfirmasi) ? date('Y-m-d', strtotime($payloadAsuransi->tgl_konfirmasi)) : null;
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
                'no_rujukan' => $payloadBpjs->no_rujukan,
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
        /** Get Golongan Umur Id */
        $pasienPayLoad->tgl_rekam_medik = date('Y-m-d');
        $pasienPayLoad->is_aps = !empty($payload->is_aps) ? true : false;
        $pasienPayLoad->tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir)
                            ?  date('Y-m-d', strtotime($pasienPayLoad->tanggal_lahir)) : null;

        if (!empty($payload->no_rekam_medik)) {
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

        /** Validate Form */
        if ($pasienBaru) {
            if (!$pasienPayLoad->validate()) $errorParse['pasien'] = $pasienPayLoad->errors;
        }
        if (!$payload->validate()) $errorParse['tipe_pasisaeen'] = $payload->errors;
        if (!$payloadKunjungan->validate()) $errorParse['kunjungan'] = $payloadKunjungan->errors;

        if (!empty($errorParse)) {
            return [
                'status' => 422,
                'data' => $errorParse
            ];
        }

        $tindakanKarcis = json_decode($payloadKunjungan->tindakan_karcis,true);
        $tindakanPenunjang = json_decode($payloadKunjungan->list_penunjang,true);
        // if (empty($tindakanPenunjang) && $payloadKunjungan->instalasi_id != DocoConstants::INST_ID_BEDAH) {
        //     return [
        //         'status' => 422,
        //         'title' => 'Proses Gagal!',
        //         'text' => 'Tindakan tidak boleh kosong.'
        //     ];
        // }
        /** Generate Tindakan Peunjang */
        if (!empty($tindakanPenunjang)) {
            $whereCond = $tmpCyto = [];
            $tmpQty = [];
            $penjamin = $payload->penjamin_id;
            $kelasPelayanan = $payloadKunjungan->kelaspelayanan_id;
            $ruanganId = $payloadKunjungan->ruangan_id;
            $type = 'penunjang';
            foreach ($tindakanPenunjang as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $list) {
                        if (!isset($list['id']) && !isset($list['is_cyto'])) continue;
                        $tindakanId = isset($list['id']) ? $list['id'] : null;
                        $tmpCyto[$key][$tindakanId] = $list['is_cyto'] ? $list['is_cyto'] : false;
                        $tmpQty[$key][$tindakanId] = isset($list['qty']) ? $list['qty'] : 1;
                        if ($key === 'paket') {
                            $whereCond[] = "(jenis = 'paket_lab'
                                    AND daftartindakan_id = {$tindakanId})";
                        } else {
                            $whereCond[] = "(jenis != 'paket_lab'
                                    AND daftartindakan_id = {$tindakanId})";
                        }
                    }
                }
            }
            $condQuery = null;
            if (!empty($whereCond)) {
                $condQuery = implode(' OR ', $whereCond);
            }

            // $komponentQuery = Yii::$app->db->createCommand("
            //     SELECT
            //         jenis_tindakan,
            //         tariftindakan_id,
            //         daftartindakan_id,
            //         komponentarif_id,
            //         harga_tariftindakan,
            //         persencyto_tindakan,
            //         persendiskon_tindakan
            //     FROM infotarifpenunjang_v
            //     WHERE ({$condQuery}) AND penjamin_id = {$penjamin} AND kelaspelayanan_id = {$kelasPelayanan}
            // ")->queryAll();

            /** New get tarif tindakan to function */
            $qryTarifTindakan = (new TarifTotalRsFn([
                'extParam' => [
                    $ruanganId,
                    $penjamin,
                    $kelasPelayanan,
                    $type
                ]
            ]));
            $getTarifTindakan = $qryTarifTindakan::find()->where($condQuery)->asArray()->all();

            $qryTarifKomponen = (new TarifKomponenRsFn([
                'extParam' => [
                    $ruanganId,
                    $penjamin,
                    $kelasPelayanan,
                    $type
                ]
            ]));
            $getTarifKomponen = $qryTarifKomponen::find()->where($condQuery)->asArray()->all();
            $komponentQuery = array_merge($getTarifTindakan, $getTarifKomponen);
            $tmpPenunjang = $this->generateTindakanNew($komponentQuery, $payloadKunjungan, $payload, $tmpCyto, $tmpQty);
        }
        /** Generate Tindakan Karcis */
        if (!empty($tindakanKarcis)) {
            // $modelTindakan = InfoTarifRsView::find()->select([
            //     'tariftindakan_id',
            //     'daftartindakan_id',
            //     'komponentarif_id',
            //     'harga_tariftindakan',
            //     'persencyto_tindakan',
            //     'persendiskon_tindakan',
            // ])->where([
            //     'ruangan_id' => $payloadKunjungan->ruangan_id,
            //     'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
            //     'daftartindakan_id' => $tindakanKarcis,
            //     'penjamin_id' => $payload->penjamin_id,
            // ])->all();
            
            $docoContantsId = new DocoConstansId();
            $kelompok_tindakan_ids = $docoContantsId->actionGetAdditional(DocoConstants::KELOMPOK_TINDAKAN_ID, true);
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

            $tarifKomponen = (new TarifKomponenRsFn([
                'extParam' => [
                    $payloadKunjungan->ruangan_id,
                    $payload->penjamin_id,
                    $payloadKunjungan->kelaspelayanan_id,
                    $tipeTarif
                ]
            ]));
            $modelTarifKomponen = $tarifKomponen::find()
            ->andWhere([
                'daftartindakan_id' => $tindakanKarcis
            ]);
            
            if(!empty($kelompok_tindakan_ids)){
                foreach ($kelompok_tindakan_ids as $value){
                    if(isset($value['operand']) || !empty($value['operand'])){
                        $modelTarifKomponen->andWhere([$value['operand'], $value['column'], $value['value']]);
                    }else{
                        $modelTarifKomponen->andWhere([$value['column'] => $value['value']]);
                    }
                }
            }
            $modelTarifKomponen = $modelTarifKomponen->all();

            $modelTindakan = array_merge($modelTarifTotal, $modelTarifKomponen);
            $listTagihan = $this->generateTindakanNew($modelTindakan, $payloadKunjungan, $payload);
            /** Comment jika sudah hit kasir */
            // $tmpPenunjang = array_merge($tmpPenunjang,$listTagihan);
        }

        $umur = ucwords(DocoHelpers::getUmur($pasienPayLoad->tanggal_lahir));

        $statusPeriksa = 1;
        $isKarcis = false;
        if (!empty($tmp) && $payload->carabayar_id == DocoConstants::CB_PEN_UMUM) {
            $isKarcis = true;
        }

        $statusPasien = !empty($payload->no_rekam_medik) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B;

        if (empty($payload->antrian_id) && $payload->carabayar_id != DocoConstants::CB_PEN_UMUM) {
            $dataAntrian = [
                'pasien_id' => $pasienPayLoad->pasien_id,
                'ruangan_id' => $payloadKunjungan->ruangan_id,
                'carabayar_id' => $payload->carabayar_id,
                'pendaftaran_id' => null, // Di Set Di Trigger
                'tgl_antrian' => date("Y-m-d H:i:s"),
                'no_antrian' => 'otomatis',
                'penjamin_id' => $payload->penjamin_id,
                'pegawai_id' => !empty($payloadKunjungan->pegawai_id) ? $payloadKunjungan->pegawai_id : null,
                'status_pasien' => $statusPasien,
                'jenisantrian_id' => DocoConstants::VAR_JA_PEN,
                'is_active' => false,
            ];
        }

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
            'antrian' => $dataAntrian,
            'penanggung_jawab' => $pjPasien,
            'asuransi' => $asuransi,
            'additional_payer' => $payloadMultipayer
        ];

        if (!empty($tmpPenunjang)) {
            $additionals['tarif_penunjang'] = $tmpPenunjang;
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
        if ($payload->is_aps && $isBpjs || $groupCaraBayarId == DocoConstants::GROUP_BPJS) {
            $autoPlafon = $this->setAutoPlafon($payloadKunjungan->instalasi_id, $payloadKunjungan->kelaspelayanan_id, $payloadKunjungan->ruangan_id);
            if (!empty($autoPlafon)) {
                $limitTagihan = $autoPlafon;
            }
        }
        
        $dataPendaftaran = [
            'tgl_pendaftaran' => date('Y-m-d H:i:s'),
            'penjamin_id' => $payload->penjamin_id,
            'pasien_id' => $pasienPayLoad->pasien_id,
            'pegawai_id' => $payloadKunjungan->pegawai_id,
            'instalasi_id' => $payloadKunjungan->instalasi_id,
            'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
            'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
            'carabayar_id' => $payload->carabayar_id,
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
            'is_aps' => $payload->is_aps,
            // 'is_aps' => !empty($tindakanPenunjang) ? true : false,
            'is_skd' => $payloadKunjungan->is_skd,
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
            $pendaftaran->scenario = 'penunjang';
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
                /* Daftar Ke bedah Tanpa Tindakan (Dengan tindakan by trigger)*/
                if ($payloadKunjungan->instalasi_id == DocoConstants::INST_ID_BEDAH && empty($tmpPenunjang)) {
                    $dataPendaftaran['pendaftaran_id'] = $idPendaftaran;
                    $dataPendaftaran['instalasiasal_id'] = $payloadKunjungan->instalasi_id;
                    $dataPendaftaran['ruanganasal_id'] = $payloadKunjungan->ruangan_id;
                    $dataPendaftaran['kunjungan'] = DocoConstants::VAR_KUN_SM;
                    $dataPendaftaran['is_aps'] = true;
                    $pasienPenunjangBedah = $this->actionDaftarBedah($dataPendaftaran);
                    if($pasienPenunjangBedah['metadata']['status'] != 200) {
                         $transaction->rollBack();
                         return [
                             'status' => 422,
                             'title' => 'Proses Gagal!',
                             'text' => 'Terjadi Kesalahan'
                         ];
                    }
                }
                $transaction->commit();

                $infoDaftar = $this->searchPendaftaran($idPendaftaran);
                $penunjangId = $infoDaftar->pasienmasukpenunjang_id;
                if(!empty($tindakanPenunjang)){

                    foreach($tmpPenunjang as $k => $v){
                        $tmpPenunjang[$k]['pasienmasukpenunjang_id'] = isset($penunjangId) ? $penunjangId : null;
                    }

                    $tagihanPenunjang =  (new KasirService)->tagihanPenunjang([
                        'pendaftaran_id' => $idPendaftaran,
                        'no_pendaftaran' =>$infoDaftar->no_pendaftaran,
                        'instalasi_id' =>$infoDaftar->instalasi_id,
                        'ruangan_id' =>$infoDaftar->ruangan_id,
                        'penjamin_id' => $payload->penjamin_id,
                        'kelas_pelayanan_id' => $payloadKunjungan->kelaspelayanan_id
                    ], $tmpPenunjang);

                    if (isset($tagihanPenunjang['meta']['result']) && $tagihanPenunjang['meta']['result'] == 'failed') {
                        $transaction->rollBack();
                        return isset($tagihanPenunjang['message']) ? $tagihanPenunjang['message'] : 'Terjadi Kesalahan API';
                    }
                } else {
                    $kunjunganPasien = InfoKunjunganRsView::find()->where(['pendaftaran_id' => $idPendaftaran])->one();

                    $pasienKirimUnitLain = new PasienKirimUnitlain;
                    $kirimUnitLain = [
                        'pegawai_id' => !empty($payloadKunjungan->pegawai_id) ? $payloadKunjungan->pegawai_id : null,
                        'instalasi_id' => $payloadKunjungan->instalasi_id,
                        'pasien_id' => $kunjunganPasien->pasien_id,
                        'pasienmasukpenunjang_id' => null,
                        'pendaftaran_id' => $idPendaftaran,
                        'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
                        'ruangan_id' => $payloadKunjungan->ruangan_id,
                        'tgl_kirimpasien' => date('Y-m-d H:i:s'),
                        'catatan_dokterpengirim' => null,
                        'instruksi_id' => null,
                        'pasienadmisi_id' => null,
                        'status_penunjang' => DocoConstants::BELUM_SETUJU,
                        'is_rujukan' => false,
                    ];
                    $pasienKirimUnitLain->attributes = $kirimUnitLain;
                    if (!$pasienKirimUnitLain->save()) {
                        $transaction->rollBack();
                        return [
                            'data' => $model->errors,
                            'status' => 422
                        ];
                    }
                }

                $pendaftaran->no_pendaftaran = $infoDaftar->no_pendaftaran;
                if(Yii::$app->params['isRabbitMq']) {   
                    (new RabbitBgProcess())->send([
                        "penjamin_id" => $pendaftaran->penjamin_id,
                        "kodebenefit" => ArrayHelper::getValue($post, 'refresh_asuransi'),
                        "data_peserta" => $dataPeserta,
                        "data_pendaftaran" => $pendaftaran->attributes
                    ], 'integrate_insurance', 'integrate_insurance');   
                }

                if(!empty($tindakanKarcis)){

                    foreach($listTagihan as $k => $v){
                        $listTagihan[$k]['pasienmasukpenunjang_id'] = isset($penunjangId) ? $penunjangId : null;
                    }

                    $tagihanKarcis = (new KasirService)->tagihan([
                        'pendaftaran_id' => $idPendaftaran,
                        'no_pendaftaran' =>$infoDaftar->no_pendaftaran,
                        'instalasi_id' =>$infoDaftar->instalasi_id,
                        'ruangan_id' =>$infoDaftar->ruangan_id,
                        'penjamin_id' => $payload->penjamin_id,
                        'kelas_pelayanan_id' => $payloadKunjungan->kelaspelayanan_id
                    ], $listTagihan);

                    if (isset($tagihanKarcis['meta']['result']) && $tagihanKarcis['meta']['result'] == 'failed') {
                        $transaction->rollBack();
                        return isset($tagihanKarcis['message']) ? $tagihanKarcis['message'] : 'Terjadi Kesalahan API';
                    }
                }

                // Integrasi Akunting
                IntegrasiAkunting::integrateKarcisPasien($idPendaftaran);

                $resPendaftaran = Pendaftaran::find()
                ->select(
                    'pendaftaran_t.pendaftaran_id, 
                    pendaftaran_t.pasien_id, 
                    pendaftaran_t.status_pasien as status_pasien_id, 
                    look_stat_pasien.lookup_name as status_pasien'
                )
                ->leftJoin('lookup_m AS look_stat_pasien', 'look_stat_pasien.lookup_id::TEXT = pendaftaran_t.status_pasien::TEXT')
                ->where(['pendaftaran_id' => $idPendaftaran])->asArray()->one();

                return [
                    'message' => 'Proses Pendaftaran berhasil',
                    'karcis' => $tagihanKarcis,
                    'penunjang' => $tagihanPenunjang,
                    'id' => DocoHelpers::encrypt($idPendaftaran),
                    'is_bpjs' => $prosesBpjs,
                    'pendaftaran' => $resPendaftaran
                ];
            }

            $errors = DocoHelpers::parseError(['data' => $pendaftaran->errors], 'KunjunganForm');
            return [
                'data' => $errors,
                'status' => 422
            ];
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
            $transaction->rollBack();
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $e->getMessage()
            ];
        }
    }

    public function actionSavePendaftaranPasienRs()
    {
        $post =  Yii::$app->request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $payload = new TipePasien;
        $payload->scenario = "pendaftaran-pasien-rs";
        $payloadKunjungan = new Kunjungan;

        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        if (!$payload->validate()) $errorParse['tipe_pasien'] = $payload->errors;
        if (!$payloadKunjungan->validate()) $errorParse['kunjungan'] = $payloadKunjungan->errors;

        if (!empty($errorParse)) {
            return [
                'status' => 422,
                'data' => $errorParse
            ];
        }
        $tindakanPenunjang = json_decode($payloadKunjungan->list_penunjang,true);
        // if (empty($tindakanPenunjang) && $payloadKunjungan->instalasi_id != DocoConstants::INST_ID_BEDAH) {
        //     return [
        //         'status' => 422,
        //         'title' => 'Proses Gagal!',
        //         'text' => 'Tindakan tidak boleh kosong.'
        //     ];
        // }
        $list_order=[];
        $response=[];

        if(!empty($tindakanPenunjang)){
            foreach ($tindakanPenunjang as $_jenisTindakan => $_tindakanPerJenis) {
                foreach ($_tindakanPerJenis as $_detailTindakan) {
                    if($payloadKunjungan->instalasi_id == DocoConstants::INST_ID_BEDAH){
                        $golId = !empty($_detailTindakan['jenispemeriksaanlab_id']) ? $_detailTindakan['jenispemeriksaanlab_id'] : false;
                        $kegId = !empty($_detailTindakan['kelompokpemeriksaanlab_id']) ? $_detailTindakan['kelompokpemeriksaanlab_id'] : false;
                      };
                    $list_order[] = [
                        'tariftindakan_id' => $_detailTindakan['tariftindakan_id'],
                        'is_paket' => $_jenisTindakan == 'tindakan' ? false : true,
                        'is_cyto' => $_detailTindakan['is_cyto'] == 0 ? false : true,
                        'golongan_id' => isset($golId) ? $golId : '',
                        'kegiatan_id' => isset($kegId) ? $kegId : ''
                    ];

                }
            }
        }

        if($payload){
            $kunjunganPasien = InfoKunjunganRsView::find()
                ->where(['pendaftaran_id' => $payload->pendaftaran_id])
                ->orderBy(['pasienadmisi_id' => SORT_ASC])
                ->one();
        }

        /** Validasi Program Fisioterapi */
        // $instalasiId = ArrayHelper::getValue($post, 'kunjungan.instalasi_id');
        // $pendaftaranId = ArrayHelper::getValue($post, 'tipe_pasien.pendaftaran_id');
        // if (isset($instalasiId)) {
        //     $lookUpTransaksi = new LookUpTransaksiRepositories;
        //     $instalasiFisioId = $lookUpTransaksi->getInstalasiIdFisio();
        //     if ($instalasiId == $instalasiFisioId) {
        //         $restFisio = Yii::$app->docoRest->fisioterapi;
        //         $request = $restFisio->post('program-fisioterapi/check-program-pasien', [
        //             'form_params'=> [
        //                 'pendaftaran_id' => $pendaftaranId
        //             ]
        //         ]);
        //         $response = json_decode($request->getBody(), true);
        //         $responseStatus = ArrayHelper::getValue($response, 'metadata.status');
        //         if ($responseStatus != 200) {
        //             return [
        //                 'status' => 422,
        //                 'title' => 'Proses Gagal!',
        //                 'text' => 'Pasien tidak mempunyai program fisioterapi'
        //             ];
        //         }
        //     }
        // }

        try{
            if(!empty($tindakanPenunjang)) {
                $data_order = [
                    'pendaftaran_id' => $payload->pendaftaran_id,
                    'pasienadmisi_id' => !empty($kunjunganPasien->pasienadmisi_id) ? $kunjunganPasien->pasienadmisi_id : null,
                    'instalasi_id' => $payloadKunjungan->instalasi_id,
                    'ruangan_id' => $payloadKunjungan->ruangan_id,
                    'pegawai_id' => !empty($payloadKunjungan->pegawai_id) ? $payloadKunjungan->pegawai_id : null,
                    'cppt_id' => null,
                    'catatan_dokterpengirim' => null,
                    'tgl_kirimpasien' => date('Y-m-d H:i:s'),
                    'instruksi_id' => null,
                    'list_order' => !empty($list_order) ? json_encode($list_order) : ''
                ];

                if ($payloadKunjungan->instalasi_id == DocoConstants::VAR_I_LAB) {
                    //hit api backend laboratorium
                    $restLab = Yii::$app->docoRest->laboratorium;
                    $request = $restLab->post('order/create?id='.$payload->pendaftaran_id, [
                        'form_params'=>$data_order
                    ]);
                    $response = json_decode($request->getBody(),true);
                } elseif ($payloadKunjungan->instalasi_id == DocoConstants::VAR_I_RAD) {
                    //hit api backend radiologi
                    $restRad = Yii::$app->docoRest->radiologi;
                    $request = $restRad->post('order/create?id='.$payload->pendaftaran_id, [
                        'form_params'=>$data_order
                    ]);
                    $response = json_decode($request->getBody(),true);
                } elseif ($payloadKunjungan->instalasi_id == DocoConstants::VAR_I_BED) {
                    // hit api bedah
                    // $resBedah = Yii::$app->docoRest->bedahsentral;
                    // $request = $resBedah->post('order/create?id='.$payload->pendaftaran_id, [
                    //     'form_params'=>$data_order
                    // ]);
                    // $response = json_decode($request->getBody(),true);
                    $lastKunjungan = $kunjunganPasien;
                    if($lastKunjungan) {
                        $dataPendaftaran['pasien_id'] = $lastKunjungan->pasien_id;
                        $dataPendaftaran['pendaftaran_id'] = $payload->pendaftaran_id;
                        $dataPendaftaran['pegawai_id'] = $lastKunjungan->pegawai_id;
                        $dataPendaftaran['kelaspelayanan_id'] = $lastKunjungan->kelaspelayanan_id;
                        $dataPendaftaran['instalasiasal_id'] = $lastKunjungan->instalasi_id;
                        $dataPendaftaran['ruanganasal_id'] = $lastKunjungan->ruangan_id;
                        $dataPendaftaran['kunjungan'] =  $lastKunjungan->status_pasien;
                        $dataPendaftaran['is_aps'] = false;
                        $dataPendaftaran['list_order'] = !empty($list_order) ? json_encode($list_order) : false;
                        $dataPendaftaran['tgl_pendaftaran'] = date('Y-m-d H:i:s');
                        $dataPendaftaran['ruangan_id'] = $payloadKunjungan->ruangan_id;
                        $dataPendaftaran['jeniskasuspenyakit_id'] = $lastKunjungan->jeniskasuspenyakit_id;
                        $dataPendaftaran['limit_tagihan'] = $payloadKunjungan->limit_tagihan ? $payloadKunjungan->limit_tagihan : 0;

                        $response = $this->actionDaftarBedah($dataPendaftaran);
                    } else {
                        $transaction->rollBack();
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Pendaftaran tidak ditemukan'
                        ];
                    }
                }
                if ($response['metadata']['status'] == 200) {
                    $transaction->commit();
                    return [
                        'messages' => 'Data berhasil di simpan',
                        'pasienkirimkeunitlain_id'=>isset($response['response']['pasienkirimkeunitlain_id']) ? $response['response']['pasienkirimkeunitlain_id'] : '',
                    ];
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($response['response'], 'KunjunganForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            } else {
                $pasienKirimUnitLain = new PasienKirimUnitlain;
                $kirimUnitLain = [
                    'pegawai_id' => !empty($payloadKunjungan->pegawai_id) ? $payloadKunjungan->pegawai_id : null,
                    'instalasi_id' => $payloadKunjungan->instalasi_id,
                    'pasien_id' => $kunjunganPasien->pasien_id,
                    'pendaftaran_id' => $payload->pendaftaran_id,
                    'kelaspelayanan_id' => $kunjunganPasien->kelaspelayanan_id,
                    'ruangan_id' => $payloadKunjungan->ruangan_id,
                    'tgl_kirimpasien' => date('Y-m-d H:i:s'),
                    'catatan_dokterpengirim' => null,
                    'instruksi_id' => null,
                    'pasienadmisi_id' => !empty($kunjunganPasien->pasienadmisi_id) ? $kunjunganPasien->pasienadmisi_id : null,
                    'status_penunjang' => DocoConstants::BELUM_SETUJU,
                    'is_rujukan' => null,
                ];
                $pasienKirimUnitLain->attributes = $kirimUnitLain;
                if (!$pasienKirimUnitLain->save()) {
                    $transaction->rollBack();
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }

                $transaction->commit();
                return [
                    'messages' => 'Data berhasil di simpan',
                    'pasienkirimkeunitlain_id'=> $pasienKirimUnitLain->pasienkirimkeunitlain_id,
                ];
            }
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
            $transaction->rollBack();
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $e->getMessage()
            ];
        }

    }

    private function generateTindakan($dataArray, $payloadKunjungan, $payload, $tmpCyto = [])
    {
        if (!empty($dataArray)) {
            $tmp = $komponen = [];
            foreach ($dataArray as $value) {
                $isPaket = isset($value['jenis']) ? ($value['jenis'] == 'paket_lab' ? 'paket' : 'tindakan') : null;
                $isCyto = !empty($tmpCyto[$isPaket][$value['daftartindakan_id']])
                                ? $tmpCyto[$isPaket][$value['daftartindakan_id']] : false;
                $persenCyto = isset($value['persencyto_tindakan']) && $isCyto ? ($value['persencyto_tindakan']/100) : 0;
                $tarifSatuan = $value['harga_tariftindakan'] + ($value['harga_tariftindakan'] * $persenCyto);
                /** Parent Tindakan */
                if ($value['komponentarif_id'] == DocoConstants::KOMPONEN_TARIF) {
                    $tmp[$value['daftartindakan_id']] = [
                        'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
                        'pasien_id' => null,
                        'daftartindakan_id' => $isPaket == 'paket' ? null : $value['daftartindakan_id'],
                        'tipepaket_id' => $isPaket == 'paket' ? $value['daftartindakan_id'] : null,
                        'carabayar_id' => $payload->carabayar_id,
                        'pendaftaran_id' => null,
                        'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
                        'instalasi_id' => $payloadKunjungan->instalasi_id,
                        'ruangan_id' => $payloadKunjungan->ruangan_id,
                        'penjamin_id' => $payload->penjamin_id,
                        'tgl_tindakan' => date('Y-m-d H:i:s'),
                        'dokterpenanggungjawab_id' => !empty($payloadKunjungan->pegawai_id) ? $payloadKunjungan->pegawai_id : null,
                        'tarif_satuan' => (int) $value['harga_tariftindakan'],
                        'qty_tindakan' => 1,
                        'tarif_tindakan' => $tarifSatuan,
                        'tarifcyto_tindakan' => $isCyto ? $value['harga_tariftindakan'] * $persenCyto : 0,
                        'cyto_tindakan' => $isCyto ? true : false,
                        'discount_tindakan' => 0,
                        'additional_data' => [
                            'list_komponen' => []
                        ]
                    ];
                } else {
                    /** Generate Komponen Tindakan */
                    $komponen[$value['daftartindakan_id']][] = [
                        'komponentarif_id' => $value['komponentarif_id'],
                        'tindakanpelayanan_id' => null,
                        'tarif_kompsatuan' => $value['harga_tariftindakan'],
                        'tarif_tindakankomp' => $tarifSatuan,
                        'tarifcyto_tindakankomp' => $isCyto ? $value['harga_tariftindakan'] * $persenCyto : 0,
                        'subsidiasuransikomp' => 0,
                        'subsidipemerintahkomp' => 0,
                        'subsidirumahsakitkomp' => 0,
                        'iurbiayakomp' => 0,
                    ];
                }
            }
            $listTagihan = [];
            foreach ($tmp as $key => $value) {
                foreach ($qty_tindakan as $i => $qty_val) {
                    if ($key == $i){
                        for ($i=0; $i < $qty_val; $i++) {
                            $tmp[$key]['additional_data']['list_komponen'] = isset($komponen[$key]) ? $komponen[$key] : null;
                            $listTagihan[] = $tmp[$key];
                        }
                    }
                }
            }
            return $listTagihan;
        }
        return [];
    }

    public function actionDaftarBedah($data_kunjungan)
    {
        $restSerconn = Yii::$app->serconn->guzzle();
        $headers = Yii::$app->request->headers;
        $post['authorization'] = $headers['authorization'];
        $post['x-owner'] = $headers['x-owner'];
        $post['pendaftaran_id'] = $data_kunjungan['pendaftaran_id'];
        if(empty($data_kunjungan['pasien_id'])){
            $pendaftaran = Pendaftaran::find($data_kunjungan['pendaftaran_id'])->one();
            $data_kunjungan['pasien_id'] = $pendaftaran->pasien_id;
        }
        $post['pasien_id'] = $data_kunjungan['pasien_id'];
        $post['pegawai_id'] = $data_kunjungan['pegawai_id'];
        $post['kelaspelayanan_id'] = $data_kunjungan['kelaspelayanan_id'];
        $post['jeniskasuspenyakit_id'] = $data_kunjungan['jeniskasuspenyakit_id'];
        $post['instalasiasal_id'] = $data_kunjungan['instalasiasal_id'];
        $post['ruangan_id'] = $data_kunjungan['ruangan_id'];
        $post['ruanganasal_id'] = $data_kunjungan['ruanganasal_id'];
        $post['kunjungan'] = $data_kunjungan['kunjungan'];
        $post['status_periksa'] = DocoConstants::ST_P_PEN_BLM_OPRS;
        $post['is_bayar'] = false;
        $post['tglmasukpenunjang'] = $data_kunjungan['tgl_pendaftaran'];
        $post['list_order'] = isset($data_kunjungan['list_order']) ? $data_kunjungan['list_order'] : '';
        $post['is_aps'] = $data_kunjungan['is_aps'];
        $post['limit_tagihan'] = $data_kunjungan['limit_tagihan'] ? $data_kunjungan['limit_tagihan'] : 0;
        $post['additional_data'] = isset($data_kunjungan['additional_data']) ? $data_kunjungan['additional_data'] : '';

        // $request = $restSerconn->post('on/daftarpenunjang/daftartobedah', [
        //     'body' => json_encode($post)
        // ]);
        // $response = json_decode($request->getBody(), true);
        // return $response['Results'][0]['data'];
        $resBedah = Yii::$app->docoRest->bedahsentral;
        $request = $resBedah->post('inf-pasien-operasi/save-bedah', [
            'form_params'=>$post
        ]);
        $response = json_decode($request->getBody(),true);
        return $response;
    }

    private function generateTindakanNew($dataArray, $payloadKunjungan, $payload, $tmpCyto = [], $tmpQty = [])
    {
        if (!empty($dataArray)) {
            $tmp = $komponen = [];
            $qty = 1;
            $qty_tindakan = [];
            foreach ($dataArray as $value) {
                $isPaket = isset($value['jenis']) ? ($value['jenis'] == 'paket_lab' ? 'paket' : 'tindakan') : null;
                $isCyto = !empty($tmpCyto[$isPaket][$value['daftartindakan_id']])
                                ? $tmpCyto[$isPaket][$value['daftartindakan_id']] : false;

                $persenCyto = isset($value['persencyto_tindakan']) && $isCyto ? ($value['persencyto_tindakan']/100) : 0;
                $tarifSatuan = ($value['harga_tariftindakan'] + ($value['harga_tariftindakan'] * $persenCyto));
                $qty = !empty($tmpQty[$isPaket][$value['daftartindakan_id']]) ? $tmpQty[$isPaket][$value['daftartindakan_id']] : 1;
                /** Parent Tindakan */
                if ($value['komponentarif_id'] == DocoConstants::KOMPONEN_TARIF) {
                        $tmp[$value['daftartindakan_id']] = [
                            'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
                            'pasien_id' => null,
                            'daftartindakan_id' => $isPaket == 'paket' ? null : $value['daftartindakan_id'],
                            'tipepaket_id' => $isPaket == 'paket' ? $value['daftartindakan_id'] : null,
                            'carabayar_id' => $payload->carabayar_id,
                            'pendaftaran_id' => null,
                            'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
                            'instalasi_id' => $payloadKunjungan->instalasi_id,
                            'ruangan_id' => $payloadKunjungan->ruangan_id,
                            'penjamin_id' => $payload->penjamin_id,
                            'tgl_tindakan' => date('Y-m-d H:i:s'),
                            'dokter_id' => !empty($payloadKunjungan->pegawai_id) ? $payloadKunjungan->pegawai_id : null,
                            'tarif_satuan' => (int) $value['harga_tariftindakan'],
                            'qty' => $qty,
                            'tarif_tindakan' => $tarifSatuan,
                            'tarifcyto_tindakan' => $isCyto ? $value['harga_tariftindakan'] * $persenCyto : 0,
                            'is_cyto' => $isCyto ? true : false,
                            'discount_tindakan' => 0,
                            'additional_data' => [
                                'list_komponen' => []
                            ]
                        ];
                        $qty_tindakan[$value['daftartindakan_id']] = $qty;
                } else {
                    /** Generate Komponen Tindakan */
                    $komponen[$value['daftartindakan_id']][] = [
                        'komponentarif_id' => $value['komponentarif_id'],
                        'tindakanpelayanan_id' => null,
                        'tarif_kompsatuan' => $value['harga_tariftindakan'],
                        'tarif_tindakankomp' => $tarifSatuan,
                        'tarifcyto_tindakankomp' => $isCyto ? $value['harga_tariftindakan'] * $persenCyto : 0,
                        'subsidiasuransikomp' => 0,
                        'subsidipemerintahkomp' => 0,
                        'subsidirumahsakitkomp' => 0,
                        'iurbiayakomp' => 0,
                    ];
                }
            }
            $listTagihan = [];
            foreach ($tmp as $key => $value) {
                $tmp[$key]['additional_data']['list_komponen'] = isset($komponen[$key]) ? $komponen[$key] : null;
                $listTagihan[] = $tmp[$key];
            }
            return $listTagihan;
        }
        return [];
    }

    private function searchPendaftaran($pendaftaranId)
    {
        $data = InfPasienPenunjang::find()->select(['pendaftaran_id','pasienmasukpenunjang_id','no_pendaftaran','instalasi_id', 'ruangan_id'])->where(['pendaftaran_id' => $pendaftaranId])->one();

        return $data;
    }

    public function actionGetStyRujukanDari() {
        $model = Instalasi::find();
        $model->where(['in', 'instalasi_id', [1, 3, 73, 85]]);
        $model->andWhere([
            'is_active' => true,
            'is_deleted' => false
        ]);
        $model->orderBy(['instalasi_nama' => SORT_ASC]);
        return $model->asArray()->all();
    }

    public function actionSavePendaftaranPenunjang()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $allowBpjs = $request->post('allow_bpjs', false);

        if(isset($post['tipe_pasien']['pendaftaran_id']) && !empty($post['tipe_pasien']['pendaftaran_id'])){
            // return $this->actionSavePendaftaranPasienRsV2();
        }

        $isBpjs = $isAps = false;
        $pasienBaru = true;
        $tagihanKarcis = $tagihanPenunjang = false;
        $pmed = null;

        /** Validasi Error */
        $errorParse = [];
        /** Tindakan Karcis */
        $listTindakan = $tmp = $sepNew = $komponen = [];
        $tmpPenunjang = $listTagihan = [];

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

        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];

        $payloadKunjungan->jeniskasuspenyakit_id = 23;
        $payloadKunjungan->kelaspelayanan_id =self::K_P_STYP;

        if($payload->carabayar_id == self::C_B_PERSONIL) {
            if(in_array($payloadKunjungan->instalasi_id, self::$LIST_REG_FAR)) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Pendaftaran Farmasi tidak bisa mendaftarkan pasien dengan cara bayar personil'
                ];
            }
        }

        if (!empty($payload->no_rekam_medik)) {
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

            $keluargapasienOld = KeluargaPasien::find()->where([
                'pasien_id' => $pasienPayLoad->pasien_id
            ])->one();
        }
        if (!empty($post['pasien'])) {
            $postPasien = $post['pasien'];
            $pasienPayLoad->nama_pasien = $postPasien['nama_pasien'] ? $postPasien['nama_pasien'] : null;
            $pasienPayLoad->jeniskelamin = $postPasien['jeniskelamin'] ? $postPasien['jeniskelamin'] : null;
            $pasienPayLoad->tanggal_lahir = $postPasien['tanggal_lahir'] ? date('Y-m-d', strtotime($postPasien['tanggal_lahir'])) : null;
            $pasienPayLoad->alamat_pasien = $postPasien['alamat_pasien'] ? $postPasien['alamat_pasien'] : null;
            $pasienPayLoad->statusperkawinan = $postPasien['statusperkawinan'] ? $postPasien['statusperkawinan'] : null;
            $pasienPayLoad->golongandarah = $postPasien['golongandarah'] ? $postPasien['golongandarah'] : null;
            $pasienPayLoad->tempat_lahir = $postPasien['tempat_lahir'] ? $postPasien['tempat_lahir'] : null;
            $pasienPayLoad->umur = $postPasien['umur'] ? $postPasien['umur'] : null;
            $pasienPayLoad->propinsi_id = $postPasien['propinsi_id'] ? $postPasien['propinsi_id'] : null;
            $pasienPayLoad->pendidikan_id = $postPasien['pendidikan_id'] ? $postPasien['pendidikan_id'] : null;
            $pasienPayLoad->pekerjaan_id = $postPasien['pekerjaan_id'] ? $postPasien['pekerjaan_id'] : null;
            $pasienPayLoad->namadepan = isset($postPasien['namadepan']) ? $postPasien['namadepan'] : null;
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
                'no_rujukan' => $payloadBpjs->no_rujukan,
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
            $rujukan = $payloadRujukan->attributes;
            if (!$payloadRujukan->validate()) $errorParse['rujukan'] = $payloadRujukan->errors;
        }

        /*if (!empty($post['pj_pasien'])) {
            $payloadPjPasien = new PjPasien;
            $payloadPjPasien->attributes = $post['pj_pasien'];
            $payloadPjPasien->pj_tanggal_lahir = !empty($payloadPjPasien->pj_tanggal_lahir)
                                        ? date('Y-m-d',strtotime($payloadPjPasien->pj_tanggal_lahir)) : null;
            $pjPasien = $payloadPjPasien->attributes;
            if (!$payloadPjPasien->validate()) $errorParse['pj_pasien'] = $payloadPjPasien->errors;
        }*/
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

        /** Get Golongan Umur Id */
        $pasienPayLoad->tgl_rekam_medik = date('Y-m-d');
        $pasienPayLoad->is_aps = !empty($payload->is_aps) ? true : false;
        $pasienPayLoad->tanggal_lahir = !empty($pasienPayLoad->tanggal_lahir)
                            ?  date('Y-m-d', strtotime($pasienPayLoad->tanggal_lahir)) : null;

        if (!empty($payload->no_rekam_medik) && empty($post['pasien'])) {
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
            $pasienPayLoad->scenario = "pendaftaran-pasien-lama";
            $pasienPayLoad->attributes = $pasien;
            $pasienBaru = false;
        }

        /** Validate Form */
        if (!$pasienPayLoad->validate()) $errorParse['pasien'] = $pasienPayLoad->errors;
        if (!$payload->validate()) $errorParse['tipe_pasien'] = $payload->errors;
        if (!$payloadKunjungan->validate()) $errorParse['kunjungan'] = $payloadKunjungan->errors;

        if (!empty($errorParse)) {
            return [
                'status' => 422,
                'data' => $errorParse
            ];
        }

        $tindakanKarcis = json_decode($payloadKunjungan->tindakan_karcis,true);
        $tindakanPenunjang = json_decode($payloadKunjungan->list_penunjang,true);
        /*if (empty($tindakanPenunjang) && $payloadKunjungan->instalasi_id != DocoConstants::INST_ID_BEDAH) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Tindakan tidak boleh kosong.'
            ];
        }*/
        /** Generate Tindakan Peunjang */
        if (!empty($tindakanPenunjang)) {
            $whereCond = $tmpCyto = [];
            $penjamin = $payload->penjamin_id;
            $kelasPelayanan = $payloadKunjungan->kelaspelayanan_id;
            $ruanganId = $payloadKunjungan->ruangan_id;
            $type = 'penunjang';
            foreach ($tindakanPenunjang as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $list) {
                        if (!isset($list['id']) && !isset($list['is_cyto'])) continue;
                        $tindakanId = isset($list['id']) ? $list['id'] : null;
                        $tmpCyto[$key][$tindakanId] = $list['is_cyto'] ? $list['is_cyto'] : false;
                        if ($key === 'paket') {
                            $whereCond[] = "(jenis = 'paket_lab'
                                    AND daftartindakan_id = {$tindakanId})";
                        } else {
                            $whereCond[] = "(jenis != 'paket_lab'
                                    AND daftartindakan_id = {$tindakanId})";
                        }
                    }
                }
            }
            $condQuery = null;
            if (!empty($whereCond)) {
                $condQuery = implode(' OR ', $whereCond);
            }

            // $komponentQuery = Yii::$app->db->createCommand("
            //     SELECT
            //         jenis_tindakan,
            //         tariftindakan_id,
            //         daftartindakan_id,
            //         komponentarif_id,
            //         harga_tariftindakan,
            //         persencyto_tindakan,
            //         persendiskon_tindakan
            //     FROM infotarifpenunjang_v
            //     WHERE ({$condQuery}) AND penjamin_id = {$penjamin} AND kelaspelayanan_id = {$kelasPelayanan}
            // ")->queryAll();

            /** New get tarif tindakan to function */
            $qryTarifTindakan = (new TarifTotalRsFn([
                'extParam' => [
                    $ruanganId,
                    $penjamin,
                    $kelasPelayanan,
                    $type
                ]
            ]));
            $getTarifTindakan = $qryTarifTindakan::find()->where($condQuery)->asArray()->all();

            $qryTarifKomponen = (new TarifKomponenRsFn([
                'extParam' => [
                    $ruanganId,
                    $penjamin,
                    $kelasPelayanan,
                    $type
                ]
            ]));
            $getTarifKomponen = $qryTarifKomponen::find()->where($condQuery)->asArray()->all();
            $komponentQuery = array_merge($getTarifTindakan, $getTarifKomponen);
            $tmpPenunjang = $this->generateTindakanNew($komponentQuery, $payloadKunjungan, $payload, $tmpCyto);
        }

        /** Generate Tindakan Karcis */
        if (!empty($tindakanKarcis)) {
            // $modelTindakan = InfoTarifRsView::find()->select([
            //     'tariftindakan_id',
            //     'daftartindakan_id',
            //     'komponentarif_id',
            //     'harga_tariftindakan',
            //     'persencyto_tindakan',
            //     'persendiskon_tindakan',
            // ])->where([
            //     'ruangan_id' => $payloadKunjungan->ruangan_id,
            //     'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
            //     'daftartindakan_id' => $tindakanKarcis,
            //     'penjamin_id' => $payload->penjamin_id,
            // ])->all();

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
                'kelompoktindakan_id' => DocoConstants::VAR_KEL_PENUNJANG,
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
            ->andWhere([
                'kelompoktindakan_id' => DocoConstants::VAR_KEL_PENUNJANG,
                'daftartindakan_id' => $tindakanKarcis
            ])->all();

            $modelTindakan = array_merge($modelTarifTotal, $modelTarifKomponen);
            $listTagihan = $this->generateTindakanNew($modelTindakan, $payloadKunjungan, $payload);
            /** Comment jika sudah hit kasir */
            // $tmpPenunjang = array_merge($tmpPenunjang,$listTagihan);
        }

        $umur = ucwords(DocoHelpers::getUmur($pasienPayLoad->tanggal_lahir));

        $statusPeriksa = 1;
        $isKarcis = false;
        if (!empty($tmp) && $payload->carabayar_id == DocoConstants::CB_PEN_UMUM) {
            $isKarcis = true;
        }

        $statusPasien = !empty($payload->no_rekam_medik) ? DocoConstants::VAR_PAS_L : DocoConstants::VAR_PAS_B;

        if (empty($payload->antrian_id) && $payload->carabayar_id != DocoConstants::CB_PEN_UMUM) {
            $dataAntrian = [
                'pasien_id' => $pasienPayLoad->pasien_id,
                'ruangan_id' => $payloadKunjungan->ruangan_id,
                'carabayar_id' => $payload->carabayar_id,
                'pendaftaran_id' => null, // Di Set Di Trigger
                'tgl_antrian' => date("Y-m-d H:i:s"),
                'no_antrian' => 'otomatis',
                'penjamin_id' => $payload->penjamin_id,
                'pegawai_id' => !empty($payloadKunjungan->pegawai_id) ? $payloadKunjungan->pegawai_id : null,
                'status_pasien' => $statusPasien,
                'jenisantrian_id' => DocoConstants::VAR_JA_PEN,
                'is_active' => false,
            ];
        }

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
            'antrian' => $dataAntrian,
            'penanggung_jawab' => $pjPasien,
            'asuransi' => $asuransi,
            'penanggungbiaya' => $penanggungbiaya,
            'keluargapasien' => $keluargapasien,
        ];

        if (!empty($tmpPenunjang)) {
            $additionals['tarif_penunjang'] = $tmpPenunjang;
        }

        if ($pasienBaru) {
            $additionals['pasien'] = $pasienPayLoad->attributes;
            $isAps = true;
        }

        $dataCaraBayar = CaraBayar::findOne($payload->carabayar_id);
        $groupCaraBayarId = ArrayHelper::getValue($dataCaraBayar, 'groupcarabayar_id');
        $limitTagihan = $payloadKunjungan->limit_tagihan ? $payloadKunjungan->limit_tagihan : 0;
        if ($isAps && $isBpjs || $groupCaraBayarId == DocoConstants::GROUP_BPJS) {
            $autoPlafon = $this->setAutoPlafon($payloadKunjungan->instalasi_id, $payloadKunjungan->kelaspelayanan_id, $payloadKunjungan->ruangan_id);
            if (!empty($autoPlafon)) {
                $limitTagihan = $autoPlafon;
            }
        }
        
        $dataPendaftaran = [
            'caramasuk_id' => $payload->asalrujukan_id,
            'tgl_pendaftaran' => date('Y-m-d H:i:s'),
            'penjamin_id' => $payload->penjamin_id,
            'pasien_id' => $pasienPayLoad->pasien_id,
            'pegawai_id' => $payloadKunjungan->pegawai_id,
            'instalasi_id' => $payloadKunjungan->instalasi_id,
            'jeniskasuspenyakit_id' => $payloadKunjungan->jeniskasuspenyakit_id,
            'kelaspelayanan_id' => $payloadKunjungan->kelaspelayanan_id,
            'carabayar_id' => $payload->carabayar_id,
            'golonganumur_id' => $getGolUmurId,
            'umur' => $umur,
            'rujukan_id' => null,
            'antrian_id' => $payload->antrian_id,
            // 'kunjungan' => !empty($payload->no_rekam_medik) ? DocoConstants::VAR_K_L : DocoConstants::VAR_K_B,
            'kunjungan' => $payloadKunjungan->status_kunjungan,
            'ruangan_id' => $payloadKunjungan->ruangan_id,
            'transportasi' => $payloadKunjungan->transportasi,
            'keadaan_masuk' => $payloadKunjungan->keadaan_masuk,
            'status_periksa' => $statusPeriksa,
            'status_pasien' => $statusPasien,
            'status_masuk' => !empty($rujukan) ? DocoConstants::VAR_SM_R : DocoConstants::VAR_SM_NR,
            'keterangan_pendaftaran' => $payloadKunjungan->keterangan,
            'is_karcis' => $isKarcis,
            'additional_data' => json_encode($additionals),
            'is_aps' => $isAps,
            'is_skd' => $payloadKunjungan->is_skd,
            'limit_tagihan' => $limitTagihan,
            'dokterpengirim_id' => $payloadKunjungan->dokterpengirim_id,
            'styrujukaninstalasi_id' => $payloadKunjungan->styrujukaninstalasi_id,
            'dokterpengganti_id' => $payloadKunjungan->dokter_pengganti_id,
            'diagnosa' => $payloadKunjungan->diagnosa
        ];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $pendaftaran = new Pendaftaran;
            $pendaftaran->scenario = 'penunjang_styp';
            $pendaftaran->attributes = $dataPendaftaran;
            $tmpPendaftaran = (new Penomoran)->setNoReg($payloadKunjungan->instalasi_id);

            if(empty($tmpPendaftaran)) return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'No pendaftaran tidak tersedia.'
            ];

            $getLastRm = (new Penomoran)->getSetNoRm(self::PREFIX_PASIEN, self::PENOMORAN_PASIEN);
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
                // ini harusnya dihapus
                /* Daftar Ke bedah Tanpa Tindakan (Dengan tindakan by trigger)*/
                if ($payloadKunjungan->instalasi_id == DocoConstants::INST_ID_BEDAH && empty($tmpPenunjang)) {
                    $dataPendaftaran['pendaftaran_id'] = $idPendaftaran;
                    $dataPendaftaran['instalasiasal_id'] = $payloadKunjungan->instalasi_id;
                    $dataPendaftaran['ruanganasal_id'] = $payloadKunjungan->ruangan_id;
                    $dataPendaftaran['kunjungan'] = '9999';
                    $dataPendaftaran['is_aps'] = true;
                    $pasienPenunjangBedah = $this->actionDaftarBedah($dataPendaftaran);
                    if($pasienPenunjangBedah['metadata']['status'] != 200) {
                         $transaction->rollBack();
                         return [
                             'status' => 422,
                             'title' => 'Proses Gagal!',
                             'text' => 'Terjadi Kesalahan'
                         ];
                    }
                }

                // Insert data ke pasienmasukpenunjang_t ketika tanpa pemeriksaan
                $pasienmasukpenunjang = new PasienMasukPenunjang;
                $pasienmasukpenunjang->attributes = $dataPendaftaran;
                if(empty($dataPendaftaran['pasien_id'])){
                    $pendaftaranNew = Pendaftaran::find()->where(['pendaftaran_id' => $idPendaftaran])->one();
                    $dataPendaftaran['pasien_id'] = $pendaftaranNew->pasien_id;
                }
                $pasienmasukpenunjang->pasien_id = $dataPendaftaran['pasien_id'];
                $pasienmasukpenunjang->ruanganasal_id = $dataPendaftaran['ruangan_id'];
                $pasienmasukpenunjang->pendaftaran_id = $idPendaftaran;
                $pasienmasukpenunjang->status_periksa = '477'; //Belum Periksa
                $pasienmasukpenunjang->tglmasukpenunjang = date('Y-m-d H:i:s');
                if(!$pasienmasukpenunjang->save()) {
                    $transaction->rollBack();
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => $pasienmasukpenunjang->getErrors()
                    ];
                }

                $transaction->commit();

                $infoDaftar = $this->searchPendaftaran($idPendaftaran);
                $penunjangId = $pasienmasukpenunjang->pasienmasukpenunjang_id;

                if(!empty($tindakanPenunjang)){

                    foreach($tmpPenunjang as $k => $v){
                        $tmpPenunjang[$k]['pasienmasukpenunjang_id'] = isset($penunjangId) ? $penunjangId : null;
                    }

                    $tagihanPenunjang =  (new KasirService)->tagihanPenunjang([
                        'pendaftaran_id' => $idPendaftaran,
                        'no_pendaftaran' =>$infoDaftar->no_pendaftaran,
                        'instalasi_id' =>$infoDaftar->instalasi_id,
                        'ruangan_id' =>$infoDaftar->ruangan_id,
                        'penjamin_id' => $payload->penjamin_id,
                        'kelas_pelayanan_id' => $payloadKunjungan->kelaspelayanan_id
                    ], $tmpPenunjang);

                    if (isset($tagihanPenunjang['meta']['result']) && $tagihanPenunjang['meta']['result'] == 'failed') {
                        $transaction->rollBack();
                        return isset($tagihanPenunjang['message']) ? $tagihanPenunjang['message'] : 'Terjadi Kesalahan API';
                    }
                }

                if(!empty($tindakanKarcis)){

                    foreach($listTagihan as $k => $v){
                        $listTagihan[$k]['pasienmasukpenunjang_id'] = isset($penunjangId) ? $penunjangId : null;
                    }

                    $tagihanKarcis = (new KasirService)->tagihan([
                        'pendaftaran_id' => $idPendaftaran,
                        'no_pendaftaran' =>$infoDaftar->no_pendaftaran,
                        'instalasi_id' =>$infoDaftar->instalasi_id,
                        'ruangan_id' =>$infoDaftar->ruangan_id,
                        'penjamin_id' => $payload->penjamin_id,
                        'kelas_pelayanan_id' => $payloadKunjungan->kelaspelayanan_id
                    ], $listTagihan);

                    if (isset($tagihanKarcis['meta']['result']) && $tagihanKarcis['meta']['result'] == 'failed') {
                        $transaction->rollBack();
                        return isset($tagihanKarcis['message']) ? $tagihanKarcis['message'] : 'Terjadi Kesalahan API';
                    }
                }

                // Integrasi Akunting
                IntegrasiAkunting::integrateKarcisPasien($idPendaftaran);

                $params['route'] = 'app/save-pendaftaran-penunjang';
                $params['data'] = $this->syncPendaftaran($idPendaftaran);
                $sendData = (new PendaftaranService)->syncPendaftaranSty($params, function($data, $result) {
                    return $result;
                });
                $payload = $params['data'];
                $this->setLog($idPendaftaran, $payload, $sendData);
                (new Penomoran)->save($tmpPendaftaran['konfig_id'], 1, $tmpPendaftaran['no_reg'], $tmpPendaftaran['no_reg']);
                (new Penomoran)->getSetNoRm(self::PREFIX_PASIEN, self::PENOMORAN_PASIEN, 'save');

                return [
                    'message' => 'Proses Pendaftaran berhasil',
                    'karcis' => $tagihanKarcis,
                    'penunjang' => $tagihanPenunjang,
                    'is_bpjs' => $prosesBpjs,
                    'id' => DocoHelpers::encrypt($idPendaftaran),
                ];
            }

            $errors = DocoHelpers::parseError(['data' => $pendaftaran->errors], 'KunjunganForm');
            return [
                'data' => $errors,
                'status' => 422
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $exception = preg_match('/(?<=ERROR:  )(.*)/',$e->getMessage(),$out);
            (new Penomoran)->save($tmpPendaftaran['konfig_id'], 0, $tmpPendaftaran['no_reg'], $tmpPendaftaran['no_reg']);
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
            (new Penomoran)->save($tmpPendaftaran['konfig_id'], 0, $tmpPendaftaran['no_reg'], $tmpPendaftaran['no_reg']);
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $e->getMessage()
            ];
        }
    }

    public function actionSavePendaftaranPasienRsV2()
    {
        $post =  Yii::$app->request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $payload = new TipePasien;
        $payloadKunjungan = new Kunjungan;

        $payload->attributes = !empty($post['tipe_pasien']) ? $post['tipe_pasien'] : [];
        $payloadKunjungan->attributes = !empty($post['kunjungan']) ? $post['kunjungan'] : [];
        if (!$payload->validate()) $errorParse['tipe_pasien'] = $payload->errors;
        if (!$payloadKunjungan->validate()) $errorParse['kunjungan'] = $payloadKunjungan->errors;

        if (!empty($errorParse)) {
            return [
                'status' => 422,
                'data' => $errorParse
            ];
        }
        $tindakanPenunjang = json_decode($payloadKunjungan->list_penunjang,true);
        /*if (empty($tindakanPenunjang) && $payloadKunjungan->instalasi_id != DocoConstants::INST_ID_BEDAH) {
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => 'Tindakan tidak boleh kosong.'
            ];
        }*/
        $list_order=[];
        $response=[];

        if(!empty($tindakanPenunjang)){
            foreach ($tindakanPenunjang as $_jenisTindakan => $_tindakanPerJenis) {
                foreach ($_tindakanPerJenis as $_detailTindakan) {
                    if($payloadKunjungan->instalasi_id == DocoConstants::INST_ID_BEDAH){
                        $golId = !empty($_detailTindakan['jenispemeriksaanlab_id']) ? $_detailTindakan['jenispemeriksaanlab_id'] : false;
                        $kegId = !empty($_detailTindakan['kelompokpemeriksaanlab_id']) ? $_detailTindakan['kelompokpemeriksaanlab_id'] : false;
                      };
                    $list_order[] = [
                        'tariftindakan_id' => $_detailTindakan['tariftindakan_id'],
                        'is_paket' => $_jenisTindakan == 'tindakan' ? false : true,
                        'is_cyto' => $_detailTindakan['is_cyto'] == 0 ? false : true,
                        'golongan_id' => isset($golId) ? $golId : '',
                        'kegiatan_id' => isset($kegId) ? $kegId : ''
                    ];

                }
            }
        }

        if($payload){
            $kunjunganPasien = InfoKunjunganRsView::find()->where(['pendaftaran_id' => $payload->pendaftaran_id])->one();
        }

        try{
            $data_order = [
                'pendaftaran_id' => $payload->pendaftaran_id,
                'pasienadmisi_id' => !empty($kunjunganPasien->pasienadmisi_id) ? $kunjunganPasien->pasienadmisi_id : null,
                'instalasi_id' => $payloadKunjungan->instalasi_id,
                'ruangan_id' => $payloadKunjungan->ruangan_id,
                'pegawai_id' => !empty($payloadKunjungan->pegawai_id) ? $payloadKunjungan->pegawai_id : null,
                'cppt_id' => null,
                'catatan_dokterpengirim' => null,
                'tgl_kirimpasien' => date('Y-m-d H:i:s'),
                'instruksi_id' => null,
                'list_order' => !empty($list_order) ? json_encode($list_order) : ''
            ];

            if ($payloadKunjungan->instalasi_id == DocoConstants::VAR_I_LAB) {
                //hit api backend laboratorium
                $restLab = Yii::$app->docoRest->laboratorium;
                $request = $restLab->post('order/create?id='.$payload->pendaftaran_id, [
                    'form_params'=>$data_order
                ]);
                $response = json_decode($request->getBody(),true);
            } elseif ($payloadKunjungan->instalasi_id == DocoConstants::VAR_I_RAD) {
                //hit api backend radiologi
                $restRad = Yii::$app->docoRest->radiologi;
                $request = $restRad->post('order/create?id='.$payload->pendaftaran_id, [
                    'form_params'=>$data_order
                ]);
                $response = json_decode($request->getBody(),true);
            } elseif ($payloadKunjungan->instalasi_id == DocoConstants::VAR_I_BED) {
                // hit api bedah
                // $resBedah = Yii::$app->docoRest->bedahsentral;
                // $request = $resBedah->post('order/create?id='.$payload->pendaftaran_id, [
                //     'form_params'=>$data_order
                // ]);
                // $response = json_decode($request->getBody(),true);
                $lastKunjungan = $kunjunganPasien;
                if($lastKunjungan) {
                    $dataPendaftaran['pasien_id'] = $lastKunjungan->pasien_id;
                    $dataPendaftaran['pendaftaran_id'] = $payload->pendaftaran_id;
                    $dataPendaftaran['pegawai_id'] = $lastKunjungan->pegawai_id;
                    $dataPendaftaran['kelaspelayanan_id'] = $lastKunjungan->kelaspelayanan_id;
                    $dataPendaftaran['instalasiasal_id'] = $lastKunjungan->instalasi_id;
                    $dataPendaftaran['ruanganasal_id'] = $lastKunjungan->ruangan_id;
                    $dataPendaftaran['kunjungan'] =  $lastKunjungan->status_pasien;
                    $dataPendaftaran['is_aps'] = false;
                    $dataPendaftaran['list_order'] = !empty($list_order) ? json_encode($list_order) : false;
                    $dataPendaftaran['tgl_pendaftaran'] = date('Y-m-d H:i:s');
                    $dataPendaftaran['ruangan_id'] = $payloadKunjungan->ruangan_id;
                    $dataPendaftaran['jeniskasuspenyakit_id'] = $lastKunjungan->jeniskasuspenyakit_id;
                    $dataPendaftaran['limit_tagihan'] = $payloadKunjungan->limit_tagihan ? $payloadKunjungan->limit_tagihan : 0;
                    $dataPendaftaran['dokterpengirim_id'] = $payloadKunjungan->dokterpengirim_id;
                    $dataPendaftaran['styrujukaninstalasi_id'] = $payloadKunjungan->styrujukaninstalasi_id;

                    $response = $this->actionDaftarBedah($dataPendaftaran);
                } else {
                    $transaction->rollBack();
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Pendaftaran tidak ditemukan'
                    ];
                }
            }
            if ($response['metadata']['status'] == 200) {
                $transaction->commit();
                return [
                    'messages' => 'Data berhasil di simpan',
                    'pasienkirimkeunitlain_id'=>isset($response['response']['pasienkirimkeunitlain_id']) ? $response['response']['pasienkirimkeunitlain_id'] : '',
                    'id' => DocoHelpers::encrypt($payload->pendaftaran_id),
                ];
            } else {
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($response['response'], 'KunjunganForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
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
            $transaction->rollBack();
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

            // if(isset($add['keluargapasien']) && !empty($add['keluargapasien'])) {
                $keluarga = SyKeluargaPasienView::find()
                ->where(['pasien_id' => $pdftrn['pasien_id']])
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

            $penunjang = SyPasienMasukPenunjangView::find()
            ->where(['pendaftaran_id' => $idPendaftaran])
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
            'penunjang' => $penunjang,
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
