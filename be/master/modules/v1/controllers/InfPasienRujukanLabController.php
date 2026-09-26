<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kelompok Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoOrderanLabView;
use app\modules\v1\models\InfoOrderanLabDetailView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PemeriksaanRad;
use app\modules\v1\models\JenisPemeriksaanRad;
use app\modules\v1\models\PasienKirimKeUnitLainT;
use app\modules\v1\models\PermintaanKePenunjangT;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\TindakanPelayananT;
use app\modules\v1\models\PendaftaranT;
use app\modules\v1\models\AntrianT;
use app\modules\v1\models\BatalOrderPenunjangT;

use Yii\helpers\arrayHelper;


class InfPasienRujukanLabController extends \Doco\components\DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\InfoOrderanLabView';

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

        // Unset actions
        unset($actions['index']);
        // var_dump($actions);
        // exit;

        // Return actions
        return $actions;
    }

    public function actionDeleted($id)
    {
        try {
            $data = PemeriksaanRad::find()->where(['InfoOrderanLabView_id' => $id])->one();
            if (!empty($data)) {
                \Yii::$app->response->statusCode = 500;
                $result = [
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data sudah di gunakan'
                ];
            } else {
                \Yii::$app->response->statusCode = 200;
                $result = (new InfoOrderanLabView)->delete($id);
            }

            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Action index
    public function actionIndex()
    {
        // Try catch
        try {
            // Define model
            $model = new InfoOrderanLabView;
            $query = $model::find();

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $tanggal_lahir = "";

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
                if(isset($_GET['advanced-filter']['tanggal_lahir'])){
                    $tanggal_lahir = date('Y-m-d', strtotime($_GET['advanced-filter']['tanggal_lahir']));
                }
            }
            if($between) {
                $query->andWhere(['between', 'tgl_rujukan', $start, $end]);
            }
            if(!empty($tanggal_lahir)){
                $query->andWhere(['tanggal_lahir'=>$tanggal_lahir]);
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

    public function actionProsesApprove(){
        // use app\modules\v1\models\PasienKirimKeUnitLainT;
        // use app\modules\v1\models\PermintaanKePenunjangT;
        // use app\modules\v1\models\PasienMasukPenunjangT;
        // use app\modules\v1\models\TindakanPelayananT;
        // use app\modules\v1\models\InfoOrderanLabDetailView;
        $post = \Yii::$app->request->post();
        

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            
            $PasienKirimUnitLainT = PasienKirimKeUnitLainT::findOne(['pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id']]);

            
            if(!empty($PasienKirimUnitLainT)){

              

                    $InfoOrderanLabView = InfoOrderanLabView::findOne(['pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id']]);
                    // proses input pasienmasukkepenunjang
                    $inputPasienMasuk = array(); // array untuk input data ke pasien masuk penunjang
                    
                    $inputPasienMasuk = array(
                        'pasienkirimkeunitlain_id' => $InfoOrderanLabView->pasienkirimkeunitlain_id,
                        'kelaspelayanan_id' => $InfoOrderanLabView->kelaspelayanan_id,
                        'jeniskasuspenyakit_id' => $InfoOrderanLabView->jeniskasuspenyakit_id,
                        'pasienadmisi_id' => $InfoOrderanLabView->pasienadmisi_id,
                        'pegawai_id' => $post['dokter_laboratorium'],
                        'ruangan_id' => $InfoOrderanLabView->ruanganpenunjang_id,
                        'pasien_id' => $InfoOrderanLabView->pasien_id,
                        'pendaftaran_id' => $InfoOrderanLabView->pendaftaran_id,
                        'ruanganasal_id' => $InfoOrderanLabView->ruangan_id,
                        'tglmasukpenunjang' => date('Y-m-d H:i:s'),
                        // 'no_antrian' => '120',
                        'kunjungan' => $InfoOrderanLabView->kunjungan,
                        'status_periksa' => '477',
                        'panggil_antrian' => false,
                        'instalasiasal_id' => $InfoOrderanLabView->instalasi_id,
                    );
               

                    $mPasienMasukPenunjangT = new PasienMasukPenunjangT;
                    $mPasienMasukPenunjangT->attributes = $inputPasienMasuk;
                    if($mPasienMasukPenunjangT->save()){

                        // update pasien kirim unit lain
                        $PasienKirimUnitLainT->pasienmasukpenunjang_id = $mPasienMasukPenunjangT->pasienmasukpenunjang_id;
                        $PasienKirimUnitLainT->status_penunjang = '471';
                        $PasienKirimUnitLainT->save();
                        // update pasien kirim unit lain

                        // input no antrian
                        $inputAntrian = array(
                        'ruangan_id' => $InfoOrderanLabView->ruanganpenunjang_id,
                        'carabayar_id' => $InfoOrderanLabView->carabayar_id,
                        'pendaftaran_id' => $InfoOrderanLabView->pendaftaran_id,
                        'layarantrian_id' => '0',
                        'loket_id' => '0',
                        'tgl_antrian' => date('Y-m-d H:i:s'),
                        // 'no_antrian' => 'No Antrian',
                        // 'carabayar_loket' => 'Carabayar Loket',
                        'panggil_flag' => false,
                        // 'additional_data' => 'Additional Data',
                        // 'created_date' => 'Created Date',
                        // 'created_by' => 'Created By',
                        // 'modified_count' => 'Modified Count',
                        // 'last_modified_date' => 'Last Modified Date',
                        // 'last_modified_by' => 'Last Modified By',
                        // 'is_deleted' => 'Is Deleted',
                        // 'is_active' => 'Is Active',
                        // 'deleted_date' => 'Deleted Date',
                        // 'deleted_by' => 'Deleted By',
                        'pasien_id' => $InfoOrderanLabView->pasien_id,
                        'penjamin_id' => $InfoOrderanLabView->penjamin_id,
                        'pegawai_id' => $post['dokter_laboratorium'],
                        'panggilan_ke' => '0',
                        'status_antrian' => '0',
                        'status_pasien' => $InfoOrderanLabView->status_pasien,
                        'groupcarabayar_id' => $InfoOrderanLabView->groupcarabayar_id,
                        'jenisantrian_id' => '179',
                        'klasifikasipasien_id' => '1',
                        'jadwaldokter_id' => '0',
                        'fungsiantrian_id' => '328',
                        'racikan_id' => '0',
                        'is_konsulpoli' => '0',
                        );
                        $mAntrianT = new AntrianT;
                        $mAntrianT->attributes = $inputAntrian;
                        if($mAntrianT->save()){
                            
                                // echo '<pre>';
                                // print_r($mPasienMasukPenunjangT->getErrors());
                                // echo '</pre>';
                                $cAntrianT = AntrianT::findOne(['antrian_id'=> $mAntrianT->antrian_id]);
                                if(!empty($cAntrianT)){
                                    $mPasienMasukPenunjangT->no_antrian = $cAntrianT->no_antrian;
                                    $mPasienMasukPenunjangT->save();
                                }
                                // echo $mAntrianT->antrian_id;
                                // exit;
                        }else{
                            // echo '<pre>';
                            // print_r($mAntrianT->getErrors());
                            // echo '</pre>';
                            // exit;
                        }
                        // input no antrian

                        // loop tindakan medis
                        $InfoOrderanLabDetailView = InfoOrderanLabDetailView::findAll(['pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id']]);

                        $inputTindakanPelayanan = array(); // digunakan untuk menampung data yang akan di input ke tindakan pelayanan

                   
                        $tempDaftarTindakan = array(); // digunakan untuk menampung daftar tindakan dan tipepaket id dari $inputTindakanPelayanan. untuk di updatekan ke permintaanPenunjang

                        if(!empty($InfoOrderanLabDetailView)){
                            $dataLabDetail = ArrayHelper::toArray($InfoOrderanLabDetailView);
                            foreach($dataLabDetail as $kt => $vt){
                            
                                // insert tindakan pelayanan
                                    $inputTindakanPelayanan[] = array(
                                        // 'shift_id' => '0',
                                        'kelaspelayanan_id' => $InfoOrderanLabView->kelaspelayanan_id,
                                        // 'kelastanggungan_id' => '0',
                                        'pasien_id' => $InfoOrderanLabView->pasien_id,
                                        // 'rencanaoperasi_id' => '0',
                                        'instalasi_id' => $InfoOrderanLabView->instalasipen_id,
                                        'daftartindakan_id' => $vt['daftartindakan_id'],
                                        // 'alatmedis_id' => '0',
                                        'tipepaket_id' => $vt['tipepaket_id'],
                                        // 'tindakansudahbayar_id' => '0',
                                        'carabayar_id' => $InfoOrderanLabView->carabayar_id,
                                        'pendaftaran_id' => $InfoOrderanLabView->pendaftaran_id,
                                        // 'hasilpemeriksaanrad_id' => '0',
                                        'jeniskasuspenyakit_id' => $InfoOrderanLabView->jeniskasuspenyakit_id,
                                        // 'hasilpemeriksaanrm_id' => '0',
                                        'ruangan_id' => $InfoOrderanLabView->ruanganpenunjang_id,
                                        // 'konsulpoli_id' => '0',
                                        'pasienmasukpenunjang_id' => $mPasienMasukPenunjangT->pasienmasukpenunjang_id,
                                        // 'hasilpemeriksaanlabdetail_id' => '0',
                                        'penjamin_id' => $InfoOrderanLabView->penjamin_id,
                                        'pasienadmisi_id' => $InfoOrderanLabView->pasienadmisi_id,
                                        // 'verifikasitagihan_id' => '0',
                                        // 'jurnalrekening_id' => '0',
                                        // 'instruksitindakan_id' => '0',
                                        'tgl_tindakan' => date('Y-m-d H:i:s'),
                                        'tarif_rsakomodasi' => '0',
                                        'tarif_medis' => '0',
                                        'tarif_paramedis' => '0',
                                        'tarif_bhp' => '0',
                                        'tarif_satuan' => $vt['tarif_pelayanan'],
                                        'tarif_tindakan' => ($vt['tarif_pelayanan'] * $vt['qtypermintaan']),
                                        'tarifcyto_tindakan' => $vt['tarif_cytotindakan'],
                                        'satuan_tindakan' => $vt['satuan_tindakan'],
                                        'qty_tindakan' => $vt['qtypermintaan'],
                                        'cyto_tindakan' => $vt['is_cyto'],
                                        'dokterpenanggungjawab_id' => $post['dokter_laboratorium'],
                                        // 'dokterpelaksana_id' => '0',
                                        // 'dokteranastesi_id' => '0',
                                        // 'dokterdelegasi_id' => '0',
                                        // 'bidan1_id' => '0',
                                        // 'bidan2_id' => '0',
                                        // 'perawat1_id' => '0',
                                        // 'perawat2_id' => '0',
                                        'discount_tindakan' => '0',
                                        'pembebasan_tindakan' => '0',
                                        'subsidiasuransi_tindakan' => '0',
                                        'subsidipemerintah_tindakan' => '0',
                                        'subsisidirumahsakit_tindakan' => '0',
                                        'uangditerima_tindakan' => '0',
                                        // 'keterangantindakan' => '',
                                        'additional_data' => json_encode(array('permintaankepenunjang_id'=>$vt['permintaankepenunjang_id'])),
                                        // 'created_date' => 'Created Date',
                                        // 'created_by' => 'Created By',
                                        // 'modified_count' => 'Modified Count',
                                        // 'last_modified_date' => 'Last Modified Date',
                                        // 'last_modified_by' => 'Last Modified By',
                                        // 'is_deleted' => 'Is Deleted',
                                        // 'is_active' => 'Is Active',
                                        // 'deleted_date' => 'Deleted Date',
                                        // 'deleted_by' => 'Deleted By',
                                        'pembulatan' => '0',

                                    );
                                // insert tindakan pelayanan
                            }


                            // echo '<pre>';
                            // print_r($inputTindakanPelayanan);
                            // prisnt_r($vt);
                            // echo '</pre>';
                            // exit;
                            $TindakanPelayananT = TindakanPelayananT::batchInsert($inputTindakanPelayanan,false);

                            // update permintaan penunjang
                            // $connection->createCommand("
                            //     update permintaankepenunjang_t as a set tindakanpelayanan_id = b.tindakanpelayanan_id from tindakanpelayanan_t as b where  (a.daftartindakan_id = b.daftartindakan_id or a.tipepaket_id = b.tipepaket_id) ORDER BY a.tindakanpelayanan_id DESC
                            // ")->execute();
                            // update permintaan penunjang

                        
                        
                        }
                        // loop tindakan medis
                        // respon sukses
                        $transaction->commit();
                        $result = [
                            'status' => 200,
                            'title' => 'Input Berhasil',
                            'text' => 'Aprroval Pasien Berhasil'
                        ]; 
                    }else{
                        $result['status'] = 500;
                        $result['title'] = 'Gagal insert';
                        $result['text'] = $mPasienMasukPenunjangT->getErrors();
                    }
                    // proses input pasienmasukkepenunjang
                
            }else{
                $transaction->rollBack();
                // \Yii::$app->response->statusCode = 500;
                $result = [
                    'status'=>500,
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data tidak ditemukan'
                ]; 
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }


    public function actionProsesBatal(){
        $post = \Yii::$app->request->post();


        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();

        try{
            $PasienKirimUnitLainT = PasienKirimKeUnitLainT::findOne(['pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id']]);
            if(!empty($PasienKirimUnitLainT)){

                $tanggal_batal = "";
                if(!empty($post['tanggal_batal'])){
                    $tanggal_batal = date('Y-m-d H:i:s', strtotime($post['tanggal_batal']));
                }

                $inputBatalOrder = array(
                    'pasienkirimkeunitlain_id' => $post['pasienkirimkeunitlain_id'],
                    // 'no_batalorder' => 'No Batalorder',
                    'tgl_batalorder' => $tanggal_batal,
                    'peg_menyetujui_id' => $post['disetujui_oleh'],
                    'alasan' => $post['alasan_pembatalan'],
                    'additional_data' => json_encode(array('pasienkirimkeunitlain_id'=>$post['pasienkirimkeunitlain_id'])),
                    // 'created_date' => 'Created Date',
                    // 'created_by' => 'Created By',
                    // 'modified_count' => 'Modified Count',
                    // 'last_modified_date' => 'Last Modified Date',
                    // 'last_modified_by' => 'Last Modified By',
                    // 'is_deleted' => 'Is Deleted',
                    // 'is_active' => 'Is Active',
                    // 'deleted_date' => 'Deleted Date',
                    // 'deleted_by' => 'Deleted By',
                );
                $mBatalOrderPenunjangT = new BatalOrderPenunjangT;
                $mBatalOrderPenunjangT->attributes = $inputBatalOrder;
                if($mBatalOrderPenunjangT->save()){

                    // update pasien kirim unit lain
                    $PasienKirimUnitLainT->status_penunjang = '472';
                    $PasienKirimUnitLainT->save();
                        // update pasien kirim unit lain
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Input Berhasil',
                        'text' => 'Pembatalan Order Berhasil'
                    ];
                }else{
                    $transaction->rollBack();
                    $result['status'] = 500;
                    $result['title'] = 'Gagal insert';
                    $result['text'] = $mBatalOrderPenunjangT->getErrors();
                    $result['post'] = $post;
                    $result['inputData'] = $inputBatalOrder;
                }

            }else {
                $transaction->rollBack();
                // \Yii::$app->response->statusCode = 500;
                $result = [
                    'status' => 500,
                    'title' => 'Terjadi kesalahan',
                    'text' => 'Data tidak ditemukan'
                ];
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

   

    /**
    * @controller actionExportPdf
    * @attribute #table_exportpdf# => table 
    **/
    public function actionExportPdf()
    {
        // Try catch
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
        // Try catch
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
            $result = InfoOrderanLabView::find()->andWhere(['pasienkirimkeunitlain_id'=>$id])->one();
            return $result;
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetPemeriksaanView($id)
    {
        // Try catch
        try {
            // Define model
            $model = new InfoOrderanLabDetailView;
            $query = $model::find()->where(['pasienkirimkeunitlain_id' => $id]);

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


     // Action index
    public function actionGetDokter()
    {
        // Try catch
        try {
            // Define model
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


    public function actionGenerateApi($id){
        $return = array('labDetail'=>array(),'listPemeriksaan'=>array());
        $labDetail = InfoOrderanLabView::find()->andWhere(['pasienkirimkeunitlain_id' => $id])->one();
        $pemeriksaan = InfoOrderanLabDetailView::find()->where(['pasienkirimkeunitlain_id' => $id])->all();
        if(!empty($labDetail)){
            $return['labDetail'] = $labDetail;
        }
        if(!empty($pemeriksaan)){
            $return['listPemeriksaan'] = $pemeriksaan;
        }
        return $return;
    }


}
?>