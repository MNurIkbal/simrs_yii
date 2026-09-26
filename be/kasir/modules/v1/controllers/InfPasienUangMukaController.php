<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\BayarUangMuka;
use app\modules\v1\models\TandaBuktiKeluar;
use app\modules\v1\models\PengembalianUangMuka;
use app\modules\v1\models\CetakKwitansiBkm;
use app\modules\v1\models\CetakKwitansiBkk;
use app\modules\v1\models\PembatalanUangMuka;
use app\modules\v1\models\InfoBayarUangMukaView;
use app\modules\v1\models\InfoBayarUangMukaDetailView;
use app\modules\v1\models\PemakaianUangMuka;
use SirsCore\features\IntegrasiAkunting;
use Doco\components\DocoMessages;
use Doco\Services\InternalService;
use yii\web\UploadedFile;

class InfPasienUangMukaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\BayarUangMuka';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        $verbs['cancel'] = ['GET'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $query = $this->actionGetFilter(false);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetFilter($isCount = true)
    {
        $request = Yii::$app->request;
        $model = new InfoBayarUangMukaView;
        $query = $model::find();
        $start = $starBayar = date('Y-m-d 00:00:00');
        $end = $endBayar = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $starBayar = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endBayar = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                
                $query->andWhere(['between', 'tgl_pembayaran', $starBayar, $endBayar]);
                unset($_GET['advanced-filter']['tgl_pembayaran']);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        if($isCount) {
            $query = $query->asArray()->all();
        }
        
        return $query;
    }

    public function actionView($id){
        $request = Yii::$app->request;
        try {
            $model = new InfoBayarUangMukaView;
            $model_pemakaian = new PemakaianUangMuka;
            $uangmuka_dipakai = false;
            $query = $model::find()->where(['pendaftaran_id'=>$id])->one();
            $query_pemakaian = $model::find()->where(['pendaftaran_id'=>$id])->asArray()->one();
            $pemakaian_uangmuka = isset($query_pemakaian['pemakaian_uangmuka']) ? (float)$query_pemakaian['pemakaian_uangmuka'] : 0;
            $pengembalian_uangmuka = isset($query_pemakaian['pengembalian']) ? (float)$query_pemakaian['pengembalian'] : 0;
            
            if( $pemakaian_uangmuka > 0 || $pengembalian_uangmuka > 0){
                $uangmuka_dipakai = true;
            };

            return [
            'header'=>$query,
            'uangmuka_dipakai'=>$uangmuka_dipakai,
            'uangmuka_dikembalikan'=>$pengembalian_uangmuka>0,
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
    public function actionDetail($id){
        $request = Yii::$app->request;
        try {
            $model = new InfoBayarUangMukaDetailView;
            $query = $model::find()->where(['pendaftaran_id'=>$id]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
            return $query;
        } catch (\Exception $e) {
            return [];
        }
    }
    public function actionDetailPengembalian($id)
    {
        $request = Yii::$app->request;
        try {
            $model = new CetakKwitansiBkk;
            $query = $model::find()->where(['pendaftaran_id'=>$id]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
            return $query;
        } catch (\Exception $e) {
            return [];
        }
    }
    //action buat handle select no pendaftaran
    public function actionGetDataPendaftaran()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $term = strtoupper($post['term']);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if($post['date']){
            $newData = explode(' - ', $post['date']);
            if(count($newData) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($newData[0]));
                $end = date('Y-m-d 23:59:59', strtotime($newData[1]));
            }
        }
        $sql = "select pendaftaran_id,no_pendaftaran from infokunjunganrs_v where kondisikeluar_id IS NULL AND no_pendaftaran LIKE '%{$term}%' and tgl_pendaftaran BETWEEN '{$start}' AND'{$end}' group by no_pendaftaran,pendaftaran_id order by no_pendaftaran asc limit 50";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;

    }

    public function actionCancel($id)
    {
        try {             

            $models = new PembatalanUangMuka; 
            $model_uangmuka = BayarUangMuka::find()->where(['bayaruangmuka_id'=>$id])->one();
            $models->bayaruangmuka_id = $id;
            $models->tandabuktibayar_id = $model_uangmuka->tandabuktibayar_id;
            $models->ruangan_id = $model_uangmuka->ruangan_id;
            $models->tglpembatalan = date('Y-m-d H:i:s');
            $models->keterangan_batal = "tester";   
            if($models->save()){
                $model_uangmuka->pembatalanuangmuka_id = $models->pembatalanuangmuka_id;
                $model_uangmuka->is_deleted = true;
                $model_uangmuka->save();
                $result = (new BayarUangMuka)->update($id); 
                return $result;
            }else{
                return "gagal";    
            }      
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionPrintKwitansi
    * @attribute #no_kwitansi# => no kwitansi 
    * @attribute #nama_pasien# => nama pasien 
    * @attribute #nama_kasir# => nama kasir 
    * @attribute #jumlah_diterima# => jumlah diterima 
    * @attribute #no_pendaftaran# => no pendaftaran 
    * @attribute #tgl_uangmuka# => tanggal uang muka 
    * @attribute #terbilang# => jumlah diterima terbilang
    **/
    public function actionPrintKwitansi()
    {
        try{
            $model = new CetakKwitansiBkm;
            $request = Yii::$app->request;
            $id = $request->post('id', null);

            if(!$id){
                throw new \yii\base\ErrorException("ID Tidak Ditemukan", 500);
            }

            $data = $model::find()->where(['bayaruangmuka_id'=>$id])->one();
            if(!$data){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#no_kwitansi#' => 
                    isset($data['no_kwitansi'])?$data['no_kwitansi']:'',
                '#nama_pasien#' => 
                    isset($data['nama_pasien']) ? $data['nama_pasien']:'',
                '#nama_kasir#' => 
                    isset($data['kasir'])?$data['kasir']:'',
                '#jumlah_diterima#' => 
                    isset($data['total_terbayar']) ? DocoHelpers::rupiahDisplay($data['total_terbayar']):'',
                '#no_pendaftaran#'=>
                    isset($data['no_pendaftaran']) ? $data['no_pendaftaran']:'',
                '#tgl_uangmuka#'=>
                    isset($data['tgl_pembayaran']) ? date("d-m-Y",strtotime($data['tgl_pembayaran'])):'',
                '#terbilang#'=>
                    isset($data['total_terbayar'])? DocoHelpers::Terbilang($data['total_terbayar']):'',
            ];

            // return $print->attributes;
            $print->Output();
        }catch (Exception $e){
            // asd
        }
    }

    /**
    * @controller actionPrintBkm
    * @attribute #no_bkm# => no bkm 
    * @attribute #nama_pasien# => nama pasien 
    * @attribute #nama_kasir# => nama kasir 
    * @attribute #jumlah_diterima# => jumlah diterima 
    * @attribute #no_pendaftaran# => no pendaftaran 
    * @attribute #tgl_uangmuka# => tanggal uang muka 
    * @attribute #terbilang# => jumlah diterima terbilang
    **/
    public function actionPrintBkm()
    {
        try{
            $model = new CetakKwitansiBkm;

            $request = Yii::$app->request;
            $id = $request->post('id',null);
            if(!$id){
                throw new \yii\base\ErrorException("ID Tidak Ditemukan", 500);
            }

           $data = $model::find()->where(['bayaruangmuka_id'=>$id])->one();
            if(!$data){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $print = new DocoPrint();            
            $print->attributes = [
                '#no_bkm#' => 
                    isset($data['no_bkm'])?$data['no_bkm']:'',
                '#nama_pasien#' => 
                    isset($data['nama_pasien']) ? $data['nama_pasien']:'',
                '#nama_kasir#' => 
                    isset($data['nama_pegawai'])?$data['nama_pegawai']:'',
                '#jumlah_diterima#' => 
                    isset($data['total_terbayar']) ? DocoHelpers::rupiahDisplay($data['total_terbayar']):'',
                '#no_pendaftaran#'=>
                    isset($data['no_pendaftaran']) ? $data['no_pendaftaran']:'',
                '#tgl_uangmuka#'=>
                    isset($data['tgl_pembayaran']) ? date("d-m-Y",strtotime($data['tgl_pembayaran'])):'',
                '#terbilang#'=>
                    isset($data['total_terbayar'])? DocoHelpers::Terbilang($data['total_terbayar']):'',
            ];
            $print->Output();
        }catch (Exception $e){
            // asd
        }
    }
    public function actionPackPengembalian()
    {
        $request = Yii::$app->request;
        $result['konfig'] = [];
        $result['data'] = [];
        try {
            $result['data'] = $this->actionView($request->get('id', null));
            $result['konfig'] = Yii::$app->runAction('v1/allow/get-konfig-system');
            $result['konfig'] = $result['konfig']['response'];
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }
    public function actionPengembalian()
    {
        $request = Yii::$app->request;
        $model = new PengembalianUangMuka;
        $modelTanda = new TandaBuktiKeluar;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $post = $request->post();
            $model->attributes = $post;
            if($model->save()){
                $pengembalianId = $model->pengembalianuangmuka_id;
                $arrTanda = [
                    'pengembalianuangmuka_id' => $model->pengembalianuangmuka_id,
                    'ruangan_id'=> $model->ruangan_id,
                    'pegawai1_id'=> $post['pegawai1_id'],
                    'tgl_buktikeluar' => $model->tgl_pengembalian,
                    'jml_pembulatan' => $model->pembulatan,
                    'biaya_administrasi' => $model->biaya_administrasi,
                    'uang_diterima' => $post['uang_diterima'],
                    'jml_pembayaran' => $post['uang_diterima'],
                    'is_tunai' => ($post['carapembayaran']) ? 0 : 1,
                    'namapemilik_rek' => $post['namapemilik_rek'],
                    'no_rek'=> $post['no_rek'],
                ];
                $modelTanda->attributes = $arrTanda;
                if($modelTanda->save()){
                    $transaction->commit();
                    IntegrasiAkunting::pengembalianUangMuka($model->pengembalianuangmuka_id);
                    return [
                        'title'=>'Proses Berhasil',
                        'text'=>'Pengembalian Uang Muka Berhasil Dilakukan',
                        'id'=>DocoHelpers::encrypt($modelTanda->tandabuktikeluar_id)
                    ];
                }else{
                    $transaction->rollBack();
                    return $modelTanda->getErrors();
                    throw new \Exception("Terjadi Kesalahan", 1);
                }
            }else{
                $transaction->rollBack();
                return $model->getErrors();
                throw new \Exception("Terjadi Kesalahan", 1);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
    }
    /**
    * @controller actionPrintKwitansiKeluar
    * @attribute #no_kwitansi# => no kwitansi 
    * @attribute #nama_pasien# => nama pasien 
    * @attribute #nama_kasir# => nama kasir 
    * @attribute #jumlah_diterima# => jumlah diterima 
    * @attribute #no_pendaftaran# => no pendaftaran 
    * @attribute #tgl_uangmuka# => tanggal uang muka 
    * @attribute #terbilang# => jumlah diterima terbilang
    **/
    public function actionPrintKwitansiKeluar()
    {
        try{
            $model = new CetakKwitansiBkk;

            $request = Yii::$app->request;
            $id = $request->post('id',null);
            if(!$id){
                throw new \yii\base\ErrorException("ID Tidak Ditemukan", 500);
            }

            $data = $model::find()->where(['tandabuktikeluar_id'=>$id])->one();
            if(!$data){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#no_kwitansi#' => 
                    isset($data['no_kwitansi'])?$data['no_kwitansi']:'',
                '#nama_pasien#' => 
                    isset($data['nama_pasien']) ? $data['nama_pasien']:'',
                '#nama_kasir#' => 
                    isset($data['nama_pegawai'])?$data['nama_pegawai']:'',
                '#jumlah_diterima#' => 
                    isset($data['jml_pembayaran']) ? DocoHelpers::rupiahDisplay($data['jml_pembayaran']):'',
                '#no_pendaftaran#'=>
                    isset($data['no_pendaftaran']) ? $data['no_pendaftaran']:'',
                '#tgl_uangmuka#'=>
                    isset($data['tgl_buktikeluar']) ? date("d-m-Y",strtotime($data['tgl_buktikeluar'])):'',
                '#terbilang#'=>
                    isset($data['jml_pembayaran'])? DocoHelpers::Terbilang($data['jml_pembayaran']):'',
            ];
            $print->Output();
        }catch (Exception $e){
            // asd
        }
    }

    /**
    * @controller actionPrintBkmKeluar
    * @attribute #no_bkm# => no bkm 
    * @attribute #nama_pasien# => nama pasien 
    * @attribute #nama_kasir# => nama kasir 
    * @attribute #jumlah_diterima# => jumlah diterima 
    * @attribute #no_pendaftaran# => no pendaftaran 
    * @attribute #tgl_uangmuka# => tanggal uang muka 
    * @attribute #terbilang# => jumlah diterima terbilang
    **/
    public function actionPrintBkmKeluar()
    {
        try{
            $model = new CetakKwitansiBkk;

            $request = Yii::$app->request;
            $id = $request->post('id',null);
            if(!$id){
                throw new \yii\base\ErrorException("ID Tidak Ditemukan", 500);
            }

           $data = $model::find()->where(['tandabuktikeluar_id'=>$id])->one();
            if(!$data){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#no_bkm#' => 
                    isset($data['no_kwitansi'])?$data['no_kwitansi']:'',
                '#nama_pasien#' => 
                    isset($data['nama_pasien']) ? $data['nama_pasien']:'',
                '#nama_kasir#' => 
                    isset($data['nama_pegawai'])?$data['nama_pegawai']:'',
                '#jumlah_diterima#' => 
                    isset($data['jml_pembayaran']) ? DocoHelpers::rupiahDisplay($data['jml_pembayaran']):'',
                '#no_pendaftaran#'=>
                    isset($data['no_pendaftaran']) ? $data['no_pendaftaran']:'',
                '#tgl_uangmuka#'=>
                    isset($data['tgl_buktikeluar']) ? date("d-m-Y",strtotime($data['tgl_buktikeluar'])):'',
                '#terbilang#'=>
                    isset($data['jml_pembayaran'])? DocoHelpers::Terbilang($data['jml_pembayaran']):'',
            ];
            $print->Output();
        }catch (Exception $e){
            // asd
        }
    }

    public function actionBatalUangMuka(){
        $request = Yii::$app->request;
        $id = $request->get('id',null);
        $alasan_batal = $request->get('alasan_batal',null);
        $is_tunai = $request->get('is_tunai',null);
        $password = $request->get('password',null);
        $helpers = new DocoHelpers;
        $date_now = date('Y-m-d H:i:s');

        try {
            $model_pemakaian = New PemakaianUangMuka;
            $query = BayarUangMuka::findOne($id);

            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
            $loginpemakai_id = isset(Yii::$app->jwt->user->loginpemakai_id) ? Yii::$app->jwt->user->loginpemakai_id : null ;
            $query_pemakaian = $model_pemakaian::find()->where(['pendaftaran_id'=>$query->pendaftaran_id])->count();

            if($query_pemakaian > 0){
                return $helpers->callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'message' => 'Proses Gagal.',
                    'text' => 'Uang Muka sudah pernah ditransaksikan'
                ]);
            }

            $query->alasan_batal = $alasan_batal;
            $query->is_tunai =  $is_tunai;
            $query->is_deleted =  TRUE;
            $query->deleted_date = $date_now;
            $query->deleted_by = $loginpemakai_id;
            if($query->validate() && $query->save()){
                return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM, [
                    'title' => 'Pembatalan Uang Muka Berhasil',
                    'text' => DocoMessages::SUC_MESSAGE_DELETED,
                ]);
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                    'data' => $query->errors
                ]);
            }    
        }                
         catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }        
    }

    public function actionExportExcel() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);
        
        $limit = 20;
        $data = $this->actionGetFilter();
        $countData = count($data);
        $totalPerPage = ceil($countData/$limit);
        $uri_kasir = Yii::$app->docoRest->getBaseUri('kasir');
        $params = [
            'sendToUrl' => 'inf-pasien-uang-muka/drop-file',
            'getDataUrl' => 'inf-pasien-uang-muka/get-filter',
            'base_uri' => $uri_kasir,
        ];
        (new InternalService)->sendTo([
            'Sirs' => [
                'DataExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $get,
                    'params' => $params
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcelUangMuka' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $get,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'params' => $params
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $path = "uploads/";
            $nameFile = $path .'/'. $fileName.'.'.$ext;
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
}