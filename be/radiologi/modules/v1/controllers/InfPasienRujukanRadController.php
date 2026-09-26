<?php


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoAkunting;
use Doco\components\DocoMessages;
use Doco\Services\Vendors\RadiologiService;
use app\modules\v1\models\InfoOrderanRadView;
use app\modules\v1\models\InfoOrderanRadDetailView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PasienKirimUnitlain;
use app\modules\v1\models\PermintaanKePenunjang;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\TindakanPelayananT;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\BatalOrderPenunjangT;
use app\modules\v1\models\TarifPaketPenunjangView;
use app\modules\v1\models\TarifPenunjangView;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\SyncPengeluaranobat;
use app\modules\v1\models\SyncTindakan;
use app\modules\v1\models\Tindakankomponen;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\PermintaanKepenunjangan;
use app\modules\v1\models\RekapRis;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\TindakanBmhpView;
use SirsCore\features\FeatureTindakanBmhp;
use app\modules\v1\models\ObatAlkesPasien;
use Doco\Notifications\RadiologiNotification;

use yii\helpers\ArrayHelper;
use SirsCore\features\IntegrasiAkunting;
use Doco\Services\KasirService;

use Doco\models\Cppt;
use app\components\object\RisObject;
use app\components\object\RekapRisObject;
use app\modules\master\models\PermintaanKePenunjangT;
use Doco\components\NoCountDataProvider;
use Doco\Traits\TindakanPenunjangTrait;
use Doco\models\radiologi\PemeriksaanPasienRadiologiView;
use Doco\models\ProfilRsView;
use Doco\models\SoapRj;
use Doco\models\TindakanPelayanan;
use Doco\exceptions\ValidationException;

class InfPasienRujukanRadController extends \Doco\components\DocoActiveController
{
    use TindakanPenunjangTrait;
    // Model class
    public $modelClass = 'app\modules\v1\models\InfoOrderanRadView';

    /**
     * Untuk Kebutuhan Integerasi GE
     * @var array
     */
    public $messageBroker = [
        'proses-approve' => [
            'services' => [
                'Ris' => [
                    'RisBroker' => [
                        'query_params' => ['pasienkirimkeunitlain_id'],
                        'state' => true
                    ]
                ],
                'InaBroker' => [
                    'RisBroker' => [
                        'state' => true,
                        'query_params' => ['pasienkirimkeunitlain_id'],
                    ]
                ]
            ]
        ],
    ];
    // Verbs
    public function verbs()
    {
        // Verbs parent
        $verbs = parent::verbs();

        // Return verbs
        return $verbs;
    }

