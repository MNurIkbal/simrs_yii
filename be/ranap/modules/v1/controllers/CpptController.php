<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-03 15:07:15
 */

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use app\modules\v1\models\CpptView;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;
use yii\db\Expression;
use Doco\components\DocoConstansId;
use Doco\components\DocoMessages;

// Using model
use app\modules\v1\models\Antrian;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\AsesmenAwal;
use app\modules\v1\models\PeriksaTubuh;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\InfoPasienRanap;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Instruksi;
use app\modules\v1\models\InfoResepturView;
use app\modules\v1\models\InfoResepturDetailView;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\KonfigAntrianView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\Racikan;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\RiwayatTindakanView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\InstruksiTindakan;
use app\modules\v1\models\RiwayatInstruksiTindakanView;
use app\modules\v1\models\PaketDetailView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\OrderPenunjangView;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\InstruksiTindakanBmhp;
use app\modules\v1\models\InfoStokObatAlkesFn;
use app\modules\v1\models\ReminderPuasa;
use app\modules\v1\models\ResumeMedisRIT;
use app\modules\v1\models\ResepturRacikan;
use app\modules\v1\models\ProgramTerapiRajal;
use Doco\models\Pendaftaran;
use Doco\models\SoapRsView;
use Doco\models\LookupTransaksi;
use Doco\Notifications\GiziNotification;
use Doco\Notifications\FarmasiNotification;
use Doco\Traits\GeneralResepturTrait;
use Doco\Traits\TindakanPenunjangTrait;
use Doco\rabbitmq\RabbitBgProcess;
use app\modules\v1\models\CpptGiziView;
use app\modules\v1\models\Pagt;
use app\modules\v1\models\InfoInstruksiView;
use Doco\Services\FarmasiService;
use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadPayload;
use Doco\Repositories\LookUpTransaksiRepositories;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\ProgramTerapi;
use app\modules\v1\models\ProgramTerapiDetail;
use app\modules\v1\models\PemeriksaanFisio;
use app\modules\v1\models\PasienKirimUnitLain;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\actions\Cppt\OrderRanapAction;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\SoapFisioterapi;
use SirsCore\models\LogActivityR;

// Class
class CpptController extends DocoActiveController
{
    use GeneralResepturTrait;
    use TindakanPenunjangTrait;

    protected $type;
    protected $cpptModel;
    protected $asesmenMedis;
    public $konfigCpptKosong;
    public $konfigEditCpptCoret;

    // Model class
    public $modelClass = 'app\modules\v1\models\Cppt';
    public $transactionClass = null;

    public $messageBroker = [
        'simpan-reseptur' => [
            'services' =>[
                'Sirs' => [
                    'AddAntrianFarmasiJkn' => [
                        'result' => true,
                        'successProcess'=>true,
                    ]
                ]
            ]
        ]
    ];
    // Verbs
    public function verbs()
    {
        // Parent
        $verbs = parent::verbs();

        // Return
        return $verbs;
    }

    // Acions
    public function actions()
    {
        // Parent
        $actions = parent::actions();

        // Unset actions
        unset($actions['index']);
        unset($actions['delete']);

        // Return
        return $actions;
    }

    public function init()
    {
        parent::init();
        $this->type = 'RI';
        $this->cpptModel = (new CpptView);
        $this->asesmenMedis = (new AsesmenMedis);

        $this->konfigSystemCppt();
    }

    public function konfigSystemCppt() {
        $konfigCppt = KonfigSystem::find()->select(['is_hide_cppt_kosong','is_edit_cppt_coret'])->asArray()->one();
        $this->konfigCpptKosong = $konfigCppt['is_hide_cppt_kosong'];
        $this->konfigEditCpptCoret = $konfigCppt['is_edit_cppt_coret'];
        
    }

    // Index
    public function actionIndex()
    {
        // Try catch
        // try {
            // Request
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $orderBy = $request->get('order', []);
            $ruangan_id = $request->get('ruangan_id', null);
            $pegawai_id = $request->get('pegawai_id', null);
            $tgl_cppt = $request->get('tgl_cppt', null);
            $length = $request->get('length', null);
            $start = $request->get('start', null);
            $newData = [];
            $orderSoap = (new DocoConstansId)->actionGetAdditional('orderby_soap');
            $pend_asal = Pendaftaran::find()
                ->select([
                    new \yii\db\Expression("(additional_data::json->'pendaftaranasal_id') AS pend_asal")
                ])
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()
                ->one();
           
            // Query
            $querySoap = SoapRsView::find($this->konfigEditCpptCoret)
                ->select([
                    new \yii\db\Expression('CASE WHEN referred_id IS NOT NULL THEN referred_id ELSE cppt_id END AS cppt_id'),
                    'cppt_id AS origin_cppt_id',
                    'tgl_soaprj AS tgl_cppt',
                    'a_diag_utama',
                    'a_diag_penyerta',
                    'kelompokpegawai_nama',
                    'nama_pegawai',
                    'ruangan_nama',
                    'subject',
                    'object',
                    'planning',
                    'no_tempattidur',
                    'pegawai_instruksi',
                    'kamarruangan_nokamar',
                    'catatan_dokter',
                    'catatan_perawat',
                    'instruksi',
                    'is_verifikasi',
                    'is_deleted',
                    'is_instruksi_pulang',
                    'dokteradmisi_id',
                    'pemberi_instruksi_id',
                    'is_verifikasi_verbal',
                    'pegawai_verifikasi_verbal',
                    'tgl_verif_verbal',
                    'tgl_verifikasi',
                    'pegawai_verifikasi',
                    'spesialis_nama',
                    'pendaftaran_id',
                    'pegawai_id',
                    'tipe',
                    'is_lab',
                    'is_rad',
                    'is_reseptur',
                    'is_konsul',
                    'pegawai_update_nama',
                    'created_date',
                    'kelompokpegawai_id',
                    'is_fisio',
                    'is_icd_x',
                    'is_verbal_order'
                ])
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->andWhere(['not', ['pasienadmisi_id' => null]]);

            if (empty($orderBy)) {
                $orderBy ='tgl_soaprj '.$orderSoap.',created_date '.$orderSoap;
                    $querySoap = $querySoap->orderBy($orderBy);
            } else {
                $querySoap = $querySoap->orderBy($orderBy);
                // $orderBy = $orderBy;
            }
            if ($pend_asal['pend_asal'] != 'null') {
                $querySoap->orWhere(['pendaftaran_id' => $pend_asal['pend_asal']]);
                $querySoap = $this->whereClauseFisioPendaftaranIdsNew($querySoap, $pend_asal['pend_asal'], null);
            }
            if (!empty($ruangan_id)) {
                $querySoap->andWhere([
                    'ruangan_id' => $ruangan_id
                ]);
            }

            if (!empty($pegawai_id)) {
                $querySoap->andWhere([
                    'pegawai_id' => $pegawai_id
                ]);
            }

            if($this->konfigCpptKosong == TRUE) {
                $querySoap->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL OR is_verbal_order = true)");
            }

            $kelompokpegawai_id = $request->get('filter_kelompokpegawai_id');
            if (
                ($kelompokpegawai_id) &&
                in_array($kelompokpegawai_id, [
                    DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                    DocoConstants::KELOMPOK_PEGAWAI_DOKTER
                ])
            ) {
                $querySoap->andWhere([
                    'kelompokpegawai_id' => $kelompokpegawai_id,
                ]);
            }

            $start = null;
            $end = null;
            if(!empty($tgl_cppt)){
                $start = date('Y-m-d 00:00:00');
                $end = date('Y-m-d 23:59:00');
                $explode = explode("-", $tgl_cppt);
                if (count($explode) == 2) {
                    $start = date_format(date_create_from_format('d/m/Y', $explode[0]), 'Y-m-d').date(' 00:00:00');
                    $end = date_format(date_create_from_format('d/m/Y', $explode[1]), 'Y-m-d').date(' 23:59:59');
                }
                $querySoap->andWhere(['between', 'tgl_soaprj', $start, $end]);
            }

            if (!empty($length)) {
                $querySoap->limit($length);
            }

            if (!empty($start)) {
                $querySoap->offset($start);
            }

