<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-13 16:33:39
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\web\UploadedFile;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;

use Doco\rabbitmq\RabbitBgProcess;

use Doco\models\SoapRsView;
use app\modules\v1\models\AsesmenMedisRD;
use app\modules\v1\models\AsesmenPerawatRD;
use app\modules\v1\models\PeriksaTubuh;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\CpptView;
use app\modules\v1\models\CpptDetailView;
use app\modules\v1\models\DiagnosaView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\InfoResepturView;
use app\modules\v1\models\InfoResepturDetailView;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Instruksi;
use app\modules\v1\models\InstruksiTindakan;
use app\modules\v1\models\InstruksiTindakanBmhp;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\PaketDetailView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\InfoStokObatAlkesFn;
use app\modules\v1\models\RiwayatInstruksiTindakanView;
use app\modules\v1\models\Racikan;
use app\modules\v1\models\KonfigAntrianView;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\OrderPenunjangView;
use app\modules\v1\models\TindakanBmhpView;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Implementasi;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\ResepturRacikan;
use app\modules\v1\models\Triase;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\UploadPayload;
use SirsCore\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use app\modules\v1\models\TindakanPelayananT;
use Doco\models\ResumeMedisRIT;


use app\modules\v1\payload\ParamModel;
use Doco\Services\KasirService;
use Doco\Notifications\FarmasiNotification;
use Doco\Traits\TindakanBmhpTrait;
use Doco\Traits\GeneralResepturTrait;
use Doco\Services\FarmasiService;
use SirsCore\models\LogActivityR;
use Doco\Services\PlafonBpjsService;