    // Actions
    public function actions()
    {
        // Actions parent
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $model = new InfoOrderanRadView;
            $query = $model::find();

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $betweenLahir = false;
            // default awal 30 hari sebelum dan 30 hari sesudah dari tanggal hari ini
            $start = date('Y-m-d 00:00:00', strtotime('-1 months'));
            $end = date('Y-m-d 23:59:00', strtotime('+1 months'));
            $startLahir = '';
            $endLahir = '';

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_rujukan']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                    $betweenLahir = true;
                }
                if (isset($_GET['advanced-filter']['is_bayar']) && $_GET['advanced-filter']['is_bayar'] != '') {
                    if ($_GET['advanced-filter']['is_bayar'] == '1') {
                        $query->andWhere(['is_bayar' => true]);
                    } else {
                        $query->andWhere(['is', 'is_bayar', null]);
                    }
                    unset($_GET['advanced-filter']['is_bayar']);
                }
                if(isset($_GET['advanced-filter']['type'])) {
                    $type = $_GET['advanced-filter']['type'];
                    if($type == 1) {
                        $query->andWhere(['status_penunjang' => (string) DocoConstants::DISETUJUI]);
                    }
                    elseif($type == 2) {
                        $query->andWhere(['status_penunjang' => (string) DocoConstants::BELUM_SETUJU]);
                    }
                    elseif($type == 3) {
                        $query->andWhere(['status_penunjang' => (string) DocoConstants::BTL_APPROVE]);
                    }
                    elseif($type == 4) {
                        $query->andWhere(['cyto_tindakan' => true]);
                    }
                }
            }

            $query->andWhere(['between', 'tgl_rujukan', $start, $end]);

            $query->andWhere([
                'or',
                ['not', ['jml_pemeriksaan' => 0]],
                ['is_rujukan' => false],
            ]);

            if (!empty($startLahir) && !empty($endLahir) && $betweenLahir) {
                $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
            }
            /**
             * End Special Condition date range
             **/

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
            return new NoCountDataProvider([
                'query' => $query,
            ]);
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

    public function actionPasienRujukan()
    {
        $params = Yii::$app->request;
        $id = $params->get('id');

        $model = new InfoOrderanRadView();
        $query = $model::find();
        $query->where(['pasienkirimkeunitlain_id' => $id]);

        return $query->one();
    }

    public function actionUpdateTanggalRujukan($id)
    {
        $request = Yii::$app->request;
        $getPasien = PasienKirimUnitlain::find()->andWhere([
            'pasienkirimkeunitlain_id' => $id
        ])->one();

        if (empty($getPasien)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Pasien Rujukan tidak ditemukan'
            ]);
        }

        if ($getPasien->status_penunjang != DocoConstants::BELUM_SETUJU) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Data pasien tidak bisa diupdate karena sudah di Approve / di Batalkan'
            ]);
        }

        $tanggalRujukan = $request->post('tgl_rujukan');

        if (empty($tanggalRujukan)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Tanggal Rujukan tidak boleh kosong.'
            ]);
        }

        $getPasien->tgl_kirimpasien = date('Y-m-d H:i:s', strtotime($tanggalRujukan));


        if ($getPasien->save()) {
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM);
    }

    public function actionProsesApprove()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $header = ArrayHelper::getValue($post, 'PasienMasukPenunjangForm');
        $detail = ArrayHelper::getValue($post, 'detail');
        if(!empty($detail)) {
            $detail = json_decode($detail, true);
        }
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $pasienKirimUnitLainId = $request->get('pasienkirimkeunitlain_id', null);
        if(empty($pasienKirimUnitLainId)) {
			return [
                'status' => 500,
                'title' => 'Terjadi kesalahan',
                'text' => 'Data tidak ditemukan'
			];
	  	}
        $listTindakanApprove = $dataDiApprove = [];
        if(!empty($detail)) {
			foreach ($detail as $val) {
				$daftarTindakanId = ArrayHelper::getValue($val, 'daftartindakan_id');
				$permintaanKePenunjangId = ArrayHelper::getValue($val, 'permintaankepenunjang_id');
				$listTindakanApprove[] = $daftarTindakanId;
				$dataDiApprove[$permintaanKePenunjangId] = $val;
			}
		}
        try {
            $pasienKirimUnitLain = PasienKirimUnitlain::findOne(
                ['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId]
            );
            
            if (!empty($pasienKirimUnitLain)) {
                $infoOrderanRadView = InfoOrderanRadView::findOne(
                    ['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId]
                );
                if(!empty($infoOrderanRadView)) {
                    $pasienId = $infoOrderanRadView->pasien_id;
					$pendaftaranId = $infoOrderanRadView->pendaftaran_id;
					$pasienAdmisiId = $infoOrderanRadView->pasienadmisi_id;
					$ruanganPenunjangId = $infoOrderanRadView->ruanganpenunjang_id;
					$noPendaftaran = $infoOrderanRadView->no_pendaftaran;
                    $inputPasienMasuk = [
                        'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId,
                        'kelaspelayanan_id' => $infoOrderanRadView->kelaspelayanan_id,
                        'jeniskasuspenyakit_id' => $infoOrderanRadView->jeniskasuspenyakit_id,
                        'pasienadmisi_id' => $pasienAdmisiId,
                        'ruangan_id' => $ruanganPenunjangId,
                        'pasien_id' => $pasienId,
                        'pendaftaran_id' => $pendaftaranId,
                        'ruanganasal_id' => $infoOrderanRadView->ruanganasal_id,
                        'tglmasukpenunjang' => date('Y-m-d H:i:s'),
                        'kunjungan' => $infoOrderanRadView->kunjungan,
                        'panggil_antrian' => false,
                        'instalasiasal_id' => $infoOrderanRadView->instalasiasal_id,
                    ];

                    /** bila pembayaran nya perorangan. maka status periksa akn menjadi null **/
                    if (($infoOrderanRadView->instalasi_id == DocoConstants::INST_ID_RI
                        || $infoOrderanRadView->instalasi_id == DocoConstants::VAR_CM_IGD)
                        || $infoOrderanRadView->penjamin_id != DocoConstants::PENJAMIN_ID
                    ) {
                        $inputPasienMasuk['status_periksa'] = DocoConstants::BLM_PERIKSA;
                    }

                    PermintaanKepenunjangan::updateAll(['is_approve' => true, 'tgl_approve' => date('Y-m-d H:i:s'), 'is_referred' => false], [
                        'and', 
                        ['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId], 
                        ['IN', 'daftartindakan_id', $listTindakanApprove] 
                    ]);

                    $getPasienMasukPenunjang =  PasienMasukPenunjangT::find()
                        ->leftJoin('ruangan_m','ruangan_m.ruangan_id = pasienmasukpenunjang_t.ruanganasal_id')
                        ->leftJoin('instalasi_m','instalasi_m.instalasi_id = ruangan_m.instalasi_id')
                        ->where(['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId])
                        ->orWhere(['and',
                            ['pendaftaran_id' => $pendaftaranId ],
                            ['ruangan_m.instalasi_id' => DocoConstants::VAR_I_RAD ],
                            ['pasienmasukpenunjang_t.pasienkirimkeunitlain_id' => null ] //ketika daftar penunjang tanpa tindakan, dari pendaftaran di create pasienmasuk penunjang dengan kondisi pasienkirimkeunitlain_id null
                            ]) //handling pendaftaran penunjang tanpa tindakan
                        ->one();
                        
                    $modelPasienMasukPenunjang = empty($getPasienMasukPenunjang) ? new PasienMasukPenunjangT : $getPasienMasukPenunjang;
                    $attributes = empty($getPasienMasukPenunjang) ? $inputPasienMasuk : $getPasienMasukPenunjang->attributes;
                    $modelPasienMasukPenunjang->attributes = $attributes;
                    if ($modelPasienMasukPenunjang->validate() && $modelPasienMasukPenunjang->save()) {
                        $pasienMasukPenunjangId = $modelPasienMasukPenunjang->pasienmasukpenunjang_id;
                        $jumlahPemeriksaan = PermintaanKepenunjangan::find()
                        ->where([
                            'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId,
                            'is_approve' => false
                        ])->count();
                        
                        if($jumlahPemeriksaan == 0) {
                            $pasienKirimUnitLain->pasienmasukpenunjang_id = $pasienMasukPenunjangId;
                            $pasienKirimUnitLain->status_penunjang = DocoConstants::DISETUJUI;
                            $pasienKirimUnitLain->catatan_dokterpengirim = ArrayHelper::getValue($header, 'catatan_dokterpengirim');
                            $pasienKirimUnitLain->save();
                        }

                        $tindakanArray = $filterTindakanId = [];
                        $infoOrderanRadDetailView = InfoOrderanRadDetailView::find()->where(
                            ['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId]
                        )->asArray()->all();
                        if(!empty($infoOrderanRadDetailView)) {
                            foreach ($infoOrderanRadDetailView as $kt => $vt) {
                                $permintaanKePenunjangId = ArrayHelper::getValue($vt, 'permintaankepenunjang_id');
                                if (!empty($dataDiApprove)) {
                                    if(isset($dataDiApprove[$permintaanKePenunjangId])) {
                                        $data = $dataDiApprove[$permintaanKePenunjangId];
                                        $daftarTindakanId = ArrayHelper::getValue($vt, 'daftartindakan_id');
                                        $tindakanArray[] = [
                                            'pasienmasukpenunjang_id' => $pasienMasukPenunjangId,
                                            'dokter_id' => ArrayHelper::getValue($data, 'dokter_id'),
                                            'daftartindakan_id' => ArrayHelper::getValue($data, 'daftartindakan_id'),
                                            'tipepaket_id' => ArrayHelper::getValue($vt, 'tipepaket_id'),
                                            'is_cyto' => ArrayHelper::getValue($vt, 'is_cyto', false),
                                            'qty' => ArrayHelper::getValue($vt, 'qtypermintaan'),
                                            'permintaankepenunjang_id' => $permintaanKePenunjangId,
                                        ];
                                        $filterTindakanId[] = $daftarTindakanId;
                                    }
                                }
                            }
                        }

                        $payloadHeader = [
                            'no_pendaftaran' => $noPendaftaran,
                            'kelaspelayanan_id' => $infoOrderanRadView->kelaspelayanan_id,
                            'penjamin_id' => $infoOrderanRadView->penjamin_id,
                            'carabayar_id' => $infoOrderanRadView->carabayar_id,
                            'ruangan_id' => $infoOrderanRadView->ruanganpenunjang_id,
                            'instalasi_id' => $infoOrderanRadView->instalasipen_id,
                        ];
                        $integrateKasir = (new KasirService)->tagihanPenunjang($payloadHeader, $tindakanArray);
                        if(!empty($integrateKasir)){
                            if(isset($integrateKasir['meta']['result']) && $integrateKasir['meta']['result'] == 'failed'){
                                $message = isset($integrateKasir['message']) ? $integrateKasir['message'] : 'Terjadi kesalahan saat integerasi dengan kasir'; 
                                throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                                    'text' => $message
                                ]);
                            }
                        }
                        $getTindakanPelayanan = TindakanPelayanan::find()->select([
                            'tindakanpelayanan_id',
                            'daftartindakan_id',
                            'tgl_tindakan',
                            'dokterpenanggungjawab_id',
                        ])->where([
                            'pasienmasukpenunjang_id' => $pasienMasukPenunjangId,
                            'daftartindakan_id' => $filterTindakanId
                        ])->asArray()->all();

                        if (!empty($getTindakanPelayanan)) {
                            foreach ($getTindakanPelayanan as $index => $tindakanPelayanan) {
                                $daftarTindakanId = ArrayHelper::getValue($tindakanPelayanan, 'daftartindakan_id');
                                $condition = 'pasienkirimkeunitlain_id = ' . $pasienKirimUnitLainId . ' and daftartindakan_id = ' . $daftarTindakanId;
                                PermintaanKepenunjangan::updateAll(
                                    [
                                        'tindakanpelayanan_id' => ArrayHelper::getValue($tindakanPelayanan, 'tindakanpelayanan_id')
                                    ],
                                    $condition
                                );
                            }
                            /** Blok mapping tindakan bmhp */
                            $this->insertBmhp($pasienKirimUnitLainId, DocoConstants::INST_ID_RAD, $pasienMasukPenunjangId, null, $tindakanArray);
                        }

                        // jika bukan rawat darurat, is karcis true & status bayar belum lunas
                        // jika rawat darurat, status bayar belum lunas
                        $statusLunas = DocoConstants::BELUM_LUNAS;
                        if ($infoOrderanRadView->instalasi_id != DocoConstants::VAR_CM_IGD) {
                            $updateArray = ['is_karcis' => true, 'status_bayar' => $statusLunas];
                        } else {
                            $updateArray = ['status_bayar' => $statusLunas];
                        }
                        Pendaftaran::updateAll($updateArray, "pendaftaran_id = {$pendaftaranId}");
                        $transaction->commit();
                        $dataPayload = [
                            'pasienkeunitlain_id' => $pasienKirimUnitLainId,
                            'pendaftaran_id' => $pendaftaranId,
                        ];
            
                        RadiologiNotification::addNotification($dataPayload, 'approve');
                        $result = [
                            'status' => 200,
                            'title' => 'Input Berhasil',
                            'text' => 'Approve Berhasil',
                        ];
                    }
                    else {
                        $result['status'] = 500;
                        $result['title'] = 'Gagal insert';
                        $result['text'] = $modelPasienMasukPenunjang->getErrors();
                    }
                }
            } else {
                $transaction->rollBack();
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data tidak ditemukan'
                ];
            }
            return $result;
        } catch (ValidationException $e) {
            $transaction->rollBack();
            return $e->getOptions();
        } catch (\Exception $e) {
            $this->logError($e);
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionProsesBatal()
    {
        $post = \Yii::$app->request->post();

        /**
         * pertama tanggal tidak boleh melewati tanggal order rujukan
         * kedua pasien yang sudah melakukan pembayaran tidak bisa membatalkan
         * ketiga pasien yang sudah input speciment atau sample tidak bisa batal
         * keempat. pasien yang sudah di batalkan di pasien lab nya tidak bisa dibatalkan
         **/
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $PasienKirimUnitLainT = PasienKirimUnitlain::findOne([
                'pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id']
            ]);
            $pendaftaran_id = $PasienKirimUnitLainT->pendaftaran_id;
            $pasienmasukpenunjang_id = $PasienKirimUnitLainT->pasienmasukpenunjang_id;
            if (empty($pendaftaran_id)) {
                $pasienAdmisi = PasienAdmisi::find()->where(['pasienadmisi_id' => $PasienKirimUnitLainT->pasienadmisi_id])->one();
                $pendaftaran_id = $pasienAdmisi->pendaftaran_id;
            }

            if (!empty($PasienKirimUnitLainT)) {
                $tanggal_batal = "";
                if (!empty($post['tanggal_batal'])) {
                    $tanggal_batal = date('Y-m-d H:i:s', strtotime($post['tanggal_batal']));
                }
                $inputBatalOrder = array(
                    'pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id'],
                    'tgl_batalorder' => $tanggal_batal,
                    'peg_menyetujui_id' => $post['disetujui_oleh'],
                    'alasan' => $post['alasan_pembatalan'],
                    'deleted_by' => Yii::$app->jwt->user->pegawai_id,
                    'additional_data' => json_encode(array('pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id'])),
                );
              
                $mBatalOrderPenunjangT = new BatalOrderPenunjangT;
                $mBatalOrderPenunjangT->attributes = $inputBatalOrder;
                if ($mBatalOrderPenunjangT->save()) {
                    /** update pasien kirim unit lain **/
                    $PasienKirimUnitLainT->status_penunjang = DocoConstants::BTL_APPROVE;
                    $PasienKirimUnitLainT->save();
                    /** update pasien kirim unit lain **/
                  
                    /** Integrasi akunting **/
                    // $this->integrateTindakanCrud($pendaftaran_id, 'DELETE');
                    if (!empty($pasienmasukpenunjang_id)) {
                        $PasienMasukPenunjangT = PasienMasukPenunjangT::find()->select(['no_masukpenunjang', 'pasienmasukpenunjang_id','instalasiasal_id','pendaftaran_id'])->where(['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id])->one();
                        $getInstalasiPenunjang = Instalasi::find()->where(['instalasi_id' => $PasienMasukPenunjangT->instalasiasal_id])
                                                ->andWhere(['is_penunjang' => true])->one();
                        if ($getInstalasiPenunjang) {
                            /** update status pasien pendaftaran **/
                            $getPendaftaran = Pendaftaran::findOne(['pendaftaran_id' => $pendaftaran_id]);
                            $getPendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_BTL_PRKS;
                            $getPendaftaran->update();
                            /** update status pasien pendaftaran **/
                        }
                        $integrateKasir = (new KasirService)->batalTindakan([
                            'no_masukpenunjang' => $PasienMasukPenunjangT->no_masukpenunjang,
                        ]);
                        if(!empty($integrateKasir)){
                            if(isset($integrateKasir['meta']['result']) && $integrateKasir['meta']['result'] == 'failed'){
                                $message = isset($integrateKasir['message']) ? $integrateKasir['message'] : 'Terjadi kesalahan saat integerasi dengan kasir'; 
                                throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                                    'text' => $message
                                ]);
                            }
                        }
                    }
                    $transaction->commit();
                    $dataPayload = [
                        'pasienkeunitlain_id' => $post['pasienkirimkeunitlain_id'],
                        'pendaftaran_id' => $pendaftaran_id,
                    ];
        
                    RadiologiNotification::addNotification($dataPayload, 'batalapprove');
                    return $this->responseJson(200, 'Pembatalan Order Berhasil!');
                } else {
                    $transaction->rollBack();
                    return $this->responseJson(500, 'Pembatalan Order Gagal!');
                }
            } else {
                $transaction->rollBack();
                return $this->responseJson(500, 'Pembatalan Order Gagal!');
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, 'Pembatalan Order Gagal!');
        }
    }


    /**
     * @controller actionExportPdf
     * @attribute #table_exportpdf# => table
     * @attribute #tgl_awal_bulan# => tanggal awal bulan
     * @attribute #tgl_akhir_bulan# => tanggal Akhir bulan
     **/
    public function actionExportPdf()
    {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
        }
        try {
            $request = Yii::$app->request;
            $model = new InfoOrderanRadView;
            $query = $model::find();
            $query->andWhere(['between', 'tgl_rujukan', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($model)) {
                $header = array();
                $print = new DocoPrint();
                $print->attributes = [
                    '#tgl_awal_bulan#' => DocoHelpers::convDateTime($start, true, false),
                    '#tgl_akhir_bulan#' => DocoHelpers::convDateTime($end, true, false),
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'header' => $header,
                        'model' => $query,
                    ]),
                ];
                $print->Output();
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

    public function actionExportExcel()
    {
        try {
            $data = array();
            $header = array();
            $request = Yii::$app->request;
            $model = new InfoOrderanRadView;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($query)) {
                $counter = 0;

                foreach ($query as $index => $value) {
                    $data[$counter]['tanggal_rujukan'] = DocoHelpers::convDateTime($value->tgl_rujukan, true, false);
                    $data[$counter]['nomer_rujukan'] = $value->no_rujukan;
                    $data[$counter]['nomer_rekam_medik'] = $value->no_rekam_medik;
                    $data[$counter]['nama_pasien'] = $value->nama_pasien;
                    $data[$counter]['tanggal_lahir'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value->tanggal_lahir)), true, false);
                    $data[$counter]['asal_rujukan'] = $value->ruangan_nama;
                    $data[$counter]['dokter_perujuk'] = $value->dokter_perujuk;
                    $data[$counter]['cara_bayar'] = $value->carabayar_nama;
                    $data[$counter]['penjamin'] = $value->penjamin_nama;
                    $data[$counter]['status'] = $value->stat_penunjang;

                    $counter++;
                }
            }

            $filePath = DocoHelpers::exportExcel("Kelompok Pemeriksaan Radiologi", $data, $header, array("uploadPath" => "./uploads"), [], [], true);

            $filePath->save('php://output');
            die;
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


    public function actionGetView($id)
    {
        try {
            $result = InfoOrderanRadView::find()->andWhere(['pasienkirimkeunitlain_id' => $id])->one();
            return $result;
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

    public function actionGetPemeriksaanView($id)
    {
        try {
            $model = new InfoOrderanRadDetailView;
            $query = $model::find()->where(['pasienkirimkeunitlain_id' => $id]);
            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
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


    public function actionGetDokter()
    {
        try {
            $model = new DokterView;
            $query = $model::find();

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_rujukan']); // Unset Advanced Filter  date range
                    $between = true;
                }
            }
            if ($between) {
                $query->andWhere(['between', 'tgl_rujukan', $start, $end]);
            }
            /**
             * End Special Condition date range
             **/

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
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

    public function actionGetOptions()
    {
        $caraBayar = CaraBayar::find()->where([
            'is_active' => true
        ])->all();

        $penjamin = Penjamin::find()->where([
            'is_active' => true
        ])->all();
        $rujukan = Ruangan::find()->where([
            'is_active' => true,
            'instalasi_id' => DocoConstants::$exceptPenunjangIncludeKamarOperasi
        ])->all();
        $asalRujukan = AsalRujukan::find()->where([
            'is_active' => true,
            'is_rujukan' => true
        ])->orderBy('asalrujukan_id')->all();
        return  [
            'cara_bayar' => $caraBayar,
            'penjamin' => $penjamin,
            'rujukan' => $rujukan,
            'asalRujukan' => $asalRujukan
        ];
    }

    public function actionGenerateApi($id)
    {
        
        $return = array('labDetail' => array(), 'listPemeriksaan' => array());
        $labDetail = InfoOrderanRadView::find()->andWhere(['pasienkirimkeunitlain_id' => $id])->one();
        $instalasiId = ArrayHelper::getValue($labDetail, 'instalasi_id');
        $pendaftaranId = ArrayHelper::getValue($labDetail, 'pendaftaran_id');
        $diagnosaUtamaId = $diagnosaUtamaText = '';
        $listDiagnosa = [];
        if($instalasiId == DocoConstants::INST_ID_RD) {
            $dataCppt = Cppt::find()->select(['cppt_id', 'a_diag_utama'])->where(['pendaftaran_id' => $pendaftaranId])->orderBy(['cppt_id' => SORT_DESC])->all();
            if(!empty($dataCppt)) {
                foreach ($dataCppt as $key => $value) {
                    $diagnosaUtama = ArrayHelper::getValue($value, 'a_diag_utama');
                    $cpptId = ArrayHelper::getValue($value, 'cppt_id');
                    if(is_array($diagnosaUtama)) {
                        $diagnosaUtamaId = ArrayHelper::getValue($diagnosaUtama, 'id');
                        $diagnosaUtamaText = trim(ArrayHelper::getValue($diagnosaUtama, 'nama'));
                        $listDiagnosa[$cpptId] = [
                            'diagnosa_utama_id' => $diagnosaUtamaId,
                            'diagnosa_utama_nama' => $diagnosaUtamaText,
                        ];
                    }
                }
                $listDiagnosa = array_values($listDiagnosa);
                $listDiagnosa = !empty($listDiagnosa) ? array_shift($listDiagnosa) : [];
            }
        }
        $pemeriksaan = InfoOrderanRadDetailView::find()
            ->where(['pasienkirimkeunitlain_id' => $id, 'is_approve' => false])
            ->andWhere(['or', ['is_referred' => null], ['is_referred' => false]])
            ->all();

        if (!empty($labDetail)) {
            $return['labDetail'] = $labDetail;
        }
        if (!empty($pemeriksaan)) {
            $return['listPemeriksaan'] = $pemeriksaan;
        }
        $return['diagnosa_utama'] = $listDiagnosa;
        return $return;
    }

    public function actionIntegrateTindakanBmhp($pendaftaran_id)
    {
        try {
            $this->integrateTindakan($pendaftaran_id);
            // $this->integrateBmhp($pendaftaran_id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function integrateBmhp($pendaftaran_id)
    {
        try {
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $resep = SyncPengeluaranobat::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'jenis' => 'BMHP', 'is_jurnal' => 'f', 'instalasi_id' => 5])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

            if ($config->is_akunting != null) {
                if (!empty($resep)) {
                    foreach ($resep as $key => $value) {
                        $data[] = [
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xtransaction_at' => $value->tgl_transaksi,
                            'xamount' => $value->harga,
                            'xdiscount_amount' => $value->discount,
                            'xmedical_number' => $value->no_rekam_medik,
                            'xnotes' => $value->uraian,
                            'xcategori_code' => $value->jenisobatalkes_kode,
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xamount_netto' => $value->harga_netto,
                            'xref_number_id' => $value->id,
                            'xis_billing' => $value->is_ditagihkan,
                        ];
                        $obatalkes = ObatAlkesPasien::findOne($value->id);
                        $obatalkes->is_jurnal = true;
                        $obatalkes->scenario = "jurnal";
                        $obatalkes->update();
                        // var_dump($obatalkes->getErrors(),100,true);
                        // die;
                    }
                    $var = DocoAkunting::api('POST', 'integrations', $data);
                }
            } else {
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function integrateTindakanCrud($pendaftaran_id, $method)
    {
        try {
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $tindakan = SyncTindakan::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'instalasi_id' => 5])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
            if ($config->is_akunting != null) {
                if (!empty($tindakan)) {
                    foreach ($tindakan as $key => $value) {
                        $harga = $value->harga;
                        $data[] = [
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number_id' => $value->id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xcategori_code' => $value->komponentarif_kode,
                            'xtransaction_at' => $value->tanggal_transaksi,
                            'xamount' => $harga,
                            'xdiscount_amount' => $value->diskon,
                            'xamount_netto' => 0,
                            'xamount_ppn' => 0,
                            'xmedical_number' => $value->rekam_medik,
                            'xnotes' => $value->uraian,
                            'xis_billing' => 'DITAGIHKAN',
                            'xcrud' => $method,
                        ];
                    }
                    return $var = DocoAkunting::api('POST', 'integrations_crud', $data);
                }
            } else {
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }


    public function integrateTindakan($pendaftaran_id)
    {
        try {
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $tindakan = SyncTindakan::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'instalasi_id' => 5, 'is_jurnal' => 'f'])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
            if ($config->is_akunting != null) {
                if (!empty($tindakan)) {
                    foreach ($tindakan as $key => $value) {
                        $harga = $value->harga;
                        $data[] = [
                            'xtransaction_type' => $value->jenis_transaksi,
                            'xcompany_id' => 1,
                            'xinstalasi_id' => $value->instalasi_id,
                            'xruangan_id' => $value->ruangan_id,
                            'xref_number_id' => $value->id,
                            'xref_number' => $value->no_pendaftaran,
                            'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                            'xcategori_code' => $value->komponentarif_kode,
                            'xtransaction_at' => $value->tanggal_transaksi,
                            'xamount' => $harga,
                            'xdiscount_amount' => $value->diskon,
                            'xamount_netto' => 0,
                            'xamount_ppn' => 0,
                            'xmedical_number' => $value->rekam_medik,
                            'xnotes' => $value->uraian,
                            'xis_billing' => 'DITAGIHKAN',
                        ];

                        $Tindakankomponen = Tindakankomponen::findOne($value->id);
                        $Tindakankomponen->is_jurnal = true;
                        $Tindakankomponen->update();
                    }
                    $var = DocoAkunting::api('POST', 'integrations', $data);
                }
            } else {
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function actionDokterList()
    {
        $page = Yii::$app->request->get('page', 1);
        $query = DokterView::find()
            ->select([
                'pegawai_id as id',
                'nama_pegawai as text'
            ])
            ->andWhere([
                'ruangan_id' => Yii::$app->jwt->ruangan_id
            ]);
        $term = Yii::$app->request->get('term');
        if (!empty($term)) {
            $query = $query->andWhere([
                'like',
                'nama_pegawai',
                $term
            ]);
        }
        return $query
            ->limit(11)
            ->offset(($page - 1) * 10)
            ->asArray()
            ->all();
    }

    /**
    * @controller actionCetakRujukan
    * @attribute #nama_pasien# => nama pasien
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #gender# => jenis kelamin
    * @attribute #umur# => umur
    * @attribute #rs_tujuan# => rs tujuan
    * @attribute #lokasi# => kota rs
    * @attribute #rs_name# => nama rs
    * @attribute #alamat_rs# => alamat rs
    * @attribute #telp_rs# => telpon rs
    * @attribute #website_rs# => website rs
    * @attribute #humas_rs# => no telp humas rs
    * @attribute #dokter# => pegawai menyetujui
    * @attribute #petugas# => dokter perujuk
    * @attribute #tanggal# => tanggal sekarang
    * @attribute #jam# => jam sekarang
    */
    public function actionCetakRujukan()
	{
		$request = Yii::$app->request;
		$id = $request->get('id');
        $rujukanKeluarId = $request->get('rujukankeluar_id');
		if(!empty($id)) {
			$data = PemeriksaanPasienRadiologiView::find()->where(['pasienkirimkeunitlain_id' => $id, 'is_referred' => true]);
            if(!empty($rujukanKeluarId)) {
                $data->andWhere(['rujukankeluar_id' => $rujukanKeluarId]);
            }
            $data = $data->all();
			$listData = [];
			if(!empty($data)) {
				foreach ($data as $key => $value) {
					$rujukanKeluarId = ArrayHelper::getValue($value, 'rujukankeluar_id');
					$tglRujukan = ArrayHelper::getValue($value, 'tgldirujuk');
					$tglRujukan = date('Y-m-d', strtotime($tglRujukan));
                    $groupedKey = $rujukanKeluarId.'-'.$tglRujukan;
					$listData[$groupedKey][] = $value;
				}
                
                $profileRs = $this->getProfileRs();
                $rsName = ArrayHelper::getValue($profileRs, 'namaRs');
                $kota = ArrayHelper::getValue($profileRs, 'kota');
                $alamatRs = ArrayHelper::getValue($profileRs, 'alamat');
                $telpRs = ArrayHelper::getValue($profileRs, 'no_telp');
                $website = ArrayHelper::getValue($profileRs, 'website');
                $noTelpHumas = ArrayHelper::getValue($profileRs, 'notelphumas');
                
				$print = new DocoPrint('rujukan-keluar-penunjang');
				if(!empty($listData)) {
                    $countData = count($listData);
                    foreach ($listData as $key => $value) {
                        foreach ($value as $k => $values) {
                            $k++;
                            $namaPasien = ArrayHelper::getValue($values, 'nama_pasien');
                            $noRekamMedik = ArrayHelper::getValue($values, 'no_rekam_medik');
                            $gender = ArrayHelper::getValue($values, 'jenis_kelamin_kode');
                            $tanggalLahir = ArrayHelper::getValue($values, 'tanggal_lahir');
                            $umur = !empty($tanggalLahir) ? DocoHelpers::getUmur($tanggalLahir) : '-';
                            $rsTujuan = ArrayHelper::getValue($values, 'rumahsakit_rujukan');
                            $pegawaiMenyetujui = ArrayHelper::getValue($values, 'pegawai_nama_perujuk');
                            $dokterPerujuk = ArrayHelper::getValue($values, 'dokter_perujuk_nama');
                            $print->attributes = [
                                '#nama_pasien#' => $namaPasien,
                                '#no_rekam_medik#' => $noRekamMedik,
                                '#umur#' => $umur,
                                '#gender#' => $gender,
                                '#rs_tujuan#' => $rsTujuan,
                                '#dokter#' => $dokterPerujuk,
                                '#petugas#' => $pegawaiMenyetujui,
                                '#lokasi#' => $kota,
                                '#rs_name#' => $rsName,
                                '#alamat_rs#' => $alamatRs,
                                '#telp_rs#' => $telpRs,
                                '#website_rs#' => $website,
                                '#humas_rs#' => $noTelpHumas,
                                '#tanggal#' => date('d M Y'),
                                '#jam#' => date('H:i'),
                                '#datatable#' => Yii::$app->controller->renderPartial('_rujukan', [
                                    'detail' => $value,
                                ]),
                            ];
                        }
                        $break = ($k == $countData) ? false : true;
                        $print->generateHtml($break);
					}
					$print->Output(true);
				}
			}
		}
	}

    protected function getProfileRs()
    {
        $profilRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
            return ProfilRsView::find()->asArray()->one();
        });
        $kota = '-';
        $namaRs = '-';
        if (!empty($profilRs['nama_rumahsakit'])) {
            $namaRs = $profilRs['nama_rumahsakit'];
        }

        if (!empty($profilRs['kota'])) {
            if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
                $pattern = "KOTA ADM. ";
            }
            elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
                $pattern = "KAB. ADM. ";
            }
            elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
                $pattern = "KAB. ";
            }
            elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
                $pattern = "KOTA ";
            }
            elseif($match = preg_match("/Kota /i", $profilRs['kota'])) {
                $pattern = "Kota ";
            }

            $kota = str_replace($pattern,"", $profilRs['kota']);
        }
            
        return [
            'namaRs' => $namaRs,
            'kota' => $kota,
            'alamat' => $profilRs['alamatlokasi_rumahsakit'],
            'no_telp' => $profilRs['no_telp_profilrs'],
            'website' => $profilRs['website'],
            'notelphumas' => $profilRs['notelphumas'],
        ];
    }

    public function actionDetailDiagnosa()
    {
        $request = Yii::$app->request;
        $pasienkirimkeunitlain_id = DocoHelpers::decrypt($request->get('pasienkirimkeunitlain_id'));
        $result = [];
        $diag_utama = null;
        $diag_penyerta = null;

        if (!empty($pasienkirimkeunitlain_id)) {
            $PasienKirimUnitlain = PasienKirimUnitlain::find()->where([
                'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
            ])->orderBy([
                'pasienkirimkeunitlain_id' => SORT_DESC
            ])->asArray()->one();
    
            $diag_utama = ArrayHelper::getValue($PasienKirimUnitlain, 'diag_utama');
            $diag_penyerta = ArrayHelper::getValue($PasienKirimUnitlain, 'diag_penyerta');
        } 

        $result['diagnosa_utama'] = $diag_utama ? ArrayHelper::getValue(json_decode(json_decode($diag_utama, true), true), 'text', '-') : '-';
        $result['diagnosa_penyerta'] = $diag_penyerta ? json_decode(json_decode($diag_penyerta, true), true) : [];
        $result['catatan_dokterpengirim'] = isset($PasienKirimUnitlain) ? ArrayHelper::getValue($PasienKirimUnitlain, 'catatan_dokterpengirim', '-') : '-';

        return $result;
    }
    
    public function actionValidasiStatusPemeriksaan()
    {
        $request = Yii::$app->request;
        $no_rujukan = $request->get('no_rujukan');
        try {
            
            $dataOrderan = InfoOrderanRadView::find()
            ->select([
                'pasienkirimkeunitlain_id',
                'status_penunjang',
                'stat_penunjang',
                'status_periksa',
                'nama_pasien',
                'pendaftaran_id',
                'no_pendaftaran'
            ])
            ->where(['no_rujukan' => $no_rujukan])
            ->asArray()
            ->one();

            if(! empty($dataOrderan)) {
                $statusPenunjang = ArrayHelper::getValue($dataOrderan, 'status_penunjang');
                $dataPasienPenunjang = PermintaanKepenunjangan::find(true)
                ->select([
                    'created_by',
                    'deleted_by'
                ])
                ->where(['pasienkirimkeunitlain_id' => $dataOrderan['pasienkirimkeunitlain_id']])
                ->asArray()
                ->one();

                $batalOrder = BatalOrderPenunjangT::find(true)
                ->select(['deleted_by'])
                ->where(['pasienkirimkeunitlain_id' => $dataOrderan['pasienkirimkeunitlain_id']])
                ->asArray()
                ->one();
            
                // Kondisi batal approve
                if($statusPenunjang == DocoConstants::BTL_APPROVE) {
                    $pegawai = ArrayHelper::getValue($batalOrder, 'deleted_by');
                } else {
                    $pegawai = ArrayHelper::getValue($dataPasienPenunjang, 'created_by');
                }

                $dataPegawai = Pegawai::find()
                ->select([
                    'nama_pegawai'
                ])
                ->where(['pegawai_id' => $pegawai])
                ->asArray()
                ->one();

                $namaPegawai = ArrayHelper::getValue($dataPegawai, 'nama_pegawai');
                $resultPayload = [
                    'pasienkirimkeunitlain_id' => $dataOrderan['pasienkirimkeunitlain_id'],
                    'status_penunjang' => $dataOrderan['status_penunjang'],
                    'stat_penunjang' => $dataOrderan['stat_penunjang'],
                    'status_periksa' => $dataOrderan['status_periksa'],
                    'nama_pasien' => $dataOrderan['nama_pasien'],
                    'pendaftaran_id' => $dataOrderan['pendaftaran_id'],
                    'no_pendaftaran' => $dataOrderan['no_pendaftaran'],
                    'nama_pegawai' => $namaPegawai
                ];
                
                return [
                    'status' => 200,
                    'data' => $resultPayload,
                    'message' => "Get data successfully !"
                ];
            }
            
            return [
                'status' => 404,
                'data' => [],
                'message' => "Data order not found !"
            ];
          
        } catch (\Exception $th) {
            return [
                'status' => 200,
                'data' => [],
                'message' => $th->getMessage() 
            ];
        }
    }

}
