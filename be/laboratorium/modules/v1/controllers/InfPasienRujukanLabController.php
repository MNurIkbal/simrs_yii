<?php

/**
 * @author Randy Vianda Putra
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoAkunting;
use app\modules\v1\models\InfoOrderanLabView;
use app\modules\v1\models\InfoOrderanLabDetailView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PasienKirimUnitlain;
use app\modules\v1\models\PermintaanKepenunjangan;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\TindakanPelayananT;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\AntrianT;
use app\modules\v1\models\TarifPenunjangView;
use app\modules\v1\models\TarifPaketPenunjangView;
use app\modules\v1\models\BatalOrderPenunjangT;
use app\modules\v1\models\AmbilSample;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\SyncPengeluaranobat;
use app\modules\v1\models\SyncTindakan;
use app\modules\v1\models\Tindakankomponen;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Ruangan;
use yii\helpers\ArrayHelper;
use SirsCore\features\IntegrasiAkunting;
use Doco\Services\KasirService;
use Doco\exceptions\ValidationException;
use yii\db\Expression;
use Doco\Traits\TindakanPenunjangTrait;
use app\modules\v1\models\InfoPasienLabView;
use Doco\models\Cppt;
use Doco\models\ProfilRsView;
use app\modules\v1\models\Daftartindakan;
use Doco\components\NoCountDataProvider;
use Doco\models\TindakanPelayanan;
use Doco\models\ObatAlkesPasien;
use Doco\models\SoapRj;

class InfPasienRujukanLabController extends \Doco\components\DocoActiveController
{
    use TindakanPenunjangTrait;
    public $modelClass = 'app\modules\v1\models\InfoOrderanLabView';
    /**
     * Untuk Kebutuhan Integerasi GE
     * @var array
     */
    public $messageBroker = [
        'proses-approve' => [
            'services' => [
                'Mhg' => [
                    'BslBilling' => [
                        'payload' => ['pasienkirimkeunitlain_id']
                    ]
                ],
                'Roche' => [
                    'Order' => [
                        'payload' => ['pasienkirimkeunitlain_id']
                    ]
                ],
                'Lis' => [
                    'BridgingLis' => [
                        'query_params' => ['pasienmasukpenunjang_id'],
                        'payload' => ['pendaftaran_id','pasienmasukpenunjang_id','pasienkirimkeunitlain_id'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => true
                    ]
                ],
            ]
        ],
        // 'proses-batal' => [
        //     'services' => [
        //         'Roche' => [
        //             'CancelOrder' => [
        //                 'payload' => ['pasienkirimkeunitlain_id']
        //             ]
        //         ]
        //     ]
        // ],
    ];
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    // Actions
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);

        return $actions;
    }

    // Action index
    public function actionIndex()
    {
        try {
            $model = new InfoOrderanLabView;
            $query = $model::find();
            $betweenLahir = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $startLahir = '';
            $endLahir = '';
            $type = "";
            $_GET['order'] = '';
            // return $_GET;
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_rujukan']) && !empty($_GET['advanced-filter']['tgl_rujukan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_rujukan']); 
                }
                if (isset($_GET['advanced-filter']['tgl_rujukan_batal']) && !empty($_GET['advanced-filter']['tgl_rujukan_batal'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan_batal']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_rujukan_batal']); 
                }
                if (isset($_GET['advanced-filter']['tanggal_lahir']) && !empty($_GET['advanced-filter']['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_lahir']);
                    $betweenLahir = true;
                }
                if(isset($_GET['advanced-filter']['type'])) {
                    $type = $_GET['advanced-filter']['type'];
                    // $approvedLunas = [
                    //     'status_penunjang' => (string) DocoConstants::DISETUJUI, 
                    //     'is_bayar' => true, 
                    //     'jumlah_tagihan' => 0,
                    // ];
                    // $approvedBelumLunas = [
                    //     'status_penunjang' => (string) DocoConstants::DISETUJUI, 
                    //     'is_bayar' => false,
                    // ];
                    // $jmlPemeriksaan = new Expression('(jml_pemeriksaan::INTEGER)');
                    // $jmlPemeriksaanApprove = new Expression('(jml_pemeriksaan_approve::INTEGER)');
                    if($type == 5) {
                        $query->andWhere(['IS NOT', 'pemeriksaan_dibatalkan', NULL]);
                        $query->andWhere(['status_periksa' => DocoConstants::BTL_PERIKSA_LAB]);
                        
                    }
                    // if($type == 1) {
                    //     $query->andWhere($approvedLunas);
                    //     $query->andWhere(['=', $jmlPemeriksaan, $jmlPemeriksaanApprove]);
                    // }
                    // elseif($type == 2) {
                    //     $query->andWhere($approvedLunas);
                    //     $query->andWhere(['<>', $jmlPemeriksaan, $jmlPemeriksaanApprove]);
                    // }
                    // elseif($type == 3) {
                    //     $query->andWhere($approvedBelumLunas);
                    //     $query->andWhere(['=', $jmlPemeriksaan, $jmlPemeriksaanApprove]);
                    // }
                    // elseif($type == 4) {
                    //     $query->andWhere($approvedBelumLunas);
                    //     $query->andWhere(['<>', $jmlPemeriksaan, $jmlPemeriksaanApprove]);
                    // }
                    // elseif($type == 5) {
                    //     $query->andWhere(['status_penunjang' => (string) DocoConstants::BTL_APPROVE]);
                    // }
                    // elseif($type == 6) {
                    //     $query->andWhere(['status_penunjang' => (string) DocoConstants::BELUM_SETUJU]);
                    // }
                    elseif($type == 7) {
                        $query->andWhere(['is_cyto' => true]);
                    }
                }
            }
            if($type != 5){
                $query->andWhere(['NOT IN', 'status_penunjang', [DocoConstants::DISETUJUI]]);
                // $query->andWhere(['<>', 'status_penunjang', DocoConstants::BTL_APPROVE]);
                $query->andWhere(['is_approved_all' => FALSE]);
            }
            $query->andWhere(['between', 'tgl_rujukan', $start, $end]);
            // if(empty($type)) {
            //     $query->andWhere(['<>', 'status_penunjang', DocoConstants::DISETUJUI]);
            //     $query->andWhere(['<>', 'status_penunjang', DocoConstants::BTL_APPROVE]);
            //     $query->andWhere(['is_approved_all' => FALSE]);
            // }
            // if(empty($type) || $type == 0) {
            //     $query->andWhere([
            //         'or',
            //         ['not', ['jml_pemeriksaan' => 0]],
            //         ['is_rujukan' => false],
            //     ]);
            // }
            if (!empty($startLahir) && !empty($endLahir) && $betweenLahir) {
                $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
            }

            $query->orderBy(['is_cyto' => SORT_DESC, 'tgl_rujukan' => SORT_DESC]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new NoCountDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            Yii::error($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Yii::error($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdateCatatan(){
        $post = $post = \Yii::$app->request->post();  
            try {
                $request = Yii::$app->request;
                $model = PasienKirimUnitlain::findOne(['pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id']]);
                    $model->catatan_dokterpengirim = $post['catatan_dokterpengirim'];
                    if ($model->update()) {
                        return $response['response'] = [
                                'title' => 'Proses Berhasil !',
                                'text' => 'Status berhasil diubah',
                           ];
                    } else {
                        return $response['response'] = [
                                'title' => 'Proses Gagal !',
                                'text' => 'Status gagal di ubah',
                                'status' => 422
                           ];
                    }                
            } catch (\yii\db\Exception $e) {
                return ['message' => $e->getMessage()];
            } catch (\Exception $e) {
                return ['message' => $e->getMessage()];
            }
    }

    public function actionProsesApprove()
    {
        $post = \Yii::$app->request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $listApproved = isset($post['list_approved']) ? $post['list_approved'] : null;
        $approveTindakan = isset($listApproved['tindakan']) ? $listApproved['tindakan'] : [];
        $approvePaket = isset($listApproved['paket']) ? $listApproved['paket'] : [];
        $pasienkirimkeunitlain_id = isset($post['pasienkirimkeunitlain_id']) ? $post['pasienkirimkeunitlain_id'] : null;
        if(empty($pasienkirimkeunitlain_id)) {
            return [
                'status' => 500,
                'title' => 'Terjadi kesalahan',
                'text' => 'Data tidak ditemukan'
            ];
        }
        
        $cacheList = Yii::$app->cache->get('approve_penunjang_'.$pasienkirimkeunitlain_id);
        if(!empty($cacheList)){
            return [
                'status' => 422,
                'title' => 'Terjadi kesalahan',
                'text' => 'Sedang Dalam Proses Approve'
            ];
        }else{
            Yii::$app->cache->set('approve_penunjang_'.$pasienkirimkeunitlain_id, $pasienkirimkeunitlain_id,10);
        }
        try {
            $PasienKirimUnitLainT = PasienKirimUnitlain::findOne([
                'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
            ]);
            $ruanganId = $PasienKirimUnitLainT->ruangan_id;
            if (!empty($PasienKirimUnitLainT)) {
                $InfoOrderanLabView = InfoOrderanLabView::find()
                    ->select([
                        'no_pendaftaran',
                        'pasienkirimkeunitlain_id',
                        'kelaspelayanan_id',
                        'jeniskasuspenyakit_id',
                        'pasienadmisi_id',
                        'ruanganpenunjang_id',
                        'pasien_id',
                        'pendaftaran_id',
                        'ruangan_id',
                        'kunjungan',
                        'instalasi_id',
                        'carabayar_id',
                        'penjamin_id',
                        'status_pasien',
                        'groupcarabayar_id',
                        'instalasipen_id',
                        'ruanganasal_id',
                        'instalasiasal_id'
                    ])
                    ->where([
                        'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id
                    ])->one();
                $pendaftaranId = $InfoOrderanLabView->pendaftaran_id;
                $no_pendaftaran = $InfoOrderanLabView->no_pendaftaran;
                /** proses input pasienmasukkepenunjang **/
                $inputPasienMasuk = []; /** array untuk input data ke pasien masuk penunjang **/
                $dokterLabId = isset($post['dokter_laboratorium']) ? $post['dokter_laboratorium'] : null;
                $inputPasienMasuk = [
                    'pasienkirimkeunitlain_id' => $InfoOrderanLabView->pasienkirimkeunitlain_id,
                    'kelaspelayanan_id' => $InfoOrderanLabView->kelaspelayanan_id,
                    'jeniskasuspenyakit_id' => $InfoOrderanLabView->jeniskasuspenyakit_id,
                    'pasienadmisi_id' => $InfoOrderanLabView->pasienadmisi_id,
                    'pegawai_id' => $dokterLabId,
                    'ruangan_id' => $InfoOrderanLabView->ruanganpenunjang_id,
                    'pasien_id' => $InfoOrderanLabView->pasien_id,
                    'pendaftaran_id' => $InfoOrderanLabView->pendaftaran_id,
                    'ruanganasal_id' => $InfoOrderanLabView->ruanganasal_id,
                    'tglmasukpenunjang' => date('Y-m-d H:i:s'),
                    'kunjungan' => $InfoOrderanLabView->kunjungan,
                    'panggil_antrian' => false,
                    'instalasiasal_id' => $InfoOrderanLabView->instalasiasal_id,
                    'status_periksa' => DocoConstants::BLM_PERIKSA,
                ];
                /** bila pembayaran nya perorangan. maka status periksa akn menjadi null **/
                if (($InfoOrderanLabView->instalasi_id == DocoConstants::INST_ID_RI 
                        || $InfoOrderanLabView->instalasi_id == DocoConstants::VAR_CM_IGD)
                        || $InfoOrderanLabView->penjamin_id != DocoConstants::PENJAMIN_ID) {
                    $inputPasienMasuk['status_periksa'] = DocoConstants::BLM_PERIKSA;
                }
                /** bila pembayaran nya perorangan. maka status periksa akn menjadi null **/
                /*cek status approve*/
                $CekTindakankunjungan = PermintaanKepenunjangan::find()
                    ->where([ 'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id])
                    ->andwhere(['in','daftartindakan_id', $approveTindakan])
                    ->andwhere(['is_approve' => true, 'is_active' =>true])->all();

                $countTindakanApprove = count($CekTindakankunjungan);
                /*jika terdapat tidakan yang sudah di approve maka akan muncul validasi*/
                if($countTindakanApprove >= 1){
                    // Get tindakan 
                    $listTindakanId = ArrayHelper::getColumn($CekTindakankunjungan,'daftartindakan_id');
                    $tindakan = Daftartindakan::find()
                        ->select(['daftartindakan_nama'])
                        ->where(['in','daftartindakan_id', $listTindakanId])->all();
                    $listTindakanNama = ArrayHelper::getColumn($tindakan,'daftartindakan_nama');
                    $listTindakanToString = implode(", ", $listTindakanNama);
                    $kunjunganTindakan = PermintaanKepenunjangan::find()->select('tglpermintaankepenunjang')->where([ 'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id])->andwhere(['is_approve' => true])->one();
                    
                    Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
                    return [
                        'status' => 422,
                        'title' => 'Terjadi kesalahan',
                        'text' => 'Pemeriksaan lab "'.$listTindakanToString.'" telah di approve pada '.$kunjunganTindakan->tglpermintaankepenunjang
                    ];

                }
                /*approve tindakan*/
                $dataApprove = [ 'is_approve' => true, 'tgl_approve' => date('Y-m-d H:i:s') ];
                PermintaanKepenunjangan::updateAll($dataApprove, [
                    'and', 
                    [ 'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id ], 
                    ['IN', 'daftartindakan_id', $approveTindakan] 
                ]);
                $getPasienMasukPenunjang =  PasienMasukPenunjangT::find()
                                        ->where(['pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id])
                                        ->one();
                
                $konfig = KonfigSystem::find()->select(['is_approve_lab_new_order'])->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->asArray()->one();
                if (!$konfig['is_approve_lab_new_order'] && $getPasienMasukPenunjang) {
                    $pasienMasukPenunjangId = $getPasienMasukPenunjang->pasienmasukpenunjang_id;
                    $getPasienMasukPenunjang->is_bayar = false;                
                    if($getPasienMasukPenunjang->save()){

                        // skip dibuatkan antrian untuk RI,RJ dan RD karena udah pertama kali save awal
                        $InfoOrderanLabDetailView = InfoOrderanLabDetailView::findAll([
                            'pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id']
                        ]);

                        $payloadHeader = [
                            'no_pendaftaran' => $InfoOrderanLabView->no_pendaftaran,
                            'kelaspelayanan_id' => $InfoOrderanLabView->kelaspelayanan_id,
                            'penjamin_id' => $InfoOrderanLabView->penjamin_id,
                            'carabayar_id' => $InfoOrderanLabView->carabayar_id,
                            'ruangan_id' => $InfoOrderanLabView->ruanganpenunjang_id,
                            'instalasi_id' => $InfoOrderanLabView->instalasipen_id,
                        ];

                        if (!empty($InfoOrderanLabDetailView)) {
                            $listPaket = $listTindakan = $listMapping = [];
                            $listBatal = $filterTindakanId = [];
                            $dataLabDetail = ArrayHelper::toArray($InfoOrderanLabDetailView);
                            foreach ($dataLabDetail as $kt => $vt) {
                                $daftarTindakanId = ArrayHelper::getValue($vt, 'daftartindakan_id');
                                $idParent = $vt['permintaankepenunjang_id'];
                                $paketId = isset($vt['tipepaket_id']) ? $vt['tipepaket_id'] : null;
                                if (!empty($paketId) && !in_array($paketId, $approvePaket) 
                                    || !empty($daftarTindakanId) && !in_array($daftarTindakanId, $approveTindakan)) {
                                    $listBatal[] = $vt['permintaankepenunjang_id'];
                                    continue;
                                }
                                $tindakanArray[] = [
                                    'pasienmasukpenunjang_id' => $pasienMasukPenunjangId,
                                    'dokter_id' => $post['dokter_laboratorium'],
                                    'daftartindakan_id' => empty($vt['tipepaket_id']) ? $daftarTindakanId : null,
                                    'tipepaket_id' => $vt['tipepaket_id'],
                                    'is_cyto' => $vt['is_cyto'],
                                    'qty' => $vt['qtypermintaan'],
                                ];
                                $filterTindakanId[] = $daftarTindakanId;
                            }

                            
                            // if (!empty($listBatal)) {
                            //     (new PermintaanKepenunjangan)->delete([
                            //         'permintaankepenunjang_id' => $listBatal
                            //     ]);
                            // }

                            //integrasi tagihan ke kasir
                            $tagihanKasir = (new KasirService)->tagihanPenunjang($payloadHeader, $tindakanArray);
                            if (isset($tagihanKasir['meta']['result']) && $tagihanKasir['meta']['result'] == 'failed') {
                                Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
                                return (new DocoHelpers)->callBack(DocoMessages::KEY_ERR_VALIDATION, [
                                    'text' => ArrayHelper::getValue($tagihanKasir, 'message', 'Terjadi kesalahan saat integerasi dengan kasir')
                                ]);
                            }
                            $getTindakanPelayanan = TindakanPelayanan::find()->select([
                                'tindakanpelayanan_id',
                                'daftartindakan_id',
                                'tgl_tindakan',
                                'dokterpenanggungjawab_id',
                            ])->where([
                                'pasienmasukpenunjang_id' => $getPasienMasukPenunjang->pasienmasukpenunjang_id,
                                'daftartindakan_id' => $filterTindakanId
                            ])->asArray()->all();
                            if (!empty($getTindakanPelayanan)) {
                                foreach ($getTindakanPelayanan as $index => $tindakanPelayanan) {
                                    $daftarTindakanId = ArrayHelper::getValue($tindakanPelayanan, 'daftartindakan_id');
                                    $condition = 'pasienkirimkeunitlain_id = ' . $pasienkirimkeunitlain_id . ' and daftartindakan_id = ' . $daftarTindakanId;
                                    PermintaanKepenunjangan::updateAll(
                                        [
                                            'tindakanpelayanan_id' => ArrayHelper::getValue($tindakanPelayanan, 'tindakanpelayanan_id')
                                        ],
                                        $condition
                                    );
                                }
                            }
                            // jika bukan rawat darurat, is karcis true & status bayar belum lunas
                            // jika rawat darurat, status bayar belum lunas
                            $statusLunas = DocoConstants::BELUM_LUNAS;
                            if ($InfoOrderanLabView->instalasi_id != DocoConstants::VAR_CM_IGD) {
                                $updateArray = ['is_karcis' => true, 'status_bayar' => $statusLunas];
                            } else {
                                $updateArray = ['status_bayar' => $statusLunas];
                            }
                            Pendaftaran::updateAll($updateArray, "pendaftaran_id = {$pendaftaranId}");
                        }
                        
                        $transaction->commit();
                        Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
                        $instalasi_id = Yii::$app->jwt->instalasi_id;
                        IntegrasiAkunting::integrateTindakanBmhp($no_pendaftaran, $instalasi_id);
                        $result = [
                            'status' => 200,
                            'title' => 'Input Berhasil',
                            'text' => 'Aprroval Pasien Berhasil'
                        ];
                    } else {
                        Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
                        $result['status'] = 500;
                        $result['title'] = 'Gagal insert';
                        $result['text'] = $getPasienMasukPenunjang->getErrors();
                    }
                }else{
                    /* menghindari duplikasi data saat approve untuk pasien aps tanpa tindakan */
                    $mPasienMasukPenunjangT = PasienMasukPenunjangT::find()->where([
                        'pendaftaran_id' => $InfoOrderanLabView->pendaftaran_id, 
                        'pasienkirimkeunitlain_id' => null,
                        'ruangan_id' => $ruanganId,
                    ])->one();
                    
                    if (empty($mPasienMasukPenunjangT)) {
                        $mPasienMasukPenunjangT = new PasienMasukPenunjangT;
                    }
                    $mPasienMasukPenunjangT->attributes = $inputPasienMasuk;
                    if ($mPasienMasukPenunjangT->save()) {
                        $pasienMasukPenunjangId = $mPasienMasukPenunjangT->pasienmasukpenunjang_id;
                        /** update pasien kirim unit lain jika semua pemeriksaan sudah di approve**/
                        $jumlahPemeriksaan = PermintaanKepenunjangan::find()
                            ->where([
                                'pasienkirimkeunitlain_id' => $pasienkirimkeunitlain_id,
                                'is_approve' => false
                            ])->count();
                        
                        if($jumlahPemeriksaan == 0) {
                            $PasienKirimUnitLainT->pasienmasukpenunjang_id = $pasienMasukPenunjangId;
                            $PasienKirimUnitLainT->status_penunjang = DocoConstants::DISETUJUI;
                            $PasienKirimUnitLainT->catatan_dokterpengirim = isset($post['catatan_dokterpengirim']) ? $post['catatan_dokterpengirim'] : '';
                            $PasienKirimUnitLainT->save(); /*ada trigger no order no_orderkeunitlain()*/
                            /** update pasien kirim unit lain **/
                        }

                        /** pengecek bila pasien adlah selain rawat jalan maka akan langsung dibuatkan no antrian **/
                        if (($InfoOrderanLabView->instalasi_id == DocoConstants::INST_ID_RI 
                            || $InfoOrderanLabView->instalasi_id == DocoConstants::VAR_CM_IGD)
                                || $InfoOrderanLabView->penjamin_id != DocoConstants::PENJAMIN_ID) {
                            /** input no antrian **/
                            $inputAntrian = array(
                                'ruangan_id' => $InfoOrderanLabView->ruanganpenunjang_id,
                                'carabayar_id' => $InfoOrderanLabView->carabayar_id,
                                'pendaftaran_id' => $InfoOrderanLabView->pendaftaran_id,
                                'layarantrian_id' => 0,
                                'loket_id' => 0,
                                'tgl_antrian' => date('Y-m-d H:i:s'),
                                'panggil_flag' => false,
                                'pasien_id' => $InfoOrderanLabView->pasien_id,
                                'penjamin_id' => $InfoOrderanLabView->penjamin_id,
                                'pegawai_id' => $post['dokter_laboratorium'],
                                'panggilan_ke' => 0,
                                'status_antrian' => 0,
                                'status_pasien' => $InfoOrderanLabView->status_pasien,
                                'groupcarabayar_id' => $InfoOrderanLabView->groupcarabayar_id,
                                'jenisantrian_id' => 179,
                                'klasifikasipasien_id' => 1,
                                'jadwaldokter_id' => 0,
                                'fungsiantrian_id' => 328,
                                'racikan_id' => 0,
                                'is_konsulpoli' => 0,
                                'instalasi_id' => $InfoOrderanLabView->instalasipen_id
                            );
                            $mAntrianT = new AntrianT;
                            $mAntrianT->attributes = $inputAntrian;
                            if ($mAntrianT->save()) {
                                $cAntrianT = AntrianT::findOne(['antrian_id' => $mAntrianT->antrian_id]);
                                if (!empty($cAntrianT)) {
                                    $mPasienMasukPenunjangT->no_antrian = $cAntrianT->no_antrian;
                                    $mPasienMasukPenunjangT->save();
                                }
                            }
                        }
                        /** pengecek bila pasien adlah selain rawat jalan maka akan langsung dibuatkan no antrian **/

                        /** loop tindakan medis **/
                        $InfoOrderanLabDetailView = InfoOrderanLabDetailView::findAll([
                            'pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id']
                        ]);

                        $inputTindakanPelayanan = array(); // digunakan untuk menampung data yang akan di input ke tindakan pelayanan
                        $tindakanArray = [];
                        $tempDaftarTindakan = array(); // digunakan untuk menampung daftar tindakan dan tipepaket id dari $inputTindakanPelayanan. untuk di updatekan ke permintaanPenunjang
                        $ruangan_id = $InfoOrderanLabView->ruanganpenunjang_id;
                        $kelas_pelayanan = $InfoOrderanLabView->kelaspelayanan_id;
                        $penjamin = $InfoOrderanLabView->penjamin_id;
                        $payloadHeader = [
                            'no_pendaftaran' => $InfoOrderanLabView->no_pendaftaran,
                            'kelaspelayanan_id' => $InfoOrderanLabView->kelaspelayanan_id,
                            'penjamin_id' => $InfoOrderanLabView->penjamin_id,
                            'carabayar_id' => $InfoOrderanLabView->carabayar_id,
                            'ruangan_id' => $InfoOrderanLabView->ruanganpenunjang_id,
                            'instalasi_id' => $InfoOrderanLabView->instalasipen_id,
                        ];
                        if (!empty($InfoOrderanLabDetailView)) {
                            $listPaket = $listTindakan = $listMapping = [];
                            $listBatal = $filterTindakanId = [];
                            $dataLabDetail = ArrayHelper::toArray($InfoOrderanLabDetailView);
                            foreach ($dataLabDetail as $kt => $vt) {
                                $daftarTindakanId = ArrayHelper::getValue($vt, 'daftartindakan_id');
                                $idParent = $vt['permintaankepenunjang_id'];
                                $paketId = isset($vt['tipepaket_id']) ? $vt['tipepaket_id'] : null;
                                if (!empty($paketId) && !in_array($paketId, $approvePaket) 
                                    || !empty($daftarTindakanId) && !in_array($daftarTindakanId, $approveTindakan)) {
                                    $listBatal[] = $vt['permintaankepenunjang_id'];
                                    continue;
                                }
                                $tindakanArray[] = [
                                    'pasienmasukpenunjang_id' => $mPasienMasukPenunjangT->getPrimaryKey(),
                                    'dokter_id' => $post['dokter_laboratorium'],
                                    'daftartindakan_id' => empty($vt['tipepaket_id']) ? $daftarTindakanId : null,
                                    'tipepaket_id' => $vt['tipepaket_id'],
                                    'is_cyto' => $vt['is_cyto'],
                                    'qty' => $vt['qtypermintaan'],
                                ];
                                $filterTindakanId[] = $daftarTindakanId;
                            }

                            // if (!empty($listBatal)) {
                            //     (new PermintaanKepenunjangan)->delete([
                            //         'permintaankepenunjang_id' => $listBatal
                            //     ]);
                            // }

                            //integrasi tagihan ke kasir
                            $tagihanKasir = (new KasirService)->tagihanPenunjang($payloadHeader, $tindakanArray);
                            if (isset($tagihanKasir['meta']['result']) && $tagihanKasir['meta']['result'] == 'failed') {
                                Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
                                return (new DocoHelpers)->callBack(DocoMessages::KEY_ERR_VALIDATION, [
                                    'text' => ArrayHelper::getValue($tagihanKasir, 'message', 'Terjadi kesalahan saat integerasi dengan kasir')
                                ]);
                            }
                            $getTindakanPelayanan = TindakanPelayanan::find()->select([
                                'tindakanpelayanan_id',
                                'daftartindakan_id',
                                'tgl_tindakan',
                                'dokterpenanggungjawab_id',
                            ])->where([
                                'pasienmasukpenunjang_id' => $mPasienMasukPenunjangT->getPrimaryKey(),
                                'daftartindakan_id' => $filterTindakanId
                            ])->asArray()->all();
                            if (!empty($getTindakanPelayanan)) {
                                foreach ($getTindakanPelayanan as $index => $tindakanPelayanan) {
                                    $daftarTindakanId = ArrayHelper::getValue($tindakanPelayanan, 'daftartindakan_id');
                                    $condition = 'pasienkirimkeunitlain_id = ' . $pasienkirimkeunitlain_id . ' and daftartindakan_id = ' . $daftarTindakanId;
                                    PermintaanKepenunjangan::updateAll(
                                        [
                                            'tindakanpelayanan_id' => ArrayHelper::getValue($tindakanPelayanan, 'tindakanpelayanan_id')
                                        ],
                                        $condition
                                    );
                                }
                            }
                            // jika bukan rawat darurat, is karcis true & status bayar belum lunas
                            // jika rawat darurat, status bayar belum lunas
                            $statusLunas = DocoConstants::BELUM_LUNAS;
                            if ($InfoOrderanLabView->instalasi_id != DocoConstants::VAR_CM_IGD) {
                                $updateArray = ['is_karcis' => true, 'status_bayar' => $statusLunas];
                            } else {
                                $updateArray = ['status_bayar' => $statusLunas];
                            }
                            Pendaftaran::updateAll($updateArray, "pendaftaran_id = {$pendaftaranId}");
                        }

                        $transaction->commit();
                        Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
                        $instalasi_id = Yii::$app->jwt->instalasi_id;
                        IntegrasiAkunting::integrateTindakanBmhp($no_pendaftaran, $instalasi_id);
                        $result = [
                            'status' => 200,
                            'title' => 'Input Berhasil',
                            'text' => 'Aprroval Pasien Berhasil'
                        ];
                    } else {
                        Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
                        $result['status'] = 500;
                        $result['title'] = 'Gagal insert';
                        $result['text'] = $mPasienMasukPenunjangT->getErrors();
                    }
                }
                $this->insertBmhp($pasienkirimkeunitlain_id, DocoConstants::INST_ID_LAB, $pasienMasukPenunjangId, $dokterLabId, $tindakanArray);
            } else {
                Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
                $transaction->rollBack();
                // \Yii::$app->response->statusCode = 500;
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data tidak ditemukan'
                ];
            }
            return $result;
        } catch (ValidationException $e) {
            Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
            $transaction->rollBack();
            return $e->getOptions();
        } catch (\Exception $e) {
            Yii::$app->cache->delete('approve_penunjang_'.$pasienkirimkeunitlain_id);
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdateKirimUnit($id)
    {
        $request = Yii::$app->request;
        $qPasien = PasienKirimUnitlain::find()->andWhere([
            'pasienkirimkeunitlain_id' => $id
        ])->one();

        if (empty($qPasien)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION,[
                'text' => 'Pasien Rujukan tidak ditemukan'
            ]);
        }

        if ($qPasien->status_penunjang != DocoConstants::BELUM_SETUJU) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION,[
                'text' => 'Data pasien tidak bisa diupdate karena sudah di Approve / di batalkan'
            ]);
        }

        $tanggalRujukan = $request->post('tgl_rujukan');

        if (empty($tanggalRujukan)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION,[
                'text' => 'Tanggal rujukan tidak boleh kosong.'
            ]);
        }

        $qPasien->tgl_kirimpasien = date('Y-m-d H:i:s', strtotime($tanggalRujukan));


        if ($qPasien->save()) {
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM);
    }

    public function actionProsesBatal()
    {
        $post = \Yii::$app->request->post();
        // pertama tanggal tidak boleh melewati tanggal order rujukan
        // kedua pasien yang sudah melakukan pembayaran tidak bisa membatalkan 
        // ketiga pasien yang sudah input speciment atau sample tidak bisa batal
        // keempat. pasien yang sudah di batalkan di pasien lab nya tidak bisa dibatalkan
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $pasienKirimUnitLainId = isset($post['pasienkirimkeunitlain_id']) ? $post['pasienkirimkeunitlain_id'] : null;
        $pasienmasukpenunjang_id = null;
        try {
            $listBatal = isset($post['list_batal']) ? $post['list_batal'] : null;
            $batalTindakan = isset($listBatal['tindakan']) ? $listBatal['tindakan'] : [];
            $pasienKirimUnitLain = PasienKirimUnitlain::findOne($pasienKirimUnitLainId);
            if(!empty($pasienKirimUnitLain)) {
                $pasienKirimUnitLainId = $pasienKirimUnitLain['pasienkirimkeunitlain_id'];
                $pendaftaran_id = $pasienKirimUnitLain->pendaftaran_id;
                $pasienmasukpenunjang_id = $pasienKirimUnitLain->pasienmasukpenunjang_id;
            }
            $getInfoOrderanLabView = InfoOrderanLabView::find()->andWhere(['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId])->one();
            $pendaftaran_id = !empty($pendaftaran_id) ? $pendaftaran_id : $getInfoOrderanLabView->pendaftaran_id;
            if (!empty($pasienKirimUnitLain)) {
                //  proses input batal
                $tanggal_batal = "";
                if (!empty($post['tanggal_batal'])) {
                    $tanggal_batal = date('Y-m-d H:i:s', strtotime($post['tanggal_batal']));
                }

                $inputBatalOrder = array(
                    'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId,
                    'tgl_batalorder' => $tanggal_batal,
                    'peg_menyetujui_id' => $post['disetujui_oleh'],
                    'alasan' => $post['alasan_pembatalan'],
                    'additional_data' => json_encode(array('pasienkirimkeunitlain_id' => $pasienKirimUnitLainId)),
                );
                
                $getBatalOrderPenunjangT = BatalOrderPenunjangT::find()->where(['pasienkirimkeunitlain_id' => $pasienKirimUnitLainId])->one();
                if ($getBatalOrderPenunjangT) {
                    $getBatalOrderPenunjangT->attributes = $inputBatalOrder;
                    if( $getBatalOrderPenunjangT->save()){
                        $dataBatal = [ 
                            'is_deleted' => true, 
                            'deleted_date' => date('Y-m-d H:i:s'), 
                            'deleted_by' => Yii::$app->jwt->user->pegawai_id 
                        ];
                        PermintaanKepenunjangan::updateAll($dataBatal, [
                            'and', 
                            [ 'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId], 
                            ['IN', 'daftartindakan_id', $batalTindakan] 
                        ]);
                        $queryPermintaanPenunjang = PermintaanKepenunjangan::find(true)
                                    ->where([ 'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId])->count();
                        $queryPermintaanPenunjangDeleted = PermintaanKepenunjangan::find()
                                    ->where([ 'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId])->count();

                        if ($pasienKirimUnitLain->status_penunjang == 471) {
                            $statusPenunjang = ($queryPermintaanPenunjang == ($queryPermintaanPenunjang - $queryPermintaanPenunjangDeleted)) ? '472' : '471';
                        }else{
                            $statusPenunjang = ($queryPermintaanPenunjang == ($queryPermintaanPenunjang - $queryPermintaanPenunjangDeleted)) ? '472' : $pasienKirimUnitLain->status_penunjang ;
                        }

                        $pasienKirimUnitLain->status_penunjang = $statusPenunjang ; // batal 472 semua, sebelum nya (471, disetujui)
                        $pasienKirimUnitLain->save();
                        if( !empty($pasienmasukpenunjang_id) ){
                            $getNoPenunjang = PasienMasukPenunjangT::find()->select(['no_masukpenunjang', 'pasienmasukpenunjang_id','instalasiasal_id','pendaftaran_id'])->where(['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id])->one();
                            $getTindakanPelayananT = TindakanPelayananT::find()
                                ->where([
                                    'pendaftaran_id' => $pendaftaran_id,
                                    'pasienmasukpenunjang_id' => $getNoPenunjang->pasienmasukpenunjang_id
                                ])->all();
                            $arrTindakanPelayanan = [];
                            foreach ($getTindakanPelayananT as $key => $value) {
                                if (in_array($value->daftartindakan_id, $batalTindakan)) {
                                    $arrTindakanPelayanan[] = [
                                        'tindakanpelayanan_id' => $value->tindakanpelayanan_id,
                                        'daftartindakan_id' => $value->daftartindakan_id
                                    ];
                                }
                            }

                            if (count($batalTindakan) == count($getTindakanPelayananT)) {
                                $getInstalasiPenunjang = Instalasi::find()->where(['instalasi_id' => $getNoPenunjang->instalasiasal_id])
                                                ->andWhere(['is_penunjang' => true])->one();
                                if ($getInstalasiPenunjang) {
                                    /** update status pasien pendaftaran **/
                                    $getPendaftaran = Pendaftaran::findOne(['pendaftaran_id' => $pendaftaran_id]);
                                    $getPendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_BTL_PRKS;
                                    $getPendaftaran->update();
                                    /** update status pasien pendaftaran **/
                                }
                            }

                            $registrationData['no_masukpenunjang'] = $getNoPenunjang->no_masukpenunjang;
                            $registrationData['ruangan_id'] = isset($registrationData['ruangan_id']) && !empty($registrationData['ruangan_id']) ? $registrationData['ruangan_id'] : Yii::$app->jwt->ruangan_id;
                            $registrationData['instalasi_id'] = isset($registrationData['instalasi_id']) && !empty($registrationData['instalasi_id']) ? $registrationData['instalasi_id'] : 1 ;
                            $registrationData['tgl_transaksi'] = date("Y-m-d H:i:s");
                            $registrationData['detail_tindakan'] = $arrTindakanPelayanan;
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
                        $transaction->commit();
                        return $this->helper->response([
                            'text' => 'Pemeriksaan Laboratorium Berhasil Dibatalkan.'
                        ], 200);
                    }else{
                        $transaction->rollBack();
                        return $this->responseJson(500, 'Pembatalan Order Gagal!');
                    }
                }else{
                    $mBatalOrderPenunjangT = new BatalOrderPenunjangT;
                    $mBatalOrderPenunjangT->attributes = $inputBatalOrder;
                    if ($mBatalOrderPenunjangT->save()) {
                    // update pasien kirim unit lain
                        $dataBatal = [ 
                            'is_deleted' => true, 
                            'deleted_date' => date('Y-m-d H:i:s'), 
                            'deleted_by' => Yii::$app->jwt->user->pegawai_id
                        ];
                        PermintaanKepenunjangan::updateAll($dataBatal, [
                            'and', 
                            [ 'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId ], 
                            ['IN', 'daftartindakan_id', $batalTindakan] 
                        ]);
                        $queryPermintaanPenunjang = PermintaanKepenunjangan::find(true)
                                    ->where([ 'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId ])->count();
                        $queryPermintaanPenunjangDeleted = PermintaanKepenunjangan::find()
                                    ->where([ 'pasienkirimkeunitlain_id' => $pasienKirimUnitLainId ])->count();

                        if ($pasienKirimUnitLain->status_penunjang == 471) {
                            $statusPenunjang = ($queryPermintaanPenunjang == ($queryPermintaanPenunjang - $queryPermintaanPenunjangDeleted)) ? '472' : '471';
                        }else{
                            $statusPenunjang = ($queryPermintaanPenunjang == ($queryPermintaanPenunjang - $queryPermintaanPenunjangDeleted)) ? '472' : $pasienKirimUnitLain->status_penunjang ;
                        }
                        $pasienKirimUnitLain->status_penunjang = $statusPenunjang ; // batal 472 semua, sebelum nya (471, disetujui)
                        $pasienKirimUnitLain->save();
                        // update pasien kirim unit lain
                        // $this->integrateTindakanCrud($pendaftaran_id, 'DELETE');
                        if( !empty($pasienmasukpenunjang_id) ){
                            $getNoPenunjang = PasienMasukPenunjangT::find()->select(['no_masukpenunjang', 'pasienmasukpenunjang_id','instalasiasal_id','pendaftaran_id'])->where(['pasienmasukpenunjang_id' => $pasienmasukpenunjang_id])->one();
                            $getTindakanPelayananT = TindakanPelayananT::find()
                                                    ->where([
                                                        'pendaftaran_id' => $pendaftaran_id,
                                                        'pasienmasukpenunjang_id' => $getNoPenunjang->pasienmasukpenunjang_id
                                                    ])->all();
                            $arrTindakanPelayanan = [];
                            foreach ($getTindakanPelayananT as $key => $value) {
                                if (in_array($value->daftartindakan_id, $batalTindakan)) {
                                    $arrTindakanPelayanan[] = [
                                            'tindakanpelayanan_id' => $value->tindakanpelayanan_id,
                                            'daftartindakan_id' => $value->daftartindakan_id
                                        ];
                                }
                            }

                            if (count($batalTindakan) == count($getTindakanPelayananT)) {
                                $getInstalasiPenunjang = Instalasi::find()->where(['instalasi_id' => $getNoPenunjang->instalasiasal_id])
                                                ->andWhere(['is_penunjang' => true])->one();
                                if ($getInstalasiPenunjang) {
                                    /** update status pasien pendaftaran **/
                                    $getPendaftaran = Pendaftaran::findOne(['pendaftaran_id' => $pendaftaran_id]);
                                    $getPendaftaran->status_periksa = DocoConstants::STATUS_PERIKSA_BTL_PRKS;
                                    $getPendaftaran->update();
                                    /** update status pasien pendaftaran **/
                                }
                            }

                            $registrationData['no_masukpenunjang'] = $getNoPenunjang->no_masukpenunjang;
                            $registrationData['ruangan_id'] = isset($registrationData['ruangan_id']) && !empty($registrationData['ruangan_id']) ? $registrationData['ruangan_id'] : Yii::$app->jwt->ruangan_id;
                            $registrationData['instalasi_id'] = isset($registrationData['instalasi_id']) && !empty($registrationData['instalasi_id']) ? $registrationData['instalasi_id'] : 1 ;
                            $registrationData['tgl_transaksi'] = date("Y-m-d H:i:s");
                            $registrationData['detail_tindakan'] = $arrTindakanPelayanan;

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
                        $transaction->commit();
                        return $this->helper->response([
                            'text' => 'Pemeriksaan Laboratorium Berhasil Dibatalkan.'
                        ], 200);
                    } else {
                        $transaction->rollBack();
                        return $this->responseJson(500, 'Pembatalan Order Gagal!');
                    }
                }

            } else {
                $transaction->rollBack();
                return $this->responseJson(400, 'Data Tidak Ditemukan!');
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi Kesalahan');
        }
    }

    /**
     * @controller actionExportPdf
     * @attribute #table_exportpdf# => table 
     **/
    public function actionExportPdf()
    {
        try {
            // Request
            $request = Yii::$app->request;

            // Define model
            $model = new InfoOrderanLabView;
            $query = $model::find()->where(['is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();

            // Check model
            if (!empty($model)) {
                // Header
                $header = array();
                
                // Print
                $print = new DocoPrint();

                // Assign attributes
                $print->attributes = [
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'header' => $header,
                        'model' => $query,
                    ]),
                ];

                // Print output
                $print->Output();
            }
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

    // Export excel
    public function actionExportExcel()
    {
        try {
            // Declare empty variables
            $data = array();
            $header = array();

            // Request
            $request = Yii::$app->request;

            // Define model
            $model = new InfoOrderanLabView;
            $query = $model::find()->where(['is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();

            // Assign data
            if (!empty($query)) {
                // Declare counter
                $counter = 0;

                // Loop
                foreach ($query as $index => $value) {
                    // Assign data
                    $data[$counter]['kode'] = $value->kode_kelompok;
                    $data[$counter]['kelompok_pemeriksaan'] = $value->nama_kelompok;
                    // Plus the counter
                    $counter++;
                }
            }
            
            // File path
            $filePath = DocoHelpers::exportExcel('Kelompok Pemeriksaan Radiologi', $data, $header, array("uploadPath" => "./uploads"));

            // Return
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
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


    public function actionGetView($id)
    {
        try {
            $result = InfoOrderanLabView::find()->andWhere(['pasienkirimkeunitlain_id' => $id])->one();
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
            $pasienKirimUnitLain = PasienKirimUnitlain::findOne($id);
            $model = new InfoOrderanLabDetailView;
            $query = $model::find()->where(['pasienkirimkeunitlain_id' => $id]);
            $query->andWhere(['is_deleted' => false, 'is_approve' => false]);
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

    public function actionGetInstalasi()
    {
        try {
            // Todos 
            $model = new Instalasi;
            $query = $model::find();
            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            // Return data
            return new ActiveDataProvider([
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


    public function actionGenerateApi($id, $ruanganid = null)
    {
        $return = array('labDetail' => array(), 'listPemeriksaan' => array(),'dataBatal' => array(),'dataPegawai' => array());
        // $pasienKirimUnitLain = PasienKirimUnitlain::find()->select(['pasienkirimkeunitlain_id'])->where(['pasienmasukpenunjang_id' => $id])->one();
        // if(!empty($pasienKirimUnitLain)) {
        //     $id = $pasienKirimUnitLain['pasienkirimkeunitlain_id'];
        // }
        $labDetail = InfoOrderanLabView::find()->andWhere(['pasienkirimkeunitlain_id' => $id])->one();
        $pemeriksaan = InfoOrderanLabDetailView::find()->where(['pasienkirimkeunitlain_id' => $id])->all();
        $dataBatal = BatalOrderPenunjangT::find()->where(['pasienkirimkeunitlain_id' => $id])->one();
        $listDokter = DokterView::find()->where(['instalasi_id'=>DocoConstants::INST_ID_LAB]);
        if ($ruanganid) {
            $listDokter->andWhere(['ruangan_id'=>$ruanganid]);
        }
        $return['listDokter'] = $listDokter->all();
        if (!empty($labDetail)) {
            $return['labDetail'] = $labDetail;
        }
        if (!empty($pemeriksaan)) {
            $return['listPemeriksaan'] = $pemeriksaan;
        }
        if (!empty($dataBatal)) {
            $return['dataBatal'] = $dataBatal;
            $return['dataPegawai'] = Pegawai::findOne($dataBatal->peg_menyetujui_id);
        }
        return $return;
    }

    public function actionIntegrateTindakanBmhp($pendaftaran_id)
    {
        try{
            $this->integrateTindakan($pendaftaran_id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function integrateBmhp($pendaftaran_id)
    {
        try{
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $resep = SyncPengeluaranobat::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'jenis' => 'BMHP', 'is_jurnal' => 'f'])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

            if($config->is_akunting != null) {
                if(!empty($resep)){
                    foreach($resep as $key => $value){
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
        try{
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $tindakan = SyncTindakan::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'instalasi_id' => 4])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
            if($config->is_akunting != null) {
                if(!empty($tindakan)){
                    foreach($tindakan as $key => $value){
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
        try{
            $id = $pendaftaran_id;
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $id])->one();
            $tindakan = SyncTindakan::find()->where(['no_pendaftaran' => $pendaftaran->no_pendaftaran, 'instalasi_id' => 4, 'is_jurnal' => 'f'])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();
            if($config->is_akunting != null) {
                if(!empty($tindakan)){
                    foreach($tindakan as $key => $value){
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

    // public function actionFilters()
    // {
    //     $request = Yii::$app->request;
    //     $result = $resultData = [];
    //     $term = $request->get('term', null);
    //     $page = $request->get('page', 1);
    //     $type = $request->get('type', []);
    //     $carabayar_id = "";
    //     $additionalPayload = $request->get('additionalPayload', []);
    //     if(isset($additionalPayload['carabayar_id']) && !empty($additionalPayload['carabayar_id'])) {
    //         $carabayar_id = $additionalPayload['carabayar_id'];
    //     }
    //     $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
    //     switch ($type) {
    //         case 'carabayar':
    //             $result = CaraBayar::find()
    //                 ->select(['carabayar_id as id', 'carabayar_nama as text'])
    //                 ->where(['is_active' => true]);

    //             if(!empty($term)) {
    //                 $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
    //             }
    //             $result->orderBy(['carabayar_nama' => SORT_ASC]);
    //             break;
            
    //         case 'penjamin':
    //             $result = Penjamin::find()
    //                 ->select(['penjamin_id as id', 'penjamin_nama as text'])
    //                 ->where(['is_active' => true]);

    //                 if(!empty($carabayar_id)) {
    //                     $result->andWhere(['carabayar_id' => $carabayar_id]);
    //                 }

    //                 if(!empty($term)) {
    //                     $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
    //                 }
    //                 $result->orderBy(['penjamin_nama' => SORT_ASC]);
    //             break;

    //         case 'ruangan':
    //             $result = Ruangan::find()
    //                 ->select(['ruangan_id as id', 'ruangan_nama as text'])
    //                 ->where(['is_active' => true, 'is_modul' => true]);

    //             if(!empty($term)) {
    //                 $result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
    //             }
    //             $result->orderBy(['ruangan_nama' => SORT_ASC]);
    //             break;

    //         case 'dokter':
    //             $result = Pegawai::find()
    //                 ->select(['pegawai_id as id', 'nama_pegawai as text'])
    //                 ->where(['is_active' => true, 'is_deleted' => false]);

    //             if(!empty($term)) {
    //                 $result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
    //             }
    //             $result->orderBy(['nama_pegawai' => SORT_ASC]);
    //             break;

    //         default:
                
    //             break;
    //     }

    //     $resultData = $result->asArray()->all();
    //     return $resultData;
    // }

    public function actionGetOptions()
    {
        $sql = "SELECT 
            'APS' as asalrujukan_nama
        UNION ALL
        SELECT 
            instalasi_m.instalasi_nama
        FROM instalasi_m WHERE is_deleted = false AND is_active = true and is_pelayanan = true
        UNION ALL
        SELECT 
            asalrujukan_m.asalrujukan_nama
        FROM asalrujukan_m WHERE is_deleted = false AND is_active = true";
        $asalRujukan = Yii::$app->db->createCommand($sql)->queryAll();

        $statusBayar = [
            1 => 'Sudah Bayar',
            0 => 'Belum Bayar'
        ];

        $statusPeriksa = DocoConstants::$status_lab;
        $caraBayar = CaraBayar::find()->select(['carabayar_id', 'carabayar_nama', 'carabayar_kode_warna'])->where(['is_active' => true])->all();
        return  [
            'asal_rujukan' => $asalRujukan,
            'status_bayar' => $statusBayar,
            'status_periksa' => $statusPeriksa,
            'cara_bayar' => $caraBayar,
        ];
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
        $penunjangId = $request->get('pasienmasukpenunjang_id');
        $where = '';
        if(!empty($id)) {
            $where = 'AND pk.pasienkirimkeunitlain_id = '.$id;
            $data = InfoPasienLabView::find()->where(['pasienkirimkeunitlain_id' => $id, 'is_referred' => true]);
            if(!empty($rujukanKeluarId)) {
                $data->andWhere(['rujukankeluar_id' => $rujukanKeluarId]);
            }
            $data = $data->all();
        }
        
        if(!empty($penunjangId)) {
            $where = 'AND i.pasienmasukpenunjang_id = '.$penunjangId;
        }

        $listData = [];
        $data = Yii::$app->db->createCommand("
            select 
            DISTINCT(p.pasiendirujukkeluar_id) pasiendirujukkeluar_id,
            p.rujukankeluar_id, 
            p.alasandirujuk, 
            p.diagnosa, 
            r.rumahsakit_rujukan, 
            d.daftartindakan_nama, 
            p.tgldirujuk AS tgl_rujukan, 
            i.nama_pasien, 
            i.no_rekam_medik, 
            i.jenis_kelamin_kode, 
            i.tanggal_lahir, 
            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama
            from pasiendirujukkeluar_t p
            left JOIN rujukankeluar_m r on r.rujukankeluar_id = p.rujukankeluar_id
            left JOIN permintaankepenunjang_t pk on pk.permintaankepenunjang_id = p.permintaankepenunjang_id
            left JOIN daftartindakan_m d on d.daftartindakan_id = pk.daftartindakan_id
            left JOIN infopasienlab_v i on i.pasienkirimkeunitlain_id = pk.pasienkirimkeunitlain_id
            LEFT JOIN ( SELECT a.pegawai_id,a.nama_pegawai, a.tanda_tangan, a.gelarbelakang, a.gelardepan
            FROM pegawai_m a) dokter_perujuk ON p.pegawai_id = dokter_perujuk.pegawai_id
            where pk.is_referred = true {$where}
            ORDER BY pasiendirujukkeluar_id DESC")
        ->queryAll();
        
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                $rujukanKeluarId = ArrayHelper::getValue($value, 'rujukankeluar_id');
                $tglRujukan = ArrayHelper::getValue($value, 'tgl_rujukan');
                $tglRujukan = date('Y-m-d', strtotime($tglRujukan));
                $groupedKey = $rujukanKeluarId.'-'.$tglRujukan;
                $value['diagnosa_dirujuk'] = !empty($value['diagnosa']) ? $value['diagnosa']: [];
                $value['pemeriksaanlab_nama'] = !empty($value['daftartindakan_nama']) ? $value['daftartindakan_nama']: '';
                $listData[$groupedKey][] = $value;
            }
            
            $profileRs = $this->getProfileRs();
            $rsName = ArrayHelper::getValue($profileRs, 'namaRs');
            $kota = ArrayHelper::getValue($profileRs, 'kota');
            $alamatRs = ArrayHelper::getValue($profileRs, 'alamat');
            $telpRs = ArrayHelper::getValue($profileRs, 'no_telp');
            $website = ArrayHelper::getValue($profileRs, 'website');
            $noTelpHumas = ArrayHelper::getValue($profileRs, 'notelphumas');
            $print = new DocoPrint('rujukan-keluar-lab');
            if(!empty($listData)) {
                $countAll = count($listData);
                $i = 0;
                foreach ($listData as $key => $value) {
                    $i++;
                    $countData = count($value);
                    foreach ($value as $k => $values) {
                        $k++;
                        $namaPasien = ArrayHelper::getValue($values, 'nama_pasien');
                        $noRekamMedik = ArrayHelper::getValue($values, 'no_rekam_medik');
                        $gender = ArrayHelper::getValue($values, 'jenis_kelamin_kode');
                        $tanggalLahir = ArrayHelper::getValue($values, 'tanggal_lahir');
                        $umur = !empty($tanggalLahir) ? DocoHelpers::getUmur($tanggalLahir) : '-';
                        $rsTujuan = ArrayHelper::getValue($values, 'rumahsakit_rujukan');
                        $pegawaiMenyetujui = ArrayHelper::getValue($values, 'dokter_perujuk_nama');
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
                    $break = ($k == $countData) ? true : false;
                    if($i == $countAll){
                        $break = false;
                    }
                    $print->generateHtml($break);
                }
                $print->Output(true);
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

        $diag_utama = ArrayHelper::getValue($PasienKirimUnitlain, 'diag_utama');
        $diag_penyerta = ArrayHelper::getValue($PasienKirimUnitlain, 'diag_penyerta');

        $result['diagnosa_utama'] = $diag_utama ? ArrayHelper::getValue(json_decode(json_decode($diag_utama, true), true), 'text', '-') : '-';
        $result['diagnosa_penyerta'] = $diag_penyerta ? json_decode(json_decode($diag_penyerta, true), true) : [];

        return $result;
    }

    public function actionValidasiStatusPemeriksaan()
    {
        $request = Yii::$app->request;
        $no_rujukan = $request->get('no_rujukan');
        try {
            
            $dataOrderan = InfoOrderanLabView::find()
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
            
                // Kondisi batal approve
                if($statusPenunjang == DocoConstants::BTL_APPROVE) {
                    $pegawai = ArrayHelper::getValue($dataPasienPenunjang, 'deleted_by');
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
?>