            $data = $querySoap->asArray()->all();
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $data[$key]['is_gizi'] = false;
                    $data[$key]['tgl_soaprj'] = $value['tgl_cppt'];
                }
            }
            
            $cpptGizi = $this->getCpptGizi($pendaftaran_id, $start, $end, $pegawai_id, $kelompokpegawai_id, $orderBy, $length);
            if (!empty($cpptGizi)) {
                foreach ($cpptGizi as $keyGizi => $valueGizi) {
                    $cpptGizi[$keyGizi]['is_gizi'] = true;
                    $cpptGizi[$keyGizi]['tgl_soaprj'] = $valueGizi['origin_tgl_cppt'];
                }
            }
            $newData = array_merge($data, $cpptGizi);
            $newData = DocoHelpers::sortArray($newData, $orderBy);
            $newData = array_slice($newData, 0, $length);
            $totalData = count($newData);

            return [
                'data' => $newData,
                'totalCount' => $totalData,
                'load_more' => $totalData == $length ? true : false,
            ];
        // } catch (\yii\db\Exception $e) {
        //     // Change status code
        //     \Yii::$app->response->statusCode = 500;

        //     // Return message
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // } catch (\Exception $e) {
        //     // Change status code
        //     \Yii::$app->response->statusCode = 500;

        //     // Return message
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
    }

    // get soap fisio rajal setelah dirujuk
    private function getPendaftaranIdsFisioRajal($pendaftaranId, $pasienId)
    {
        $programTerapiRajals = [];
        $pendaftaranIds = [];
        $pendaftaranTempIds = [];
        if(!$pendaftaranId) {
            $pendaftarans = Pendaftaran::find()
                ->where(['pasien_id' => $pasienId])
                ->asArray()
                ->all();
            $pendaftaranTempIds = [];
            foreach ($pendaftarans as $key => $value) {
                $pendaftaranTempIds[] = ArrayHelper::getValue($value, 'pendaftaran_id');
            }
        } else {
            $pendaftaranTempIds[] = $pendaftaranId;
        }
        $programTerapis = ProgramTerapi::find()
            ->where(['in', 'pendaftaran_id', $pendaftaranTempIds])
            ->asArray()
            ->all();
        $programTerapiTempIds = [];
        foreach ($programTerapis as $key => $value) {
            $programTerapiTempIds[] = ArrayHelper::getValue($value, 'programterapi_id');
        }
        $programTerapiRajals = ProgramTerapiRajal::find()
            ->where(['in', 'programterapi_id', $programTerapiTempIds])
            ->asArray()
            ->all();
        foreach ($programTerapiRajals as $key => $value) {
            $pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
            array_push($pendaftaranIds, $pendaftaranId);
        }
        return $pendaftaranIds;
    }

    private function whereClauseFisioPendaftaranIds($query, $pendaftaranId, $pasienId)
    {
        $pendaftaranIdsFisioRajal = $this->getPendaftaranIdsFisioRajal($pendaftaranId, $pasienId);
        return $query->orWhere(['in', 'pendaftaran_id', $pendaftaranIdsFisioRajal]);
    }

    // Get list data
    public function actionGetListData()
    {
        // Try catch
        // try {
            // Request
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $pasien_id = $request->get('pasien_id');
            $pegawai_id = $request->get('pegawai_id');
            $ruangan_id = $request->get('ruangan_id');
            $pasienadmisi_id = $request->get('pasienadmisi_id');
            $cppt_id = $request->get('cppt_id', 0);
            $tindakanExist = RiwayatTindakanView::find()->select(['pendaftaran_id'])->where(['pendaftaran_id' => $pendaftaran_id])->one();

            // Return
            return [
                'listRuangan' => $this->getListRuangan($pasien_id),
                'listDiagnosa' => $this->getListDiagnosa(),
                'pegawai' => $this->getPegawai($pegawai_id, $ruangan_id),
                // 'informasiRawatInap' => $this->getInformasiRawatInap($pendaftaran_id),
                'listPemberiInstruksi' => $this->getPemberiInstruksi(DocoConstants::INST_ID_RI, $ruangan_id),
                'listDataSigna' => $this->getOrSetCache(DocoConstants::VC_SO, SignaObat::find()),
                'isExistTindakan' => !empty($tindakanExist) ? 1 : 0,
                'listDataApotek' => $this->getOrSetCache(DocoConstants::VC_R_I, $this->getRuanganInstalasi(DocoConstants::VAR_I_A), true, DocoConstants::VAR_I_A),
                // 'listDataObatalkes' => $this->getOrSetCache(DocoConstants::VAR_CACHE_OBATALKES, $this->getObatAlkes(DocoConstants::JENIS_OBATALKES_OBAT)),
                // 'listDataObatalkesByInstalasi' => $this->getObatAlkesByInstalasi($this->getRuanganInstalasi(DocoConstants::VAR_I_A)->all()),
                'listDataTerapi' => $this->getListTerapi(),
                'listInstalasiPenunjang' => $this->getListInstalasiPenunjang(),
                'statusPulang' => $this->getSoapIsPulang($pendaftaran_id),
                'listJenisPemakaian' => Lookup::find()->where(['lookup_type' => 'group_jenisobat'])->asArray()->all(),
                'listJenisInstruksi' => $this->getOrSetCache(
                    DocoConstants::VAR_CACHE_LOOKUP_JENISINSTRUKSI,
                    Lookup::find()->where(['lookup_type' => 'jenis_instruksi']),
                    true,
                    'jenis_instruksi'
                ),
                'lastCppt' => $this->getLastCppt($pendaftaran_id, $pegawai_id, $pasienadmisi_id),
                'suggestion' => $this->suggestionCppt($pendaftaran_id, $pasienadmisi_id),
                'cpptInactive' => $this->getCpptInactive($pendaftaran_id, $pegawai_id)
            ];
        // } catch (\yii\db\Exception $e) {
        //     // Change status code
        //     \Yii::$app->response->statusCode = 500;
        //     throw $e;
        //     throw $e;

        //     // Return message
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // } catch (\Exception $e) {
        //     // Change status code
        //     \Yii::$app->response->statusCode = 500;

        //     // Return message
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
    }

    /*
     * @see Fungsi get data obatalkes ruangan
     * @return array, activeQueryRecords
     *
     */
    public function actionListObatAlkes()
    {
        // Try
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('ruangan_id', null);

            $data = InfoStokObatAlkesView::find()->where(['ruangan_id' => $ruangan_id]);
            $items = $data->all();

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

    /**
     * @todo Fungsi untuk mendapatkan list obatalkes by function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionListObatAlkesFn()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id', null);

        $model = InfoStokObatAlkesFn::find()->select([
            'obatalkes_nama',
            'obatalkes_namalain',
            'obatalkes_id',
            'ruangan_id',
            'obatalkes_kode',
            'qty_tersedia',
            'satuankecil_id',
            'satuankecil_nama',
            'hargaygdipakai',
            'harganetto_ygdipakai',
            'jml_margin as jmlmargin',
            'jml_discount as jmldiscount',
            'jml_ppn as jmlppn',
            'persen_ppn as persenppn',
            'persen_disc as persendiscount',
            'persen_margin as persenmargin'
        ])->where([
            'ruangan_id' => $ruangan_id
        ])
            ->orderBy(['obatalkes_namalain' => SORT_ASC])
            // ->limit(10)
            ->asArray()->all();

        return empty($model) ? [] : $model;
    }

    // fungsi mendapatkan list obat alkes with pagination
    // public function actionListObatAlkesFn()
    // {
    //     try {
    //         $request = Yii::$app->request;
    //         $ruangan_id = $request->get('ruangan_id');
    //         $q = $request->get('keyword');
    //         $page = $request->get('page');

    //         $data = InfoStokObatAlkesFn::find()
    //             ->where(['ruangan_id' => $ruangan_id])
    //             ->andWhere(['LIKE', 'LOWER(obatalkes_nama)', strtolower($q)])
    //             ->offset(($page-1)*10)->limit(11)
    //             ->asArray()->all();

    //         return [
    //             'data' => $data,
    //             'payload' => $request->get(),
    //         ];
    //     } catch (\yii\db\Exception $e) {
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     } catch (\Exception $e) {
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    /**
     * @todo Fungsi untuk mendapatkan list satuan besar dari masing2 obat
     * @author Wahyu Saepuloh
     */
    public function actionListSatuanBesar()
    {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id', null);
            $data = SatuanKonversiView::find()
                ->where([
                    'obatalkes_id' => $obatalkes_id,
                    'jenis' => 'obat',
                    'is_active' => true
                ]);
            $items = $data->asArray()->all();

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

    public function actionGetListStokObat()
    {
        $request = Yii::$app->request;
        $result = [];
        try {
            if (!empty($request->get('instalasi_id')) && !empty($request->get('ruangan_id'))) {
                $keyword = $request->get('keyword');
                $page = $request->get('page');
                $query = $this->listObatAlkesFn($request->get('instalasi_id'), $request->get('ruangan_id'));
                $query->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($keyword)]);
                $query->offset(($page - 1) * 10)->limit(11);
                $result = $query->all();
            }
            return [
                'data' => $result,
                'payload' => $request->get()
            ];
        } catch (Exception $e) {
            return $result;
        }
    }

    public function actionGetKonversi()
    {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id', null);
            $satuanbesar_id = $request->get('satuanbesar_id', null);
            $data = SatuanKonversiView::find()
                ->where([
                    'jenis' => 'obat',
                    'obatalkes_id' => $obatalkes_id,
                    'satuanbesar_id' => $satuanbesar_id
                ]);

            $items = $data->asArray()->one();

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

    /**
     *
     * @see Fungsi insert reseptur pemeriksaan rawat inap
     * @return array response
     *
     */
    public function actionCreateReseptur()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $data = $request->post();
            $data_instruksi = $data['data_instruksi'];
            $data_reseptur = $data['data_reseptur'];
            $data_resepturdetail = $data['data_resepturdetail'];
            $racikanKode = [];

            // generate cppt
            if ($data_instruksi['cppt_id'] == '0') {
                $getCppt = AllowController::actionCreateSoap($data_instruksi['ruangan'], $data_instruksi['pendaftaran_id'], $data_instruksi['pegawai_id'], $data_instruksi['pasien_id'], $data_instruksi['admisi_id']);
                $data_instruksi['cppt_id'] = $getCppt;
            }

            // Assign data ke attribut instruksi
            $modelInstruksi = new Instruksi;
            $modelInstruksi->attributes = $data_instruksi;

            // Parse cppt id dan tgl instruksi
            $modelInstruksi->cppt_id = $modelInstruksi->cppt_id;
            $modelInstruksi->tgl_instruksi = date('Y-m-d H:i:s', strtotime($modelInstruksi->tgl_instruksi));

            if (isset($modelInstruksi['instruksi_id']) && $modelInstruksi['instruksi_id'] == '') {
                unset($modelInstruksi['instruksi_id']);
            }

            // Validasi model instruksi
            if ($modelInstruksi->validate()) {
                // Cek save
                if ($modelInstruksi->save(false)) {
                    foreach ($data_resepturdetail as $key => $value) {
                        $racikanKode[] = $value['racikan_id'];
                    }

                    $lookup = Lookup::findOne(DocoConstants::VAR_FA_NR);
                    $const = isset($lookup['lookup_value']) ? $lookup['lookup_value'] : '';
                    $racikanType = "NR";
                    $racikanKode = array_unique($racikanKode);
                    if (count($racikanKode) > 1 || $racikanKode[0] == "OR") {
                        $lookup = Lookup::findOne(DocoConstants::VAR_FA_R);
                        $const = isset($lookup['lookup_value']) ? $lookup['lookup_value'] : '';
                        $racikanType = "OR";
                    }
                    $list_racikan = Racikan::find()->all();
                    $list_racikan = ArrayHelper::map($list_racikan, 'racikan_singkatan', 'racikan_id');

                    $modelReseptur = new Reseptur;
                    $modelReseptur->attributes = $data_reseptur;
                    /*sementara approve langsung*/
                    $modelReseptur->status_reseptur = 347;
                    $modelReseptur->tglreseptur = date('Y-m-d H:i:s');
                    $modelReseptur->instruksi_id = $modelInstruksi->instruksi_id;

                    if ($modelReseptur->validate()) {
                        // create antrian
                        // note: no reseptur generated automatically on trigger before insert reseptur_t
                        // get konfig antrian farmasi
                        $data_konfigantrianfarmasi = KonfigAntrianView::find()->where(['lookup_value' => $const, 'ruangan_id' => $modelReseptur->ruangan_id, 'is_default' => true])->one();
                        // $data_konfigantrianfarmasi = KonfigAntrianView::find()->where(['lookup_value'=>$const, 'is_default' => true])->one();
                        $modelAntrian = new Antrian;
                        $modelAntrian->ruangan_id = $modelReseptur->ruangan_id;
                        $modelAntrian->tgl_antrian = date('Y-m-d H:i:s');
                        $modelAntrian->jenisantrian_id = DocoConstants::VAR_JA_F;
                        $modelAntrian->racikan_id = $list_racikan[$racikanType];
                        // $fungsiantrian_id = !empty($data_konfigantrianfarmasi->fungsiantrian_id) ? $data_konfigantrianfarmasi->fungsiantrian_id : null;
                        // untuk mengatur fungsi antrian
                        if ($racikanType == 'OR') {
                            $fungsiantrian_id = 324;
                        } else {
                            $fungsiantrian_id = 325;
                        }

                        $modelAntrian->fungsiantrian_id = $fungsiantrian_id;
                        $modelAntrian->save(false);
                        $antrian_id = $modelAntrian->antrian_id;

                        $modelReseptur->antrian_id = $antrian_id;
                        if ($modelReseptur->save(false)) {
                            // define missing attributes
                            foreach ($data_resepturdetail as $key => $value) {
                                $data_resepturdetail[$key]['reseptur_id'] = $modelReseptur->reseptur_id;
                                $data_resepturdetail[$key]['racikan_id'] = $list_racikan[$value['racikan_id']];

                                $harganetto_reseptur = $value['hargasatuan_reseptur'];
                                $hargajual_reseptur = $value['qty_reseptur'] * $harganetto_reseptur;
                                $data_resepturdetail[$key]['harganetto_reseptur'] = $harganetto_reseptur;
                                $data_resepturdetail[$key]['hargajual_reseptur'] = $hargajual_reseptur;
                                $data_resepturdetail[$key]['status_implementasi'] = '454';
                                $data_resepturdetail[$key]['tgl_resepturdetail'] = date('Y-m-d H:i:s');
                                $data_resepturdetail[$key]['signa'] = isset($value['signa']) ? json_encode(['text' => $value['signa']]) : null;

                                // fill null values
                                if ($value['r'] == 'null') {
                                    $data_resepturdetail[$key]['r'] = null;
                                }

                                if ($value['rke'] == 'null') {
                                    $data_resepturdetail[$key]['rke'] = null;
                                }

                                unset($data_resepturdetail[$key]['resepturdetail_id']);
                            }

                            ResepturDetail::batchInsert($data_resepturdetail);

                            $transaction->commit();
                            $reseptur_data = InfoResepturView::find()->where(['reseptur_id' => $modelReseptur->reseptur_id])->one();
                            FarmasiNotification::newResep($reseptur_data);
                            $return = ['message' => 'Data Berhasil di simpan', 'cppt_id' => $this->helper->encrypt($data_instruksi['cppt_id'])];
                        } else {
                            $transaction->rollBack();
                        }
                    } else {
                        $errors = DocoHelpers::parseError($modelReseptur->errors, 'ResepturForm');
                        $return = [
                            'data' => $errors,
                            'message' => 'Terjadi Kesalahan Pada inputan',
                            'status' => 422
                        ];

                        $transaction->rollBack();
                    }
                } else {
                    $transaction->rollBack();
                    return $modelInstruksi->errors;
                }
            } else {
                $errors = DocoHelpers::parseError($modelInstruksi->errors, 'InstruksiForm');
                $return = [
                    'data' => $errors,
                    'message' => 'Terjadi Kesalahan Pada inputan',
                    'status' => 422
                ];

                $transaction->rollBack();
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            Yii::error([
                'Message' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile(),
                'payload' => $request->post()
            ]);
            $transaction->rollBack();

            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Yii::error([
                'Message' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile(),
                'payload' => $request->post()
            ]);
            $transaction->rollBack();

            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSimpanReseptur()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $data = $request->post();
            $data_instruksi = $data['data_instruksi'];
            $data_reseptur = $data['data_reseptur'];
            $data_resepturdetail = $data['data_resepturdetail'];
            $racikanKode = [];
            
            $vPayload = self::extractValidationPayload($data_reseptur, $data_resepturdetail);
            $obatalkes_tidak_tersedia = self::validateResepturDetail($vPayload['resepturdetail'], $vPayload['ruangan_id'], $vPayload['penjamin_id'], $vPayload['kelaspelayanan_id']);
            if (count($obatalkes_tidak_tersedia) > 0) {
                return (new DocoHelpers)->callback(DocoMessages::KEY_DYNAMIC_STATUS, [
                    'title' => 'Proses tidak Bisa Dilanjutkan',
                    'text' => 'Terdapat obat yang tidak tersedia di depo tujuan',
                    'data' => [
                          'obatalkes_tidak_tersedia' => $obatalkes_tidak_tersedia
                    ]
                ], 422);
            }

            $stok_tersedia = self::validateResepturDetailStok($vPayload['resepturdetail'], $vPayload['ruangan_id']);
            if (!empty($stok_tersedia['data'])) {
                return (new DocoHelpers)->callback(DocoMessages::KEY_DYNAMIC_STATUS, [
                    'title' => 'Proses tidak Bisa Dilanjutkan',
                    'text' => 'Qty tidak boleh melebihi stok tersedia',
                    'data' => [
                        'stok_tidak_tersedia' => $stok_tersedia['data']
                    ]
                ], 422);
            }

            // generate cppt
            if ($data_instruksi['cppt_id'] == '0' || empty($data_instruksi['cppt_id'])) {
                $getCppt = AllowController::actionCreateSoap($data_instruksi['ruangan'], $data_instruksi['pendaftaran_id'], $data_instruksi['pegawai_id'], $data_instruksi['pasien_id'], $data_instruksi['admisi_id'], [
                    'is_dokter' => isset($data['is_dokter']) && $data['is_dokter'] ?: false
                ]);
                $data_instruksi['cppt_id'] = $getCppt;
            }

            // Assign data ke attribut instruksi
            $modelInstruksi = new Instruksi;
            $modelInstruksi->attributes = $data_instruksi;

            // Parse cppt id dan tgl instruksi
            $modelInstruksi->cppt_id = $modelInstruksi->cppt_id;
            $modelInstruksi->tgl_instruksi = date('Y-m-d H:i:s', strtotime($modelInstruksi->tgl_instruksi));

            if (isset($modelInstruksi['instruksi_id']) && $modelInstruksi['instruksi_id'] == '') {
                unset($modelInstruksi['instruksi_id']);
            }

            // Validasi model instruksi
            if ($modelInstruksi->validate()) {
                // Cek save
                if ($modelInstruksi->save(false)) {
                    $instruksi_id = $modelInstruksi->instruksi_id;
                    $pasienadmisi_id = $data_instruksi['admisi_id'];
                    $sentReseptur = (new FarmasiService)->saveResep(compact('instruksi_id', 'pasienadmisi_id', 'data_reseptur', 'data_resepturdetail'));
                    if (isset($sentReseptur['code']) && $sentReseptur['code'] != 200) {
                        $transaction->rollback();
                        \Yii::$app->response->statusCode = $sentReseptur['code'];
                        return [
                            'message' => isset($sentReseptur['data']['message']) ? $sentReseptur['data']['message'] : 'Simpan Reseptur Gagal!'
                        ];
                    }
                    $transaction->commit();
                    
                    $pendaftaranId = ArrayHelper::getValue($data_instruksi, 'pendaftaran_id');

                    return [
                        'message' => 'Simpan Resep Berhasil',
                        'cppt_id' => DocoHelpers::encrypt($modelInstruksi->cppt_id),
                        'data_reseptur' => $sentReseptur['data'],
                    ];
                } else {
                    $transaction->rollBack();
                    return $modelInstruksi->errors;
                }
            } else {
                $errors = DocoHelpers::parseError($modelInstruksi->errors, 'InstruksiForm');
                $return = [
                    'data' => $errors,
                    'message' => $errors,
                    'status' => 422
                ];

                $transaction->rollBack();
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            Yii::error([
                'Message' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile(),
                'payload' => $request->post()
            ]);
            $transaction->rollBack();

            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Yii::error([
                'Message' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile(),
                'payload' => $request->post()
            ]);
            $transaction->rollBack();

            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi update reseptur pemeriksaan rawat inap
     * @return array response
     *
     */
    public function actionUpdateReseptur()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $data = $request->post();
            if (!isset($data['data_resepturdetail'])) {
                $return = [
                    'text' => 'Data Obat Tidak Boleh Kosong!',
                    'title' => 'Proses Gagal!',
                    'status' => 422
                ];

                return $return;
            }
            $data_instruksi = $data['data_instruksi'];
            $data_reseptur = $data['data_reseptur'];
            $data_resepturdetail = $data['data_resepturdetail'];
            $racikanKode = [];

            if (isset($data_instruksi['instruksi_id']) && $data_instruksi['instruksi_id'] != '') {
                $modelInstruksi = Instruksi::findOne($data_instruksi['instruksi_id']);
                $modelInstruksi->load($data_instruksi, '');
            } else {
                $return = [
                    'message' => 'Data gagal diubah.',
                    'status' => 422
                ];

                return $return;
            }

            if ($modelInstruksi->validate()) {
                if ($modelInstruksi->save(false)) {
                    foreach ($data_resepturdetail as $key => $value) {
                        $racikanKode[] = $value['racikan_id'];
                    }

                    $lookup = Lookup::findOne(DocoConstants::VAR_FA_NR);
                    $const = isset($lookup['lookup_value']) ? $lookup['lookup_value'] : '';
                    $racikanType = "NR";
                    $racikanKode = array_unique($racikanKode);
                    if (count($racikanKode) > 1 || $racikanKode[0] == "OR") {
                        $lookup = Lookup::findOne(DocoConstants::VAR_FA_R);
                        $const = isset($lookup['lookup_value']) ? $lookup['lookup_value'] : '';
                        $racikanType = "OR";
                    }
                    $list_racikan = Racikan::find()->all();
                    $list_racikan = ArrayHelper::map($list_racikan, 'racikan_singkatan', 'racikan_id');

                    if (isset($data_reseptur['reseptur_id']) && $data_reseptur['reseptur_id'] != '') {
                        $modelReseptur = Reseptur::findOne($data_reseptur['reseptur_id']);
                        $tempRuanganId = $modelReseptur->ruangan_id;
                        $modelReseptur->load($data_reseptur, '');
                        $modelReseptur->ruangan_id = $tempRuanganId;
                    } else {
                        $return = [
                            'message' => 'Data gagal diubah.',
                            'status' => 422
                        ];

                        $transaction->rollBack();

                        return $return;
                    }

                    if ($modelReseptur->validate()) {
                        $data_konfigantrianfarmasi = KonfigAntrianView::find()->where(['lookup_value' => $const, 'ruangan_id' => $modelReseptur->ruangan_id, 'is_default' => true])->one();
                        $data_konfigantrianfarmasi = KonfigAntrianView::find()->where(['lookup_value' => $const, 'is_default' => true])->one();
                        $modelAntrian = new Antrian;
                        $modelAntrian->ruangan_id = $modelReseptur->ruangan_id;
                        $modelAntrian->tgl_antrian = date('Y-m-d H:i:s');
                        $modelAntrian->jenisantrian_id = DocoConstants::VAR_JA_F;
                        $modelAntrian->racikan_id = $list_racikan[$racikanType];
                        // $fungsiantrian_id = ($data_konfigantrianfarmasi->fungsiantrian_id) ? $data_konfigantrianfarmasi->fungsiantrian_id : null;
                        // untuk mengatur fungsi antrian
                        if ($racikanType == 'OR') {
                            $fungsiantrian_id = 324;
                        } else {
                            $fungsiantrian_id = 325;
                        }

                        $modelAntrian->fungsiantrian_id = $fungsiantrian_id;
                        $modelAntrian->save(false);
                        $antrian_id = $modelAntrian->antrian_id;

                        $modelReseptur->antrian_id = $antrian_id;
                        $resepturDetailId = [];
                        if ($modelReseptur->save(false)) {
                            foreach ($data_resepturdetail as $key => $value) {
                                $data_resepturdetail[$key]['reseptur_id'] = $modelReseptur->reseptur_id;
                                $data_resepturdetail[$key]['racikan_id'] = $list_racikan[$value['racikan_id']];

                                $harganetto_reseptur = $value['hargasatuan_reseptur'];
                                $hargajual_reseptur = $value['qty_reseptur'] * $harganetto_reseptur;
                                $data_resepturdetail[$key]['harganetto_reseptur'] = $harganetto_reseptur;
                                $data_resepturdetail[$key]['hargajual_reseptur'] = $hargajual_reseptur;

                                // fill null values
                                if ($value['r'] == 'null') {
                                    $data_resepturdetail[$key]['r'] = null;
                                }

                                if ($value['rke'] == 'null') {
                                    $data_resepturdetail[$key]['rke'] = null;
                                }

                                if ($value['resepturdetail_id'] != '') {
                                    $resepturDetailId[] = $value['resepturdetail_id'];
                                }

                                if ($value['resepturdetail_id'] != '') {
                                    $modelResepturDetail = ResepturDetail::findOne($value['resepturdetail_id']);
                                    $modelResepturDetail->load($data_resepturdetail[$key], '');
                                    $modelResepturDetail->save();
                                } else {
                                    unset($value['resepturdetail_id']);
                                    $modelResepturDetail = new ResepturDetail;
                                    $modelResepturDetail->load($data_resepturdetail[$key], '');
                                    $modelResepturDetail->status_implementasi = '454';
                                    if ($modelResepturDetail->save()) {
                                        $resepturDetailId[] = $modelResepturDetail->resepturdetail_id;
                                    }
                                }
                            }

                            $modelResepturDetail = ResepturDetail::find()->where(
                                [
                                    'reseptur_id' => $modelReseptur->reseptur_id,
                                    'is_deleted' => false,
                                ]
                            )->asArray()->all();
                            // return $modelResepturDetail;
                            if (!empty($modelResepturDetail)) {
                                foreach ($modelResepturDetail as $value) {
                                    if (!in_array($value['resepturdetail_id'], $resepturDetailId)) {
                                        (new ResepturDetail)->delete([
                                            'resepturdetail_id' => $value['resepturdetail_id']
                                        ]);
                                    }
                                }
                            }

                            $transaction->commit();
                            $return = ['message' => 'Data Berhasil di ubah', 'data' => $modelInstruksi];
                        } else {
                            $transaction->rollBack();
                        }
                    } else {
                        $errors = DocoHelpers::parseError($modelReseptur->errors, 'ResepturForm');
                        $return = [
                            'data' => $errors,
                            'message' => $errors,
                            'status' => 422
                        ];

                        $transaction->rollBack();
                    }
                } else {
                    return $modelInstruksi->errors;
                    $transaction->rollBack();
                }
            } else {
                $errors = DocoHelpers::parseError($modelInstruksi->errors, 'InstruksiForm');
                $return = [
                    'data' => $errors,
                    'message' => $errors,
                    'status' => 422
                ];

                $transaction->rollBack();
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();

            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();

            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /*
     * @see Fungsi get data cppt dan asesmenmedis
     * @return array, activeQueryRecords
     *
     */
    public function actionGetCpptAsesmenMedis()
    {
        // Try
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $cppt_id = $request->get('cppt_id');
            $isPindahKamar = false;
            $pindahKamar = [];

            $pasienRanap = InfoPasienRanap::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            $cppt = CpptView::find()
                ->where(['pendaftaran_id' => $pendaftaran_id])
                ->andWhere(['cppt_id' => $cppt_id])
                ->andWhere(['not', ['pasienadmisi_id' => null]])
                ->orderBy(['cppt_id' => SORT_DESC])
                ->limit(1)->all();
            $reseptur = InfoResepturView::find()->where(['pendaftaran_id' => $pendaftaran_id])->orderBy(['reseptur_id' => SORT_DESC])->limit(1)->all();

            // get data pindah kamar
            $pindahKamar = PindahKamar::getKelasTitipan($pendaftaran_id);

            if (!empty($pindahKamar)) {
                $isPindahKamar = true;
            }

            // Return
            return [
                'asesmenMedis' => $pasienRanap,
                'cppt' => $cppt,
                'reseptur' => $reseptur,
                'isPindahKamar' => $isPindahKamar,
                'dataPindahKamar' => $pindahKamar
            ];
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

    /*
     * @see Fungsi get list diagnosa
     * @return array, activeQueryRecords
     *
     */
    public function actionGetListDiagnosa()
    {
        // Try
        try {
            // Return
            return $this->getListDiagnosa();
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

    /**
     * @controller actionCetakReseptur
     * @attribute #table_detail# => table
     **/
    public function actionCetakReseptur()
    {
        // Request
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id');
        $pendaftaran_id = DocoHelpers::decrypt(Yii::$app->request->get('pendaftaran_id'));
        $pegawai_id = Yii::$app->request->get('pegawai_id');
        $ruangan_id = Yii::$app->request->get('ruangan_id');
        $instruksi_id = Yii::$app->request->get('instruksi_id');

        // Reseptur
        $reseptur = InfoResepturView::find()->where([
            'pendaftaran_id' => $pendaftaran_id,
            'pasienadmisi_id' => $pasienadmisi_id,
            // 'instruksi_id' => $instruksi_id,
        ])
            ->orderBy(['reseptur_id' => SORT_DESC])
            ->limit(1)
            ->one();

        // Directory Creation
        $header1 = array(
            Yii::t('app', "Nama pasien") => isset($reseptur['nama_pasien']) ? $reseptur['nama_pasien'] : '',
            Yii::t('app', "No rekam medik") => isset($reseptur['no_rekam_medik']) ? $reseptur['no_rekam_medik'] : '',
            Yii::t('app', "Tanggal lahir") => isset($reseptur['tanggal_lahir']) ? ($reseptur['tanggal_lahir'] ? date('d-m-Y', strtotime($reseptur['tanggal_lahir'])) : '') : '',
            Yii::t('app', "Jenis kelamin") => isset($reseptur['jenis_kelamin']) ? $reseptur['jenis_kelamin'] : '',
            Yii::t('app', "Umur") => isset($reseptur['umur']) ? $reseptur['umur'] : '',
            Yii::t('app', "Ruangan / kelas") => isset($reseptur['ruangan_reseptur']) ? $reseptur['ruangan_reseptur'] . ' / ' . $reseptur['kelaspelayanan_nama'] : '',
            Yii::t('app', "Dokter DPJP") => isset($reseptur['nama_pegawai']) ? $reseptur['nama_pegawai'] : '',
            Yii::t('app', "Penjamin") => isset($reseptur['penjamin_nama']) ? $reseptur['penjamin_nama'] : '',
        );
        $header2 = array(
            Yii::t('app', "Berat Badan") => isset($reseptur['berat_badan']) ? $reseptur['berat_badan'] : '',
            Yii::t('app', "Tinggi Badan") => isset($reseptur['tinggi_badan']) ? $reseptur['tinggi_badan'] : '',
            Yii::t('app', "Luas Permukaan Tubuh") => isset($reseptur['luas_tubuh']) ? $reseptur['luas_tubuh'] : '',
            Yii::t('app', "Status Kehamilan") => (isset($reseptur['is_hamil']) && $reseptur['is_hamil'] == true) ? Yii::t('app', 'Ya') : Yii::t('app', 'Tidak'),
            Yii::t('app', "Diagnosa") => isset($reseptur['diagnosa_text']) ? $reseptur['diagnosa_text'] : '',
        );

        // Reseptur detail
        $resepturDetail = InfoResepturDetailView::find()->where(['reseptur_id' => $reseptur->reseptur_id])->all();

        // Pegawai
        $pegawai = PegawaiView::find()->where(['pegawai_id' => $pegawai_id, 'ruangan_id' => $ruangan_id])->one();

        // Print
        $print = new DocoPrint();

        // Attributes
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('pdf', [
                'header1' => $header1,
                'header2' => $header2,
                'reseptur' => $reseptur,
                'resepturDetail' => $resepturDetail,
                'pegawai' => $pegawai,
            ]),
        ];

        $print->Output();
    }

    // Hapus terapi
    public function actionHapusTerapi()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        // Try catch
        try {
            // Request
            $request = Yii::$app->request;
            $cppt_id = DocoHelpers::decrypt($request->get('cppt_id'));
            $instruksi_id = DocoHelpers::decrypt($request->get('instruksi_id'));
            $jenis_instruksi = $request->get('tipeinstruksi');
            // Query
            $instruksi = Instruksi::find(true)->where(['instruksi_id' => $instruksi_id])->one();

            // Cek instruksi
            if (!empty($instruksi)) {

                if ($jenis_instruksi == "RESEPTUR") {
                    $reseptur = Reseptur::find()->where(['instruksi_id' => $instruksi_id])->one();
                    // Cek reseptur
                    if (!empty($reseptur)) {
                        if ($reseptur->status_reseptur != 346) {
                            return [
                                'status' => 422,
                                'text' => Yii::t('app', 'Data tidak dapat dihapus, Sudah ada data yang diapprove atau dibatalkan!'),
                                'message' => Yii::t('app', 'Proses Tidak dapat dilanjutkan!'),
                            ];
                        }
                        // Hapus reseptur detail
                        (new ResepturDetail)->delete([
                            'reseptur_id' => $reseptur->reseptur_id,
                            'is_deleted' => false
                        ]);

                        // Hapus reseptur
                        (new Reseptur)->delete($reseptur->reseptur_id);
                    }
                } else if ($jenis_instruksi == "TINDAKANBMHP") {
                    $instruksiTindakan = InstruksiTindakan::find(true)->where(['instruksi_id' => $instruksi_id])->all();
                    if (!empty($instruksiTindakan)) {
                        $cekStatus = [];
                        foreach ($instruksiTindakan as $ins_tindakan) {
                            $cekStatus[] = $ins_tindakan->status_implementasi;
                        }
                        if (in_array('455', $cekStatus) || in_array('456', $cekStatus)) {
                            return [
                                'status' => 422,
                                'text' => Yii::t('app', 'Data tidak dapat dihapus, Sudah ada data implementasi!'),
                                'message' => Yii::t('app', 'Proses Tidak dapat dilanjutkan!'),
                            ];
                        }
                        (new InstruksiTindakan)->delete([
                            'instruksi_id' => $instruksi_id
                        ]);
                    }
                    (new InstruksiTindakanBmhp)->delete(['instruksi_id' => $instruksi_id]);
                } else {
                    \Yii::$app->response->statusCode = 422;
                    return ['message' => 'Tipe Instruksi Tidak Dikenal'];
                }


                // Hapus instruksi
                (new Instruksi)->delete($instruksi->instruksi_id);
            } else {
                // Return
                return [
                    'message' => Yii::t('app', 'Tidak ada data yang dihapus')
                ];
            }

            $transaction->commit();
            return [
                'message' => Yii::t('app', 'Data berhasil dihapus')
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get list ruangan
    private function getListRuangan($pasien_id)
    {
        // Try catch
        try {
            // Query
            $query = (new \yii\db\Query())
                ->select([
                    PindahKamar::tableName() . '.pendaftaran_id',
                    PindahKamar::tableName() . '.pasienadmisi_id',
                    PindahKamar::tableName() . '.pasien_id',
                    PindahKamar::tableName() . '.ruangan_id',
                    PindahKamar::tableName() . '.kamarruangan_id',
                    PindahKamar::tableName() . '.kamartempattidur_id',
                    Ruangan::tableName() . '.ruangan_nama',
                    KamarRuangan::tableName() . '.kamarruangan_nokamar',
                    KamarTempatTidur::tableName() . '.no_tempattidur'
                ])
                ->from(PindahKamar::tableName())
                ->join('LEFT JOIN', Ruangan::tableName(), Ruangan::tableName() . '.ruangan_id = ' . PindahKamar::tableName() . '.ruangan_id')
                ->join('LEFT JOIN', KamarRuangan::tableName(), KamarRuangan::tableName() . '.kamarruangan_id = ' . PindahKamar::tableName() . '.kamarruangan_id')
                ->join('LEFT JOIN', KamarTempatTidur::tableName(), KamarTempatTidur::tableName() . '.kamartempattidur_id = ' . PindahKamar::tableName() . '.kamartempattidur_id')
                ->where(['pindahkamar_t.pasien_id' => $pasien_id])
                ->all();

            // Return
            return $query;
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get list diagnosa
    private function getListDiagnosa()
    {
        // Try catch
        try {
            // Query
            $query = (new \yii\db\Query())
                ->select([
                    DiagnosaView::tableName() . '.diagnosa_id',
                    DiagnosaView::tableName() . '.diagnosa_nama'
                ])
                ->from(DiagnosaView::tableName());

            // Return
            return $query->all();
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get ruangan instalasi
    private function getRuanganInstalasi($instalasi_singkatan = null)
    {
        // Try catch
        try {
            // Sql
            $sql = "
                SELECT DISTINCT
                    *
                FROM
                    ruangan_m
                LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE ruangan_m.is_deleted = FALSE
            ";

            if ($instalasi_singkatan) {
                $sql .= " AND instalasi_m.instalasi_singkatan = '" . $instalasi_singkatan . "'";
            }

            // Result
            $result = Ruangan::findBySql($sql);

            // Return result
            return $result;
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get obatalkes
    private function getObatAlkes($jenisobatalkes_id)
    {
        // Find
        $result = ObatAlkes::find();

        // Cek jenis
        if ($jenisobatalkes_id) {
            // Tambah kondisi
            $result->where(['jenisobatalkes_id' => $jenisobatalkes_id]);
        }

        // Return
        return $result;
    }

    // Get obatalkes
    private function getObatAlkesByInstalasi($list_ruangan = [], $medIds = [])
    {
        // Try catch
        try {
            // Find
            $model = InfoStokObatAlkesView::find();

            // Cek ruangan
            if (!empty($medIds) && is_array($medIds)) {
                $model->where(['in', 'obatalkes_id', $medIds]);
            }
            if (!empty($list_ruangan)) {
                // Loop
                $wards = [];
                foreach ($list_ruangan as $value) {
                    // Kondisi
                    $wards[] = $value['ruangan_id'];
                }
                $model->andWhere(['in', 'ruangan_id', $wards]);
            }

            // Return
            return $model->all();
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get pasien
    private function getInformasiRawatInap($pendaftaran_id)
    {
        // Try catch
        try {
            // Model
            $model = InfoPasienRanap::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();

            // Return
            return $model;
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get Pemberi Instruksi
    private function getPemberiInstruksi($instalasi_id, $ruangan_id)
    {
        // Try catch
        try {
            // Model
            $model = DokterView::find()->where([
                'instalasi_id' => $instalasi_id,
                'ruangan_id' => $ruangan_id
            ])->all();

            // Return
            return $model;
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get list terapi
    private function getListTerapi()
    {
        // Try catch
        try {
            // Query
            $query = (new \yii\db\Query())
                ->select([
                    Lookup::tableName() . '.lookup_id',
                    Lookup::tableName() . '.lookup_name'
                ])
                ->from(Lookup::tableName())
                ->where([Lookup::tableName() . '.lookup_type' => 'jenis_instruksi']);

            // Return
            return $query->all();
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }


    // Get pegawai
    private function getPegawai($pegawai_id, $ruangan_id)
    {
        // Try catch
        try {
            // Model
            $model = PegawaiView::find()->where(['pegawai_id' => $pegawai_id])->andWhere(['ruangan_id' => $ruangan_id])->one();

            // Return
            return $model;
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCreateVerbalOrder()
    {
        try {
            $request = Yii::$app->request;
            $model = new Cppt;
            $post = $request->post();
            $model->scenario = 'verbalorder';
            $model->attributes = $post;
            if ($model->validate()) {
                if ($post) {
                    if ($model->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors, 'VerbalOrderRanapForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }
            } else {
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422, $response);
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

    // Create soap
    public function actionCreateSoap()
    {
        // Try
        $this->transactionClass = Yii::$app->db->beginTransaction();
        try {
            $auto = Yii::$app->request->post('auto', null);
            if (!empty($auto)) {
                return $this->soapCreateOrUpdate(null, true);
            } else {
                return $this->soapCreateOrUpdate();
            }
        } catch (\yii\db\Exception $e) {
            $this->transactionClass->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->transactionClass->rollBack();
            $this->logError($e);
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }

    // rizal
    // Get list instalasi penunjang
    private function getListInstalasiPenunjang()
    {
        try {
            $model = Instalasi::find()
                ->andWhere(['is_penunjang' => true])
                ->orderBy(['instalasi_id' => SORT_ASC])
                ->asArray()->all();
            return $model;
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

    // author : rizal
    // save terapi penunjang
    public function actionCreateTerapiPenunjang()
    {
        try {
            $lookUpTransaksi = new LookUpTransaksiRepositories;
            $instalasiFisioId = $lookUpTransaksi->getInstalasiIdFisio();
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $request = Yii::$app->request;

            $posts = $request->post();
            $model = new Instruksi;
            $posts['tgl_kirimpasien'] = date('Y-m-d H:i:s', strtotime($posts['tgl_kirimpasien']));
            // generate cppt
            if ($posts['cppt_id'] == '0' || empty($posts['cppt_id'])) {
                $getCppt = AllowController::actionCreateSoap($posts['ruangan'], $posts['pendaftaran_id'], $posts['pegawai_id'], $posts['pasien_id'], $posts['pasienadmisi_id']);
                $posts['cppt_id'] = $getCppt;
            }

            $model->cppt_id = $posts['cppt_id'];
            $model->tgl_instruksi = $posts['tgl_kirimpasien'];
            $model->jenis_instruksi = DocoConstants::J_INST_PNJG;
            $model->catatan_instruksi = $posts['catatan_dokterpengirim'];
            $model->is_puasa = isset($posts['is_puasa']) ? $posts['is_puasa'] : 0;
            $model->save();

            $instruksi_id = $model->instruksi_id;
            $posts['instruksi_id'] = $instruksi_id;
            $posts['list_order'] = isset($posts['list_order']) ? json_encode($posts['list_order']) : null;
            $typeTransaction = '';

            if ($posts['instalasi_id'] == DocoConstants::VAR_I_LAB) {
                $typeTransaction = 'lab';
                //hit api backend laboratorium
                $restLab = Yii::$app->docoRest->laboratorium;
                $request = $restLab->post('order/create?id=' . $posts['pendaftaran_id'], [
                    'form_params' => $posts
                ]);
                $response = json_decode($request->getBody(), true);
            } elseif ($posts['instalasi_id'] == DocoConstants::VAR_I_RAD) {
                $typeTransaction = 'radiologi';
                //hit api backend radiologi
                $restRad = Yii::$app->docoRest->radiologi;
                $request = $restRad->post('order/create?id=' . $posts['pendaftaran_id'], [
                    'form_params' => $posts
                ]);
                $response = json_decode($request->getBody(), true);
            } elseif ($posts['instalasi_id'] == DocoConstants::INST_ID_BEDAH) {
                $typeTransaction = 'bedah';
                // set jadwal operasi
                $posts['jam_mulai'] = $posts['jadwal_operasi']['jam_mulai'];
                $posts['jam_selesai'] = $posts['jadwal_operasi']['jam_selesai'];
                $posts['dr_operator_id'] = $posts['jadwal_operasi']['dr_operator_id'];
                $posts['dr_anastesi_id'] = isset($posts['jadwal_operasi']['dr_anestesi_id']) ? $posts['jadwal_operasi']['dr_anestesi_id'] : '';

                //hit api backend bedah
                $restRad = Yii::$app->docoRest->bedah;
                $request = $restRad->post('order/create?id=' . $posts['pendaftaran_id'], [
                    'form_params' => $posts
                ]);
                $response = json_decode($request->getBody(), true);
            } elseif ($posts['instalasi_id'] == $instalasiFisioId) {
                //hit api backend fisioterapi
                $response = OrderRanapAction::saveOrder($posts);
            }
            if ($model->is_puasa) {
                GiziNotification::fastingReminder([
                    'tgl_instruksi' => $model->tgl_instruksi,
                    'pendaftaran_id' => $posts['pendaftaran_id'],
                    'typeTransaction' => $typeTransaction,
                    'instruksi_id' => $instruksi_id
                ]);
            }

            if ($response['metadata']['status'] == 200) {
                $transaction->commit();
                $url = "/ranap/pemeriksaan-rawat-inap/cetak-penunjang?id=#pendaftaran_id#&pasienadmisi_id=#pasienadmisi_id#&instruksi_id=#instruksi_id#";
                $keys = ['#pendaftaran_id#', '#pasienadmisi_id#', '#instruksi_id#'];
                $replacements = [DocoHelpers::encrypt($posts['pendaftaran_id']), $posts['pasienadmisi_id'], $instruksi_id];
                return [
                    'messages' => 'Data berhasil di simpan',
                    'instruksi_id' => $instruksi_id,
                    'url' => str_replace($keys, $replacements, $url),
                    'cppt_id' => $this->helper->encrypt($model->cppt_id),
                    'pendaftaran_id' => DocoHelpers::encrypt($posts['pendaftaran_id']),
                    'pasienadmisi_id' => $posts['pasienadmisi_id'],
                    'pasienkirimkeunitlain_id' => ArrayHelper::getValue($response, 'response.pasienkirimkeunitlain_id')
                ];
            } else {
                $transaction->rollBack();
                return [
                    'status' => 500,
                    'title' => 'Proses Gagal !',
                    'message' => $response['response']['text']
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetListDataPenunjang()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $instruksi_id = $request->get('instruksi_id', 0);
            $cppt_id = $request->get('cppt_id', 0);

            $instruksi = Instruksi::find()
                ->joinWith('pasienKirimKeUnitLain')
                ->andWhere(['instruksi_t.instruksi_id' => $instruksi_id])
                ->asArray()->one();

            $instalasi_id = $instruksi['pasienKirimKeUnitLain']['instalasi_id'];
            if ($instalasi_id == DocoConstants::INST_ID_LAB) {
                $rest = Yii::$app->docoRest->laboratorium;
            } elseif ($instalasi_id == DocoConstants::INST_ID_RAD) {
                $rest = Yii::$app->docoRest->radiologi;
            } elseif ($instalasi_id == DocoConstants::INST_ID_REHAB) {
            } elseif ($instalasi_id == DocoConstants::INST_ID_BEDAH) {
            }


            $request = $rest->get('order/get-list-order', [
                'query' => ['id' => $instruksi_id]
            ]);
            $response = json_decode($request->getBody(), true);
            return [
                'riwayat_instruksi' => $response['response'],
                'data_instruksi' => $instruksi
            ];
            // if ($instruksi->jenis_instruksi)
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

    /**
     * @controller actionCetakPenunjang
     * @attribute #poliklinik# => poliklinik
     * @attribute #no_pendaftaran# => no_pendaftaran
     * @attribute #no_rekam_medik# => no_rekam_medik
     * @attribute #nama_pasien# => nama_pasien
     * @attribute #dokter_perujuk# => dokter_perujuk
     * @attribute #unit_penunjang# => unit_penunjang
     * @attribute #jenis_kelamin# => jenis_kelamin
     * @attribute #tgl_lahir# => tgl_lahir
     * @attribute #cara_bayar# => cara_bayar
     * @attribute #penjamin# => penjamin
     * @attribute #tgl_permintaan# => tgl_permintaan
     * @attribute #no_rujukan# => no_rujukan
     * @attribute #table_list_order# => table
     **/
    public function actionCetakPenunjang()
    {
        // try {
            $request = Yii::$app->request;

            $pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
            $pasienadmisi_id = $request->get('pasienadmisi_id', null);
            $instruksi_id = $request->get('instruksi_id', null);
            $pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
            $model = OrderPenunjangView::find()
                ->andWhere(['pendaftaran_id' => $pendaftaran_id]);
            if ($pasienadmisi_id) {
                $model = $model->andWhere(['pasienadmisi_id' => $pasienadmisi_id]);
            }
            if ($instruksi_id) {
                $model = $model->andWhere(['instruksi_id' => $instruksi_id]);
            }
            if ($pasienkirimkeunitlain_id) {
                $model = $model->andWhere(['instruksi_id' => $instruksi_id]);
            }
            $model = $model->asArray()->all();
            Yii::error($model);
            $header = ArrayHelper::getValue($model, '0', []);
            $detail = $this->renderPartial('cetak_penunjang', ['model' => $model]);
            // return $detail;
            $print = new DocoPrint();

            $print->attributes = [
                '#poliklinik#' => ArrayHelper::getValue($header, 'ruangan_nama'),
                '#no_pendaftaran#' => ArrayHelper::getValue($header, 'no_pendaftaran'),
                '#no_rekam_medik#' => ArrayHelper::getValue($header, 'no_rekam_medik'),
                '#nama_pasien#' => ArrayHelper::getValue($header, 'nama_pasien'),
                '#dokter_perujuk#' => ArrayHelper::getValue($header, 'dokter_perujuk'),
                '#unit_penunjang#' => ArrayHelper::getValue($header, 'instalasi_penunjang') . ' - ' . ArrayHelper::getValue($header, 'ruangan_penunjang'),
                '#jenis_kelamin#' => ArrayHelper::getValue($header, 'jenis_kelamin'),
                '#tgl_lahir#' => date('d-m-Y', strtotime(ArrayHelper::getValue($header, 'tanggal_lahir'))),
                '#cara_bayar#' => ArrayHelper::getValue($header, 'carabayar_nama'),
                '#penjamin#' => ArrayHelper::getValue($header, 'penjamin_nama'),
                '#tgl_permintaan#' => date('d-m-Y H:i:s', strtotime(ArrayHelper::getValue($header, 'tgl_kirimpasien'))),
                '#no_rujukan#' => ArrayHelper::getValue($header, 'no_orderkeunitlain'),

                '#table_list_order#' => $detail,
            ];
            $print->Output();
        // } catch (\yii\db\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // } catch (\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
    }

    	/**
     * @controller actionCetakPenunjangFisio
     * @attribute #poliklinik# => poliklinik
     * @attribute #no_pendaftaran# => no_pendaftaran
     * @attribute #no_rekam_medik# => no_rekam_medik
     * @attribute #nama_pasien# => nama_pasien
     * @attribute #dokter_perujuk# => dokter_perujuk
     * @attribute #unit_penunjang# => unit_penunjang
     * @attribute #jenis_kelamin# => jenis_kelamin
     * @attribute #tgl_lahir# => tgl_lahir
     * @attribute #cara_bayar# => cara_bayar
     * @attribute #penjamin# => penjamin
     * @attribute #tgl_permintaan# => tgl_permintaan
     * @attribute #no_rujukan# => no_rujukan
     * @attribute #table_list_order# => table
     **/

	public function actionCetakPenunjangFisio()
	{
		$request = Yii::$app->request;
		$pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
		$pasienadmisi_id = $request->get('pasienadmisi_id', null);
		$pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id', null);
		$model = OrderPenunjangView::find()
			->andWhere(['pendaftaran_id' => $pendaftaran_id]);
		if (!empty($pasienadmisi_id)) {
			$model = $model->andWhere(['pasienadmisi_id' => $pasienadmisi_id]);
		}
		if (!empty($pasienkirimkeunitlain_id)) {
			$model = $model->andWhere(['pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id]);
		}
		$model = $model->asArray()->all();
		$header = ArrayHelper::getValue($model, '0', []);
		$detail = $this->renderPartial('cetak_penunjang_fisio', ['model' => $model]);
		// return $detail;
		$print = new DocoPrint();

		$print->attributes = [
			'#poliklinik#' => ArrayHelper::getValue($header, 'ruangan_nama'),
			'#no_pendaftaran#' => ArrayHelper::getValue($header, 'no_pendaftaran'),
			'#no_rekam_medik#' => ArrayHelper::getValue($header, 'no_rekam_medik'),
			'#nama_pasien#' => ArrayHelper::getValue($header, 'nama_pasien'),
			'#dokter_perujuk#' => ArrayHelper::getValue($header, 'dokter_perujuk'),
			'#unit_penunjang#' => ArrayHelper::getValue($header, 'instalasi_penunjang') . ' - ' . ArrayHelper::getValue($header, 'ruangan_penunjang'),
			'#jenis_kelamin#' => ArrayHelper::getValue($header, 'jenis_kelamin'),
			'#tgl_lahir#' => date('d-m-Y', strtotime(ArrayHelper::getValue($header, 'tanggal_lahir'))),
			'#cara_bayar#' => ArrayHelper::getValue($header, 'carabayar_nama'),
			'#penjamin#' => ArrayHelper::getValue($header, 'penjamin_nama'),
			'#tgl_permintaan#' => date('d-m-Y H:i:s', strtotime(ArrayHelper::getValue($header, 'tgl_kirimpasien'))),
			'#no_rujukan#' => ArrayHelper::getValue($header, 'no_orderkeunitlain'),

			'#table_list_order#' => $detail,
		];
		$print->Output();
	}

    // instruksi tindakan & BMHP
    public function actionGetListDataInstruksi()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $instruksi_id = $request->get('instruksi_id', 0);
            $cppt_id = $request->get('cppt_id', 0);

            if ($id != null) {
                $data = RiwayatInstruksiTindakanView::find()
                    ->where([
                        'pendaftaran_id' => $id,
                        'cppt_id' => $cppt_id,
                        'instruksi_id' => $instruksi_id
                    ])
                    ->asArray()
                    ->all();
                $data_riwayat = [];
                if (count($data) > 0) {
                    foreach ($data as $rowRiwayat) {
                        if ($rowRiwayat['tipe'] == 'PAKET') {
                            $paketDetail = PaketDetailView::find()->where(['tipepaket_id' => $rowRiwayat['tindakan_paket_obat_id']])->asArray()->all();
                            if (count($paketDetail) > 0) {
                                $rowRiwayat['paketDetail'] = ArrayHelper::map($paketDetail, 'daftartindakan_id', 'daftartindakan_nama');
                            }
                        }
                        $data_riwayat[] = $rowRiwayat;
                    }
                }
                $data_instruksi = Instruksi::find()->where(['instruksi_id' => $instruksi_id])->one();
                return [
                    'riwayat_instruksi' => $data_riwayat,
                    'data_instruksi' => $data_instruksi
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

    /*
     * Fungsi untuk get terapi reseptur
     */
    public function actionGetTerapiReseptur()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $instruksi_id = $request->get('instruksi_id', 0);
            $cppt_id = $request->get('cppt_id', 0);

            if ($id != null && $instruksi_id != null) {
                $data_instruksi = Instruksi::find()->where(['instruksi_id' => $instruksi_id])->one();
                $data_reseptur = InfoResepturView::find()->where(['instruksi_id' => $instruksi_id])->one();
                $data_resepturdetail = InfoResepturDetailView::find()->where(['reseptur_id' => $data_reseptur['reseptur_id']])->all();
                $data_obatalkes = $model = InfoStokObatAlkesFn::find()->select([
                    'obatalkes_nama',
                    'obatalkes_namalain',
                    'obatalkes_id',
                    'ruangan_id',
                    'obatalkes_kode',
                    'qty_tersedia',
                    'satuankecil_id',
                    'satuankecil_nama',
                    'hargaygdipakai',
                    'harganetto_ygdipakai',
                    'jml_margin as jmlmargin',
                    'jml_discount as jmldiscount',
                    'jml_ppn as jmlppn',
                    'persen_ppn as persenppn',
                    'persen_disc as persendiscount',
                    'persen_margin as persenmargin'
                ])->where([
                    'ruangan_id' => $data_reseptur['ruangan_id']
                ])
                    ->orderBy(['obatalkes_namalain' => SORT_ASC])
                    ->asArray()->all();

                return [
                    'data_instruksi' => $data_instruksi,
                    'data_reseptur' => $data_reseptur,
                    'data_resepturdetail' => $data_resepturdetail,
                    'data_obatalkes' => $data_obatalkes,
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

    public function actionVerifikasi()
    {
        try {
            $request = Yii::$app->request;
            $cppt_id = $request->post('id');
            $ispagt = $request->post('ispagt');
            $ispagt = ($ispagt == 1) ? true : false;
            $model = ($ispagt) ? Pagt::findOne($cppt_id) : Cppt::findOne($cppt_id);
            if ($request->post('jenis') == 'dpjp') {
                $model->is_verifikasi = true;
                $model->pegawai_verifikasi_id = Yii::$app->jwt->user->pegawai_id;
                $model->tgl_verifikasi = date('Y-m-d H:i:s');
            } elseif ($request->post('jenis') == 'verbal') {
                $model->is_verifikasi_verbal = true;
                $model->pegawai_verbal_id = Yii::$app->jwt->user->pegawai_id;
                $model->tgl_verif_verbal = date('Y-m-d H:i:s');
            }
            if ($model->save(false)) {
                return ['message' => 'Sukses'];
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Terjadi kesalahan sistem'
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

    /**
     * @controller actionCetakPdfListCppt
     * @attribute #no_pendaftaran# => no_pendaftaran
     * @attribute #nama_pasien# => nama_pasien
     * @attribute #tgl_cetak# => tgl_cetak
     * @attribute #nama_user# => nama_user
     * @attribute #table_list_cppt# => table
     * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
     * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
     * @attribute #inf_nopendaftaran# => Informasi Pasien: No Pendaftaran
     * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
     * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
     * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
     * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
     * @attribute #inf_umur# => Informasi Pasien: Umur
     * @attribute #inf_dokterdpjp# => Informasi Pasien: Dokter DPJP
     * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
     * @attribute #inf_nokamar# => Informasi Pasien: No Kamar
     * @attribute #inf_nobed# => Informasi Pasien: No Bed
     * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
     * @attribute #inf_carabayar# => Informasi Pasien: Cara Bayar
     **/
    // public function actionCetakPdfListCppt()
    // {
    //     $connection = Yii::$app->db;
    //     $request = Yii::$app->request;
    //     $pendaftaran_id = $request->get('pendaftaran_id', 0);
    //     $ruangan_id = $request->get('ruangan_id', 0);
    //     $pasienadmisi_id = $request->get('pasienadmisi_id', 0);
    //     $pegawai_id = $request->get('pegawai_id', 0);
    //     $kelompokpegawai_id = $request->get('kelompokpegawai_id', 0);
    //     $nama_usercetak = $request->get('nama_usercetak', '');
    //     $id_usercetak = $request->get('id_usercetak', 0);

    //     $nama_user = '';
    //     $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id' => $id_usercetak])->asArray()->one();

    //     if (is_null($mNamaPegawai)) {
    //         $nama_user = $nama_usercetak;
    //     } else {
    //         $nama_user = @$mNamaPegawai['nama_pegawai'];
    //     }

    //     $modelHeader = new InfoPasienRanap;
    //     $queryHeader = $modelHeader::find()
    //         ->andWhere([
    //             'pendaftaran_id' => $request->get('pendaftaran_id'),
    //             'pasienadmisi_id' => $request->get('pasienadmisi_id'),
    //         ]);
    //     $resultHeader = $queryHeader->asArray()->one();

    //     $header1 = array(
    //         Yii::t('app', "No rekam medik") => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
    //         Yii::t('app', "Tanggal pendaftaran") => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d-m-Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
    //         Yii::t('app', "No Pendaftaran") => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
    //         Yii::t('app', "Nama pasien") => $resultHeader ? $resultHeader['nama_pasien'] : '',
    //         Yii::t('app', "Jenis kelamin") => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
    //         Yii::t('app', "Kasus Penyakit") => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : ''
    //     );

    //     $header2 = array(
    //         Yii::t('app', "Tanggal lahir") => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
    //         Yii::t('app', "Umur") => $resultHeader ? $resultHeader['umur'] : '',
    //         Yii::t('app', "Dokter DPJP") => $resultHeader ? $resultHeader['dokter_admisi'] : '',
    //         Yii::t('app', "Kelas Pelayanan") => $resultHeader ? $resultHeader['kelas_pelayanan'] : '',
    //         Yii::t('app', "No. Kamar / No. Bed") => $resultHeader ? $resultHeader['kamarruangan_nokamar'] . ' / ' . $resultHeader['no_tempattidur'] : ''
    //     );
    //     $data = [];
    //     $newData = [];

    //     $pend_asal = Pendaftaran::find()
    //         ->select([
    //             new \yii\db\Expression("(additional_data::json->'pendaftaranasal_id') AS pend_asal")
    //         ])
    //         ->andWhere(['pendaftaran_id' => $pendaftaran_id])
    //         ->asArray()
    //         ->one();

    //     // Model
    //     $model = new SoapRsView;

    //     // Query
    //     $query = $model::find()
    //         ->andWhere(['pendaftaran_id' => $pendaftaran_id])
    //         ->andWhere(['not', ['pasienadmisi_id' => null]])
    //         ->orderBy(['tgl_soaprj' => SORT_DESC]);

    //     if ($pend_asal['pend_asal'] != 'null') {
    //         $query->orWhere(['pendaftaran_id' => $pend_asal['pend_asal']]);
    //     }
    //     $data = $query->all();

    //     // Cek data
    //     if (!empty($data)) {
    //         // Looping untuk assign detail
    //         foreach ($data as $key => $value) {
    //             // Set detail
    //             $newData[$key]['cppt'] = $value;
    //             $newData[$key]['data_instruksi'] = (new \yii\db\Query())
    //                 ->from('infoinstruksi_v')
    //                 ->where([
    //                     'cppt_id' => $value['cppt_id'],
    //                 ])
    //                 ->orderBy(['tgl_instruksi' => SORT_DESC])
    //                 ->all();
    //         }
    //     }
    //     $cppt_data = [];
    //     $rownum = 1;
    //     // $htmlpe = '';
    //     foreach ($newData as $key => $value) {
    //         // Assign data
    //         $cppt_data[$key]['no'] = $rownum;
    //         // $cppt_data[$key]['ruangan'] = $value['cppt']['ruangan_nama'].' '.$value['cppt']['no_tempattidur'].' '.$value['cppt']['kamarruangan_nokamar'];
    //         $cppt_data[$key]['tgl_cppt'] = date('d/m/Y / H:i:s', strtotime($value['cppt']['tgl_soaprj']));
    //         $cppt_data[$key]['ruanganprofesi'] = $value['cppt']['ruangan_nama'] . ' ' . $value['cppt']['no_tempattidur'] . ' ' . $value['cppt']['kamarruangan_nokamar'] . '<br><hr><br>' . $value['cppt']['kelompokpegawai_nama'] . ' - ' . $value['cppt']['spesialis_nama'] . '<br><hr><br>' . $value['cppt']['nama_pegawai'];
    //         $cppt_data[$key]['penatalaksanaan'] = $this->getPenatalaksanaan($value['cppt']);
    //         $cppt_data[$key]['verifikasi'] = $this->br2mn($this->getVerifikasi($value['cppt'], $value['data_instruksi'], $pegawai_id, $kelompokpegawai_id));
    //         // $cppt_data[$key]['instruksi_dpjp'] = $this->br2mn($this->getInstruksiCppt($value['data_instruksi']));
    //         $cppt_data[$key]['instruksi_dpjp'] = nl2br(htmlspecialchars($value['cppt']['instruksi']));
    //         // $cppt_data[$key]['instruksi_dpjp'] = '<table class="inner"><tr><td>data1</td></tr></table>';
    //         $rownum++;
    //     }

    //     // cppt gizi
    //     $cpptGizi = $this->getCpptGizi($pendaftaran_id);
    //     foreach ($cpptGizi as $key => $value) {
    //         $data = [];
    //         // Assign data
    //         $data['no'] = $rownum;
    //         // $cppt_data[$key]['ruangan'] = $value['cppt']['ruangan_nama'].' '.$value['cppt']['no_tempattidur'].' '.$value['cppt']['kamarruangan_nokamar'];
    //         $data['tgl_cppt'] = $value['tgl_cppt'];
    //         $data['ruanganprofesi'] =  $value['kelompokpegawai_nama'] . ' >' . $value['pegawai_nama'];
    //         $data['penatalaksanaan'] = $this->getPenatalaksanaanGizi($value['adime']);
    //         $data['verifikasi'] = $this->br2mn($this->getVerifikasiGizi($value));
    //         // $cppt_data[$key]['instruksi_dpjp'] = $this->br2mn($this->getInstruksiCppt($value['data_instruksi']));
    //         $data['instruksi_dpjp'] = nl2br(htmlspecialchars($value['instruksi_ppa']));
    //         // $cppt_data[$key]['instruksi_dpjp'] = '<table class="inner"><tr><td>data1</td></tr></table>';
    //         $cppt_data[] = $data;
    //         $rownum++;
    //     }



    //     $print = new DocoPrint();
    //     // echo $this->renderPartial('cetakan_list_cppt',['data'=>$cppt_data,'header1'=>$header1,'header2'=>$header2]); die();
    //     // echo $this->renderPartial('cetakan2',['data'=>$cppt_data,'header1'=>$header1,'header2'=>$header2]); die();
    //     $print->attributes = [
    //         '#inf_norekammedik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
    //         '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d/m/Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
    //         '#inf_nopendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
    //         '#inf_namapasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
    //         '#inf_jeniskelamin#' => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
    //         '#inf_kasuspenyakit#' => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : '',
    //         '#inf_tgllahir#' => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d/m/Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
    //         '#inf_umur#' => $resultHeader ? $resultHeader['umur'] : '',
    //         '#inf_dokterdpjp#' => $resultHeader ? $resultHeader['dokter_admisi'] : '',
    //         '#inf_kelaspelayanan#' => $resultHeader ? $resultHeader['kelas_pelayanan'] : '',
    //         '#inf_nokamar#' => $resultHeader ? $resultHeader['kamarruangan_nokamar'] : '',
    //         '#inf_nobed#' => $resultHeader ? $resultHeader['no_tempattidur'] : '',
    //         '#inf_penjamin#' => $resultHeader ? $resultHeader['penjamin_nama'] : '',
    //         '#inf_carabayar#' => $resultHeader ? $resultHeader['carabayar_nama'] : '',
    //         '#table_list_cppt#' => $this->renderPartial('cetakan_list_cppt', ['data' => $cppt_data, 'header1' => $header1, 'header2' => $header2]),
    //         '#no_pendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
    //         '#nama_pasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
    //         '#nama_user#' => $nama_user,
    //         '#tgl_cetak#' => date('d F Y H:i:s'),
    //     ];
    //     $print->Output();
    // }

    public function actionExportPdfCpptBgproses()
    {
        $kode_doc = 'RI-list-cppt';
        $request = Yii::$app->request;
        $get = $request->get();
        $is_ftp = $request->get('is_ftp');
        $fileName = $request->get('nama_dokumen');
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $fetchLimit = 20;
        $data = $this->getData();
        $countDataGizi = count($data['cpptGizi']);
        $data = $data['data']->asArray()->all();
        $countData = count($data) + $countDataGizi;
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'request' => $request->get(),
            'totalPerPage' => 1,
            'countData' => 1, 
            'token' => $auth,
            'xOwner' => $xOwner,
            'kode_doc' => $kode_doc,
            'is_ftp' => $is_ftp,
            'fileName' => $fileName,
            'pendaftaran_id' => ArrayHelper::getValue($get, 'pendaftaran_id')
        ], 'cppt_pdf', 'import_data_cppt_pdf');

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    private function getData(){
        $request = Yii::$app->request;
        $params = $request->get();
        $pend_asal = Pendaftaran::find()
        ->select([
            new \yii\db\Expression("(additional_data::json->'pendaftaranasal_id') AS pend_asal")
        ])
        ->andWhere(['pendaftaran_id' => $params['pendaftaran_id']])
        ->asArray()
        ->one();

        $querySoap = SoapRsView::find($this->konfigEditCpptCoret)
        ->select([
            new \yii\db\Expression('CASE WHEN referred_id IS NOT NULL THEN referred_id ELSE cppt_id END AS cppt_id'),
            'cppt_id AS origin_cppt_id',
            'tgl_soaprj AS tgl_cppt',
            'a_diag_utama',
            'a_diag_penyerta',
            'kelompokpegawai_nama',
            'nama_pegawai',
            'ruangan_nama',
            'subject',
            'object',
            'planning',
            'no_tempattidur',
            'pegawai_instruksi',
            'kamarruangan_nokamar',
            'catatan_dokter',
            'catatan_perawat',
            'instruksi',
            'is_verifikasi',
            'is_deleted',
            'is_instruksi_pulang',
            'dokteradmisi_id',
            'pemberi_instruksi_id',
            'is_verifikasi_verbal',
            'pegawai_verifikasi_verbal',
            'tgl_verif_verbal',
            'tgl_verifikasi',
            'pegawai_verifikasi',
            'spesialis_nama',
            'pendaftaran_id',
            'pegawai_id',
            'tipe',
            'is_lab',
            'is_rad',
            'is_reseptur',
            'is_konsul',
            'pegawai_update_nama',
            'created_date',
            'kelompokpegawai_id',
        ])
        ->andWhere(['pendaftaran_id' => $params['pendaftaran_id']])
        ->andWhere(['not', ['pasienadmisi_id' => null]]);

        if ($pend_asal['pend_asal'] != 'null') {
            $querySoap->orWhere(['pendaftaran_id' => $pend_asal['pend_asal']]);
        }

        if($this->konfigCpptKosong == TRUE) {
            $querySoap->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
        }

        if (
            !empty($params['filter_kelompokpegawai_id']) &&
            in_array($params['filter_kelompokpegawai_id'], [
                DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                DocoConstants::KELOMPOK_PEGAWAI_DOKTER
            ])
        ) {
            $querySoap->andWhere([
                'kelompokpegawai_id' => $params['filter_kelompokpegawai_id'],
            ]);
        }

        $cpptGizi = $this->getCpptGizi($params['pendaftaran_id']);
        return [
            'data' => $querySoap,
            'cpptGizi' => $cpptGizi,
        ];
    }

    public function actionDataPdfCpptRanap()
    {
        Yii::error("xx");
        ini_set('memory_limit', '-1'); // set memori lebih banyak untuk kebutuhan cetak data yang banyak
        ini_set("pcre.backtrack_limit", "500000000"); // menambah large code size untuk mpdf, default limit = 1000000
        ini_set('max_execution_time', '600'); // set maks process execute
        set_time_limit(600);
        $request = Yii::$app->request;
        $_GET = $request->get('params', null);

        $pendaftaran_id = isset($_GET['pendaftaran_id']) ? $_GET['pendaftaran_id'] : 0;
        $ruangan_id = isset($_GET['ruangan_id']) ? $_GET['ruangan_id'] : 0;
        $pasienadmisi_id = isset($_GET['pasienadmisi_id']) ? $_GET['pasienadmisi_id'] : 0;
        $pegawai_id = isset($_GET['pegawai_id']) ? $_GET['pegawai_id'] : 0;
        $kelompokpegawai_id = isset($_GET['kelompokpegawai_id']) ? $_GET['kelompokpegawai_id'] : 0;
        $nama_usercetak = isset($_GET['nama_usercetak']) ? $_GET['nama_usercetak'] : '';
        $id_usercetak = isset($_GET['id_usercetak']) ? $_GET['id_usercetak'] : 0;
        $orderBy = !empty($_GET['order']) ? $_GET['order'] : ['tgl_soaprj' => 'SORT_ASC'];


        $nama_user = '';
        $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id' => $id_usercetak])->asArray()->one();

        if (is_null($mNamaPegawai)) {
            $nama_user = $nama_usercetak;
        } else {
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }

        $modelHeader = new InfoPasienRanap;
        $queryHeader = $modelHeader::find()
            ->andWhere([
                'pendaftaran_id' => $request->get('pendaftaran_id'),
                'pasienadmisi_id' => $request->get('pasienadmisi_id'),
            ]);
        $resultHeader = $queryHeader->asArray()->one();

        $header1 = array(
            Yii::t('app', "No rekam medik") => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            Yii::t('app', "Tanggal pendaftaran") => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d-m-Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            Yii::t('app', "No Pendaftaran") => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            Yii::t('app', "Nama pasien") => $resultHeader ? $resultHeader['nama_pasien'] : '',
            Yii::t('app', "Jenis kelamin") => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            Yii::t('app', "Kasus Penyakit") => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : ''
        );

        $header2 = array(
            Yii::t('app', "Tanggal lahir") => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            Yii::t('app', "Umur") => $resultHeader ? $resultHeader['umur'] : '',
            Yii::t('app', "Dokter DPJP") => $resultHeader ? $resultHeader['admisi_dokter'] : '',
            Yii::t('app', "Kelas Pelayanan") => $resultHeader ? $resultHeader['kelas_pelayanan'] : '',
            Yii::t('app', "No. Kamar / No. Bed") => $resultHeader ? $resultHeader['kamarruangan_nokamar'] . ' / ' . $resultHeader['no_tempattidur'] : ''
        );
        $data = [];
        $newData = [];


        $pend_asal = Pendaftaran::find()
            ->select([
                new \yii\db\Expression("(additional_data::json->'pendaftaranasal_id') AS pend_asal")
            ])
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->asArray()
            ->one();

        // Model
        $model = new SoapRsView;

        // Query
        $query = $model::find($this->konfigEditCpptCoret)
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->andWhere(['not', ['pasienadmisi_id' => null]])
            ->orderBy($orderBy);

        $pendaftaran_asal =  ArrayHelper::getValue($pend_asal, 'pend_asal');
        if ( $pend_asal['pend_asal'] != 'null' ) {
            $query->orWhere(['pendaftaran_id' => $pendaftaran_asal]);
            $query = $this->whereClauseFisioPendaftaranIds($query, $pendaftaran_asal, null);
        }

        if (!empty($_GET['filter_ruangan_id'])) {
            $query->andWhere([
                'ruangan_id' => $_GET['filter_ruangan_id'],
            ]);
        }

        if (!empty($_GET['filter_pegawai_id'])) {
            $query->andWhere([
                'pegawai_id' => $_GET['filter_pegawai_id'],
            ]);
        }

        if (
            ($filter_kelompokpegawai_id = $request->get('filter_kelompokpegawai_id')) &&
            in_array($filter_kelompokpegawai_id, [
                DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                DocoConstants::KELOMPOK_PEGAWAI_DOKTER
            ])
        ) {
            $query->andWhere([
                'kelompokpegawai_id' => $filter_kelompokpegawai_id,
            ]);
        }

        if($this->konfigCpptKosong == TRUE) {
            $query->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
        }

        if (!empty($_GET['filter_tgl_cppt'])) {
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $explode = explode("-", $_GET['filter_tgl_cppt']);
            if (count($explode) == 2) {
                $start = date_format(date_create_from_format('d/m/Y', $explode[0]), 'Y-m-d').date(' 00:00:00');
                $end = date_format(date_create_from_format('d/m/Y', $explode[1]), 'Y-m-d').date(' 23:59:59');
            }
            $query->andWhere(['between', 'tgl_soaprj', $start, $end]);
        }
        $data = $query->all();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $newData[$key]['cppt'] = $value;
            }
        }
        $cppt_data = [];
        $rownum = 1;
        foreach ($newData as $key => $value) {
            // Assign data
            $ruangan_nama = ArrayHelper::getValue($value, 'cppt.ruangan_nama');
            $no_tempattidur = ArrayHelper::getValue($value, 'cppt.no_tempattidur');
            $kamarruangan_nokamar = ArrayHelper::getValue($value, 'cppt.kamarruangan_nokamar');
            $kelompokpegawai_nama = ArrayHelper::getValue($value, 'cppt.kelompokpegawai_nama');
            $tgl_soaprj = ArrayHelper::getValue($value, 'cppt.tgl_soaprj');
            $instruksi = ArrayHelper::getValue($value, 'cppt.instruksi');
            $nama_pegawai = ArrayHelper::getValue($value, 'cppt.nama_pegawai');
            $cppt_data[$key]['no'] = $rownum;
            $cppt_data[$key]['tgl_cppt'] = date('d/m/Y / H:i:s', strtotime($tgl_soaprj));
            $cppt_data[$key]['ruanganprofesi'] = $ruangan_nama . ' ' . $no_tempattidur . ' ' . $kamarruangan_nokamar . '<br><hr><br>' . date('d/m/Y / H:i:s', strtotime($tgl_soaprj)) . '<br><hr><br>'.$kelompokpegawai_nama.'<br>'. $nama_pegawai;
            $cppt_data[$key]['penatalaksanaan'] = $this->getPenatalaksanaan($value['cppt']);
            $cppt_data[$key]['verifikasi'] = $this->br2mn($this->getVerifikasi($value['cppt'], [], $pegawai_id, $kelompokpegawai_id));
            $cppt_data[$key]['instruksi_dpjp'] = nl2br($instruksi);
            $cppt_data[$key]['is_deleted_soap'] = $value['cppt']['is_deleted'] ? $value['cppt']['is_deleted'] : false;
            $cppt_data[$key]['created_date'] = $value['cppt']['created_date'] ? date('d/m/Y / H:i:s', strtotime($value['cppt']['created_date'])) : '';
            $cppt_data[$key]['pegawai_update_nama'] = $value['cppt']['pegawai_update_nama'] ? $value['cppt']['pegawai_update_nama'] : '';
            $rownum++;
        }

        // cppt gizi
        $cpptGizi = $this->getCpptGizi($pendaftaran_id);
        if(!empty($cpptGizi)){
            foreach ($cpptGizi as $key => $value) {
                $data = [];
                // Assign data
                $data['no'] = $rownum;
                $data['tgl_cppt'] = $value['tgl_cppt'];
                $data['ruanganprofesi'] =  $value['kelompokpegawai_nama'] . ' >' . $value['pegawai_nama'];
                $data['penatalaksanaan'] = $this->getPenatalaksanaanGizi($value['adime']);
                $data['verifikasi'] = $this->br2mn($this->getVerifikasiGizi($value));
                $data['instruksi_dpjp'] = nl2br(htmlspecialchars($value['instruksi_ppa']));
                $cppt_data[] = $data;
                $rownum++;
            }
            usort($cppt_data, function($a, $b){
                return $a['tgl_cppt'] >= $b['tgl_cppt'];
            });
        }

        $attributes = [
            '#inf_norekammedik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d/m/Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            '#inf_nopendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#inf_namapasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#inf_jeniskelamin#' => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            '#inf_kasuspenyakit#' => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : '',
            '#inf_tgllahir#' => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d/m/Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_umur#' => $resultHeader ? $resultHeader['umur'] : '',
            '#inf_dokterdpjp#' => $resultHeader ? $resultHeader['admisi_dokter'] : '',
            '#inf_kelaspelayanan#' => $resultHeader ? $resultHeader['kelas_pelayanan'] : '',
            '#inf_nokamar#' => $resultHeader ? $resultHeader['kamarruangan_nokamar'] : '',
            '#inf_nobed#' => $resultHeader ? $resultHeader['no_tempattidur'] : '',
            '#inf_penjamin#' => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            '#inf_carabayar#' => $resultHeader ? $resultHeader['carabayar_nama'] : '',
            '#table_list_cppt#' => $this->renderPartial('cetakan_list_cppt', ['data' => $cppt_data, 'header1' => $header1, 'header2' => $header2]),
            '#no_pendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#nama_pasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#nama_user#' => $nama_user,
            '#tgl_cetak#' => date('d F Y H:i:s'),
        ];
        return $attributes;
    }

    public function actionSendFile()
    {
        $request = Yii::$app->request;
        $filePath = $request->get('filePath', null);
        $model = new UploadPayload;
        if($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            $path = 'uploads/'. $filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $nameFile = $path.'/'.$model->file;
            if($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'Upload File Berhasil'
                ];
            }
        }
    }

    public function actionDownloadFilePdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('no_request', null);
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.'.pdf';
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
            die();
        }
    }

    /* Get list data signa dan obatalkes */
    public function actionGetDataSignaDanObatalkes()
    {
        try {
            $medIds = Yii::$app->request->post('medIds', []);
            return [
                'listDataSigna' => $this->getOrSetCache(DocoConstants::VC_SO, SignaObat::find()),
                'listDataObatalkesByInstalasi' => $this->getObatAlkesByInstalasi($this->getRuanganInstalasi(DocoConstants::VAR_I_A)->all(), $medIds),
            ];
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

    private function getPenatalaksanaan($data)
    {
        // Check if related cppt also ordered lab/rad/reseptur
        $hasLab = !empty($data['is_lab']) && $data['is_lab'] ? '* Pasien dilakukan pemeriksaan laboratorium <br/>' : '';
        $hasRad = !empty($data['is_rad']) && $data['is_rad'] ? '* Pasien dilakukan pemeriksaan radiologi <br/>' : '';
        $hasResep = !empty($data['is_reseptur']) && $data['is_reseptur'] ? '* Pasien diberikan resep <br/>' : '';
        $hasKonsul = !empty($data['is_konsul']) && $data['is_konsul'] ? '* Pasien dikonsulkan <br/>' : '';

        // Deklarasi html
        $html = '<table><tbody>';
        $html2 = '<ul>';

        // Cek subjek
        if (isset($data['subject']) && $data['subject'] != '') {
            // Set html
            $html .= '<tr>';
            $html .= '<td>S</td>';
            $html .= '<td>:</td>';
            $html .= '<td>' . $data['subject'] . '</td>';
            $html .= '</tr>';
            $html2 .= '<li><b>S :</b>';
            $html2 .= nl2br($data['subject']);
            $html2 .= '</li>';
        }

        // Cek objek
        if (isset($data['object']) && $data['object'] != '') {
            // Set html
            $html .= '<tr>';
            $html .= '<td>O</td>';
            $html .= '<td>:</td>';
            $html .= '<td>' . $data['object'] . '</td>';
            $html .= '</tr>';
            $html2 .= '<li><b>O :</b>';
            $html2 .= nl2br($data['object']);
            $html2 .= '</li>';
        }

        // Cek subject
        if (($data['subject'] != '') && ($data['object'] != '') && ($data['planning'] != '')) {
            // Cek asesmen
            if (isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') {
                // $diag_utama = json_decode($data['a_diag_utama'],TRUE);
                $diag_utama = $data['a_diag_utama'];
                // Set html
                $html .= '<tr>';
                $html .= '<td>A ' . Yii::t('app', 'Diagnosa Utama') . '</td>';
                $html .= '<td>:</td>';
                $html .= '<td>' . @$diag_utama['text'] . '</td>';
                $html .= '</tr>';
                $html2 .= '<li><b>A ' . Yii::t('app', 'Diagnosa Utama') . ':</b>';
                $html2 .= @$diag_utama['text'];
                $html2 .= '</li>';
            }

            // Cek asesmen
            if (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '') {
                // Encode
                // $diagnosaPenyerta = json_decode($data['a_diag_penyerta'],TRUE);
                $diagnosaPenyerta = $data['a_diag_penyerta'];

                // Cek diagnosa
                if ($diagnosaPenyerta != '') {
                    // Inisialisasi counter
                    $counter = 0;

                    // Loop
                    foreach ($diagnosaPenyerta as $valueDiagnosaPenyerta) {
                        // Cek counter
                        if ($counter == 0) {
                            // Set html
                            $html .= '<tr>';
                            $html .= '<td>A ' . Yii::t('app', 'Diagnosa Penyerta') . '</td>';
                            $html .= '<td>:</td>';
                            $html .= '<td>- ' . @$valueDiagnosaPenyerta['text'] . '</td>';
                            $html .= '</tr>';
                            $html2 .= '<li><b>A ' . Yii::t('app', 'Diagnosa Penyerta') . ':</b>' . @$valueDiagnosaPenyerta['text'];
                            $html2 .= '</li>';
                        } else {
                            // Set html
                            $html .= '<tr>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td>- ' . @$valueDiagnosaPenyerta['text'] . '</td>';
                            $html .= '</tr>';
                            $html2 .= '<li> ' . @$valueDiagnosaPenyerta['text'] . '</li>';
                        }

                        // Plus the counter
                        $counter++;
                    }
                } else {
                    // Set strip
                    // $html .= '<td>-</td>';
                }

                // Close tag
                // $html .= '</tr>';
            }
        } else {
            // Set html
            $html .= '<tr>';
            $html .= '<td>' . $data['instruksi'] . '<br>' . $data['pegawai_instruksi'] . '</td>';
            $html .= '</tr>';
            $html2 .= '<li>' . $data['instruksi'] . '</li><br>' . $data['pegawai_instruksi'];
        }

        // Cek penanda order penunjang
        if (!empty($hasLab) || !empty($hasRad) || !empty($hasResep) || isset($data['planning'])) {
            $html2 .= '<li><b>P :</b> ';
            $html .= '<td><b>Planning:</b> <br/>';

            // Cek planning
            if (isset($data['planning']) && $data['planning'] != '') {
                // Set html
                $html2 .= nl2br($data['planning']);
                // $html .= '<tr>';
                // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;"></td>';
                // $html .= '</tr>';
            }

            $html2 .= '<br/>' . $hasLab . $hasRad . $hasResep . $hasKonsul;
            $html2 .= '</li>';
        }

        // Cek catatan dokter
        if (isset($data['catatan_dokter']) && $data['catatan_dokter'] != '') {
            // Set html
            $html .= '<tr>';
            $html .= '<td>' . Yii::t('app', 'Catatan Dokter') . '</td>';
            $html .= '<td>:</td>';
            $html .= '<td>' . $data['catatan_dokter'] . '</td>';
            $html .= '</tr>';
            $html2 .= '<li><b>' . Yii::t('app', 'Catatan Dokter') . ' :</b> ';
            $html2 .= nl2br($data['catatan_dokter']) . '</li>';
        }

        // Cek catatan perawat
        if (isset($data['catatan_perawat']) && $data['catatan_perawat'] != '') {
            // Set html
            $html .= '<tr>';
            $html .= '<td>' . Yii::t('app', 'Catatan Perawat') . '</td>';
            $html .= '<td>:</td>';
            $html .= '<td>' . $data['catatan_perawat'] . '</td>';
            $html .= '</tr>';
            $html2 .= '<li><b>' . Yii::t('app', 'Catatan Perawat') . ' :</b> ';
            $html2 .= nl2br($data['catatan_perawat']) . '</li>';
        }

        // Cek instruksi pulang
        if (isset($data['is_instruksi_pulang']) && $data['is_instruksi_pulang'] == true) {
            // Set html
            $html .= '<tr>';
            $html .= '<td>' . Yii::t('app', 'Instruksi Pulang') . '</td>';
            $html .= '<td>:</td>';
            $html .= '<td>' . Yii::t('app', 'Ya') . '</td>';
            $html .= '</tr>';
            $html2 .= '<li><b>' . Yii::t('app', 'Instruksi Pulang') . ' :</b> ';
            $html2 .= Yii::t('app', 'Ya') . '</li>';
        }

        // Set end tag html
        $html .= '</tbody></table>';
        $html2 .= '</ul>';
        // Return
        return $html2;
    }

    private function getPenatalaksanaanGizi($data)
    {
        // Deklarasi html
        $html = '<table><tbody>';
        $html2 = '<ul>';


        // Set html
        $html2 .= '<li><b>A :</b>';
        $html2 .= isset($data['asesmen_adime']) && !empty($data['asesmen_adime']) ? $data['asesmen_adime'] : '-';
        $html2 .= '</li>';

        // Set html
        $html2 .= '<li><b>D :</b>';
        $html2 .= isset($data['diagnosa_adime']) && !empty($data['diagnosa_adime']) ? $data['diagnosa_adime'] : '-';
        $html2 .= '</li>';

        // Set html
        $html2 .= '<li><b>I :</b>';
        $html2 .= isset($data['intervensi_adime']) && !empty($data['intervensi_adime']) ? $data['intervensi_adime'] : '-';
        $html2 .= '</li>';

        // Set html
        $html2 .= '<li><b>M :</b>';
        $html2 .= isset($data['monitoring_adime']) && !empty($data['monitoring_adime']) ? $data['monitoring_adime'] : '-';
        $html2 .= '</li>';

        // Set html
        $html2 .= '<li><b>E :</b>';
        $html2 .= isset($data['evaluasi_adime']) && !empty($data['evaluasi_adime']) ? $data['evaluasi_adime'] : '-';
        $html2 .= '</li>';

        // Set end tag html
        $html2 .= '</ul>';
        // Return
        return $html2;
    }

    private function getInstruksiDpjp($data_instruksi)
    {
        $groupInstruksi = [];
        $html = '<table border="1"><tr><td>';
        foreach ($data_instruksi as $d_instruksi) {
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_id'] = $d_instruksi['instruksi_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cppt_id'] = $d_instruksi['cppt_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['tipe_instruksi'] = $d_instruksi['tipe_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['catatan_instruksi'] = $d_instruksi['catatan_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cpptpegawai_id'] = $d_instruksi['cpptpegawai_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['is_verifikasi_dpjp'] = $d_instruksi['is_verifikasi_dpjp'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_deleted'] = $d_instruksi['instruksi_deleted'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['ruangan_pertindakan'] = $d_instruksi['ruangan_pertindakan'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['list_tindakan'][] = [
                'nama_tindakan' => $d_instruksi['tindakaninstruksi_nama'],
                'qty_tindakan' => $d_instruksi['qty'],
                'tgl_tindakan' => $d_instruksi['tgl_instruksi'],
                'ket_cyto' => $d_instruksi['ket_cyto'],
                'ket_racik' => $d_instruksi['ket_racik'],
                'instruksi_deleted' => $d_instruksi['instruksi_deleted'],
                'tindakan_deleted' => $d_instruksi['tindakan_deleted'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'tipe_instruksi' => $d_instruksi['tipe_instruksi'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'instruksi' => $d_instruksi['instruksi'],
                'is_telah_implementasi' => $d_instruksi['is_telah_implementasi']
            ];
        }
        foreach ($groupInstruksi as $id_ins => $groupTipe) {
            foreach ($groupTipe as $nama_tipe => $data_ins) {
                if ($nama_tipe == 'TINDAKANBMHP') {
                    $label_nama_tipe = 'Tindakan';
                } else if ($nama_tipe == 'RESEPTUR') {
                    $label_nama_tipe = 'Obat';
                } else if ($nama_tipe == 'PENUNJANG') {
                    $label_nama_tipe = 'Penunjang';
                } else {
                    $label_nama_tipe = 'Tindakan';
                }
                if ($data_ins['instruksi_deleted'] == true) {
                    $html .= '<tr class="strikeout"><td><table>';
                } else {
                    $html .= '<tr><td><table>';
                }
                $html .= '<tr><td>' . @$label_nama_tipe . '</td>';

                $array_status_implemented = [];
                foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                    $array_status_implemented[] = $ins_tindakan['is_telah_implementasi'];
                }
                $instruksi_implemented = false;
                if (count($array_status_implemented) > 0) {
                    if (count(array_unique($array_status_implemented)) === 1) {
                        if (current($array_status_implemented) == true) {
                            $instruksi_implemented = true;
                        }
                    }
                }

                $html .= '</tr>';
                if ($nama_tipe == 'PENUNJANG') {
                    if (substr($data_ins['tipe_instruksi'], 0, 3) == 'LAB') {
                        $label_instalasi = 'Laboratorium';
                    } else if (substr($data_ins['tipe_instruksi'], 0, 3) == 'RAD') {
                        $label_instalasi = 'Radiologi';
                    } else {
                        $label_instalasi = $data_ins['tipe_instruksi'];
                    }
                    $html .= '<tr><td>';
                    $html .= @$label_instalasi . ' - ';
                    $html .= @$data_ins['ruangan_pertindakan'];
                    $html .= '<td></tr>';
                }
                $html .= '<tr><td>' . @$data_ins['catatan_instruksi'] . '</td></tr>';
                $html .= '<tr><td><table>';
                $groupTglTindakan = [];
                foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                    $groupTglTindakan[$ins_tindakan['tipe_instruksi']][$ins_tindakan['tgl_tindakan']][] = $ins_tindakan;
                }
                foreach ($groupTglTindakan as $tipe_instruksi => $group_tgl) {
                    $html .= '<tr>';
                    if ($tipe_instruksi == 'LAB_TINDAKAN') {
                        $label_tipe_instruksi = 'Tindakan Laboratorium';
                    } else if ($tipe_instruksi == 'RAD_TINDAKAN') {
                        $label_tipe_instruksi = 'Tindakan Radiologi';
                    } else if ($tipe_instruksi == 'LAB_PAKET') {
                        $label_tipe_instruksi = 'Paket Laboratorium';
                    } else if ($tipe_instruksi == 'RAD_PAKET') {
                        $label_tipe_instruksi = 'Paket Radiologi';
                    } else {
                        $label_tipe_instruksi = $tipe_instruksi;
                    }
                    $html .= '<td>&nbsp;</td><td>&nbsp;</td><td>' . @$label_tipe_instruksi . '</td></tr>';
                    foreach ($group_tgl as $tgl_ins => $ins_tgltindakan) {
                        $hitungTgl = 0;
                        $hitungInsTindakan = count($ins_tgltindakan);
                        foreach ($ins_tgltindakan as $row_tgltindakan) {
                            $html .= '<tr>';
                            if ($hitungTgl == 0) {
                                if ($hitungInsTindakan == 1 && $row_tgltindakan['tindakan_deleted'] == true) {
                                    $html .= '<td rowspan="' . @$hitungInsTindakan . '"><strike>' . @$row_tgltindakan['tgl_tindakan'] . '</strike></td>';
                                } else {
                                    $html .= '<td rowspan="' . @$hitungInsTindakan . '">' . @$row_tgltindakan['tgl_tindakan'] . '</td>';
                                }
                            }
                            $html .= '<td>&nbsp;</td>';
                            if ($row_tgltindakan['tindakan_deleted'] == true || $row_tgltindakan['instruksi_deleted'] == true) {
                                $html .= '<td><strike>';
                                $html .= @$row_tgltindakan['instruksi'];
                                if (substr($tipe_instruksi, -3) == 'KET') {
                                    $dftr_paket = json_decode($row_tgltindakan['daftar_paket'], true);
                                    if (count($dftr_paket) > 1) {
                                        $html .= '<ul>';
                                        foreach ($dftr_paket as $paket) {
                                            $html .= '<li>' . $paket . '</li>';
                                        }
                                        $html .= '</ul>';
                                    }
                                }
                                $html .= '</strike></td>';
                            } else {
                                $html .= '<td>';
                                $html .= @$row_tgltindakan['instruksi'];
                                if (substr($tipe_instruksi, -3) == 'KET') {
                                    $dftr_paket = json_decode($row_tgltindakan['daftar_paket'], true);
                                    if (count($dftr_paket) > 1) {
                                        $html .= '<ul>';
                                        foreach ($dftr_paket as $paket) {
                                            $html .= '<li>' . $paket . '</li>';
                                        }
                                        $html .= '</ul>';
                                    }
                                }
                                $html .= '</td>';
                            }
                            $html .= '</tr>';
                            $hitungTgl++;
                        }
                    }
                    $html .= '<tr><td>&nbsp;</td></tr>';
                }
                $html .= '</table></td></tr>';
                $html .= '</table>';
            }
        }
        $html .= '</td></tr></table>';
        return $html;
    }

    private function getInstruksiCppt($data_instruksi)
    {
        $groupInstruksi = [];
        foreach ($data_instruksi as $d_instruksi) {
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_id'] = $d_instruksi['instruksi_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cppt_id'] = $d_instruksi['cppt_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['tipe_instruksi'] = $d_instruksi['tipe_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['catatan_instruksi'] = $d_instruksi['catatan_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cpptpegawai_id'] = $d_instruksi['cpptpegawai_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['is_verifikasi_dpjp'] = $d_instruksi['is_verifikasi_dpjp'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_deleted'] = $d_instruksi['instruksi_deleted'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['ruangan_pertindakan'] = $d_instruksi['ruangan_pertindakan'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['list_tindakan'][] = [
                'nama_tindakan' => $d_instruksi['tindakaninstruksi_nama'],
                'qty_tindakan' => $d_instruksi['qty'],
                // 'tgl_tindakan' => $d_instruksi['tgl_instruksi'],
                'tgl_tindakan' => $d_instruksi['tanggal_input'],
                'ket_cyto' => $d_instruksi['ket_cyto'],
                'ket_racik' => $d_instruksi['ket_racik'],
                'instruksi_deleted' => $d_instruksi['instruksi_deleted'],
                'tindakan_deleted' => $d_instruksi['tindakan_deleted'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'tipe_instruksi' => $d_instruksi['tipe_instruksi'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'instruksi' => $d_instruksi['instruksi'],
                'is_telah_implementasi' => $d_instruksi['is_telah_implementasi'],
                'status_implementasi' => $d_instruksi['status_implementasi']
            ];
        }

        $html = '';
        $lineCounter = 1;
        $lengthInstruksi = count($groupInstruksi);
        foreach ($groupInstruksi as $id_ins => $groupTipe) {
            foreach ($groupTipe as $nama_tipe => $data_ins) {
                if ($nama_tipe == 'TINDAKANBMHP') {
                    $label_nama_tipe = 'Tindakan';
                } else if ($nama_tipe == 'RESEPTUR') {
                    $label_nama_tipe = 'Obat';
                } else if ($nama_tipe == 'PENUNJANG') {
                    $label_nama_tipe = 'Penunjang';
                } else {
                    $label_nama_tipe = 'Tindakan';
                }
                if ($data_ins['instruksi_deleted'] == true) {
                    // $html .= '<tr class="strikeout"><td><table>';
                    $html .= '<b><strike>' . @$label_nama_tipe . '</strike></b><br>';
                } else {
                    // $html .= '<tr><td><table>';
                    $html .= '<b>' . @$label_nama_tipe . '</b><br>';
                }
                // $html .= '<tr><td>'.@$label_nama_tipe.'</td>';

                $array_status_implemented = [];
                $array_status_penunjang_batal = [];
                foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                    if ($ins_tindakan['tindakan_deleted'] != true) {
                        $array_status_implemented[] = $ins_tindakan['is_telah_implementasi'];
                        if ($nama_tipe == 'PENUNJANG') {
                            $array_status_penunjang_batal[] = $ins_tindakan['status_implementasi'];
                        }
                    }
                }
                $instruksi_implemented = false;
                if (count($array_status_implemented) > 0) {
                    if (count(array_unique($array_status_implemented)) === 1) {
                        if (current($array_status_implemented) == true) {
                            $instruksi_implemented = true;
                        }
                    }
                }

                $is_penunjang_batal = false;
                $is_penunjang_ditolak = false;
                if (count($array_status_penunjang_batal) > 0) {
                    if (in_array('472', $array_status_penunjang_batal)) {
                        $is_penunjang_batal = true;
                    }
                    if (in_array('541', $array_status_penunjang_batal)) {
                        $is_penunjang_ditolak = true;
                    }
                }
                if ($nama_tipe == 'PENUNJANG') {
                    if (substr($data_ins['tipe_instruksi'], 0, 3) == 'LAB') {
                        $label_instalasi = 'Laboratorium';
                    } else if (substr($data_ins['tipe_instruksi'], 0, 3) == 'RAD') {
                        $label_instalasi = 'Radiologi';
                    } else if (substr($data_ins['tipe_instruksi'], 0, 3) == 'BED') {
                        $label_instalasi = 'Bedah Sentral';
                    } else {
                        $label_instalasi = $data_ins['tipe_instruksi'];
                    }
                    // $html .= '<tr><td>';
                    $html .= '<b>';
                    $html .= @$label_instalasi . ' - ';
                    $html .= @$data_ins['ruangan_pertindakan'];
                    if ($is_penunjang_batal == true) {
                        $html .= '-  <b>DIBATALKAN</b>';
                    }
                    if ($is_penunjang_ditolak == true) {
                        $html .= '-  <b>DITOLAK</b>';
                    }
                    $html .= '</b><br>';
                    // $html .= '<td></tr>';
                }

                if ($data_ins['instruksi_deleted'] == true) {
                    $html .= '<b><strike>' . @$data_ins['catatan_instruksi'] . '</strike></b><br>';
                } else {
                    $html .= '<b>' . @$data_ins['catatan_instruksi'] . '</b><br>';
                }
                // $html .= '<tr><td>'.@$data_ins['catatan_instruksi'].'</td></tr>';
                // $html .= '<tr><td><table>';
                $groupTglTindakan = [];
                foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                    $groupTglTindakan[$ins_tindakan['tipe_instruksi']][$ins_tindakan['tgl_tindakan']][] = $ins_tindakan;
                }
                $html .= '<ul>';
                foreach ($groupTglTindakan as $tipe_instruksi => $group_tgl) {
                    // $html .= '<tr>';
                    if ($tipe_instruksi == 'LAB_TINDAKAN') {
                        $label_tipe_instruksi = 'Tindakan Laboratorium';
                    } else if ($tipe_instruksi == 'RAD_TINDAKAN') {
                        $label_tipe_instruksi = 'Tindakan Radiologi';
                    } else if ($tipe_instruksi == 'LAB_PAKET') {
                        $label_tipe_instruksi = 'Paket Laboratorium';
                    } else if ($tipe_instruksi == 'RAD_PAKET') {
                        $label_tipe_instruksi = 'Paket Radiologi';
                    } else if ($tipe_instruksi == 'BED_TINDAKAN') {
                        $label_tipe_instruksi = 'Tindakan Bedah Sentral';
                    } else {
                        $label_tipe_instruksi = $tipe_instruksi;
                    }
                    // $html .= '<td>&nbsp;</td><td>&nbsp;</td><td>'.@$label_tipe_instruksi.'</td></tr>';

                    if ($data_ins['instruksi_deleted'] == true) {
                        $html .= '<li><strike>' . @$label_tipe_instruksi . '</strike></li>';
                    } else {
                        $html .= '<li>' . @$label_tipe_instruksi . '</li>';
                    }
                    $html .= '<ul>';
                    foreach ($group_tgl as $tgl_ins => $ins_tgltindakan) {
                        $hitungTgl = 0;
                        $hitungInsTindakan = count($ins_tgltindakan);
                        foreach ($ins_tgltindakan as $row_tgltindakan) {
                            // $html .= '<tr>';
                            if ($hitungTgl == 0) {
                                if ($hitungInsTindakan == 1 && $row_tgltindakan['tindakan_deleted'] == true) {
                                    // $html .= '<td rowspan="'.@$hitungInsTindakan.'"><strike>'.@$row_tgltindakan['tgl_tindakan'].'</strike></td>';
                                } else {
                                    // $html .= '<td rowspan="'.@$hitungInsTindakan.'">'.@$row_tgltindakan['tgl_tindakan'].'</td>';
                                }
                            }
                            // $html .= '<td>&nbsp;</td>';
                            if ($row_tgltindakan['tindakan_deleted'] == true || $row_tgltindakan['instruksi_deleted'] == true) {
                                $html .= '<li><strike>';
                                // $html .= '<td><strike>';
                                $html .= date('d/m/Y H:i:s', strtotime(@$row_tgltindakan['tgl_tindakan'])) . '-' . @$row_tgltindakan['instruksi'];
                                if (substr($tipe_instruksi, -3) == 'KET') {
                                    $dftr_paket = json_decode($row_tgltindakan['daftar_paket'], true);
                                    if (count($dftr_paket) > 1) {
                                        $html .= '<ul>';
                                        foreach ($dftr_paket as $paket) {
                                            $html .= '<li>' . $paket . '</li>';
                                        }
                                        $html .= '</ul>';
                                    }
                                }
                                // $html .= '</strike></td>';
                                $html .= '</strike></li>';
                            } else {
                                // $html .= '<td>';
                                $html .= '<li>';
                                $html .= date('d/m/Y H:i:s', strtotime(@$row_tgltindakan['tgl_tindakan'])) . '-' . @$row_tgltindakan['instruksi'];
                                if (substr($tipe_instruksi, -3) == 'KET') {
                                    $dftr_paket = json_decode($row_tgltindakan['daftar_paket'], true);
                                    if (count($dftr_paket) > 1) {
                                        $html .= '<ul>';
                                        foreach ($dftr_paket as $paket) {
                                            $html .= '<li>' . $paket . '</li>';
                                        }
                                        $html .= '</ul>';
                                    }
                                }
                                // $html .= '</td>';
                                $html .= '</li>';
                            }
                            // $html .= '</tr>';
                            $hitungTgl++;
                        }
                    }
                    $html .= '</ul>';
                }
                $html .= '</ul>';
            }
            if ($lineCounter != $lengthInstruksi) {
                $html .= '<br>';
                $html .= '<hr>';
            }
            $lineCounter++;
        }
        $html .= '';
        return $html;
    }

    private function getVerifikasi($data, $data_instruksi = [], $pegawai_id, $kelompokpegawai_id)
    {
        $dpjp = $data['dokteradmisi_id'];
        $pemberi_instruksi = $data['pemberi_instruksi_id'];
        $html = '';

        // cek verifikasi verbal order
        if ($data['instruksi']) {
            if ($data['is_verifikasi_verbal']) {
                $html .= '<span>' . $data['pegawai_verifikasi_verbal'] . '<br><hr><br>' . date('d/m/Y H:i:s', strtotime($data['tgl_verif_verbal'])) . '</span>';
            } else {
                $html .= '<h5>Belum Verifikasi Verbal Order</h5>';
            }
            $html .= '<br>';
        } else if ($data['is_verifikasi']) { // cek verifikasi dpjp
            $html .= '<span>' . $data['pegawai_verifikasi'] . '<br><hr><br>' . date('d/m/Y H:i:s', strtotime($data['tgl_verifikasi'])) . '</span>';
            $html .= '<br>';
        } else {
            $html .= '<h5>Belum Verifikasi DPJP</h5>';
        }

        return $html;
    }

    private function getVerifikasiGizi($data)
    {
        $html = '';
        if($data['is_verifikasi']) {
            $html .= '<span>' . $data['pegawai_verifikasi_nama'] . '<br><hr><br>' . date('d/m/Y H:i:s', strtotime($data['tgl_verifikasi'])) . '</span>';
            $html .= '<br>';
        } else {
            $html .= '<h5>Belum Verifikasi DPJP</h5>';
        }
        return $html;
    }

    private function getSoapIsPulang($pendaftaran_id)
    {
        $model = Cppt::find()->select(['pendaftaran_id'])->where(['pendaftaran_id' => $pendaftaran_id, 'is_instruksi_pulang' => true])->all();

        if (count($model) > 0) {
            return true;
        } else {
            return false;
        }
    }


    // rizal
    // Get list jadwal operasi
    public function actionGetDataJadwalOperasi()
    {
        try {
            //hit api backend laboratorium
            // $restLab = Yii::$app->docoRest->laboratorium;
            // $request = $restLab->post('jadwal-operasi/create?id='.$posts['pendaftaran_id'], [
            //     'form_params'=>$posts
            // ]);

            $request = Yii::$app->request;
            $restBedah = Yii::$app->docoRest->bedah;
            $response = $restBedah->get('jadwal-operasi/index', [
                'query' => $request->get()
            ]);
            $response = json_decode($response->getBody(), true);

            return $response;
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

    public function actionViewJadwalOperasi()
    {
        try {
            $request = Yii::$app->request;
            $restBedah = Yii::$app->docoRest->bedah;
            $response = $restBedah->get('jadwal-operasi/view', [
                'query' => $request->get()
            ]);
            $response = json_decode($response->getBody(), true);

            return $response;
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


    /**
     * This function will edit soap
     *
     * @param String $cpptId
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionEditSoap($cpptId)
    {
        $this->transactionClass = Yii::$app->db->beginTransaction();
        try {
            return $this->soapCreateOrUpdate($cpptId);
        } catch (\yii\db\Exception $e) {
            $this->transactionClass->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->transactionClass->rollBack();
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * This function will send data SOAP on create / update
     *
     * @return Array/Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function soapCreateOrUpdate($cpptId = null, $auto = null)
    {
        $request = Yii::$app->request;
        $model = new Cppt;
        $post = $request->post();

        $loginpemakai_id = Yii::$app->jwt->user->loginpemakai_id;
        $pegawai_id = $post['pegawai_id'];
        $model->scenario = ($auto) ? 'auto' : 'soap';
        $payload = $post;
        if (isset($post['a_diag_utama'])) {
            $valDiagUtama = [];
            $splitVal = explode('_', $post['a_diag_utama']);
            if (count($splitVal) > 1) {
                $valDiagUtama['id'] = $splitVal[0];
                $splitTxt = explode('-', $splitVal[1]);
                if (count($splitTxt) > 1) {
                    $valDiagUtama['kode'] = $splitTxt[0];
                    $valDiagUtama['nama'] = $splitTxt[1];
                }
                $valDiagUtama['text'] = $splitVal[1];
            } else {
                $valDiagUtama['text'] = $post['a_diag_utama'];
            }
            $model->a_diag_utama = $valDiagUtama;
            unset($post['a_diag_utama']);
        }
        $valueDiagPenyerta = [];
        if (isset($post['a_diag_penyerta']) && is_array($post['a_diag_penyerta'])) {
            foreach ($post['a_diag_penyerta'] as $key_diag) {
                $valDiag = [];
                $splitVal = explode('_', $key_diag);
                if (count($splitVal) > 1) {
                    $valDiag['id'] = $splitVal[0];
                    // $valDiag['kode']$splitTxt = explode('-', $splitVal[1]);
                    $splitTxt = explode('-', $splitVal[1]);
                    if (count($splitTxt) > 1) {
                        $valDiag['kode'] = $splitTxt[0];
                        unset($splitTxt[0]);
                        $txtDiag = '';
                        foreach ($splitTxt as $keyTxt => $valueTxt) {
                            $txtDiag .= $valueTxt.'-';
                        }
                        $valDiag['nama'] = rtrim($txtDiag, '-');
                    }
                    $valDiag['text'] = $splitVal[1];
                } else {
                    $valDiag['text'] = $key_diag;
                }
                $valueDiagPenyerta[] = $valDiag;
            }
            $model->a_diag_penyerta = $valueDiagPenyerta;
            unset($post['a_diag_penyerta']);
        }
        // Add nl2br function - @santuy | June 25 2020
        $post['subject'] = $post['subject'];
        $post['object'] = $post['object'];
        $post['planning'] = $post['planning'];
        $post['instruksi'] = $post['instruksi'];
        $latestCpptAutoInactive = Cppt::find(true)
            ->andWhere(['pasienadmisi_id' => @$post['pasienadmisi_id']])
            ->andWhere(['pegawai_id' => @$post['pegawai_id']])
            ->andWhere(['is_active' => false])
            ->one();
        if ($auto) {
            $model->is_active = false;
        }
        $model->attributes = $post;

        if (!empty($cpptId)) {
            $existingCppt = Cppt::find(true)
                ->select([
                    'cppt_id',
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'pasien_id',
                    'ruangan_id',
                    'kamar_tempattidur',
                    'tgl_cppt',
                    'subject',
                    'object',
                    'planning',
                    'is_instruksi_pulang',
                    'pegawai_id',
                    'catatan_dokter',
                    'catatan_perawat',
                    'instruksi',
                    'pemberi_instruksi_id',
                    'is_verifikasi',
                    'pegawai_verifikasi_id',
                    'tgl_verifikasi',
                    'kamarruangan_id',
                    'kamartempattidur_id',
                    'is_visitedokter',
                    'tindakanvisite_id',
                    'is_verifikasi_verbal',
                    'pegawai_verbal_id',
                    'tgl_verif_verbal',
                    'referred_id'
                ])
                ->andWhere(['cppt_id' => $cpptId])
                ->asArray()
                ->one();
            if (!empty($existingCppt)) {
                $getDiagnosa = Cppt::find(true)->select(['a_diag_utama','a_diag_penyerta'])->andWhere(['cppt_id' => $cpptId])->asArray()->one();
                $groupEmployee = Pegawai::find()->select(['kelompokpegawai_id'])->andWhere(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])->asArray()->one();
                $getDiagnosaText = json_decode($getDiagnosa['a_diag_utama'], true);
                $getDiagnosaPenyertaText = isset($getDiagnosa['a_diag_penyerta']) ? json_decode($getDiagnosa['a_diag_penyerta'], true) : null;
                if(isset($getDiagnosaPenyertaText) && !empty($valueDiagPenyerta)){
                    $key = 0;
                    $getDiagnosaPenyertaText = ArrayHelper::getColumn($getDiagnosaPenyertaText, function ($val) use (&$key,$valueDiagPenyerta) {
                        $getText = ArrayHelper::getValue($val, 'text', '-');
                        $val['text'] = DocoHelpers::crossOutDifferences($getText, isset($valueDiagPenyerta[$key]['text']) ? $valueDiagPenyerta[$key]['text'] : '');
                        $key++;
                        return $val;
                    });
                }else{
                    if(isset($getDiagnosaPenyertaText)){
                        $getDiagnosaPenyertaText = ArrayHelper::getColumn($getDiagnosaPenyertaText, function ($val) {
                            $getText = ArrayHelper::getValue($val, 'text', '-');
                            $val['text'] = DocoHelpers::crossOutDifferences($getText, '');
                            return $val;
                        });
                    }
                }
                $subject_strike_text = DocoHelpers::crossOutDifferences($existingCppt['subject'], $post['subject']);
                $object_strike_text = DocoHelpers::crossOutDifferences($existingCppt['object'], $post['object']);
                $planning_strike_text = DocoHelpers::crossOutDifferences($existingCppt['planning'], $post['planning']);
                $getDiagnosaText['text'] = DocoHelpers::crossOutDifferences($getDiagnosaText['text'], $valDiagUtama['text']);
                $instruksi_strike_text = isset($post['instruksi']) ? DocoHelpers::crossOutDifferences($existingCppt['instruksi'], !empty($post['instruksi']) ? $post['instruksi'] : '') : $existingCppt['instruksi'];
                $catatan_dokter_strike_text = isset($post['catatan_dokter']) ? DocoHelpers::crossOutDifferences($existingCppt['catatan_dokter'], !empty($post['catatan_dokter']) ? $post['catatan_dokter'] : '') : $existingCppt['catatan_dokter'];
                $catatan_perawat_strike_text = isset($post['catatan_perawat']) ? DocoHelpers::crossOutDifferences($existingCppt['catatan_perawat'], !empty($post['catatan_perawat']) ? $post['catatan_perawat'] : '') : $existingCppt['catatan_perawat'];
                Cppt::updateAll([
                'subject' => $subject_strike_text,
                'object' => $object_strike_text,
                'planning' => $planning_strike_text,
                'a_diag_utama' => $getDiagnosaText ,
                'a_diag_penyerta' => $getDiagnosaPenyertaText,
                'instruksi' => $instruksi_strike_text,
                $groupEmployee['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN ? 'catatan_perawat' : 'catatan_dokter' => $groupEmployee['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN ? $catatan_perawat_strike_text : $catatan_dokter_strike_text
                ],
                ['cppt_id' => $cpptId]);
                if (!empty($existingCppt['referred_id'])) {
                    // querying again to get tgl_cppt of origin
                    $record = Cppt::find(true)
                        ->select([
                            'tgl_cppt',
                            'referred_id',
                            'cppt_id'
                        ])
                        ->andWhere([
                            'cppt_id' => $existingCppt['referred_id']
                        ])
                        ->asArray()
                        ->one();
                    if (!empty($existingCppt['referred_id'])) {
                        $originCpptDate = date("Y-m-d H:i", strtotime($record['tgl_cppt']));
                    } else {
                        return $this->responseJson(400, 'Referensi CPPT tidak ditemukan.');
                    }
                } else {
                    $originCpptDate = date("Y-m-d H:i", strtotime($existingCppt['tgl_cppt']));
                }
                $cpptDateFormated = date("Y-m-d H:i", strtotime($post['tgl_cppt']));
                // di comment karna ada perubahan untuk melepas validasi ini
                // if ($originCpptDate > $cpptDateFormated || $cpptDateFormated > date("Y-m-d H:i:s")) {
                //     $this->transactionClass->rollBack();
                //     return $this->responseJson(400, $originCpptDate > $cpptDateFormated ? 'Tanggal CPPT tidak boleh kurang dari tanggal referensi CPPT (' . $this->helper->convertDate($originCpptDate, 'd-m-Y H:i') . ')' : 'Tanggal CPPT tidak boleh lebih dari tanggal dan jam saat ini');
                // }
                if ($existingCppt['pegawai_id'] != Yii::$app->jwt->user->pegawai_id) {
                    $this->transactionClass->rollBack();
                    return $this->responseJson(400, 'Anda dilarang untuk mengubah data CPPT ini.');
                }
                // check the date should be more than or equal referred data
                // return $this->responseJson(400, 'JASD');
                unset($post['ruangan_id'], $post['pendaftaran_id'], $post['pegawai_id'], $post['pasien_id'], $post['pasienadmisi_id'], $post['kamarruangan_id'], $post['kamartempattidur_id'], $post['kamar_tempattidur'], $existingCppt['cppt_id']);
                $model->attributes = array_merge($existingCppt, $post);

                if (!empty($existingCppt['referred_id'])) {
                    $referredId = $existingCppt['referred_id'];
                } else {
                    $referredId = $cpptId;
                    Cppt::updateAll([
                    'is_deleted' => true,
                    'last_modified_by' => $loginpemakai_id,
                    'deleted_by' => $loginpemakai_id,
                    'deleted_date' =>  date("Y-m-d H:i:s"),
                    ],
                    ['cppt_id' => $cpptId]);
                }
                Cppt::updateAll([
                'is_deleted' => true,
                'last_modified_by' =>$loginpemakai_id,
                'deleted_by' => $loginpemakai_id,
                'deleted_date' =>  date("Y-m-d H:i:s"),
                ],
                ['referred_id' => $referredId]);
            } else {
                $this->transactionClass->rollBack();
                return $this->responseJson(400, 'Cppt tidak ditemukan!');
            }
        } else {
            $model->attributes = $post;
            if (!isset($model->tgl_cppt) || empty($model->tgl_cppt)) {
                $model->tgl_cppt = date('Y-m-d H:i:s');
            }
        }
        $additional_data = $model->additional_data;
        if(!is_array($additional_data)) {
            $additional_data = json_decode($additional_data, true);
        }
        $additional_data['via_soap'] = true;
        $model->additional_data = json_encode($additional_data);
        $model->is_icd_x = $post['is_icd_x'];;
        if ($model->validate()) {
            if (isset($existingCppt)) {
                // edit
                $model->referred_id = $referredId;
                if (!$model->save()) {
                    $this->transactionClass->rollBack();
                    $errors = DocoHelpers::parseError($model->errors, 'CpptForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
                $payload['pendaftaran_id'] = $model->pendaftaran_id;
                $payload['pasienadmisi_id'] = $model->pasienadmisi_id;
            } else if (!is_null($latestCpptAutoInactive)) {
                $_diag_utama = $model->a_diag_utama;
                $_diag_penyerta = $model->a_diag_penyerta;
                $model = $latestCpptAutoInactive;
                if ($auto) {
                    $model->scenario = 'auto';
                } else {
                    $model->is_active = true;
                    $model->scenario = 'soap';
                }
                $model->attributes = $post;
                $model->a_diag_utama = $_diag_utama;
                $model->a_diag_penyerta = $_diag_penyerta;

                if (!$model->save()) {
                    $this->transactionClass->rollBack();
                    $errors = DocoHelpers::parseError($model->errors, 'CpptForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            } else {
                // create
                if (!$model->save()) {
                    $this->transactionClass->rollBack();
                    $errors = DocoHelpers::parseError($model->errors, 'CpptForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }

            $this->transactionClass->commit();

            if ($post['is_dokter']) {
                Yii::error([
                    'msg' => 'its edit, u r here',
                    'payload' => $payload
                ]);
                (new Cppt)->addVisiteDokter($payload);
            }
            /*
            * start visit dokter integration
            * kondisi integrasi visit dokter adalah:
            * 1. ketika dokter terkait belom ada tindakan visit dokter di hari yg dipilih di tgl cppt
            * 2. ketika bill pasien belom closing
            * 3. ketika dokter yg melakukan input soap
            */
            // end visit dokter integration

            return [
                'message' => 'Data Berhasil di simpan',
                'data' => [
                    'a_diag_utama' => $model->a_diag_utama
                ]
            ];
        } else {
            $this->transactionClass->rollBack();
            $response = $model->getErrors();
            return DocoHelpers::responseTemplate(422, $response);
        }
    }

    /**
     * @param $pendaftaran_id
     * @param $tgl_mulai Untuk filterisasi data
     * @param $tgl_akhir Untuk filterisasi data
     * @param $pegawai_id Untuk filterisasi pegawai
     * @param $kelompokpegawai_id filterisasi kelompok pegawai
     */
    private function getCpptGizi($pendaftaran_id, $tgl_mulai = null, $tgl_akhir = null, $pegawai_id = null, $kelompokpegawai_id = null, $orderBy = null, $length = null)
    {
        $cpptGizi = [];
        $query = CpptGiziView::find()
        ->where(['pendaftaran_id' => $pendaftaran_id]);
        if (! empty($tgl_mulai) && ! empty($tgl_akhir)) {
            $query->andWhere(['between', 'tgl_kajian', $tgl_mulai, $tgl_akhir]);
        }

        if( ! empty($pegawai_id)) {
            $query->andWhere([
                'pemberi_instruksi_id' => $pegawai_id
            ]);
        }

        if(! empty($kelompokpegawai_id)) {
            if (
                ($kelompokpegawai_id) &&
                in_array($kelompokpegawai_id, [
                    DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                    DocoConstants::KELOMPOK_PEGAWAI_DOKTER
                ])
            ) {
                $query->andWhere([
                    'kelompokpegawai_id' => $kelompokpegawai_id,
                ]);
            }
        }

        if (!empty($orderBy)) {
            // Hard code dulu untuk mengakomodir sorting tanggal cppt gizi
            $order = explode(' ', $orderBy, 2);
            if ($order[0] == 'tgl_soaprj') {
                $order[0] = 'tgl_kajian';
            }
            $newOrderBy = implode(' ', $order);
            $query = $query->orderBy($newOrderBy);
        }

        if (!empty($length)) {
            $query->limit($length);
        }
        
        $getCpptGizi = $query->asArray()->all();
        if (!empty($getCpptGizi)) {
            foreach ($getCpptGizi as $key => $value) {
                $cppt = $value;
                $cppt['asesmen_adime'] = isset($cppt['asesmen_adime']) && !empty($cppt['asesmen_adime']) ? $cppt['asesmen_adime'] : $this->renderPartial('adime/_asesmen_gizi', ['cppt' => $cppt]);
                $cppt['diagnosa_adime'] = isset($cppt['diagnosa_adime']) && !empty($cppt['diagnosa_adime']) ? $cppt['diagnosa_adime'] : $this->renderPartial('adime/_diagnosa_gizi', ['cppt' => $cppt]);
                $cppt['intervensi_adime'] = isset($cppt['intervensi_adime']) && !empty($cppt['intervensi_adime']) ? $cppt['intervensi_adime'] : $this->renderPartial('adime/_intervensi_gizi', ['cppt' => $cppt]);
                $cppt['monitoring_adime'] = isset($cppt['monitoring_adime']) && !empty($cppt['monitoring_adime']) ? $cppt['monitoring_adime'] : $this->renderPartial('adime/_monitoring', ['cppt' => $cppt]);
                $cppt['evaluasi_adime'] = isset($cppt['evaluasi_adime']) && !empty($cppt['evaluasi_adime']) ? $cppt['evaluasi_adime'] : $this->renderPartial('adime/_evaluasi', ['cppt' => $cppt]);
                $cpptGizi[] = [
                    'origin_tgl_cppt' => $cppt['tgl_kajian'],
                    'sort' => !empty($cppt['tgl_kajian']) ? date('Y-m-d H:i:s', strtotime($cppt['tgl_kajian'])) : '',
                    'kelompokpegawai_nama' => isset($cppt['kelompokpegawai_nama']) ? $cppt['kelompokpegawai_nama'] : '',
                    'tgl_cppt' => !empty($cppt['tgl_kajian']) ? date('d/m/Y / H:i:s', strtotime($cppt['tgl_kajian'])) : '',
                    'pegawai_nama' => isset($cppt['pemberi_asuhan']) ? $cppt['pemberi_asuhan'] : null,
                    'instruksi_ppa' => isset($cppt['instruksi_ppa']) ? $cppt['instruksi_ppa'] : null,
                    'pagt_id' => isset($cppt['pagt_id']) ? $cppt['pagt_id'] : null,
                    'is_verifikasi' => isset($cppt['is_verifikasi']) ? $cppt['is_verifikasi'] : false,
                    'tgl_verifikasi' => isset($cppt['tgl_verifikasi']) ? $cppt['tgl_verifikasi'] : null,
                    'pegawai_verifikasi_id' => isset($cppt['pegawai_verifikasi_id']) ? $cppt['pegawai_verifikasi_id'] : null,
                    'pegawai_verifikasi_nama' => isset($cppt['pegawai_verifikasi_nama']) ? $cppt['pegawai_verifikasi_nama'] : null,
                    'dokteradmisi_id' => isset($cppt['dokteradmisi_id']) ? $cppt['dokteradmisi_id'] : null,
                    'pemberi_instruksi_id' => isset($cppt['pemberi_instruksi_id']) ? $cppt['pemberi_instruksi_id'] : null,
                    'adime' => [
                        'asesmen_adime' => $cppt['asesmen_adime'],
                        'diagnosa_adime' => $cppt['diagnosa_adime'],
                        'intervensi_adime' => $cppt['intervensi_adime'],
                        'monitoring_adime' => $cppt['monitoring_adime'],
                        'evaluasi_adime' => $cppt['evaluasi_adime'],
                    ],
                    'hasil_asesmen' => $this->renderPartial('_hasilasesmen', compact('cppt')),
                ];
            }
        }
        return $cpptGizi;
    }

    private function getLastCppt($pendaftaran_id, $pegawai_id, $pasienadmisi_id)
    {
        $today = date('Y-m-d 00:00:00', strtotime('NOW'));
        $model = CpptView::find()
            ->select([new \yii\db\Expression('CASE WHEN referred_id IS NOT NULL THEN referred_id ELSE cppt_id END AS cppt_id')])
            ->where([
                'pendaftaran_id' => $pendaftaran_id,
                'pegawai_id' => $pegawai_id,
                'pasienadmisi_id' => $pasienadmisi_id,
                'is_verifikasi' => false,
                'is_active' => true
            ])
            ->andWhere(['>', 'tgl_cppt', $today])
            // START CONDITION VERBAL ORDER
            ->andWhere(['IS', 'instruksi', null])
            ->andWhere(['IS', 'pemberi_instruksi_id', null])
            ->andWhere(['IS NOT', 'pasienadmisi_id', null])
            // END CONDITION VERBAL ORDER
            ->orderBy(['tgl_cppt' => SORT_DESC])
            ->asArray()
            ->one();

        if ($model) {
            return $model['cppt_id'];
        } else {
            return 0;
        }
    }

    private function getCpptInactive($pendaftaran_id, $pegawai_id)
    {
        $cppt = Cppt::find(true)
            ->andWhere(['pendaftaran_id' => $pendaftaran_id])
            ->andWhere(['pegawai_id' => $pegawai_id])
            ->andWhere(['is_active' => false])
            ->one();
        return $cppt;
    }

    private function suggestionCppt($pendaftaran_id, $pasienadmisi_id)
    {
        // get cppt
        $model = [
            'subject' => '',
            'object' => '',
            'planning' => ''
        ];
        
        $employeeRecord = Pegawai::find(true)->select(['pegawai_id', 'kelompokpegawai_id'])->where(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])->asArray()->one();
        if(!empty($employeeRecord) && $employeeRecord['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_PERAWAT) {
            $_perawat_subject = '';
            $_perawat_object = '';

            $ttv = AsesmenAwal::find()
                    ->select([
                        new \yii\db\Expression("additional_data::json->>'tinggi_badan' as tinggi_badan"),
                        new \yii\db\Expression("additional_data::json->>'berat_badan' as berat_badan"),
                        new \yii\db\Expression("additional_data::json->>'detak_nadi' as detak_nadi"),
                        new \yii\db\Expression("additional_data::json->>'suhu_tubuh' as suhu_tubuh"),
                        new \yii\db\Expression("additional_data::json->>'tensi' as tekanan_darah"),
                        'keluhan_utama'
                    ])
                    ->andWhere(compact('pendaftaran_id'))
                    ->andWhere(['is_active' => true])
                    ->andWhere(['IS NOT', 'pasienadmisi_id', null])
                    ->asArray()->one();
            if(!empty($ttv)){
                $_perawat_subject .= ArrayHelper::getValue($ttv,'keluhan_utama','');

                $_perawat_object .='Berat Badan : ' . ArrayHelper::getValue($ttv,'berat_badan','-');
                $_perawat_object .="\nTinggi Badan : " . ArrayHelper::getValue($ttv,'tinggi_badan','-');
                $_perawat_object .="\nNadi : " . ArrayHelper::getValue($ttv,'detak_nadi','-');
                $_perawat_object .="\nTensi/TD : " . ArrayHelper::getValue($ttv,'tekanan_darah','-');
                $_perawat_object .="\nSuhu : " . ArrayHelper::getValue($ttv,'suhu_tubuh','-');
            }
            
            $model['subject'] = $_perawat_subject;
            $model['object'] = $_perawat_object;
        }

        $latestCpptRecord = Cppt::find()->select(['cppt_id'])->andWhere(compact('pendaftaran_id'))->andWhere(['is_active' => true])->andWhere(['IS NOT', 'pasienadmisi_id', null])->orderBy(['tgl_cppt' => SORT_DESC])->asArray()->one();
        if (!empty($latestCpptRecord)) {
            $model['planning'] .= $this->getPlanningSuggestion($pasienadmisi_id);
            return $model;
        }
        
        if (!empty($employeeRecord) && $employeeRecord['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_DOKTER) {
            $asmedRecord = AsesmenMedis::find()
                ->select([
                    'asesmenmedis_id',
                    'r_penyakitsekarang',
                    'r_penyakitdahulu',
                    'r_alergiobat',
                    'tekanan_darah',
                    'detak_nadi',
                    'obat_diberikan',
                    'nafas',
                    'pernapasan_lainnya',
                    'suhu_tubuh',
                    'tinggi_badan',
                    'berat_badan',
                    'denyut_jantung',
                    'kategori_denyut_nadi'
                ])
                ->andWhere([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
            if (!empty($asmedRecord)) {
                $areaTubuhDanTerapi = PeriksaTubuh::find()
                    ->select([
                        'periksatubuh_id',
                        'asesmenmedis_id',
                        'catatan_tubuh',
                        'bagiantubuh_m.namabagtubuh as area_tubuh',
                        'bagiantubuhdetail_m.nama_bagiantubuh as spesifik_area_tubuh'
                    ])
                    ->join('LEFT JOIN', 'bagiantubuh_m', 'bagiantubuh_m.bagiantubuh_id=periksatubuh_t.bagiantubuh_id')
                    ->join('LEFT JOIN', 'bagiantubuhdetail_m', 'bagiantubuhdetail_m.bagiantubuhdetail_id=periksatubuh_t.bagiantubuhdetail_id')
                    ->andWhere([
                        'asesmenmedis_id' => $asmedRecord['asesmenmedis_id']
                    ])
                    ->asArray()
                    ->all();
                $terapi = '';
                if (!empty($areaTubuhDanTerapi)) {
                    foreach ($areaTubuhDanTerapi as $areaTubuh) {
                        $terapi .= $areaTubuh['area_tubuh'] . ' (' . $areaTubuh['spesifik_area_tubuh'] . ') : ' . $areaTubuh['catatan_tubuh'] . "\n";
                    }
                }
                $oldDiagnose = '';
                if (!empty($asmedRecord['r_penyakitdahulu'])) {
                    $oldDiagnoseArray = json_decode($asmedRecord['r_penyakitdahulu'], true);
                    $totalDiagnose = count($oldDiagnoseArray);
                    foreach ($oldDiagnoseArray as $indexDiagnose => $eachDiagnose) {
                        if (!empty($eachDiagnose['penyakit'])) {
                            $oldDiagnose .= (($indexDiagnose + 1) <= $totalDiagnose && $indexDiagnose > 0 ? ", " : "") . ArrayHelper::getValue($eachDiagnose, 'penyakit') . ' - ' . ArrayHelper::getValue($eachDiagnose, 'terapi') . (!empty($eachDiagnose['tahun']) ? ' (' . $eachDiagnose['tahun'] . ')' : '');
                        }
                    }
                }
                $pernapasan = (ArrayHelper::getValue($asmedRecord, 'nafas') == 1) ? 'Normal' : ArrayHelper::getValue($asmedRecord, 'pernapasan_lainnya');
                $model = [
                    'subject' => 'Riwayat Penyakit Sekarang : ' . $asmedRecord['r_penyakitsekarang'] . "\nRiwayat penyakit dahulu : " . $oldDiagnose . "\nRiwayat terapi sebelumnya : " . $asmedRecord['obat_diberikan'] . "\nRiwayat alergi obat : " . $asmedRecord['r_alergiobat'],
                    'object' => 'Tekanan Darah : ' . $asmedRecord['tekanan_darah'] . "\nNadi : " . $asmedRecord['detak_nadi'] . "\nSuhu : " . $asmedRecord['suhu_tubuh'] . "\nTinggi Badan : " . $asmedRecord['tinggi_badan'] . "\nBerat Badan : " . $asmedRecord['berat_badan'] . "\nDenyut Jantung : " . $asmedRecord['denyut_jantung'] . "\nKategori Denyut Nadi: " . $asmedRecord['kategori_denyut_nadi'],
                    'planning' => $terapi,
                ];
            }
            $model['planning'] .= $this->getPlanningSuggestion($pasienadmisi_id);
        }

        return $model;
    }

    private function br2mn($text)
    {
        $text = preg_replace("/(\r\n|\n|\r)/", "", $text);
        return preg_replace("=&lt;br */?&gt;=i", '<br/>', $text);
    }

    private function getPlanningSuggestion($pasienadmisi_id) {
        $lastDate = Cppt::find()->select(new Expression('max(tgl_cppt)'))
            ->andWhere(['pasienadmisi_id' => $pasienadmisi_id])
            ->andWhere(new Expression("COALESCE((additional_data::json->>'via_soap')::boolean, false) = true"))
            ->andWhere(['is_active' => true])
            ->scalar();
        $query = InfoInstruksiView::find()
            ->select(['tgl_instruksi', 'tipe_instruksi', 'grouping_tipe', 'tindakaninstruksi_nama', 'qty', 'satuankecil_nama', 'catatan_instruksi'])
            ->andWhere(['pasienadmisi_id'=>$pasienadmisi_id])
            ->orderBy(['tgl_instruksi' => SORT_ASC]);

        if(!empty($lastDate)) {
            $query->andWhere(['>', 'tgl_instruksi', $lastDate]);
        }
        $listInstruksi = $query->asArray()->distinct()->all();

        $planning = '';
        foreach ($listInstruksi as $instruksi) {
            if($instruksi['grouping_tipe'] == 'RESEPTUR') {
                $planning .= "Resep - " . $instruksi['tindakaninstruksi_nama'] .
                    " (" . $instruksi['qty']. " " . $instruksi['satuankecil_nama'] . ")\n";
            } else if($instruksi['grouping_tipe'] == 'TINDAKANBMHP' || $instruksi['tipe_instruksi'] == 'LAB_TINDAKAN' ||
                $instruksi['tipe_instruksi'] == 'LAB_PAKET' || $instruksi['tipe_instruksi'] == 'RAD_TINDAKAN' ||
                $instruksi['tipe_instruksi'] == 'RAD_PAKET' || $instruksi['tipe_instruksi'] == '') {
                $planning .= "Pemeriksaan - " . $instruksi['tindakaninstruksi_nama'] . "\n";
            } else if($instruksi['tipe_instruksi'] == 'BED_TINDAKAN') {
                $planning .= "Rencana Operasi - " . $instruksi['tindakaninstruksi_nama'] . "\n";
            } else if($instruksi['grouping_tipe'] == 'TINDAKANDIET') {
                $planning .= "Diet - " . $instruksi['catatan_instruksi'] . "\n";
            } else if($instruksi['grouping_tipe'] == 'PENUNJANGFISIO') {
                $planning .= "Penjadwalan fisioterapi - " . $instruksi['tindakaninstruksi_nama'] . " (" . $instruksi['qty'] . ")\n";
            }
        }


        $query = (new \yii\db\Query())
            ->select(['permintaankonsul_t.ket_konsul', 'pegawai_m.nama_pegawai'])
            ->from('permintaankonsul_t')
            ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = permintaankonsul_t.dokter_id')
            ->leftJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = permintaankonsul_t.pendaftaran_id')
            ->andWhere(['pendaftaran_t.pasienadmisi_id' => $pasienadmisi_id])
            ->orderBy(['permintaankonsul_t.waktu_permintaan' => SORT_DESC]);
        if(!empty($lastDate)) {
            $query->andWhere(['>', 'permintaankonsul_t.waktu_permintaan', $lastDate]);
        }
        $listKonsultasi = $query->all();
        foreach ($listKonsultasi as $konsultasi) {
            $planning .= "Konsultasi - " . $konsultasi['nama_pegawai'] . " " . $konsultasi['ket_konsul'] . "\n";
        }
        return $planning;
    }

    public function actionGetFilterCppt($pendaftaran_id, $term, $type)
    {
        $pend_asal = Pendaftaran::find()
        ->select([
            new \yii\db\Expression("(additional_data::json->'pendaftaranasal_id') AS pend_asal")
        ])
        ->andWhere(['pendaftaran_id' => $pendaftaran_id])
        ->asArray()
        ->one();

        switch ($type) {
            case 'ruangan':
                $dataGroup = SoapRsView::find()
                    ->select([
                        'ruangan_id as id',
                        'ruangan_nama as text',
                    ])
                    ->where(['pendaftaran_id' => $pendaftaran_id]);
                    if ($pend_asal['pend_asal'] != null) {
                        $dataGroup->orWhere(['pendaftaran_id' => $pend_asal['pend_asal']]);
                    }
                    $dataGroup->andWhere(['LIKE', 'LOWER(ruangan_nama)', strtolower($term)]);
                break;
            default:
                $dataGroup = SoapRsView::find()
                    ->select([
                        'pegawai_id as id',
                        'nama_pegawai as text',
                    ])
                    ->where(['pendaftaran_id' => $pendaftaran_id]);
                    if ($pend_asal['pend_asal'] !=  null) {
                        $dataGroup->orWhere(['pendaftaran_id' => $pend_asal['pend_asal']]);
                    }
                    $dataGroup->andWhere(['kelompokpegawai_id' => DocoConstants::KELOMPOK_MEDIS]);
                    $dataGroup->andWhere(['LIKE', 'LOWER(nama_pegawai)', strtolower($term)]);

                break;
        }

        if($this->konfigCpptKosong == TRUE) {
            $dataGroup->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
        }

        return $dataGroup->distinct()->asArray()->all();
    }

    public function actionGetSoapRehabMedic()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $orderBy = $request->get('order', ['tgl_soaprj' => SORT_DESC]);
            $ruangan_id = $request->get('ruangan_id', null);
            $kelompokpegawai_id = $request->get('kelompokpegawai_id');

            $pend_asal = Pendaftaran::find()
                ->select([
                    new \yii\db\Expression("(additional_data::json->'pendaftaranasal_id') AS pend_asal")
                ])
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->asArray()
                ->one();

            $querySoap = SoapRsView::find(true)
                ->select([
                    new \yii\db\Expression('CASE WHEN referred_id IS NOT NULL THEN referred_id ELSE cppt_id END AS cppt_id'),
                    'a_diag_utama',
                    'instruksi',
                    'a_diag_penyerta',
                ])
                ->andWhere(['pendaftaran_id' => $pendaftaran_id])
                ->andWhere(['not', ['pasienadmisi_id' => null]]);

            if ($pend_asal['pend_asal'] != 'null') {
                $querySoap->orWhere(['pendaftaran_id' => $pend_asal['pend_asal']]);
                $querySoap = $this->whereClauseFisioPendaftaranIds($querySoap, $pend_asal['pend_asal'], null);
            }
            if (!empty($ruangan_id)) {
                $querySoap->andWhere([
                    'ruangan_id' => $ruangan_id
                ]);
            }
            if (!empty($kelompokpegawai_id)) {
                $querySoap->andWhere([
                    'kelompokpegawai_id' => $kelompokpegawai_id
                ]);
            }

            if($this->konfigCpptKosong == TRUE) {
                $querySoap->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
            }

            $querySoap->orderBy($orderBy);
            $data = $querySoap->one();
            return [
                'data' => $data,
            ];
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetLatestCppt()
    {
        $request = Yii::$app->request;

        try {
            $last_cppt = SoapRsView::find()
                    ->select([
                      new \yii\db\Expression('CASE WHEN referred_id IS NOT NULL THEN referred_id ELSE cppt_id END AS cppt_id'),
                      'cppt_id AS origin_cppt_id',
                      'subject',
                      'object',
                      'planning',
                      'a_diag_utama',
                      'a_diag_penyerta',
                      'tgl_soaprj AS tgl_cppt']);

            if($request->get('pasien_id', null) == true){
                $last_cppt = $last_cppt->andWhere(['pasien_id' => $request->get('pasien_id')]);
            }

            if($request->get('pendaftaran_id', null) == true){
                $last_cppt = $last_cppt->andWhere(['pendaftaran_id' => $request->get('pendaftaran_id')]);
            }

            if($request->get('is_dokter', null) == true){
                $last_cppt = $last_cppt->andWhere(['kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER]);
            }

            if($request->get('is_nurse', null) == true){
                $last_cppt = $last_cppt->andWhere(['kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_PERAWAT]);
            }

            if($this->konfigCpptKosong == TRUE) {
                $last_cppt->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
            }

            $last_cppt = $last_cppt->orderBy(['tgl_cppt' => SORT_DESC])->asArray()->one();

            return $this->responseJson(200, 'Data berhasil diambil', $last_cppt);


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

    /**
     * get list data baru untuk cpp ranap 
     * 
     */
    public function actionGetListDataCppt()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $pegawai_id = $request->get('pegawai_id');
        $ruangan_id = $request->get('ruangan_id');
        $pasienadmisi_id = $request->get('pasienadmisi_id');
        $cppt_id = $request->get('cppt_id', 0);
        
        return [
            'listRuangan' => $this->getListRuangan($pasien_id),
            'listDiagnosa' => [],
            'listPemberiInstruksi' => [],
            'listDataSigna' => [],
            'isExistTindakan' => 0,
            'listDataApotek' => [],
            'listDataTerapi' => [],
            'listJenisPemakaian' => [],
            'listJenisInstruksi' => $this->getOrSetCache(
                DocoConstants::VAR_CACHE_LOOKUP_JENISINSTRUKSI,
                Lookup::find()->where(['lookup_type' => 'jenis_instruksi']),
                true,
                'jenis_instruksi'
            ),
            'lastCppt' => $this->getLastCppt($pendaftaran_id, $pegawai_id, $pasienadmisi_id),
            'suggestion' => $this->suggestionCppt($pendaftaran_id, $pasienadmisi_id),
            'cpptInactive' => $this->getCpptInactive($pendaftaran_id, $pegawai_id),
            'total_belum_baca' =>  $this->getTotalHasilRadiologi($pendaftaran_id, null),
        ];
    }

    private function whereClauseFisioPendaftaranIdsNew($query, $pendaftaranId, $pasienId)
    {
        $pendaftaranIdsFisioRajal = $this->getPendaftaranIdsFisioRajalNewRanap($pendaftaranId, $pasienId);
        return $query->orWhere(['in', 'pendaftaran_id', $pendaftaranIdsFisioRajal]);
    }

    /** get pendaftaran_id
     *  fisio rajal */
    private function getPendaftaranIdsFisioRajalNewRanap($pendaftaranId, $pasienId)
    {
        $pendaftaranIds = [];  
        $result = Yii::$app->db->createCommand("SELECT programterapirajal_r.pendaftaran_id
            FROM programterapi_t 
            LEFT JOIN (SELECT programterapirajal_id, 
                programterapi_id,
                pendaftaran_id
                FROM programterapirajal_r
            ) programterapirajal_r ON programterapirajal_r.programterapi_id = programterapi_t.programterapi_id
            WHERE programterapi_t.pendaftaran_id = :pendaftaran_id 
            AND programterapirajal_r.pendaftaran_id IS NOT NULL")
        ->bindValue(':pendaftaran_id', $pendaftaranId)
        ->queryAll();

        if (!empty($result)) {
            $pendaftaranIds = ArrayHelper::getColumn($result, 'pendaftaran_id');
        }
        return $pendaftaranIds;
    }

    private function listPenunjangRadiologi($pendaftaran_id, $is_cppt = false)
    {
    	$data_radiologi = (new \yii\db\Query())
        ->select([
            'infopasienradiologi_v.pendaftaran_id',
            'infopasienradiologi_v.pasienmasukpenunjang_id',
            'infopasienradiologi_v.daftartindakan_id',
            'infopasienradiologi_v.tindakanpelayanan_id',
            'infopasienradiologi_v.tglmasukpenunjang',
            'infopasienradiologi_v.no_rujukan',
            'infopasienradiologi_v.daftartindakan_nama',
            'infopasienradiologi_v.tgl_verifikasi',
            'hasilpemeriksaanrad_t.is_deleted',
            'hasilpemeriksaanrad_t.hasilpemeriksaanrad_id',
            'hasilpemeriksaanrad_t.is_hasilkritis',
            'hasilpemeriksaanrad_t.no_hasilrad',
            'infopasienradiologi_v.status_penunjang',
            'statusperiksa_penunjangan.lookup_name AS stat_penunjang',
            'infopasienradiologi_v.status_periksa',
            'statusperiksa_rad.lookup_name AS stat_periksa',
            'infopasienradiologi_v.no_pendaftaran',
            'infopasienradiologi_v.dokter_penunjang',
            'infopasienradiologi_v.ruangan_nama',
            'infopasienradiologi_v.is_hasil',
            'infopasienradiologi_v.tgl_verifikasi',
            'infopasienradiologi_v.status_batal',
            'infopasienradiologi_v.is_read',
            'hasilbridgingradiologi_t.image_link',
        ])
        ->from('infopasienradiologi_v')
        ->leftJoin('hasilpemeriksaanrad_t','infopasienradiologi_v.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND infopasienradiologi_v.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id')
        ->leftJoin('lookup_m statusperiksa_rad','infopasienradiologi_v.status_periksa::integer = statusperiksa_rad.lookup_id')
        ->leftJoin('lookup_m statusperiksa_penunjangan','infopasienradiologi_v.status_penunjang::integer = statusperiksa_penunjangan.lookup_id')
        ->leftJoin('hasilbridgingradiologi_t','hasilbridgingradiologi_t.order_no = (infopasienradiologi_v.no_masukpenunjang || \'-\' || infopasienradiologi_v.tindakanpelayanan_id)')
        ->leftJoin('pasienmasukpenunjang_t', 'infopasienradiologi_v.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id')
        ->where(['infopasienradiologi_v.pendaftaran_id' => $pendaftaran_id])
        ->groupBy(
            'infopasienradiologi_v.pendaftaran_id,
            infopasienradiologi_v.pasienmasukpenunjang_id,
            infopasienradiologi_v.daftartindakan_id,
            infopasienradiologi_v.tindakanpelayanan_id,
            infopasienradiologi_v.tglmasukpenunjang,
            infopasienradiologi_v.no_rujukan,
            infopasienradiologi_v.tgl_verifikasi,
            infopasienradiologi_v.daftartindakan_nama,
            hasilpemeriksaanrad_t.is_deleted,
            hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
            hasilpemeriksaanrad_t.is_hasilkritis,
            infopasienradiologi_v.status_penunjang,
            infopasienradiologi_v.status_penunjang ,
            infopasienradiologi_v.status_periksa,
            statusperiksa_rad.lookup_name ,
            infopasienradiologi_v.no_pendaftaran,
            infopasienradiologi_v.dokter_penunjang,
            infopasienradiologi_v.ruangan_nama,
            infopasienradiologi_v.is_hasil,
            infopasienradiologi_v.tgl_verifikasi,
            infopasienradiologi_v.status_batal,
            statusperiksa_penunjangan.lookup_name,
            hasilbridgingradiologi_t.image_link,
            infopasienradiologi_v.is_read'
        )
        ->orderBy([
            'infopasienradiologi_v.tglmasukpenunjang' => SORT_DESC,
            'infopasienradiologi_v.daftartindakan_nama' => SORT_ASC
        ])
        ->all();

        $total_belum_baca = 0;
        foreach($data_radiologi as $key => $val){
            if($val['is_hasil'] && $val['is_read'] == false && $val['tgl_verifikasi'] != null){
                $total_belum_baca++;
            }
        }
        return $total_belum_baca;
    }

    // Get List Pemberi Instruksi
    public function actionGetListPemberiInstruksi()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        $ruangan_id = $request->get('ruangan_id');
        $instalasi_id = $request->get('instalasi_id');
        $page = $request->get('page', 1);
        $query = DokterView::find()->select(['pegawai_id', 'nama_pegawai'])
        ->where([
            'instalasi_id' => $instalasi_id,
            'ruangan_id' => $ruangan_id
        ]);
        if ($term) {
            $query->andWhere(['ILIKE', 'nama_pegawai', $term]);
        }

        $perpage = 10;
        $limit = 11;
        $offset = ($page - 1) * $perpage;

        $query->offset($offset)->limit($limit);
        $result = $query->asArray()->all();

        return [
            'data' => $result,
        ];
    }

    public function actionDeleteCppt() {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id');
            $cppt_id = $request->post('cppt_id');
            $user_id = $request->post('user_id');
            $tipe = $request->post('tipe');
            $transaction = Yii::$app->db->beginTransaction();
            if($tipe == 'RI') {
                $deleteCppt = Cppt::updateAll(
                [
                    'is_deleted' => true,
                    'deleted_date' => date('Y-m-d H:i:s'),
                    'deleted_by' => $user_id
                ], 'pendaftaran_id = '.$pendaftaran_id.' AND cppt_id = '.$cppt_id.'');
            }
            elseif($tipe == 'RI-SOAPFISIO') {
                $deleteCppt = SoapFisioterapi::updateAll(
                [
                    'is_deleted' => true,
                    'is_edit' => true,
                    'deleted_date' => date('Y-m-d H:i:s'),
                    'deleted_by' => $user_id
                ], 'pendaftaran_id = '.$pendaftaran_id.' AND soapfisioterapi_id = '.$cppt_id.'');
            }

            if($deleteCppt) {
                $this->insertLogActivity($cppt_id, $tipe);
                $transaction->commit();
                return [
                    'status' => 200,
                    'message' => 'SOAP berhasil dihapus',
                ];
            } else {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'SOAP gagal dihapus',
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function insertLogActivity($cppt_id, $tipe)
    {
        $tabel = ($tipe == 'RI') ? 'cppt_t' : 'soapfisioterapi_t';
        $tabelId = ($tipe == 'RI') ? 'cppt_id' : 'soapfisioterapi_id';
        $getCppt = Yii::$app->db->createCommand("
            SELECT subject,object,a_diag_utama,a_diag_penyerta,planning
            FROM {$tabel} WHERE {$tabelId} = {$cppt_id}
        ")->queryOne();
        
        $logModel = new LogActivityR;
        $logModel->transaksi_id = $cppt_id;
        $logModel->tgl = date('Y-m-d H:i:s');
        $logModel->tipe = 'CPPT-'.$tipe;
        $logModel->aksi = 'hapus';

        $diagnosaUtama = ArrayHelper::getValue($getCppt, 'a_diag_utama');
        $diagnosaPenyerta = ArrayHelper::getValue($getCppt, 'a_diag_penyerta');
        if(!empty($diagnosaUtama)) {
            $diagnosaUtama = json_decode($diagnosaUtama, true);
            $diagnosaUtama = ArrayHelper::getValue($diagnosaUtama, 'text');
        }
        
        $diagnosaPenyertaText = '';
        if($diagnosaPenyerta) {
            $diagnosaPenyerta = json_decode($diagnosaPenyerta, true);
            foreach ($diagnosaPenyerta as $key => $value) {
                $diagnosaPenyertaText .= '<p> - ' .ArrayHelper::getValue($value, 'text') .'</p>';
            }
        }
        $keterangan = '<p> Subjektif : '. ArrayHelper::getValue($getCppt, 'subject') .'</p>';
        $keterangan .= '<p> Objektif : '. ArrayHelper::getValue($getCppt, 'object') .'</p>';
        $keterangan .= '<p> Asesmen Diagnosa Utama : '. $diagnosaUtama .'</p>';
        $keterangan .= '<p> Asesmen Diagnosa Penyerta : '. $diagnosaPenyertaText .'</p>';
        $keterangan .= '<p> Planning : '. ArrayHelper::getValue($getCppt, 'planning') .'</p>';
        $logModel->keterangan = $keterangan;
        if($logModel->validate()) {
            $logModel->save();
        }
    }
}