class AsesmenDpjpController extends DocoActiveController
{
    public $messageBroker = [
        'create-reseptur' => [
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
    use TindakanBmhpTrait;
    use GeneralResepturTrait;

    public $transactionClass;
    public $cpptModel;
    public $asmedModel;
    public $askepModel;
    public $modelClass = 'app\modules\v1\models\Cppt';
    public $konfigCpptKosong;
    public $konfigEditCpptCoret;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["dpjp-create-terapi-tindakan"] = ["POST"];
        $verbs["create-soap"] = ["POST"];
        $verbs["get-soap"] = ["GET"];
        return $verbs;
    }

    public function init()
    {
        parent::init();
        $this->type = 'RD';
        $this->cpptModel = (new CpptView);
        $this->asmedModel = (new AsesmenMedisRD);
        $this->askepModel = (new AsesmenPerawatRD);

        $this->konfigSystemCppt();
    }
    /**
     *
     * @see Fungsi get list data asesmen dpjp
     * @return array list data asesmen dpjp
     *
     */

    public function konfigSystemCppt() {
        $konfigCppt = KonfigSystem::find()->select(['is_hide_cppt_kosong','is_edit_cppt_coret'])->asArray()->one();
        $this->konfigCpptKosong = $konfigCppt['is_hide_cppt_kosong'];
        $this->konfigEditCpptCoret = $konfigCppt['is_edit_cppt_coret'];
    }

    public function actionGetListDataAsesmenDpjp()
    {
        try {
            $request = Yii::$app->request;
            $pegawai_id = $request->get('pegawai_id');
            $ruangan_id = $request->get('ruangan_id');

            return [
                'pegawai' => $this->getPegawai($pegawai_id, $ruangan_id),
                'listPemberiInstruksi' => $this->getPemberiInstruksi(DocoConstants::INST_ID_RD, $ruangan_id),
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

    /**
     *
     * @see Fungsi get list data diagnosa
     * @return array list data diagnosa
     *
     */
    public function actionGetListDiagnosa()
    {
        try {
            $request = Yii::$app->request;
            $q = $request->get('q');

            $listDiagnosa = (new \yii\db\Query())
                ->select([
                    'diagnosa_id as id',
                    'diagnosa_nama AS text'
                ])
                ->from(DiagnosaView::tableName())
                ->where(['like', 'diagnosa_nama', $q])
                ->all();

            return [
                'data' => $listDiagnosa
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
     * @see Fungsi get data obatalkes ruangan
     * @return array, activeQueryRecords
     *
     */
    public function actionListObatAlkes()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('ruangan_id');
            $q = $request->get('keyword');
            $page = $request->get('page');

            $data = InfoStokObatAlkesFn::find()
                ->select([
                    'ruangan_id',
                    'satuanbesar_id',
                    'satuanbesar_nama',
                    'satuankecil_id',
                    'satuankecil_nama',
                    'qty_dipesan',
                    'qty_keluar',
                    'qty_masuk',
                    'qty_stok',
                    'qty_tersedia',
                    'hargajual',
                    'harganetto',
                    'obatalkes_nama',
                    'obatalkes_id',
                ])
                ->where(['ruangan_id' => $ruangan_id])
                ->andWhere(['LIKE', 'LOWER(obatalkes_nama)', strtolower($q)])
                ->orderBy(['obatalkes_nama' => SORT_ASC])
                ->offset(($page - 1) * 10)->limit(11)
                ->asArray()->all();

            return [
                'data' => $data,
                'payload' => $request->get(),
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

    /**
     *
     * @see Fungsi create soap
     * @return array
     *
     */
    public function actionCreateSoap($cppt_id = null, $pegawai_id = null)
    {
        $this->transactionClass = Yii::$app->db->beginTransaction();
        try {
            return $this->soapCreateOrUpdate($cppt_id);
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
     *
     * @see Fungsi create verbal order
     * @return array
     *
     */
    public function actionCreateVerbalOrder()
    {
        try {
            $request = Yii::$app->request;
            $model = new Cppt;
            $post = $request->post();
            $model->scenario = 'verbalorder';
            if (isset($post['tgl_cppt'])) {
                unset($post['tgl_cppt']);
            }
            $model->attributes = $post;
            $model->tgl_cppt = date('Y-m-d H:i:s');

            if ($model->validate()) {
                if ($post) {
                    if ($model->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors, 'CpptForm');
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

    /**
     *
     * @see Fungsi get pegawai
     * @var params integer id = primary key pegawai id dan ruangan_id
     * @return array, activeQueryRecords
     *
     */
    private function getPegawai($pegawai_id, $ruangan_id)
    {
        try {
            $model = PegawaiView::find()->where(['pegawai_id' => $pegawai_id])->andWhere(['ruangan_id' => $ruangan_id])->one();

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

    /**
     *
     * @see Fungsi get list pemberi instruksi
     * @var params integer instalasi_id = primary key instalasi id & integer ruangan_id = ruangan id
     * @return array, activeQueryRecords
     *
     */
    private function getPemberiInstruksi($instalasi_id, $ruangan_id)
    {
        try {
            $model = DokterView::find()->where(['instalasi_id' => $instalasi_id, 'ruangan_id' => $ruangan_id])->all();

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

    public function actionGetDataAsesmenDpjp()
    {
        // Try catch
        // try {
            // Request
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $orders = $request->get('order', []);
            $ruangan_id = $request->get('filterruangan_id', null);
            $pegawai_id = $request->get('filterpegawai_id', null);
            $tgl_cppt = $request->get('tgl_cppt', null);
            $length = $request->get('length', null);
            $start = $request->get('start', null);
            $orderSoap = (new DocoConstansId)->actionGetAdditional('orderby_soap');
            

            // Get No Rekam Medik
            $query = CpptView::find()
                ->select(['no_rekam_medik'])
                ->andWhere(['pendaftaran_id' => $pendaftaran_id]);
            $noRM = $query->asArray()->one();

            if (!$noRM) {
                return [
                    'data' => [],
                    'totalCount' => 0
                ];
            }

            $queryDpjp = SoapRsView::find($this->konfigEditCpptCoret)
                ->select([
                    new \yii\db\Expression('CASE WHEN referred_id IS NOT NULL THEN referred_id ELSE cppt_id END AS cppt_id'),
                    'cppt_id AS origin_cppt_id',
                    'ruangan_nama',
                    'no_tempattidur',
                    'kamarruangan_nokamar',
                    'tgl_soaprj AS tgl_cppt',
                    'kelompokpegawai_id',
                    'kelompokpegawai_nama',
                    'nama_pegawai',
                    'subject',
                    'object',
                    'planning',
                    'a_diag_utama',
                    'a_diag_penyerta',
                    'instruksi',
                    'pegawai_instruksi',
                    'catatan_dokter',
                    'catatan_perawat',
                    'is_instruksi_pulang',
                    'dokteradmisi_id',
                    'pemberi_instruksi_id',
                    'is_verifikasi_verbal',
                    'pegawai_verifikasi_verbal',
                    'tgl_verif_verbal',
                    'is_verifikasi',
                    'is_deleted',
                    'tgl_verifikasi',
                    'pegawai_verifikasi',
                    'pendaftaran_id',
                    'pegawai_id',
                    'pasienadmisi_id',
                    'is_lab',
                    'is_rad',
                    'is_reseptur',
                    'tipe',
                    'spesialis_nama',
                    'is_deleted',
                    'pegawai_update_nama',
                    'created_date',
                    'is_icd_x',
                    'is_verbal_order'
                ]);
            $queryDpjp->andWhere(['pendaftaran_id' => $pendaftaran_id]);
            $queryDpjp->andWhere(['tipe' => 'RD']);
            if (empty($orderBy)) {
                $orderBy ='tgl_soaprj '.$orderSoap.',created_date '.$orderSoap;
                    $queryDpjp = $queryDpjp->orderBy($orderBy);
            } else {
                    $orderBy = $orderBy;
            }

            if($this->konfigCpptKosong == TRUE) {
                $queryDpjp->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL OR is_verbal_order = true)");
            }

            if (
                ($kelompokpegawai_id = $request->get('filter_kelompokpegawai_id')) &&
                in_array($kelompokpegawai_id, [
                    DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                    DocoConstants::KELOMPOK_PEGAWAI_DOKTER
                ])
            ) {
                $queryDpjp->andWhere([
                    'kelompokpegawai_id' => $kelompokpegawai_id,
                ]);
            }
            if (!empty($ruangan_id)) {
                $queryDpjp->andWhere([
                    'ruangan_id' => $ruangan_id
                ]);
            }

            if (!empty($pegawai_id)) {
                $queryDpjp->andWhere([
                    'pegawai_id' => $pegawai_id
                ]);
            }

            if(!empty($tgl_cppt)){
                $start = date('Y-m-d 00:00:00');
                $end = date('Y-m-d 23:59:00');
                $explode = explode("-", $tgl_cppt);
                if (count($explode) == 2) {
                    $start = date_format(date_create_from_format('d/m/Y', $explode[0]), 'Y-m-d').date(' 00:00:00');
                    $end = date_format(date_create_from_format('d/m/Y', $explode[1]), 'Y-m-d').date(' 23:59:59');
                }
                $queryDpjp->andWhere(['between', 'tgl_soaprj', $start, $end]);
            }

            if (!empty($length)) {
                $queryDpjp->limit($length);
            }

            if (!empty($start)) {
                $queryDpjp->offset($start);
            }

            $data = $queryDpjp->asArray()->all();
            $totalData = count($data);
            return [
                'data' => $data,
                'totalCount' => count($data),
                'load_more' => $totalData == $length ? true : false,
            ];
        // } catch (\yii\db\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     $this->logError($e);
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // } catch (\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     $this->logError($e);
        //     return [
        //         'message' => $e->getMessage()
        //     ];
        // }
    }

    public function actionAmbilDataInstruksi()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $cppt_id = $request->get('cppt_id', 0);
            $instruksi_id = $request->get('instruksi_id', 0);

            $data_instruksi = null;
            if ($instruksi_id != 0) {
                $data_instruksi = Instruksi::find()->where(['instruksi_id' => $instruksi_id])->asArray()->one();
            }

            return [
                'list_jenis_instruksi' => Lookup::find()->where(['lookup_type' => 'jenis_instruksi'])->all(),
                'data_instruksi' => $data_instruksi
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

    public function getRiwayatTindakanBmhp($pendaftaran_id, $cppt_id, $instruksi_id)
    {
        $data = RiwayatInstruksiTindakanView::find()
            ->where([
                'pendaftaran_id' => $pendaftaran_id,
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
        return $data_riwayat;
    }

    public function actionBundleDataReseptur()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $ruangan_id = $request->get('ruangan_id');
            $kelaspelayanan_id = $request->get('kelaspelayanan_id');
            $penjamin_id = $request->get('penjamin_id');
            $cppt_id = $request->get('cppt_id');
            $pegawai_id = $request->get('pegawai_id');
            $instruksi_id = $request->get('instruksi_id');

            if (isset($instruksi_id) && $instruksi_id != '') {
                $reseptur = InfoResepturView::find()->where(['instruksi_id' => $instruksi_id])->one();

                return [
                    'listDataApotek' => $this->getOrSetCache(DocoConstants::VC_R_I, $this->getRuanganInstalasi(DocoConstants::VAR_I_A), true, DocoConstants::VAR_I_A),
                    'listDataSigna' => $this->getOrSetCache(DocoConstants::VC_SO, SignaObat::find()),
                    'dataCppt' => CpptView::find()->where(['cppt_id' => $cppt_id])->one(),
                    'pegawai' => PegawaiView::find()->where(['pegawai_id' => $pegawai_id, 'ruangan_id' => $ruangan_id])->one(),
                    'lastReseptur' => InfoResepturView::find()->where(['pendaftaran_id' => $pendaftaran_id])->orderBy(['reseptur_id' => SORT_DESC])->limit(1)->one(),
                    'reseptur' => $reseptur,
                    'resepturDetail' => InfoResepturDetailView::find()->where(['reseptur_id' => $reseptur->reseptur_id])->all(),
                    'obatalkes' => InfoStokObatAlkesFn::find()->where(['ruangan_id' => $reseptur['ruangan_id']])->all()
                ];
            } else {
                return [
                    'listDataApotek' => $this->getOrSetCache(DocoConstants::VC_R_I, $this->getRuanganInstalasi(DocoConstants::VAR_I_A), true, DocoConstants::VAR_I_A),
                    'listDataSigna' => $this->getOrSetCache(DocoConstants::VC_SO, SignaObat::find()),
                    'dataCppt' => CpptView::find()->where(['cppt_id' => $cppt_id])->one(),
                    'pegawai' => PegawaiView::find()->where(['pegawai_id' => $pegawai_id, 'ruangan_id' => $ruangan_id])->one(),
                    'lastReseptur' => InfoResepturView::find()->where(['pendaftaran_id' => $pendaftaran_id])->orderBy(['reseptur_id' => SORT_DESC])->limit(1)->one(),

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
     * @controller actionCetakReseptur
     * @attribute #table_detail# => table
     **/
    public function actionCetakReseptur()
    {
        $pendaftaran_id = DocoHelpers::decrypt(Yii::$app->request->get('pendaftaran_id'));
        $pegawai_id = Yii::$app->request->get('pegawai_id');
        $ruangan_id = Yii::$app->request->get('ruangan_id');
        $instruksi_id = Yii::$app->request->get('instruksi_id');

        $reseptur = InfoResepturView::find()->where([
            'pendaftaran_id' => $pendaftaran_id,
            'instruksi_id' => $instruksi_id,
        ])
            ->orderBy(['reseptur_id' => SORT_DESC])
            ->limit(1)
            ->one();

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

        $resepturDetail = InfoResepturDetailView::find()->where(['reseptur_id' => $reseptur->reseptur_id])->asArray()->all();
        $pegawai = PegawaiView::find()->where(['pegawai_id' => $pegawai_id, 'ruangan_id' => $ruangan_id])->one();
        $print = new DocoPrint();

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

    public function actionBundleDataPenunjang()
    {
        try {
            return [
                'listInstalasiPenunjang' => $this->getListInstalasiPenunjang(),
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
                    return [
                        'status' => 422,
                        'text' => Yii::t('app', 'Data tidak dapat dihapus!'),
                        'message' => Yii::t('app', 'Tipe Instruksi Tidak Dikenal'),
                    ];
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

    private function getDataPaketRuangan(
        $ruangan_id = null,
        $kelaspelayanan_id = null,
        $penjamin_id = null,
        $komponentarif_id = DocoConstants::KOMPONEN_TARIF
    ) {
        $request = Yii::$app->request;
        $keyword = $request->get('keyword');
        $keyword = !empty($keyword) ? strtolower($keyword) : '';
        $result = [];

        try {
            $where = '';
            if (!empty($keyword)) {
                $where = "AND LOWER(tipepaket_nama) LIKE '%$keyword%' ";
            }
            $result = Yii::$app->db->createCommand('SELECT tipepaket_id,
                    tipepaket_nama, harga_tariftindakan, is_akomodasi,
                    persencyto_tindakan, tariftindakan_id, komponentarif_id
                    FROM tariftotalrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id)
                    WHERE tipepaket_id IS NOT NULL
                    ' . $where . '
                    ORDER BY tipepaket_nama ASC')
                ->bindParam(':ruangan_id', $ruangan_id)
                ->bindParam(':penjamin_id', $penjamin_id)
                ->bindParam(':kelaspelayanan_id', $kelaspelayanan_id)
                ->queryAll();

            return $result;
        } catch (Exception $e) {
            return $result;
        }

        // $result = InfoTarifRs::find()->select([
        //     'infotarifrs_v.tipepaket_id',
        //     'infotarifrs_v.tipepaket_nama',
        //     'daftartindakan_id',
        //     'daftartindakan_nama',
        //     'harga_tariftindakan',
        //     'is_akomodasi',
        //     'persencyto_tindakan',
        //     'tariftindakan_id',
        //     'komponentarif_id',
        // ]);

        // if ($ruangan_id){
        //     $result->andWhere(['infotarifrs_v.ruangan_id' => $ruangan_id]);
        // }

        // if ($kelaspelayanan_id){
        //     $result->andWhere(['infotarifrs_v.kelaspelayanan_id' => $kelaspelayanan_id]);
        // }

        // if ($penjamin_id){
        //     $result->andWhere(['infotarifrs_v.penjamin_id' => $penjamin_id]);
        // }

        // if ($komponentarif_id){
        //     $result->andWhere(['infotarifrs_v.komponentarif_id' => $komponentarif_id]);
        // }

        // $result->andWhere('infotarifrs_v.tipepaket_id IS NOT NULL');

        // return $result;
    }

    private function getDataTindakanRuangan(
        $ruangan_id = null,
        $kelaspelayanan_id = null,
        $penjamin_id = null,
        $komponentarif_id = DocoConstants::KOMPONEN_TARIF
    ) {
        $request = Yii::$app->request;
        $keyword = $request->get('keyword');
        $keyword = !empty($keyword) ? strtolower($keyword) : '';
        $result = [];

        try {
            $where = '';
            if (!empty($keyword)) {
                $where = "AND LOWER(daftartindakan_nama) LIKE '%$keyword%' ";
            }

            $result = Yii::$app->db->createCommand('SELECT daftartindakan_id,
                    daftartindakan_nama, harga_tariftindakan, is_akomodasi,
                    persencyto_tindakan, tariftindakan_id, komponentarif_id
                    FROM tariftotalrs_fn(:ruangan_id,:penjamin_id,:kelaspelayanan_id)
                    WHERE tipepaket_id IS NULL
                    ' . $where . '
                    ORDER BY daftartindakan_nama ASC')
                ->bindParam(':ruangan_id', $ruangan_id)
                ->bindParam(':penjamin_id', $penjamin_id)
                ->bindParam(':kelaspelayanan_id', $kelaspelayanan_id)
                ->queryAll();

            return $result;
        } catch (Exception $e) {
            return $result;
        }
        // $result = InfoTarifRs::find()->select([
        //     'daftartindakan_id',
        //     'daftartindakan_nama',
        //     'harga_tariftindakan',
        //     'is_akomodasi',
        //     'persencyto_tindakan',
        //     'tariftindakan_id',
        //     'komponentarif_id',
        // ]);

        // if ($ruangan_id){
        //     $result->andWhere(['ruangan_id' => $ruangan_id]);
        // }

        // if ($kelaspelayanan_id){
        //     $result->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
        // }

        // if ($penjamin_id){
        //     $result->andWhere(['penjamin_id' => $penjamin_id]);
        // }

        // if ($komponentarif_id){
        //     $result->andWhere(['komponentarif_id' => $komponentarif_id]);
        // }

        // $result->andWhere('tipepaket_id IS NULL');

        // return $result;
    }

    private function getPegawaiRuangan($ruangan_id = null, $kelompokpegawai = null)
    {
        $sql = "
            SELECT
                ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.*
            FROM ruanganpegawai_mp
            JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id
            JOIN kelompokpegawai_m ON kelompokpegawai_m.kelompokpegawai_id = pegawai_m.kelompokpegawai_id
            WHERE ruanganpegawai_mp.is_deleted = FALSE
        ";

        if ($ruangan_id) {
            $sql .= " AND ruanganpegawai_mp.ruangan_id =" . $ruangan_id;
        }

        if ($kelompokpegawai) {
            $sql .= " AND kelompokpegawai_m.kelompokpegawai_namalainnya ='" . $kelompokpegawai . "'";
        }

        $result = RuanganPegawai::findBySql($sql);

        return $result;
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

    public function actionDpjpCreateTerapiTindakan()
    {
        $request = Yii::$app->request;
        $data_instruksi = $request->post('instruksi');
        $pendaftaranId = $request->post('pendaftaran_id');
        $depoId = $request->post('depo_id');
        $data_tindakan = $request->post('tindakan', []);
        $data_bmhpalkes = $request->post('bmhp', []);
        $perawat_cppt = $request->post('perawat_cppt');
        $dokterdpjp_id = $request->post('dokterdpjp_id');

        $qPendaftaran = Pendaftaran::find()->select([
            'pasienadmisi_id',
            'no_pendaftaran',
            'ruangan_id',
            'kelaspelayanan_id',
            'jeniskasuspenyakit_id',
            'instalasi_id',
            'penjamin_id',
            'is_close_bill'
        ])->andWhere([
            'pendaftaran_id' => $pendaftaranId
        ])->asArray()->one();

        if (empty($qPendaftaran)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => DocoMessages::ERR_MESSAGE_PENDAFTARAN_NOT_EXIST
            ]);
        }

        $isCloseBill = isset($qPendaftaran['is_close_bill']) ? $qPendaftaran['is_close_bill'] : false;
        if($isCloseBill) {
            $errorMessage = 'Pasien sudah dilakukan proses Lock Bill.';
            return $this->responseJson(400, $errorMessage);
        }

        $pasienadmisi_id = $qPendaftaran['pasienadmisi_id'];
        $ruangan_id = $qPendaftaran['ruangan_id'];
        $kelaspelayanan_id = $qPendaftaran['kelaspelayanan_id'];
        $jeniskasuspenyakit_id = $qPendaftaran['jeniskasuspenyakit_id'];
        $instalasi_id = $qPendaftaran['instalasi_id'];
        $penjamin_id = $qPendaftaran['penjamin_id'];
        $no_pendaftaran = $qPendaftaran['no_pendaftaran'];

        /** case pemotongan stok */
        $stokRuangan = ($depoId == $ruangan_id) ? true : false;


        // Declare sme variables
        $instruksitindakan = [];
        $instruksibmhp = [];
        $tindakanKomponen = [];

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if (!empty($data_instruksi['instruksi_id'])) {
                $modelInstruksi = Instruksi::find(true)->where([
                    'instruksi_id' => $data_instruksi['instruksi_id']
                ])->one();
            } else {
                if (isset($data_instruksi['instruksi_id'])) unset($data_instruksi['instruksi_id']);
                $modelInstruksi = new Instruksi;
            }
            $modelInstruksi->attributes = $data_instruksi;
            $modelInstruksi->jenis_instruksi = DocoConstants::J_INST_TIND;
            // Parse cppt id dan tgl instruksi
            $modelInstruksi->cppt_id = (int) $modelInstruksi->cppt_id;
            $modelInstruksi->tgl_instruksi = date('Y-m-d H:i:s');

            $pegawai_cppt = $dokterdpjp_id;
            if ($perawat_cppt != null) {
                $pegawai_cppt = $perawat_cppt;
            }
            // generate cppt
            if (empty($data_instruksi['cppt_id'])) {
                $getCppt = AllowController::actionCreateSoap($request->post('ruangan_id'), $pendaftaranId, $pegawai_cppt, $request->post('pasien_id'));
                $modelInstruksi->cppt_id = $getCppt;
            }

            if (!$modelInstruksi->save()) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $modelInstruksi->errors
                ]);
            }

            $id_instruksi = $modelInstruksi->getPrimaryKey();
            $id_cppt = $modelInstruksi->cppt_id;
            $list_idInstruksiTindakan = [];
            $tindakan = $paket = [];

            foreach ($data_tindakan as $key => $value) {
                if (isset($value['komponentarif_id'])) unset($value['komponentarif_id']);

                if (isset($value['is_ubah_deleted']) && $value['is_ubah_deleted'] == 1) {
                    if (isset($value['instruksitindakan_id'])) {
                        (new InstruksiTindakan)->delete([
                            'instruksitindakan_id' => $value['instruksitindakan_id']
                        ]);
                    }
                } else {
                    if (!isset($value['instruksitindakan_id'])) {
                        if (isset($value['tipe']) && isset($value['tipepaket_id'])) {
                            if ($value['tipe'] == 'paket') {
                                if (isset($value['daftartindakan_id'])) unset($value['daftartindakan_id']);
                                $paket[] = $value['tipepaket_id'];
                            } else {
                                $tindakan[] = $value['daftartindakan_id'];
                            }
                        }
                        $value['qty_sisa'] = 0;
                        $value['instruksi_id'] = $id_instruksi;
                        $value['pasienadmisi_id'] = $pasienadmisi_id;
                        $value['status_implementasi'] = (string) DocoConstants::IMPLEMENTASI_SUDAH_IMPLEMENTASI;
                        $value['tgl_tindakan'] = date('Y-m-d H:i:s');
                        $mInstruksiTindakan = new InstruksiTindakan;
                        $mInstruksiTindakan->attributes = $value;
                        $mInstruksiTindakan->pasienadmisi_id = 0;

                        if (!$mInstruksiTindakan->save()) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                                'data' => $mInstruksiTindakan->errors
                            ]);
                        }

                        if (isset($value['id_instruksi_tindakan']) && $value['id_instruksi_tindakan'] != '' && $value['id_instruksi_tindakan'] != 0) {
                            $list_idInstruksiTindakan[$value['id_instruksi_tindakan']] = $mInstruksiTindakan->getPrimaryKey();
                        }
                    } else {
                        $mInstruksiTindakanUpdate = InstruksiTindakan::find(true)->where([
                            'instruksitindakan_id' => $value['instruksitindakan_id']
                        ])->one();

                        if ($value['qty'] > $mInstruksiTindakanUpdate->qty) {
                            $ditambahkan = $value['qty'] - $mInstruksiTindakanUpdate->qty;
                            $mInstruksiTindakanUpdate->qty_sisa = $mInstruksiTindakanUpdate->qty_sisa + $ditambahkan;
                            $mInstruksiTindakanUpdate->qty = $mInstruksiTindakanUpdate->qty + $ditambahkan;

                            if (!$mInstruksiTindakanUpdate->update()) {
                                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                                    'data' => $mInstruksiTindakanUpdate->errors
                                ]);
                            }
                        }
                    }
                }
            }
            $detailTrans = [];
            foreach ($data_bmhpalkes as $key => $value) {
                if (isset($value['is_ubah_deleted']) && $value['is_ubah_deleted'] == 1) {
                    if (isset($value['instruksitindakanbmhp_id'])) {
                        // if ($stokRuangan) {
                        //       $stokObatR = StokObatAlkesR::find()
                        //                 ->where(['obatalkes_id' => $value['obatalkes_id']])
                        //                 ->andWhere(['ruangan_id' => $ruangan_id])
                        //                 ->one();

                        //         if (empty($stokObatR)) {
                        //             throw new \yii\base\ErrorException("Info Stok Obat Tidak Ada", 500);
                        //         }

                        //         $stokObatR->qty_tersedia = (int) $stokObatR->qty_tersedia +  (int) $value['qty_oa'];
                        //         $stokObatR->qty_dipesan = (int) $stokObatR->qty_dipesan -  (int) $value['qty_oa'];

                        //         if (!$stokObatR->update()) {
                        //             return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        //                 'data' => $stokObatR->errors
                        //             ]);
                        //         }
                        // }
                        (new InstruksiTindakanBmhp)->delete([
                            'instruksitindakanbmhp_id' => $value['instruksitindakanbmhp_id']
                        ]);
                        continue;
                    }
                }

                if (isset($value['instruksitindakanbmhp_id']) && $value['instruksitindakanbmhp_id'] != '') {
                    $mInstruksiTindakanBmhpUpdate = InstruksiTindakanBmhp::find(true)->where([
                        'instruksitindakanbmhp_id' => $value['instruksitindakanbmhp_id']
                    ])->one();

                    $is_update_bmhp = false;

                    if ($value['daftartindakan_id'] == '') {
                        $is_update_bmhp = true;
                        $mInstruksiTindakanBmhpUpdate->daftartindakan_id = null;
                        $mInstruksiTindakanBmhpUpdate->instruksitindakan_id = null;
                    }

                    if ($value['qty'] > $mInstruksiTindakanBmhpUpdate->qty) {
                        $is_update_bmhp = true;
                        $ditambahkan = $value['qty'] - $mInstruksiTindakanBmhpUpdate->qty;
                        $mInstruksiTindakanBmhpUpdate->qty_sisa = $mInstruksiTindakanBmhpUpdate->qty_sisa + $ditambahkan;
                        $mInstruksiTindakanBmhpUpdate->qty = $mInstruksiTindakanBmhpUpdate->qty + $ditambahkan;
                        /** Pasang Kondisi Disini */
                        // di hide dulu
                        /*
                        if ($stokRuangan) {
                            $stokObatR = StokObatAlkesR::find()
                                    ->where(['obatalkes_id' => $value['obatalkes_id']])
                                    ->andWhere(['ruangan_id' => $ruangan_id])
                                    ->one();

                            if (empty($stokObatR)) {
                                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                                    'text' => "Info Stok Obat Tidak Ada"
                                ]);
                            }
                            $stokObatR->qty_tersedia = (int) $stokObatR->qty_tersedia -  (int) $ditambahkan;
                            $stokObatR->qty_dipesan = (int) $stokObatR->qty_dipesan +  (int) $ditambahkan;

                            if(!$stokObatR->update()){
                                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                                    'data' => $stokObatR->errors
                                ]);
                            }
                        }
                        */
                    }

                    if ($is_update_bmhp) {
                        if (!$mInstruksiTindakanBmhpUpdate->update()) {
                            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                                'data' => $mInstruksiTindakanBmhpUpdate->errors
                            ]);
                        }
                    }
                    continue;
                }

                $value['instruksi_id'] = $id_instruksi;
                $value['pasienadmisi_id'] = 0;
                $value['tgl_pelayanan'] = date('Y-m-d H:i:s');
                $value['status_implementasi'] = '455';
                $value['kelaspelayanan_id'] = $kelaspelayanan_id;
                $value['instalasi_id'] = $instalasi_id;
                $value['jeniskasuspenyakit_id'] = $jeniskasuspenyakit_id;

                if (empty($value['obatalkes_id'])) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => "Obat Alkes Id Tidak Di set"
                    ]);
                }

                $infoObat = $connection->createCommand("
                    SELECT
                        obatalkes_nama,
                        obatalkes_id,
                        ruangan_id,
                        obatalkes_kode,
                        qty_tersedia,
                        satuankecil_id,
                        satuankecil_nama,
                        hargaygdipakai,
                        harganetto_ygdipakai,
                        harganetto_ygdipakai as harganetto,
                        jml_margin as jmlmargin,
                        jml_discount as jmldiscount,
                        jml_ppn as jmlppn,
                        persen_ppn as persenppn,
                        persen_disc as persendiscount,
                        persen_margin as persenmargin
                    FROM infostokobatalkes_fnr_new(:penjamin_id, :kelas_id, :ruangan_id)
                    WHERE obatalkes_id = :obatalkes_id
                ")
                    ->bindValue(':penjamin_id', $penjamin_id)
                    ->bindValue(':kelas_id', $kelaspelayanan_id)
                    ->bindValue(':ruangan_id', $depoId)
                    ->bindValue(':obatalkes_id', $value['obatalkes_id'])
                    ->queryOne();

                if (empty($infoObat)) {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                        'text' => $value['obatalkes_id'] . " Info Obat Tidak Ada"
                    ]);
                }

                $value['satuankecil_id'] = $infoObat['satuankecil_id'];
                $value['ruangan_id'] = $depoId;
                $value['is_ditagihkan'] = isset($value['is_ditagihkan']) && $value['is_ditagihkan'] != '' ? $value['is_ditagihkan'] : false;
                if ($value['is_ditagihkan'] == 0 || $value['is_ditagihkan'] == false) {
                    $value['harga_netto'] = 0;
                    $value['harga_jualsatuan'] = 0;
                    $value['harga_jumlah'] = 0;
                } else {
                    $value['harga_netto'] = $infoObat['harganetto_ygdipakai'];
                    $value['harga_jualsatuan'] = ceil($infoObat['hargaygdipakai']);
                    $value['harga_jumlah'] = $value['harga_jualsatuan'] * $value['qty'];
                }

                $value['additional_data'] = json_encode([
                    'satuaninput_id' => $infoObat['satuankecil_id'],
                    'satuan_input' => $infoObat['satuankecil_nama'],
                    'satuan_konversi' => $infoObat['satuankecil_nama'],
                    'nilai_konversi' => 1,
                    'jml_konversi' => $value['qty'],
                    'satuan_penyimpanan' => $infoObat['satuankecil_nama'],
                ]);

                $value['qty_sisa'] = $stokRuangan ? $value['qty'] : 0;
                if (isset($value['id_instruksi_tindakan']) && $value['id_instruksi_tindakan'] != '' && $value['id_instruksi_tindakan'] != 0) {
                    if (isset($list_idInstruksiTindakan[$value['id_instruksi_tindakan']])) {
                        $value['instruksitindakan_id'] = $list_idInstruksiTindakan[$value['id_instruksi_tindakan']];
                    }
                }
                $instruksibmhp[] = $value;
                /** Pasang Kondisi Disini, tapi di comment */
                /*
                if ($stokRuangan) {
                    $stokObatR = StokObatAlkesR::find()
                                ->where(['obatalkes_id' => $value['obatalkes_id']])
                                ->andWhere(['ruangan_id' => $ruangan_id])
                                ->one();

                    if (empty($stokObatR)) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                            'text' => "Info Stok Obat Tidak Ada"
                        ]);
                    }
                    $stokObatR->qty_tersedia = (int) $stokObatR->qty_tersedia -  (int) $value['qty'];
                    $stokObatR->qty_dipesan = (int) $stokObatR->qty_dipesan +  (int) $value['qty'];
                    if (!$stokObatR->update()) {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                            'data' => $stokObatR->errors
                        ]);
                    }
                }
                */
                $detailTrans[] = [
                    'obatalkes_id' => $value['obatalkes_id'],
                    'qty_satuanpakai' => $value['qty'],
                    'satuankecil_id' => $infoObat['satuankecil_id'],
                    'obatalkespasien_id' => null,
                    'harganetto' => $infoObat['harganetto'],
                    'jmlppn' => $infoObat['jmlppn'],
                    'jmlmargin' => $infoObat['jmlmargin'],
                    'jmldiscount' => $infoObat['jmldiscount'],
                    'persendiscount' => $infoObat['persendiscount'],
                    'persenmargin' => $infoObat['persenmargin'],
                    'persenppn' => $infoObat['persenppn'],
                ];
            }

            if (!empty($instruksibmhp)) {
                InstruksiTindakanBmhp::batchInsert($instruksibmhp);
            }

            /* remove kondisi stok ruangan, agar meskipun ke ruangan sendiri, implementasi tetap dilakukan */
            // if (!$stokRuangan) {
            $mImplementasi = new Implementasi;
            $mImplementasi->attributes = [
                'tgl_implementasi' => date('Y-m-d H:i:s'),
                'catatan_implementasi' => date('Y-m-d H:i:s'),
                'instruksi_id' => $id_instruksi,
            ];

            if (!$mImplementasi->save()) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $mImplementasi->errors
                ]);
            }

            $implemnId = $mImplementasi->implementasi_id;

            $qInsTindakan = InstruksiTindakan::find()->where([
                'instruksi_id' => $id_instruksi
            ])->asArray()->all();

            if (!empty($qInsTindakan)) {
                $postBill = [
                    'no_pendaftaran' => $no_pendaftaran,
                    'ruangan_id' => $ruangan_id,
                    'instalasi_id' => $instalasi_id,
                    'tgl_transaksi' => date('Y-m-d H:i:s'),
                    'detail_tindakan' => [],
                ];
                foreach ($qInsTindakan as $value) {
                    $postBill['detail_tindakan'][] = [
                        "dokter_id" => !empty($value['dokterdpjp_id']) ? $value['dokterdpjp_id'] : null,
                        "perawat_id" => !empty($value['perawat1_id']) ? $value['perawat1_id'] : null,
                        "perawat2_id" => !empty($value['perawat2_id']) ? $value['perawat2_id'] : null,
                        "tipepaket_id" => !empty($value['tipepaket_id']) ? $value['tipepaket_id'] : null,
                        "is_cyto" => !empty($value['is_cyto']) ? true : false,
                        "is_penyulit" => false,
                        "qty" => !empty($value['qty']) ? (int) $value['qty'] : 0,
                        "daftartindakan_id" => !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null,
                        "instruksitindakan_id" => !empty($value['instruksitindakan_id']) ? $value['instruksitindakan_id'] : null,
                        "implementasi_id" => $implemnId,
                    ];
                }
                $billKasir = (new KasirService)->post('api/billing', [
                    'form_params' => $postBill,
                    'failed' => function ($data) {
                        \Yii::error([
                            "Message-Error" => $data
                        ]);
                        return [
                            'failed' => true,
                            'message' => [
                                'status' => 422,
                                'text' => isset($data['message']) ? $data['message'] : 'Billing tindakan gagal disimpan'
                            ]
                        ];
                    }
                ]);

                if (isset($billKasir['failed'])) {
                    $transaction->rollBack();
                    return $this->responseJson(400, isset($billKasir['message']['text']) ? $billKasir['message']['text'] : 'Tindakan Tidak Tersedia!');
                }
            }

            $qInsBhp = InstruksiTindakanBmhp::find()->where([
                'instruksi_id' => $id_instruksi
            ])->asArray()->all();

            $listBhp = [];
            $jwt = Yii::$app->jwt->user;
            $obatalkespasien_ids = [];
            if (!empty($qInsBhp)) {
                $filteredId = [
                    'instruksitindakanbmhp_id' => []
                ];
                $totalTarif = 0;
                foreach ($qInsBhp as $value) {
                    $totalTarif += ArrayHelper::getValue($value, 'harga_jumlah', 0);
                    if (!isset($filteredId['pendaftaran_id'])) {
                        $filteredId['pendaftaran_id'] = $value['pendaftaran_id'];
                    }
                    if (!isset($filteredId['ruangan_id'])) {
                        $filteredId['ruangan_id'] = $value['ruangan_id'];
                    }

                    if (isset($filteredId['instruksitindakanbmhp_id']) && !in_array($value['instruksitindakanbmhp_id'], $filteredId['instruksitindakanbmhp_id'])) {
                        $filteredId['instruksitindakanbmhp_id'][] = $value['instruksitindakanbmhp_id'];
                    }
                    $listBhp[] = [
                        'ruangan_id' => $value['ruangan_id'],
                        'carabayar_id' => $value['carabayar_id'],
                        'pegawai_id' => $jwt->pegawai_id,
                        'daftartindakan_id' => $value['daftartindakan_id'],
                        'tipepaket_id' => $value['tipepaket_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'pendaftaran_id' => $value['pendaftaran_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'pasien_id' => $value['pasien_id'],
                        'penjamin_id' => $value['penjamin_id'],
                        'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                        'tglpelayanan' => date('Y-m-d H:i:s'),
                        'qty_oa' => $value['qty'],
                        'hargasatuan_oa' => $value['harga_jualsatuan'],
                        'harganetto_oa' => $value['harga_netto'],
                        'hargajual_oa' => $value['harga_jumlah'],
                        'additional_data' => $value['additional_data'],
                        'perawat1_id' => $value['perawat1_id'],
                        'perawat2_id' => $value['perawat2_id'],
                        'instruksitindakanbmhp_id' => $value['instruksitindakanbmhp_id'],
                        'implementasi_id' => $implemnId,
                        'status_bmhp' => $stokRuangan ? DocoConstants::BMHP_SUDAH_VERIFIKASI : DocoConstants::BMHP_BELUM_VERIFIKASI
                    ];
                }

                $validasiPlafon = new PlafonBpjsService($pendaftaranId, $totalTarif);
                $result = $validasiPlafon->validasiPlafon();
                if (!$result['isValid']) {
                    return $this->responseJson(400, $result['message'] ? $result['message'] : 'Validasi Plafon Gagal');
                }
                ObatAlkesPasien::batchInsert($listBhp);
                if ($stokRuangan) {
                    $getObatAlkesPasien = ObatAlkesPasien::find()->select([
                        'obatalkespasien_id',
                        'obatalkes_id',
                        'instruksitindakanbmhp_id'
                    ])->where($filteredId)->asArray()->all();

                    $obatAlkesAfterSave = ArrayHelper::index($getObatAlkesPasien, 'obatalkes_id');
                    foreach($detailTrans as $key => $value) {
                        $obatAlkes = isset($obatAlkesAfterSave[ArrayHelper::getValue($value, 'obatalkes_id')]) ? $obatAlkesAfterSave[ArrayHelper::getValue($value, 'obatalkes_id')] : [];
                        $detailTrans[$key]['obatalkespasien_id'] = ArrayHelper::getValue($obatAlkes, 'obatalkespasien_id');
                    }
                    $tanggalBerlaku = date('Y-m-d');
                    // Mencari Metode
                    $konfig = Yii::$app->db->createCommand("
                            SELECT metodeantrian FROM konfigfarmasi_k
                            WHERE tglberlaku >= '{$tanggalBerlaku}'
                            AND konfigfarmasi_aktif = true
                            AND is_active = true
                        ")->queryOne();
                    // Mencari Metode dengan nilai default FEFO
                    $currentMetode = LogicStokObatAlkes::FEFO;
                    if ($konfig) {
                        $currentMetode = isset($konfig['metodeantrian'])
                            ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
                    }
                    if (count($detailTrans) > 0) {
                        $tanggalPemakaian = date('Y-m-d H:i:s');
                        // Execute By Condition
                        LogicStokObatAlkes::$distribusi = false;
                        if ($currentMetode === LogicStokObatAlkes::FEFO) {
                            $methode = LogicStokObatAlkes::methodeFEFO($detailTrans, $tanggalPemakaian);
                        } else {
                            $methode = LogicStokObatAlkes::methodeFIFO($detailTrans, $tanggalPemakaian);
                        }
                    }
                }
            }

            // }
            // resume medis
            // ResumeMedisRIT::updateResume($pendaftaranId, 'tindakan');

            $transaction->commit();

            return [
                'message' => 'Data Berhasil di simpan',
                'pendaftaran_id' => $pendaftaranId,
                'cppt_id' => $this->helper->encrypt($modelInstruksi->cppt_id)
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            \Yii::error([
                "Message-Error" => $e->getMessage()
            ]);
            return [
                'text' => 'Kesalahan Pada Sistem'
            ];
        } catch (\yii\base\ErrorException $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            \Yii::error([
                "Message-Error" => $e->getMessage()
            ]);
            return [
                'text' => 'Kesalahan Pada Sistem'
            ];
        }
    }

    /**
     *
     * @see Fungsi get tindakan ruangan
     * @return object
     *
     */
    public function actionDpjpGetTindakanRuangan($ruanganId = null, $kelasPelayananId = null, $id = null, $penjaminId = null, $komponenTarifId = DocoConstants::KOMPONEN_TARIF)
    {
        // Try catch
        try {
            // Get data
            $model = InfoTarifRs::find()->where(['daftartindakan_id' => $id]);

            // Check condition
            if ($ruanganId != null) {
                // Add condition
                $model->andWhere(['ruangan_id' => $ruanganId]);
            }

            // Check condition
            if ($kelasPelayananId != null) {
                // Add condition
                $model->andWhere(['kelaspelayanan_id' => $kelasPelayananId]);
            }

            // Check condition
            if ($penjaminId != null) {
                // Add condition
                $model->andWhere(['penjamin_id' => $penjaminId]);
            }

            // Check condition
            if ($komponenTarifId != null) {
                // Add condition
                $model->andWhere(['komponentarif_id' => $komponenTarifId]);
            }
            $dataTarif = $model->one();
            $dataTarif->harga_tariftindakan = (int) $dataTarif->harga_tariftindakan;
            return $dataTarif;
            // Return model
        } catch (\yii\db\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi get tindakan ruangan
     * @return object
     *
     */
    public function actionDpjpGetPaketRuangan($ruanganId = null, $kelasPelayananId = null, $id = null, $penjaminId = null)
    {
        // Try catch
        try {
            $result = InfoTarifRs::find()
                ->where(['infotarifrs_v.tipepaket_id' => $id])
                ->leftJoin('tariftindakan_m', 'tariftindakan_m.tariftindakan_id = infotarifrs_v.tariftindakan_id');

            if ($ruanganId) {
                $result->andWhere(['infotarifrs_v.ruangan_id' => $ruanganId]);
            }

            if ($kelasPelayananId) {
                $result->andWhere(['infotarifrs_v.kelaspelayanan_id' => $kelasPelayananId]);
            }

            if ($penjaminId) {
                $result->andWhere(['infotarifrs_v.penjamin_id' => $penjaminId]);
            }

            $result->andWhere(['infotarifrs_v.komponentarif_id' => DocoConstants::KOMPONEN_TARIF]);

            $result->andWhere('infotarifrs_v.tipepaket_id IS NOT NULL');

            $data_paket = $result->asArray()->one();
            if ($data_paket) {
                $result = PaketDetailView::find()->where(['tipepaket_id' => $data_paket['tipepaket_id']])->asArray()->all();
                $data_paket['paketDetail'] = $result;
            }
            return $data_paket;
        } catch (\yii\db\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Exception
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDpjpGetTindakanPaketPasien()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $ruangan_id = $request->get('ruangan_id');
        $infoPasien = InfoPasienRdV::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $kelaspelayanan_id =  $infoPasien['kelaspelayanan_id'];
        $penjamin_id = $infoPasien['penjamin_id'];
        return [
            'data_tindakanruangan' => $this->getDataTindakanRuangan($ruangan_id, $kelaspelayanan_id, $penjamin_id)->asArray()->all(),
            'data_paket' => $this->getDataPaket($ruangan_id, $kelaspelayanan_id, $penjamin_id, DocoConstants::KOMPONEN_TARIF)
        ];
    }

    /**
     * @see Fungsi get data paket
     * @return array
     *
     */
    private function getDataPaket($ruangan_id = null, $kelaspelayanan_id = null, $penjamin_id = null, $komponentarif_id = DocoConstants::KOMPONEN_TARIF)
    {
        $result = InfoTarifRs::find()
            ->leftJoin('tariftindakan_m', 'tariftindakan_m.tariftindakan_id = infotarifrs_v.tariftindakan_id');

        if ($ruangan_id) {
            $result->andWhere(['infotarifrs_v.ruangan_id' => $ruangan_id]);
        }

        if ($kelaspelayanan_id) {
            $result->andWhere(['infotarifrs_v.kelaspelayanan_id' => $kelaspelayanan_id]);
        }

        if ($penjamin_id) {
            $result->andWhere(['infotarifrs_v.penjamin_id' => $penjamin_id]);
        }

        if ($komponentarif_id) {
            $result->andWhere(['infotarifrs_v.komponentarif_id' => $komponentarif_id]);
        }

        $result->andWhere('infotarifrs_v.tipepaket_id IS NOT NULL');

        // Group
        $result->groupBy([
            'infotarifrs_v.tipepaket_id',
            'infotarifrs_v.tipepaket_nama',
            'infotarifrs_v.ruangan_id',
            'infotarifrs_v.ruangan_nama',
            'infotarifrs_v.instalasi_id',
            'infotarifrs_v.instalasi_nama',
            'infotarifrs_v.ruanganpaket_id',
            'infotarifrs_v.ruanganpaket_nama',
            'infotarifrs_v.tariftindakan_id',
            'infotarifrs_v.is_default',
            'infotarifrs_v.penjamin_id',
            'infotarifrs_v.penjamin_nama',
            'infotarifrs_v.kelaspelayanan_id',
            'infotarifrs_v.kelaspelayanan_nama',
            'infotarifrs_v.perdatarif_id',
            'infotarifrs_v.perdanama_sk',
            'infotarifrs_v.komponentarif_id',
            'infotarifrs_v.komponentarif_nama',
            'infotarifrs_v.daftartindakan_id',
            'infotarifrs_v.daftartindakan_nama',
            'infotarifrs_v.kelompoktindakan_id',
            'infotarifrs_v.kelompoktindakan_nama',
            'infotarifrs_v.kategoritindakan_id',
            'infotarifrs_v.kategoritindakan_nama',
            'infotarifrs_v.harga_tariftindakan',
            'infotarifrs_v.persencyto_tindakan',
            'infotarifrs_v.persendiskon_tindakan',
        ]);
        return $result->asArray()->all();
    }

    /**
     * @todo get obat/alkes by jenis obatalkes
     */
    public function actionDpjpGetObatAlkes()
    {
        $request = Yii::$app->request;
        $group_jenis = $request->get('group_jenis');
        $ruangan_id = $request->get('ruangan_id');
        $modelPayload = new ParamModel;
        /** validation payload */
        $modelPayload->attributes = [
            'ruangan_id' => $ruangan_id,
            'group_jenis' => $group_jenis,
        ];

        /** Validate Type data payload */
        if (!$modelPayload->validate()) {
            Yii::$app->response->statusCode = 422;
            return [
                'status' => 422,
                'data' => $modelPayload->errors
            ];
        }
        $query = InfoStokObatAlkesFn::find()->select([
            'obatalkes_id',
            'obatalkes_nama',
            'qty_tersedia',
            'harganetto',
            'hargamaksimum',
            'hargaminimum',
            'hargaratarata',
            'ruangan_id',
            'hargaygdipakai',
            'jml_hargajual',
            'group_jenisobat',
        ])->where(['ruangan_id' => $ruangan_id]);
        if ($group_jenis) {
            $query->andWhere(['group_jenisobat' => $group_jenis]);
        }

        $data = $query->all();

        return empty($data) ? [] : $data;
    }

    /**
     * @controller actionCetakPdfListDpjp
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
     * @attribute #inf_dokterjaga# => Informasi Pasien: Dokter Jaga
     * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
     * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
     * @attribute #inf_carabayar# => Informasi Pasien: Cara bayar
     **/
    public function actionCetakPdfListDpjp()
    {
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', 0);
        $ruangan_id = $request->get('ruangan_id', 0);
        $pegawai_id = $request->get('pegawai_id', 0);
        $kelompokpegawai_id = $request->get('kelompokpegawai_id', 0);
        $nama_usercetak = $request->get('nama_usercetak', '');
        $id_usercetak = Yii::$app->jwt->user->pegawai_id;

        $nama_user = '';
        $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id' => $id_usercetak])->asArray()->one();

        if (is_null($mNamaPegawai)) {
            $nama_user = $nama_usercetak;
        } else {
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }

        $queryHeader = Pendaftaran::find()
            ->select(['pm.no_rekam_medik', 'pendaftaran_t.tgl_pendaftaran', 'pendaftaran_t.no_pendaftaran', 'pm.nama_pasien', 'jk.lookup_name as jenis_kelamin', 'pm.tanggal_lahir', 'pendaftaran_t.pegawai_id as dokter_jaga_id'])
            ->leftJoin('pasien_m pm', 'pm.pasien_id = pendaftaran_t.pasien_id')
            ->leftJoin('lookup_m jk', 'jk.lookup_id = pm.jeniskelamin::integer')
            ->andWhere([
                'pendaftaran_id' => $request->get('pendaftaran_id')
            ]);
        $resultHeader = $queryHeader->asArray()->one();

        $data = [];
        $newData = [];
        $dokterjaga_id = $resultHeader['dokter_jaga_id'];
        $dataAsesmenDpjp = $this->actionGetDataAsesmenDpjp($pendaftaran_id);

        if (count($dataAsesmenDpjp['data']) > 0) {
            foreach ($dataAsesmenDpjp['data'] as $key => $valAsesmenDpjp) {
                $newData[$key]['cppt'] = $valAsesmenDpjp;
                $newData[$key]['data_instruksi'] = (new \yii\db\Query())
                    ->from('infoinstruksi_v')
                    ->where([
                        'cppt_id' => $valAsesmenDpjp['cppt_id'],
                    ])
                    ->orderBy(['tgl_instruksi' => 'SORT_ASC'])
                    ->all();
            }
        }
        $cppt_data = [];
        $rownum = 1;
        // $htmlpe = '';
        foreach ($newData as $key => $value) {
            // Assign data
            $spesialis_nama = strtolower($value['cppt']['spesialis_nama']);
            $cppt_data[$key]['no'] = $rownum;
            $cppt_data[$key]['ruangan'] = $value['cppt']['ruangan_nama'] . '<hr>';
            $cppt_data[$key]['tgl_cppt'] = date('d/m/Y', strtotime($value['cppt']['tgl_cppt']));
            $cppt_data[$key]['jam_cppt'] = date('H:i:s', strtotime($value['cppt']['tgl_cppt']));
            $cppt_data[$key]['profesi'] = $value['cppt']['kelompokpegawai_nama'] . " - " . ucwords(str_replace('_', ' ', $spesialis_nama)) . '<hr>' . $value['cppt']['nama_pegawai'];
            $cppt_data[$key]['penatalaksanaan'] = $this->getPenatalaksanaan($value['cppt']);
            $cppt_data[$key]['verifikasi'] = $this->getVerifikasi($value['cppt'], $value['data_instruksi'], $pegawai_id, $kelompokpegawai_id, $dokterjaga_id);
            $cppt_data[$key]['instruksi_dpjp'] = @$value['cppt']['instruksi'];
            $cppt_data[$key]['is_deleted'] = $value['cppt']['is_deleted'];
            $cppt_data[$key]['pegawai_update_nama'] = $value['cppt']['pegawai_update_nama'];
            $cppt_data[$key]['created_date'] = $value['cppt']['created_date'];
            $rownum++;
        };
        $print = new DocoPrint();
        $print->attributes = [
            '#inf_norekammedik#' => $resultHeader ? @$resultHeader['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d/m/Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            '#inf_nopendaftaran#' => $resultHeader ? @$resultHeader['no_pendaftaran'] : '',
            '#inf_namapasien#' => $resultHeader ? @$resultHeader['nama_pasien'] : '',
            '#inf_jeniskelamin#' => $resultHeader ? @$resultHeader['jenis_kelamin'] : '',
            '#inf_tgllahir#' => $resultHeader ? (isset($resultHeader['tanggal_lahir']) ? date('d/m/Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#table_list_cppt#' => $this->renderPartial('cetakan_list_dpjp', [
                'data' => $cppt_data,
            ]),
        ];

        if ($request->get('only_attributes') == true) {
           return $print->attributes;
        }

        return $print->output();
    }

    private function deleteTagBr($string)
    {
        $resStr = str_replace('<br />', '', $string);
        return $resStr;
    }

    private function getPenatalaksanaan($data)
    {
        // Check if related cppt also ordered lab/rad/reseptur
        $hasLab = !empty($data['is_lab']) && $data['is_lab'] ? '* Pasien dilakukan pemeriksaan laboratorium <br/>' : '';
        $hasRad = !empty($data['is_rad']) && $data['is_rad'] ? '* Pasien dilakukan pemeriksaan radiologi <br/>' : '';
        $hasResep = !empty($data['is_reseptur']) && $data['is_reseptur'] ? '* Pasien diberikan resep <br/>' : '';

        // Deklarasi html
        $html = '<table><tbody>';
        $html2 = '<ul>';
        // Cek subjek
        if (isset($data['subject']) && $data['subject'] != '') {
            // Set html
            $html .= '<tr>';
            $html .= '<td><b>S</b></td>';
            $html .= '<td>:</td>';
            $html .= '<td>' . $data['subject'] . '</td>';
            $html .= '</tr>';
            $html2 .= '<li><b>S</b> :';
            $html2 .= $data['subject'];
            $html2 .= '</li>';
        }

        // Cek objek
        if (isset($data['object']) && $data['object'] != '') {
            // Set html
            $html .= '<tr>';
            $html .= '<td><b>O</b></td>';
            $html .= '<td>:</td>';
            $html .= '<td>' . $data['object'] . '</td>';
            $html .= '</tr>';
            $html2 .= '<li><b>O</b> :';
            $html2 .= $data['object'];
            $html2 .= '</li>';
        }

        // Cek subject
        if (($data['subject'] != '') && ($data['object'] != '') && ($data['planning'] != '')) {
            // Cek asesmen
            if (isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') {
                $diag_utama = json_decode($data['a_diag_utama'], TRUE);
                // Set html
                $html .= '<tr>';
                $html .= '<td><b>A Diagnosa Utama </b></td>';
                $html .= '<td>:</td>';
                $html .= '<td>' . @$diag_utama['text'] . '</td>';
                $html .= '</tr>';
                $html2 .= '<li><b>A Diagnosa Utama </b>:';
                $html2 .= @$diag_utama['text'];
                $html2 .= '</li>';
            }

            // Cek asesmen
            if (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '') {
                // Encode
                $diagnosaPenyerta = json_decode($data['a_diag_penyerta'], TRUE);

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
                            $html .= '<td><b>A Diagnosa Penyerta </b></td>';
                            $html .= '<td>:</td>';
                            $html .= '<td>- ' . @$valueDiagnosaPenyerta['text'] . '</td>';
                            $html .= '</tr>';
                            $html2 .= '<li><b>A Diagnosa Penyerta</b> :' . @$valueDiagnosaPenyerta['text'];
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
            $html2 .= '<li>' . $this->deleteTagBr($data['instruksi']) . '<br>' . $data['pegawai_instruksi'] . '</li>';
        }



        // Cek penanda order penunjang
        if (!empty($hasLab) || !empty($hasRad) || !empty($hasResep) || isset($data['planning'])) {
            $html2 .= '<li><b>P</b> : ';
            $html .= '<td><b>Planning:</b> <br/>';

            // Cek planning
            if (isset($data['planning']) && $data['planning'] != '') {
                // Set html
                $html2 .= $data['planning'];
                // $html .= '<tr>';
                // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;"></td>';
                // $html .= '</tr>';
            }

            $html2 .= '<br/>' . $hasLab . $hasRad . $hasResep;
            $html2 .= '</li>';
        }

        if (!empty($data['catatan_dokter'])) {
            $html .= '<tr>';
            $html .= '<td><b>Catatan</b></td>';
            $html .= '<td>:</td>';
            $html .= '<td>' . $data['catatan_dokter'] . '</td>';
            $html .= '</tr>';
            $html2 .= '<li><b>Catatan</b> : ';
            $html2 .= $data['catatan_dokter'] . '</li>';
        }

        // Set end tag html
        $html .= '</tbody></table>';
        $html2 .= '</ul>';
        // Return
        return $html2;
    }

    private function getInstruksiDpjp($data_instruksi)
    {
        $groupInstruksi = [];
        $html = '<table><tr><td>';
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
                    } else if (substr($data_ins['tipe_instruksi'], 0, 3) == 'BED') {
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
                    } else if ($tipe_instruksi == 'BED_TINDAKAN') {
                        $label_tipe_instruksi = 'Tindakan Bedah Sentral';
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
                'tgl_tindakan' => date('d/m/Y', strtotime($d_instruksi['tgl_instruksi'])),
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

        $html = '<ul>';
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
                    $html .= '<li><strike>' . @$label_nama_tipe . '</strike></li>';
                } else {
                    // $html .= '<tr><td><table>';
                    $html .= '<li><b>' . @$label_nama_tipe . '</b></li>';
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
                    $html .= '<li><b>';
                    $html .= @$label_instalasi . ' - ';
                    $html .= @$data_ins['ruangan_pertindakan'];
                    if ($is_penunjang_batal == true) {
                        $html .= '-  <b>DIBATALKAN</b>';
                    }
                    if ($is_penunjang_ditolak == true) {
                        $html .= '-  <b>DITOLAK</b>';
                    }
                    $html .= '</b></li>';
                    // $html .= '<td></tr>';
                }

                if ($data_ins['instruksi_deleted'] == true) {
                    $html .= '<li><strike>' . @$data_ins['catatan_instruksi'] . '</strike></li>';
                } else {
                    $html .= '<li><b>' . @$data_ins['catatan_instruksi'] . '</b></li>';
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
                        $html .= '<li><b>' . @$label_tipe_instruksi . '</b></li>';
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
                                $html .= @$row_tgltindakan['tgl_tindakan'] . '-' . @$row_tgltindakan['instruksi'];
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
                                $html .= @$row_tgltindakan['tgl_tindakan'] . '-' . @$row_tgltindakan['instruksi'];
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
        $html .= '</ul>';
        return $html;
    }

    private function getVerifikasi($data, $data_instruksi, $pegawai_id, $kelompokpegawai_id, $dokterjaga_id)
    {
        $dpjp = $dokterjaga_id;
        $pemberi_instruksi = $data['pemberi_instruksi_id'];
        $html = '';

        $instruksi_implemented = false;
        $array_status = [];
        foreach ($data_instruksi as $instruksi) {
            $array_status[] = $instruksi['is_telah_implementasi'];
        }
        if (count($array_status) > 0) {
            if (count(array_unique($array_status)) === 1) {
                if (current($array_status) == true) {
                    $instruksi_implemented = true;
                }
            }
        }

        // cek verifikasi verbal order dan cek verifikasi dpjp
        if ($data['pegawai_instruksi']) {
            if ($data['is_verifikasi_verbal']) {
                $html .= '<span>' . $data['pegawai_verifikasi_verbal'] . '<br><hr><br>' . date('d/m/Y H:i:s', strtotime($data['tgl_verif_verbal'])) . '</span>';
            } else {
                $html .= '<h5>Belum Verifikasi Verbal Order</h5>';
            }
        } else {
            if ($data['is_verifikasi']) { // cek verifikasi dpjp
                $html .= '<span>' . $data['pegawai_verifikasi'] . '<br><hr><br>' . date('d/m/Y H:i:s', strtotime($data['tgl_verifikasi'])) . '</span>';
                $html .= '<br>';
            } else {
                $html .= '<h5>Belum Verifikasi DPJP</h5>';
            }
        }


        return $html;
    }

    public function actionVerifikasi()
    {
        try {
            $request = Yii::$app->request;
            $cppt_id = $request->post('id');

            $model = Cppt::find(true)->andWhere(['cppt_id' => $cppt_id])->one();
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

    public function actionDeleteVerbal()
    {
        
        try {
            $request = Yii::$app->request;
            $cppt_id = $request->post('id');
            $model = Cppt::find(true)->andWhere(['cppt_id' => $cppt_id])->one();
            $dataGroup = SoapRsView::find()->where(['cppt_id' => $cppt_id])->one();

            if(!empty($model->pendaftaran_id)){
                $getTindakanPelayananT = TindakanPelayananT::find()
                ->where([
                    'pendaftaran_id' => $model->pendaftaran_id,
                    'dokterpenanggungjawab_id'=> $dataGroup->pemberi_instruksi_id,
                    'daftartindakan_id'=>$model->tindakankonsul_id
                ])->one();
                $arrTindakanPelayanan = [];
               
                if(!empty($getTindakanPelayananT)){

                    $arrTindakanPelayanan[] = [
                        'tindakanpelayanan_id' => $getTindakanPelayananT->tindakanpelayanan_id
                    ];
                }

   
            }

            $registrationData['ruangan_id'] = $model->ruangan_id;
            $registrationData['no_pendaftaran'] = $dataGroup->no_pendaftaran;
            $registrationData['instalasi_id'] = $dataGroup->instalasi_id;
            $registrationData['tgl_transaksi'] = date("Y-m-d H:i:s");
            $registrationData['detail_tindakan'] = $arrTindakanPelayanan;

            if ($request->post('jenis') == 'verbal') {
                if ($model->delete()) {
                    
                    
                    if (!empty($arrTindakanPelayanan)) {
                        $reqBatalTagihan = (new KasirService)->post('api/batal-tagihan', [
                            'form_params' => $registrationData,
                            'failed' => function($data) {
                                \Yii::error([
                                    "Message-Error" => $data
                                ]);
                                return [
                                    'failed' => true,
                                    'message' => [
                                        'status' => 422,
                                        'text' => $data['message']
                                    ]
                                ];
                            }
                        ]);
                        if (isset($reqBatalTagihan['failed'])) {
                            return [
                                'title' => 'Gagal Batal',
                                'text' => $reqBatalTagihan['message']['text'],
                                'status' => 422
                            ];
                        }
                    }
                }
            } else {
                
                return $response['response'] = [
                    'title' => 'Proses Hapus Gagal !',
                    'text' => 'Data Gagal di hapus',
                    'status' => 422
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

    /* Get obatalkes by instalasi */
    private function getObatAlkesByInstalasi($list_ruangan = [], $medIds)
    {
        try {
            $model = InfoStokObatAlkesView::find();

            if (!empty($medIds) && is_array($medIds)) {
                $model->where(['in', 'obatalkes_id', $medIds]);
            }
            if (!empty($list_ruangan)) {
                $wards = [];
                foreach ($list_ruangan as $value) {
                    $wards[] = $value['ruangan_id'];
                }
                $model->andWhere(['in', 'ruangan_id', $wards]);
            }

            return $model->all();
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
     * @see Fungsi insert reseptur pemeriksaan rawat darurat
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

            $modelInstruksi = new Instruksi;
            $modelInstruksi->attributes = $data_instruksi;

            $modelInstruksi->cppt_id = $modelInstruksi->cppt_id;
            $modelInstruksi->tgl_instruksi = date('Y-m-d H:i:s', strtotime($modelInstruksi->tgl_instruksi));

            // generate cppt
            if ($data['data_instruksi']['cppt_id'] == '0') {
                $getCppt = AllowController::actionCreateSoap($data['ruangan'], $data['data_reseptur']['pendaftaran_id'], $data['data_reseptur']['pegawai_id'], $data['pasien_id']);
                $modelInstruksi->cppt_id = $getCppt;
            }

            if (isset($modelInstruksi['instruksi_id']) && $modelInstruksi['instruksi_id'] == '') {
                unset($modelInstruksi['instruksi_id']);
            }

            // Validasi model instruksi
            if ($modelInstruksi->validate()) {
                // Cek save
                if ($modelInstruksi->save(false)) {
                    $instruksi_id = $modelInstruksi->instruksi_id;
                    $sentReseptur = (new FarmasiService)->saveResep(compact('instruksi_id', 'data_reseptur', 'data_resepturdetail'));
                    if (isset($sentReseptur['code']) && $sentReseptur['code'] != 200) {
                        $transaction->rollback();
                        \Yii::$app->response->statusCode = $sentReseptur['code'];
                        return [
                            'message' => isset($sentReseptur['data']['message']) ? $sentReseptur['data']['message'] : 'Simpan Reseptur Gagal!'
                        ];
                    }
                    $transaction->commit();
                    return [
                        'message' => 'Simpan Resep Berhasil',
                        'cppt_id' => DocoHelpers::encrypt($modelInstruksi->cppt_id)
                    ];
                } else {
                    $transaction->rollBack();
                    return $modelInstruksi->errors;
                }
            } else {
                $errors = DocoHelpers::parseError($modelInstruksi->errors, 'InstruksiForm');
                $return = [
                    'data' => $errors,
                    'message' => 'Terjadi kesalahan pada inputan',
                    'status' => 422
                ];

                $transaction->rollBack();
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi update reseptur pemeriksaan rawat darurat
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
            if (empty($data['data_resepturdetail'])) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Reseptur tidak boleh kosong.'
                ];
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

                        // kondisi jika non racikan di set 325 else 324
                        $racikan_id = $list_racikan[$racikanType];
                        $fungsiantrian_id = ($racikan_id == 2) ? DocoConstants::VAR_FA_NR : DocoConstants::VAR_FA_R;

                        $modelAntrian = new Antrian;
                        $modelAntrian->ruangan_id = $modelReseptur->ruangan_id;
                        $modelAntrian->tgl_antrian = date('Y-m-d H:i:s');
                        $modelAntrian->jenisantrian_id = DocoConstants::VAR_JA_F;
                        $modelAntrian->racikan_id = $racikan_id;
                        $modelAntrian->fungsiantrian_id = $fungsiantrian_id;
                        $modelAntrian->save(false);
                        $antrian_id = $modelAntrian->antrian_id;

                        $modelReseptur->antrian_id = $antrian_id;
                        $resepturDetailId = [];
                        if ($modelReseptur->save(false)) {
                            $dataResepturDetail = [];
                            $resepturId = $modelReseptur->reseptur_id;
                            foreach ($data_resepturdetail as $key => $value) {
                                if (!empty($value['resepturdetail_id'])) {
                                    $dataResepturDetail[] = $value['resepturdetail_id'];
                                    $respturDetail = $value['resepturdetail_id'];
                                    $signaId = $value['signa_id'];
                                    if (!empty($signaId)) {
                                        Yii::$app->db->createCommand("
                                            UPDATE resepturdetail_t SET signa_id = {$signaId} WHERE resepturdetail_id = {$respturDetail}
                                        ")->execute();
                                    }
                                    unset($data_resepturdetail[$key]);
                                    continue;
                                }
                                $data_resepturdetail[$key]['tgl_resepturdetail'] = date('Y-m-d H:i:s');
                                $data_resepturdetail[$key]['reseptur_id'] = $resepturId;
                                $data_resepturdetail[$key]['racikan_id'] = $list_racikan[$value['racikan_id']];
                                $harganetto_reseptur = $value['hargasatuan_reseptur'];
                                $hargajual_reseptur = $value['qty_reseptur'] * $harganetto_reseptur;
                                $data_resepturdetail[$key]['harganetto_reseptur'] = $harganetto_reseptur;
                                $data_resepturdetail[$key]['hargajual_reseptur'] = $hargajual_reseptur;
                                $data_resepturdetail[$key]['status_implementasi'] = 454;

                                // fill null values
                                if ($value['r'] == 'null') {
                                    $data_resepturdetail[$key]['r'] = null;
                                }

                                if ($value['rke'] == 'null') {
                                    $data_resepturdetail[$key]['rke'] = null;
                                }

                                // if ($value['resepturdetail_id'] != ''){
                                //     $resepturDetailId[] = $value['resepturdetail_id'];
                                // }

                                // if ($value['resepturdetail_id'] != '') {
                                //     $modelResepturDetail = ResepturDetail::findOne($value['resepturdetail_id']);
                                //     $modelResepturDetail->load($data_resepturdetail[$key], '');
                                //     $modelResepturDetail->qty_konversi = $value['qty_konversi'];
                                //     $modelResepturDetail->save();
                                // } else {
                                //     unset($value['resepturdetail_id']);
                                //     $modelResepturDetail = new ResepturDetail;
                                //     $modelResepturDetail->load($data_resepturdetail[$key], '');
                                //     $modelResepturDetail->status_implementasi = '454';
                                //     $modelResepturDetail->qty_konversi = $value['qty_konversi'];
                                //     if ($modelResepturDetail->save()) {
                                //         $resepturDetailId[] = $modelResepturDetail->resepturdetail_id;
                                //     }
                                // }
                            }

                            $addCondition = "";
                            if (!empty($dataResepturDetail)) {
                                $notIn = implode(",", $dataResepturDetail);
                                $addCondition .= " AND resepturdetail_id NOT IN ({$notIn})";
                            }
                            Yii::$app->db->createCommand("
                                UPDATE resepturdetail_t SET is_deleted = true WHERE reseptur_id = {$resepturId}
                                AND is_deleted = false
                                {$addCondition}
                            ")->execute();
                            ResepturDetail::batchInsert($data_resepturdetail, false);
                            $transaction->commit();
                            // $modelResepturDetail = ResepturDetail::find()->where(['reseptur_id' => $modelReseptur->reseptur_id])->all();

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

    // ======== penunjang

    // author : rizal
    // save terapi penunjang
    public function actionCreateTerapiPenunjang()
    {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $request = Yii::$app->request;

            $posts = $request->post();
            $model = new Instruksi;
            $posts['tgl_kirimpasien'] = date('Y-m-d H:i:s', strtotime($posts['tgl_kirimpasien']));
            // generate cppt
            if ($posts['cppt_id'] == '0') {
                $getCppt = AllowController::actionCreateSoap($posts['ruangan'], $posts['pendaftaran_id'], $posts['pegawai_id'], $posts['pasien_id']);
                $posts['cppt_id'] = $getCppt;
            }
            $model->cppt_id = $posts['cppt_id'];
            $model->tgl_instruksi = $posts['tgl_kirimpasien'];
            $model->jenis_instruksi = DocoConstants::J_INST_PNJG;
            $model->catatan_instruksi = $posts['catatan_dokterpengirim'];
            $model->save();

            $instruksi_id = $model->instruksi_id;
            $posts['instruksi_id'] = $instruksi_id;
            $posts['list_order'] = isset($posts['list_order']) ? json_encode($posts['list_order']) : null;

            if ($posts['instalasi_id'] == DocoConstants::VAR_I_LAB) {
                //hit api backend laboratorium
                $restLab = Yii::$app->docoRest->laboratorium;
                $request = $restLab->post('order/create?id=' . $posts['pendaftaran_id'], [
                    'form_params' => $posts
                ]);
                $response = json_decode($request->getBody(), true);
            } elseif ($posts['instalasi_id'] == DocoConstants::VAR_I_RAD) {
                //hit api backend radiologi
                $restRad = Yii::$app->docoRest->radiologi;
                $request = $restRad->post('order/create?id=' . $posts['pendaftaran_id'], [
                    'form_params' => $posts
                ]);
                $response = json_decode($request->getBody(), true);
            } elseif ($posts['instalasi_id'] == DocoConstants::INST_ID_BEDAH) {
                // return $posts;
                // set jadwal operasi
                $jadwalOperasi = isset($posts['jadwal_operasi']) ? $posts['jadwal_operasi'] : [];
                $posts['jam_mulai'] = !empty($jadwalOperasi['jam_mulai'])
                    ? $jadwalOperasi['jam_mulai'] : null;
                $posts['jam_selesai'] = !empty($jadwalOperasi['jam_selesai'])
                    ? $jadwalOperasi['jam_selesai'] : null;
                $posts['dr_operator_id'] = !empty($jadwalOperasi['dr_operator_id'])
                    ? $jadwalOperasi['dr_operator_id'] : null;
                $posts['dr_anastesi_id'] = !empty($jadwalOperasi['dr_anestesi_id']) ? $jadwalOperasi['dr_anestesi_id'] : null;
                if (!empty($jadwalOperasi['tgl_kirimpasien'])) {
                    $jadwalKirim = date('Y-m-d', strtotime($jadwalOperasi['tgl_kirimpasien'])) . ' ' . date('H:i:s', strtotime($posts['jam_mulai']));
                    $posts['tgl_kirimpasien'] = $jadwalKirim;
                }

                //hit api backend bedah
                $restBedah = Yii::$app->docoRest->bedah;
                $request = $restBedah->post('order/create?id=' . $posts['pendaftaran_id'], [
                    'form_params' => $posts
                ]);
                $response = json_decode($request->getBody(), true);
                // return $response;
            }


            if ($response['metadata']['status'] == 200) {
                $transaction->commit();
                $url = "/igd/pemeriksaan-igd/cetak-penunjang?id=#pendaftaran_id#&instruksi_id=#instruksi_id#";
                $keys = ['#pendaftaran_id#', '#instruksi_id#'];
                $replacements = [DocoHelpers::encrypt($posts['pendaftaran_id']), $model->instruksi_id];
                return [
                    'messages' => 'Data berhasil di simpan',
                    'pasienkirimkeunitlain_id' => $response['response']['pasienkirimkeunitlain_id'],
                    'url' => str_replace($keys, $replacements, $url),
                    'instruksi_id' => $model->instruksi_id,
                    'cppt_id' => $this->helper->encrypt($model->cppt_id)
                ];
            } elseif ($response['metadata']['status'] == 422) {
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => $response['response']['text']
                ];
            } else {
                $transaction->rollBack();
                return [
                    'status' => 500,
                    'title' => 'Proses Gagal !',
                    'text' => 'Terjadi kesalahan sistem'
                ];
            }
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
        try {
            $request = Yii::$app->request;

            $pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
            $instruksi_id = $request->get('instruksi_id', null);

            $model = OrderPenunjangView::find()
                ->andWhere(['pendaftaran_id' => $pendaftaran_id]);
            if ($instruksi_id) {
                $model = $model->andWhere(['instruksi_id' => $instruksi_id]);
            }
            $model = $model->asArray()->all();
            $header = $model[0];
            $detail = $this->renderPartial('cetak_penunjang', ['model' => $model]);
            // return $detail;
            $print = new DocoPrint();

            $print->attributes = [
                '#poliklinik#' => $header['ruangan_nama'],
                '#no_pendaftaran#' => $header['no_pendaftaran'],
                '#no_rekam_medik#' => $header['no_rekam_medik'],
                '#nama_pasien#' => $header['nama_pasien'],
                '#dokter_perujuk#' => $header['dokter_perujuk'],
                '#unit_penunjang#' => $header['instalasi_penunjang'] . ' - ' . $header['ruangan_penunjang'],
                '#jenis_kelamin#' => $header['jenis_kelamin'],
                '#tgl_lahir#' => date('d-m-Y', strtotime($header['tanggal_lahir'])),
                '#cara_bayar#' => $header['carabayar_nama'],
                '#penjamin#' => $header['penjamin_nama'],
                '#tgl_permintaan#' => date('d-m-Y H:i:s', strtotime($header['tgl_kirimpasien'])),
                '#no_rujukan#' => $header['no_orderkeunitlain'],
                '#table_list_order#' => $detail,
            ];
            $print->Output();
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

    public function getTindakanBmhp($pendaftaran_id = false)
    {
        $queryDpjp = (new \yii\db\Query())
            ->select([
                'infoinstruksi_v.tipe_instruksi',
                'infoinstruksi_v.grouping_tipe',
                'infoinstruksi_v.dokter as dokterdpjp_tindakan',
                'cppt_v.pendaftaran_id',
                'infoinstruksi_v.tgl_instruksi',
                'perawattindakan_1.nama_pegawai as pegawaitindakan_1',
                'perawattindakan_2.nama_pegawai as pegawaitindakan_2',
                'perawatbmhp_1.nama_pegawai as pegawaibmhp_1',
                'perawatbmhp_2.nama_pegawai as pegawaibmhp_2',
                'dokterdelegasi.nama_pegawai as dokterdelegasi_tindakan',
                'infoinstruksi_v.qty',
                'instruksitindakan_t.tarif_satuan as tarif_satuan_tindakan',
                'instruksitindakan_t.tarif_cyto as tarif_cyto_tindakan',
                'instruksitindakan_t.jumlah_tarif as jumlah_tarif_tindakan',
                'infoinstruksi_v.bmhp_tindakandetail',
                'infoinstruksi_v.tindakaninstruksi_nama',
                'instruksitindakanbmhp_t.is_ditagihkan as is_ditagihkan_bmhp',
                'instruksitindakanbmhp_t.harga_jumlah as jumlah_tarif_bmhp',
                'infoinstruksi_v.daftar_paket'
            ])
            ->from('infoinstruksi_v')
            ->leftJoin('cppt_v', 'cppt_v.cppt_id = infoinstruksi_v.cppt_id')
            ->leftJoin('instruksitindakan_t', 'instruksitindakan_t.instruksitindakan_id = infoinstruksi_v.instruksitindakan_id AND tipe_instruksi IN (\'TINDAKAN\',\'PAKET\') ')
            ->leftJoin('instruksitindakanbmhp_t', 'instruksitindakanbmhp_t.instruksitindakanbmhp_id = infoinstruksi_v.instruksitindakan_id AND tipe_instruksi = \'BMHP\' ')
            ->leftJoin('pegawai_m perawattindakan_1', 'instruksitindakan_t.perawat1_id = perawattindakan_1.pegawai_id')
            ->leftJoin('pegawai_m perawattindakan_2', 'instruksitindakan_t.perawat2_id = perawattindakan_2.pegawai_id')
            ->leftJoin('pegawai_m perawatbmhp_1', 'instruksitindakanbmhp_t.perawat1_id = perawatbmhp_1.pegawai_id')
            ->leftJoin('pegawai_m perawatbmhp_2', 'instruksitindakanbmhp_t.perawat2_id = perawatbmhp_2.pegawai_id')
            ->leftJoin('pegawai_m dokterdelegasi', 'instruksitindakan_t.dokterdelegasi_id = dokterdelegasi.pegawai_id')
            ->where(['grouping_tipe' => 'TINDAKANBMHP', 'tindakan_deleted' => FALSE]);
        if ($pendaftaran_id !== false) {
            $queryDpjp->andWhere(['cppt_v.pendaftaran_id' => $pendaftaran_id]);
        }
        return $queryDpjp->all();
    }

    /**
     * @controller actionCetakTindakanBmhp
     * @attribute #tgl_cetak# => tgl_cetak
     * @attribute #nama_user# => nama_user
     * @attribute #table_tindakan_paket# => Table Tindakan & Paket
     * @attribute #table_bmhp# => Table Bmhp
     * @attribute #total_keseluruhan# => Total Keseluruhan
     * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
     * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
     * @attribute #inf_nopendaftaran# => Informasi Pasien: No Pendaftaran
     * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
     * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
     * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
     * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
     * @attribute #inf_umur# => Informasi Pasien: Umur
     * @attribute #inf_dokterjaga# => Informasi Pasien: Dokter Jaga
     * @attribute #inf_dokterdpjp# => Informasi Pasien: Dokter DPJP
     * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
     * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
     * @attribute #inf_carabayar# => Informasi Pasien: Cara bayar
     **/
    public function actionCetakTindakanBmhp()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id', 0);
        $ruangan_id = $request->get('ruangan_id', 0);
        $pegawai_id = $request->get('pegawai_id', 0);
        $kelompokpegawai_id = $request->get('kelompokpegawai_id', 0);
        $nama_usercetak = $request->get('nama_usercetak', '');
        $id_usercetak = $request->get('id_usercetak', 0);

        $nama_user = '';
        $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id' => $id_usercetak])->asArray()->one();

        if (is_null($mNamaPegawai)) {
            $nama_user = $nama_usercetak;
        } else {
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }

        $modelHeader = new InfoPasienRdV;
        $queryHeader = $modelHeader::find()
            ->andWhere([
                'pendaftaran_id' => $request->get('pendaftaran_id')
            ]);
        $resultHeader = $queryHeader->asArray()->one();

        $data_tindakan_bmhp = $this->getTindakanBmhp($pendaftaran_id);

        $grandTotalKeseluruhan = 0;
        foreach ($data_tindakan_bmhp as $key_data_tindakan_bmhp => $val_data_tindakan_bmhp) {
            if ($val_data_tindakan_bmhp['tipe_instruksi'] == 'TINDAKAN' || $val_data_tindakan_bmhp['tipe_instruksi'] == 'PAKET') {
                $grandTotalKeseluruhan += @$val_data_tindakan_bmhp['jumlah_tarif_tindakan'];
            } else if ($val_data_tindakan_bmhp['tipe_instruksi'] == 'BMHP') {
                $grandTotalKeseluruhan += @$val_data_tindakan_bmhp['jumlah_tarif_bmhp'];
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#nama_user#' => $nama_user,
            '#tgl_cetak#' => date('d F Y H:i:s'),
            '#table_tindakan_paket#' => $this->renderPartial('table_tindakan_paket', get_defined_vars()),
            '#table_bmhp#' => $this->renderPartial('table_bmhp', get_defined_vars()),
            '#total_keseluruhan#' => DocoHelpers::rupiahDisplay($grandTotalKeseluruhan),
            '#inf_norekammedik#' => $resultHeader ? @$resultHeader['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d F Y H:i:s', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            '#inf_nopendaftaran#' => $resultHeader ? @$resultHeader['no_pendaftaran'] : '',
            '#inf_namapasien#' => $resultHeader ? @$resultHeader['nama_pasien'] : '',
            '#inf_jeniskelamin#' => $resultHeader ? @$resultHeader['jenis_kelamin'] : '',
            '#inf_kasuspenyakit#' => $resultHeader ? @$resultHeader['jeniskasuspenyakit_nama'] : '',
            '#inf_tgllahir#' => $resultHeader ? (isset($resultHeader['tanggal_lahir']) ? date('d F Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_umur#' => $resultHeader ? @$resultHeader['umur'] : '',
            '#inf_dokterjaga#' => $resultHeader ? @$resultHeader['dokter_jaga'] : '',
            '#inf_dokterdpjp#' => $resultHeader ? @$resultHeader['dokter'] : '',
            '#inf_kelaspelayanan#' => $resultHeader ? @$resultHeader['kelaspelayanan_nama'] : '',
            '#inf_penjamin#' => $resultHeader ? @$resultHeader['penjamin_nama'] : '',
            '#inf_carabayar#' => $resultHeader ? @$resultHeader['carabayar_nama'] : '',
        ];
        $print->Output();
    }

    public function actionGetDataResepturDetail()
    {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id');
            $instruksi_id = $request->get('instruksi_id');
            $dataReseptur = InfoResepturView::find()
                ->where([
                    'pendaftaran_id' => $pendaftaran_id,
                    'instruksi_id' => $instruksi_id,
                ])->asArray()->one();


            if ($dataReseptur) {
                $resepturId = $dataReseptur['reseptur_id'];
                $model = new InfoResepturDetailView;
                $query = $model->find()
                    ->where(['pendaftaran_id' => $pendaftaran_id, 'reseptur_id' => $resepturId])
                    ->asArray()->all();

                return $query;
                // $query = DocoRestActiveFilter::advancedFilter($model, $query);
                // return new ActiveDataProvider([
                //     'query' => $query,
                // ]);
            } else {
                return [];
            }
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDeleteReseptur()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $request = Yii::$app->request;
            $model = ResepturDetail::findOne($id);
            if ($model->delete()) {
                return $response['response'] = [
                    'title' => 'Proses Hapus Berhasil !',
                    'text' => 'Data berhasil dihapus',
                ];
            } else {
                return $response['response'] = [
                    'title' => 'Proses Hapus Gagal !',
                    'text' => 'Data Gagal di hapus',
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetSoap($cppt_id)
    {
        $payload = new ParamModel;
        $payload->cppt_id = $cppt_id;

        if ($payload->validate()) {
            $model = Cppt::find()->select([
                'subject',
                'object',
                'a_diag_utama',
                'a_diag_penyerta',
                'planning',
                'catatan_dokter',
            ])->andWhere([
                'cppt_id' => $cppt_id
            ])->asArray()->one();

            return [
                'data' => $model
            ];
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
            'data' => $payload->errors
        ]);
    }

    public function actionGetSoapDraft($pendaftaran_id)
    {
        $payload = new ParamModel;
        $payload->pendaftaran_id = $pendaftaran_id;

        if ($payload->validate()) {
            $model = Cppt::find()
                ->select([
                    'subject',
                    'object',
                    'tgl_cppt',
                    'a_diag_utama',
                    'a_diag_penyerta',
                    'instruksi',
                    'planning',
                    'catatan_dokter',
                ])
                ->andWhere([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->andWhere([
                    'pegawai_id' => Yii::$app->jwt->user->pegawai_id
                ])
                ->andWhere([
                    'is_active' => false
                ])
                ->andWhere([
                    'BETWEEN', 'tgl_cppt', date("Y-m-d H:i:s", strtotime('-24 hours', time())), date('Y-m-d H:i:00')
                ])
                ->asArray()
                ->one();
            if (empty($model)) {
                $existingCppt = Cppt::find(true)->select(['pendaftaran_id', 'cppt_id'])->andWhere(['pendaftaran_id' => $pendaftaran_id, 'is_active' => true])->andWhere(['pasienadmisi_id' => null])->andWhere(['additional_data' => '{"via_soap":true}'])->asArray()->one();
                // retrieve kelompok pegawai by pegawai_id
                $employeeRecord = Pegawai::find(true)->select(['pegawai_id', 'kelompokpegawai_id'])->where(['pegawai_id' => Yii::$app->jwt->user->pegawai_id])->asArray()->one();
                if (empty($existingCppt) && !empty($employeeRecord) && $employeeRecord['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_DOKTER) {
                    // get suggestion from any of tables
                    $asmedRecord = AsesmenMedisRD::find()
                        ->select([
                            'asesmenmedisrd_id',
                            'pendaftaran_id',
                            'riwayat_penyakit_sekarang',
                            'riwayat_penyakit_dahulu',
                            'riwayat_terapi_sebelumnya',
                            'tekanandarah',
                            'nadi',
                            'pernapasan',
                            'suhu',
                            'saturasi_o2',
                            'berat_badan',
                            'tinggi_badan',
                            'g1.metodegcs_nilai AS gcs_eye',
                            'g2.metodegcs_nilai AS gcs_verbal',
                            'g3.metodegcs_nilai AS gcs_motorik'
                        ])
                        ->join('LEFT JOIN', 'metodegcs_m g1', 'g1.metodegcs_id=asesmenmedisrd_t.gcseye_id')
                        ->join('LEFT JOIN', 'metodegcs_m g2', 'g2.metodegcs_id=asesmenmedisrd_t.gcsverbal_id')
                        ->join('LEFT JOIN', 'metodegcs_m g3', 'g3.metodegcs_id=asesmenmedisrd_t.gcsmotorik_id')
                        ->andWhere(compact('pendaftaran_id'))
                        ->asArray()
                        ->one();
                    if (!empty($asmedRecord)) {
                        $areaTubuhDanTerapi = PeriksaTubuh::find()
                            ->select([
                                'periksatubuh_id',
                                'asesmenmedisrd_id',
                                'catatan_tubuh',
                                'bagiantubuh_m.namabagtubuh as area_tubuh',
                                'bagiantubuhdetail_m.nama_bagiantubuh as spesifik_area_tubuh'
                            ])
                            ->join('LEFT JOIN', 'bagiantubuh_m', 'bagiantubuh_m.bagiantubuh_id=periksatubuh_t.bagiantubuh_id')
                            ->join('LEFT JOIN', 'bagiantubuhdetail_m', 'bagiantubuhdetail_m.bagiantubuhdetail_id=periksatubuh_t.bagiantubuhdetail_id')
                            ->andWhere([
                                'asesmenmedisrd_id' => $asmedRecord['asesmenmedisrd_id']
                            ])
                            ->asArray()
                            ->all();
                        $terapi = '';
                        if (!empty($areaTubuhDanTerapi)) {
                            foreach ($areaTubuhDanTerapi as $areaTubuh) {
                                $terapi .= $areaTubuh['area_tubuh'] . ' (' . $areaTubuh['spesifik_area_tubuh'] . ') : ' . $areaTubuh['catatan_tubuh'] . "\n";
                            }
                        }
                        // GCS Suggestion
                        if (empty($asmedRecord['gcs_eye']) && empty($asmedRecord['gcs_verbal']) && empty($asmedRecord['gcs_motorik'])) {
                            $gcsAskep = AsesmenPerawatRD::find()
                                ->select([
                                    'g1.metodegcs_nilai AS gcs_eye',
                                    'g2.metodegcs_nilai AS gcs_verbal',
                                    'g3.metodegcs_nilai AS gcs_motorik'
                                ])
                                ->join('LEFT JOIN', 'metodegcs_m g1', 'g1.metodegcs_id=asesmenperawatrd_t.gcseye_id')
                                ->join('LEFT JOIN', 'metodegcs_m g2', 'g2.metodegcs_id=asesmenperawatrd_t.gcsverbal_id')
                                ->join('LEFT JOIN', 'metodegcs_m g3', 'g3.metodegcs_id=asesmenperawatrd_t.gcsmotorik_id')
                                ->andWhere(compact('pendaftaran_id'))
                                ->asArray()
                                ->one();

                            if (empty($gcsAskep['gcs_eye']) && empty($gcsAskep['gcs_verbal']) && empty($gcsAskep['gcs_motorik'])) {
                                $gcsTriage = Triase::find()
                                    ->select([
                                        'g1.metodegcs_nilai AS gcs_eye',
                                        'g2.metodegcs_nilai AS gcs_verbal',
                                        'g3.metodegcs_nilai AS gcs_motorik'
                                    ])
                                    ->join('LEFT JOIN', 'metodegcs_m g1', 'g1.metodegcs_id=triase_t.gcseye_id')
                                    ->join('LEFT JOIN', 'metodegcs_m g2', 'g2.metodegcs_id=triase_t.gcsverbal_id')
                                    ->join('LEFT JOIN', 'metodegcs_m g3', 'g3.metodegcs_id=triase_t.gcsmotorik_id')
                                    ->andWhere(compact('pendaftaran_id'))
                                    ->asArray()
                                    ->one();

                                // Assign suggested gcs triage
                                $asmedRecord['gcs_eye'] = $gcsTriage['gcs_eye'];
                                $asmedRecord['gcs_verbal'] = $gcsTriage['gcs_verbal'];
                                $asmedRecord['gcs_motorik'] = $gcsTriage['gcs_motorik'];
                            }

                            // Assign suggested gcs askep
                            $asmedRecord['gcs_eye'] = $gcsAskep['gcs_eye'];
                            $asmedRecord['gcs_verbal'] = $gcsAskep['gcs_verbal'];
                            $asmedRecord['gcs_motorik'] = $gcsAskep['gcs_motorik'];
                        }

                        $dataSubject = (!empty($asmedRecord['riwayat_penyakit_sekarang']) ? 'Riwayat Penyakit Sekarang : ' . $asmedRecord['riwayat_penyakit_sekarang'] . "\n" : '') . (!empty($asmedRecord['riwayat_penyakit_dahulu']) ? 'Riwayat Penyakit Dahulu : ' . $asmedRecord['riwayat_penyakit_dahulu'] . "\n" : '') . (!empty($asmedRecord['riwayat_terapi_sebelumnya']) ? 'Riwayat Terapi Sebelumnya : ' . $asmedRecord['riwayat_terapi_sebelumnya'] . "\n" : '');
                        // $dataObject = (!empty($asmedRecord['tekanandarah']) ? 'Tekanan Darah : ' . $asmedRecord['tekanandarah'] . "\n" : '') . (!empty($asmedRecord['nadi']) ? 'Nadi : ' . $asmedRecord['nadi'] . "\n" : '') . (!empty($asmedRecord['pernapasan']) ? 'Pernapasan : ' . $asmedRecord['pernapasan'] . "\n" : '') . (!empty($asmedRecord['suhu']) ? 'Suhu : ' . $asmedRecord['suhu'] . "\n" : '') . (!empty($asmedRecord['berat_badan']) ? 'Berat Badan : ' . $asmedRecord['berat_badan'] . "\n" : '') . (!empty($asmedRecord['tinggi_badan']) ? 'Tinggi badan : ' . $asmedRecord['tinggi_badan'] . "\n" : '') . (!empty($asmedRecord['saturasi_o2']) ? 'Saturasi O2 : ' . $asmedRecord['saturasi_o2'] . "\n" : '') . (!empty($asmedRecord['gcs_eye']) ? 'GCS Eye : ' . $asmedRecord['gcs_eye'] . "\n" : '') . (!empty($asmedRecord['gcs_verbal']) ? 'GCS Verbal : ' . $asmedRecord['gcs_verbal'] . "\n" : '') . (!empty($asmedRecord['gcs_motorik']) ? 'GCS Motorik : ' . $asmedRecord['gcs_motorik'] . "\n" : '') . (!empty($terapi) ? "\n- Status Lokalis -\n" . $terapi : '');
                        $dataObject = 'Tekanan Darah : ' . $asmedRecord['tekanandarah'] . "\n" . 'Nadi : ' . $asmedRecord['nadi'] . "\n" . 'Pernapasan : ' . $asmedRecord['pernapasan'] . "\n" . 'Suhu : ' . $asmedRecord['suhu'] . "\n" . 'Berat Badan : ' . $asmedRecord['berat_badan'] . "\n" . 'Tinggi badan : ' . $asmedRecord['tinggi_badan'] . "\n" . 'Saturasi O2 : ' . $asmedRecord['saturasi_o2'] . "\n" . 'GCS Eye : ' . $asmedRecord['gcs_eye'] . "\n" . 'GCS Verbal : ' . $asmedRecord['gcs_verbal'] . "\n" . 'GCS Motorik : ' . $asmedRecord['gcs_motorik'] . "\n" . (!empty($terapi) ? "\n- Status Lokalis -\n" . $terapi : '');

                        $model = [
                            'subject' => $dataSubject,
                            'object' => $dataObject
                        ];
                    } else {
                        $gcsAskep = AsesmenPerawatRD::find()
                            ->select([
                                'g1.metodegcs_nilai AS gcs_eye',
                                'g2.metodegcs_nilai AS gcs_verbal',
                                'g3.metodegcs_nilai AS gcs_motorik'
                            ])
                            ->join('LEFT JOIN', 'metodegcs_m g1', 'g1.metodegcs_id=asesmenperawatrd_t.gcseye_id')
                            ->join('LEFT JOIN', 'metodegcs_m g2', 'g2.metodegcs_id=asesmenperawatrd_t.gcsverbal_id')
                            ->join('LEFT JOIN', 'metodegcs_m g3', 'g3.metodegcs_id=asesmenperawatrd_t.gcsmotorik_id')
                            ->andWhere(compact('pendaftaran_id'))
                            ->asArray()
                            ->one();

                        if (empty($gcsAskep['gcs_eye']) && empty($gcsAskep['gcs_verbal']) && empty($gcsAskep['gcs_motorik'])) {
                            $gcsTriage = Triase::find()
                                ->select([
                                    'g1.metodegcs_nilai AS gcs_eye',
                                    'g2.metodegcs_nilai AS gcs_verbal',
                                    'g3.metodegcs_nilai AS gcs_motorik'
                                ])
                                ->join('LEFT JOIN', 'metodegcs_m g1', 'g1.metodegcs_id=triase_t.gcseye_id')
                                ->join('LEFT JOIN', 'metodegcs_m g2', 'g2.metodegcs_id=triase_t.gcsverbal_id')
                                ->join('LEFT JOIN', 'metodegcs_m g3', 'g3.metodegcs_id=triase_t.gcsmotorik_id')
                                ->andWhere(compact('pendaftaran_id'))
                                ->asArray()
                                ->one();

                            // Assign suggested gcs triage
                            $gcsAskep['gcs_eye'] = $gcsTriage['gcs_eye'];
                            $gcsAskep['gcs_verbal'] = $gcsTriage['gcs_verbal'];
                            $gcsAskep['gcs_motorik'] = $gcsTriage['gcs_motorik'];
                        }

                        $dataObject = (!empty($gcsAskep['gcs_eye']) ? 'GCS Eye : ' . $gcsAskep['gcs_eye'] . "\n" : '') . (!empty($gcsAskep['gcs_verbal']) ? 'GCS Verbal : ' . $gcsAskep['gcs_verbal'] . "\n" : '') . (!empty($gcsAskep['gcs_motorik']) ? 'GCS Motorik : ' . $gcsAskep['gcs_motorik'] . "\n" : '');

                        $model = [
                            'object' => $dataObject
                        ];
                    }
                } else if (!empty($employeeRecord) && $employeeRecord['kelompokpegawai_id'] == DocoConstants::KELOMPOK_PEGAWAI_PERAWAT) {
                    $gcsAskep = AsesmenPerawatRD::find()
                            ->select([
                                'keluhan',
                                'berat_badan',
                                'tinggi_badan',
                                'detak_nadi',
                                'suhu_tubuh',
                                'tensi'
                            ])
                            ->andWhere(compact('pendaftaran_id'))
                            ->asArray()
                            ->one();
                    $_askep_subject = '';
                    $_askep_object = '';
                    if(!empty($gcsAskep)){
                        $_askep_subject = ArrayHelper::getValue($gcsAskep,'keluhan','');
                        $_askep_object .='Berat Badan : ' . ArrayHelper::getValue($gcsAskep,'berat_badan','-');
                        $_askep_object .="\nTinggi Badan : " . ArrayHelper::getValue($gcsAskep,'tinggi_badan','-');
                        $_askep_object .="\nNadi : " . ArrayHelper::getValue($gcsAskep,'detak_nadi','-');
                        $_askep_object .="\nTensi/TD : " . ArrayHelper::getValue($gcsAskep,'tensi','-');
                        $_askep_object .="\nSuhu : " . ArrayHelper::getValue($gcsAskep,'suhu_tubuh','-');

                        $model = [
                            'subject' => $_askep_subject,
                            'object' => $_askep_object
                        ];
                    }
                    
                } else {
                    $model = [
                        'subject' => '',
                        'object' => '',
                        'planning' => '',
                    ];
                }
            }
            $model['planning'] = (isset($model['planning']) ? $model['planning'] : '') . $this->getPlanningSuggestion($pendaftaran_id);
            $model['tgl_cppt'] = date('Y-m-d H:i:00');
            return [
                'data' => $model
            ];
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
            'data' => $payload->errors
        ]);
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
    public function soapCreateOrUpdate($cpptId = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $loginpemakai_id = Yii::$app->jwt->user->loginpemakai_id;
        // checking pendaftaran date with cppt date
        if (isset($post['tgl_cppt']) && !empty($post['tgl_cppt'])) {
            $post['tgl_cppt'] = date("Y-m-d H:i:s", strtotime($post['tgl_cppt']));
        } else {
            $post['tgl_cppt'] = date("Y-m-d H:i:s");
        }
        $registrationRecord = Pendaftaran::find()->select(['pendaftaran_id', 'tgl_pendaftaran'])->andWhere(['pendaftaran_id' => $post['pendaftaran_id']])->asArray()->one();
        if (empty($registrationRecord)) {
            $this->transactionClass->rollBack();
            return $this->responseJson(400, 'Pendaftaran tidak ditemukan.');
        }  else if (date("Y-m-d H:i", strtotime('+1 minutes')) < $post['tgl_cppt']) {
            $this->transactionClass->rollBack();
            return $this->responseJson(400, 'Tanggal CPPT tidak boleh lebih dari saat ini.');
        }
        $model = new Cppt;
        if (empty($cpptId)) {
            $model = Cppt::find()
                ->andWhere([
                    'pendaftaran_id' => $registrationRecord['pendaftaran_id'],
                    'pegawai_id' => Yii::$app->jwt->user->pegawai_id,
                    'is_active' => false,
                    'tgl_cppt' => $post['tgl_cppt']
                ])
                ->one();

            if (empty($model)) {
                $model = new Cppt;
            }
            if ($post['is_active'] == 0) {
                $post['is_active'] = false;
                // ambil draft
            } else if ($post['is_active'] == 1) {
                $post['is_active'] = true;
            }
        }

        if (isset($post['a_diag_utama'])) {
            $valDiagUtama = [];
            $splitVal = explode('_', $post['a_diag_utama']);
            if (count($splitVal) > 1) {
                $valDiagUtama['id'] = $splitVal[0];
                $splitText = explode('-', $splitVal[1]);
                if (count($splitText) > 1) {
                    $valDiagUtama['kode'] = $splitText[0];
                    $valDiagUtama['nama'] = $splitText[1];
                }
                $valDiagUtama['text'] = $splitVal[1];
            } else {
                $valDiagUtama['text'] = $post['a_diag_utama'];
            }
            $model->a_diag_utama = ($valDiagUtama);
            unset($post['a_diag_utama']);
        }
        $valueDiagPenyerta = [];
        if (isset($post['a_diag_penyerta']) && is_array($post['a_diag_penyerta'])) {
            foreach ($post['a_diag_penyerta'] as $key_diag) {
                $valDiag = [];
                $splitVal = explode('_', $key_diag);
                if (count($splitVal) > 1) {
                    $valDiag['id'] = $splitVal[0];
                    // $valDiag['kode']
                    $splitText = explode('-', $splitVal[1]);
                    if (count($splitText) > 1) {
                        $valDiag['kode'] = $splitText[0];
                        unset($splitText[0]);
                        $txtDiag = '';
                        foreach ($splitText as $keyTxt => $valueTxt) {
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
            $model->a_diag_penyerta = ($valueDiagPenyerta);
            unset($post['a_diag_penyerta']);
        }
        if (!empty($cpptId)) {
            $post['is_active'] = true;
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
                $instruksi_strike_text = isset($post['instruksi']) ? DocoHelpers::crossOutDifferences($existingCppt['instruksi'], $post['instruksi']) : $existingCppt['instruksi'];
                $catatan_dokter_strike_text = isset($post['catatan_dokter']) ? DocoHelpers::crossOutDifferences($existingCppt['catatan_dokter'], $post['catatan_dokter']) : $existingCppt['catatan_dokter'];
                $getDiagnosaText['text'] = DocoHelpers::crossOutDifferences($getDiagnosaText['text'], $valDiagUtama['text']);
                Cppt::updateAll([
                'subject' => $subject_strike_text,
                'object' => $object_strike_text,
                'planning' => $planning_strike_text,
                'a_diag_utama' => $getDiagnosaText,
                'a_diag_penyerta' => $getDiagnosaPenyertaText,
                'instruksi' => $instruksi_strike_text,
                'catatan_dokter' => $catatan_dokter_strike_text 
                ],
                ['cppt_id' => $cpptId]);
                if ($existingCppt['pegawai_id'] != Yii::$app->jwt->user->pegawai_id) {
                    $this->transactionClass->rollBack();
                    return $this->responseJson(400, 'Anda dilarang untuk mengubah data CPPT ini.');
                }
                // unset the unfollowing field
                unset($post['ruangan_id'], $post['pendaftaran_id'], $post['pegawai_id'], $post['pasien_id'], $post['pasienadmisi_id'], $post['kamarruangan_id'], $post['kamartempattidur_id'], $post['kamar_tempattidur'], $post['cppt_id'], $existingCppt['cppt_id']);
                $model->attributes = array_merge($existingCppt, $post);

                if (!empty($existingCppt['referred_id'])) {
                    $referredId = $existingCppt['referred_id'];
                } else {
                    $referredId = $cpptId;
                    Cppt::updateAll(['is_deleted' => true,
                    'last_modified_by' => $loginpemakai_id,
                    'deleted_by' => $loginpemakai_id,
                    'deleted_date' =>  date("Y-m-d H:i:s"),
                    ],
                    ['cppt_id' => $cpptId]);
                }
                Cppt::updateAll(['is_deleted' => true,
                'last_modified_by' => $loginpemakai_id,
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
        }
        $model->is_icd_x = $post['is_icd_x'];
        $model->scenario = 'soap';
        $model->tgl_cppt = $post['tgl_cppt'];

        $additional_data = $model->additional_data;
        if(!is_array($additional_data)) {
            $additional_data = json_decode($additional_data, true);
        }
        $additional_data['via_soap'] = true;
        $model->additional_data = json_encode($additional_data);

        if ($model->validate()) {
            if (isset($referredId)) {
                $model->referred_id = $referredId;
            }
            if ($model->save()) {
                $this->transactionClass->commit();
                return [
                    'message' => 'Data Berhasil di simpan',
                    'data' => [
                        'a_diag_utama' => $model->a_diag_utama
                    ]
                ];
            } else {
                $this->transactionClass->rollBack();
                return $this->responseJson(400, 'Proses penyimpanan data gagal.');
            }
        } else {
            $this->transactionClass->rollBack();
            $response = $model->getErrors();
            return $this->responseJson(400, 'Simpan data gagal', $response);
        }
    }

    public function actionGetLastCppt($pendaftaran_id, $pegawai_id)
    {
        $today = date('Y-m-d 00:00:00', strtotime('NOW'));
        $model = CpptView::find()
            ->select(['cppt_id', 'referred_id'])
            ->where(['pendaftaran_id' => $pendaftaran_id, 'pegawai_id' => $pegawai_id, 'is_verifikasi' => false, 'is_active' => true])
            ->andWhere(['>', 'tgl_cppt', $today])
            ->andWhere(['IS', 'pemberi_instruksi_id', null])
            ->orderBy(['tgl_cppt' => SORT_DESC])
            ->asArray()
            ->one();
        if ($model) {
            return !empty($model['referred_id']) ? $model['referred_id'] : $model['cppt_id'];
        } else {
            return 0;
        }
    }

    private function getPlanningSuggestion($pendaftaran_id) {
        $lastDate = Cppt::find()->select(new Expression('max(tgl_cppt)'))
            ->andWhere(['pendaftaran_id' => $pendaftaran_id, 'pasienadmisi_id' => null])
            ->andWhere(new Expression("COALESCE((additional_data::json->>'via_soap')::boolean, false) = true"))
            ->andWhere(['is_active' => true])
            ->scalar();
        $query = InfoInstruksiView::find()
            ->select(['tgl_instruksi', 'tipe_instruksi', 'grouping_tipe', 'tindakaninstruksi_nama', 'qty', 'satuankecil_nama', 'catatan_instruksi'])
            ->andWhere(['pendaftaran_id' => $pendaftaran_id, 'pasienadmisi_id' => null])
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
            ->andWhere(['pendaftaran_t.pendaftaran_id' => $pendaftaran_id, 'pendaftaran_t.pasienadmisi_id' => null])
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

    public function actionGetFilterCppt($pasien_id, $term, $type)
    {
        switch ($type) {
            case 'ruangan':
                $dataGroup = SoapRsView::find()
                    ->select([
                        'ruangan_id as id',
                        'ruangan_nama as text',
                    ])
                    ->where(['pasien_id' => $pasien_id])
                    ->andWhere(['LIKE', 'LOWER(ruangan_nama)', strtolower($term)]);
                break;
            default:
                $dataGroup = SoapRsView::find()
                    ->select([
                        'pegawai_id as id',
                        'nama_pegawai as text',
                    ])
                    ->where(['pasien_id' => $pasien_id])
                    ->andWhere(['kelompokpegawai_id' => DocoConstants::KELOMPOK_MEDIS])
                    ->andWhere(['LIKE', 'LOWER(nama_pegawai)', strtolower($term)]);
                break;
        }

        if($this->konfigCpptKosong == TRUE) {
            $dataGroup->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
        }

        return $dataGroup->distinct()->asArray()->all();
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

    public function actionDeleteCppt() {
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id');
            $cppt_id = $request->post('cppt_id');
            $user_id = $request->post('user_id');
            $transaction = Yii::$app->db->beginTransaction();
            
            $deleteCppt = Cppt::updateAll(
            [
                'is_deleted' => true,
                'deleted_date' => date('Y-m-d H:i:s'),
                'deleted_by' => $user_id
            ], 'pendaftaran_id = '.$pendaftaran_id.' AND cppt_id = '.$cppt_id.'');

            if($deleteCppt) {
                $this->insertLogActivity($cppt_id);
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

    private function insertLogActivity($cppt_id)
    {
        $getCppt = Yii::$app->db->createCommand("
            SELECT subject,object,a_diag_utama,a_diag_penyerta,planning
            FROM cppt_t WHERE cppt_id = {$cppt_id}
        ")->queryOne();
        
        $logModel = new LogActivityR;
        $logModel->transaksi_id = $cppt_id;
        $logModel->tgl = date('Y-m-d H:i:s');
        $logModel->tipe = 'CPPT-RD';
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

    public function actionExportPdfCpptBgproses()
    {
        $kode_doc = 'Rd-List-Dpjp';
        $request = Yii::$app->request;
        $get = $request->get();
        $get['only_attributes'] = true;
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $fetchLimit = 20;

        $filter = [
            'ruangan_id' => ArrayHelper::getValue($get, 'filterruangan_id'),
            'pegawai_id' => ArrayHelper::getValue($get, 'filterpegawai_id'),
            'kelompokpegawai_id' => ArrayHelper::getValue($get, 'filter_kelompokpegawai_id'),
            'tgl_cppt' => ArrayHelper::getValue($get, 'tgl_cppt'),
            'order' => ArrayHelper::getValue($get, 'order'),
        ];

        $pendaftaran_id = ArrayHelper::getValue($get, 'pendaftaran_id');

        $total_data = $this->getTotalDataCppt($pendaftaran_id, $filter, $this->konfigEditCpptCoret, $this->konfigCpptKosong);
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $totalPerPage = ceil($total_data/$fetchLimit);

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'request' => $get,
            'totalPerPage' => 1,
            'countData' => 1,
            'token' => $auth,
            'xOwner' => $xOwner,
            'kode_doc' => $kode_doc,
            'is_ftp' => ArrayHelper::getValue($get, 'is_ftp'),
            'fileName' => ArrayHelper::getValue($get, 'nama_dokumen'),
            'pendaftaran_id' => $pendaftaran_id,
        ], 'cppt_pdf_igd', 'import_data_cppt_pdf');

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $total_data,
        ];
    }

    private function getTotalDataCppt($pendaftaran_id, $filter = [], $konfig_edit_cppt_coret = false, $konfig_cppt_kosong = false)
    {
        $request = Yii::$app->request;
        $params = $request->get();

        $queryDpjp = SoapRsView::find($konfig_edit_cppt_coret)
            ->select([
                'pendaftaran_id'
            ]);
        $queryDpjp->andWhere(['pendaftaran_id' => $pendaftaran_id]);
        $queryDpjp->andWhere(['tipe' => 'RD']);

        if($konfig_cppt_kosong == TRUE) {
            $queryDpjp->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL OR is_verbal_order = true)");
        }

        if (
            in_array(ArrayHelper::getValue($filter, 'kelompokpegawai_id', ''), [
                DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                DocoConstants::KELOMPOK_PEGAWAI_DOKTER
            ])
        ) {
            $queryDpjp->andWhere([
                'kelompokpegawai_id' => ArrayHelper::getValue($filter, 'kelompokpegawai_id', ''),
            ]);
        }
        if (!empty(ArrayHelper::getValue($filter, 'ruangan_id'))) {
            $queryDpjp->andWhere([
                'ruangan_id' => ArrayHelper::getValue($filter, 'ruangan_id')
            ]);
        }

        if (!empty(ArrayHelper::getValue($filter, 'pegawai_id'))) {
            $queryDpjp->andWhere([
                'pegawai_id' => ArrayHelper::getValue($filter, 'pegawai_id')
            ]);
        }

        if(!empty(ArrayHelper::getValue($filter, 'tgl_cppt'))){
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $explode = explode("-", ArrayHelper::getValue($filter, 'tgl_cppt'));
            if (count($explode) == 2) {
                $start = date_format(date_create_from_format('d/m/Y', $explode[0]), 'Y-m-d').date(' 00:00:00');
                $end = date_format(date_create_from_format('d/m/Y', $explode[1]), 'Y-m-d').date(' 23:59:59');
            }
            $queryDpjp->andWhere(['between', 'tgl_soaprj', $start, $end]);
        }

        $total_data = $queryDpjp->count();

        return $total_data;
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

}
