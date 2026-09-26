<?php
// Author : Budi

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\kasir\models\BayarUangMukaForm;

class TraPembayaranUangMukaController extends DocoController
{
    protected $_title = "Transaksi Pembayaran Uang Muka";
    protected $_module = 'kasir/tra-pembayaran-uang-muka/';
    protected $_restKasir; protected $_restMaster;
    // protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; $this->_restMaster = Yii::$app->docoRest->master;
        // $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    //action index sama save data
    public function actionIndex()
    {   
        $title = DHtml::getTitleMenu();
        if(empty($title)){
            $title = $this->_title;
        }
        $request = Yii::$app->request;
        $model_form = new BayarUangMukaForm;
        if ($request->post()) {
            $post = $request->post();
            $model_form->attributes = isset($post['BayarUangMukaForm']) ? $post['BayarUangMukaForm'] : [];
            $model_form->no_pendaftaran = isset($post['no_pendaftaran']) ? $post['no_pendaftaran'] : null;
            $model_form->tanggal_pembayaran = date('Y-m-d', strtotime($model_form->tanggal_pembayaran));
            $model_form->biayaadministrasi = 0;
            $model_form->jmlpembulatan = 0;
            if ($model_form->validate()) {
                $response = $this->_restKasir->request('POST', 'tra-pembayaran-uang-muka/create',[
                                'form_params'=> $model_form->attributes
                            ]); 
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body,false);
            } else {
                    $errors = DocoHelpers::parseError($model_form->errors, 'BayarUangMukaForm');
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                
            }
            return DocoHelpers::response($model_form->getErrors(), 422, 'BayarUangMukaForm');
        }

        $setCache = $this->_restKasir->get('allow/get-konfig-system', []);
        $konfig = json_decode($setCache->getBody(), true)['response'];
        return $this->render('index', get_defined_vars());
    }
    //action buat modal data pendaftaran
    public function actionGetDataPendaftaran()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restKasir->get('tra-pembayaran-uang-muka/get-pendaftaran?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            // $body = [];
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                unset($value['pendaftaran_id']);
                //$primaryKey = 1;

                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-pendaftaran' => $value['no_pendaftaran'],
                    'data-no_rekam_medik' => $value['no_rekam_medik'],
                    'data-nama_pasien' => $value['nama_pasien'],
                    'data-carabayar_nama' => $value['carabayar_nama'],
                    'data-penjamin_nama' => $value['penjamin_nama'],
                    'data-kelas_pelayanan' => $value['kelaspelayanan_nama'],
                    'title' => \Yii::t('fe', 'Pilih'),
                ]);
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    //action buat ambil data pasien terus disimpen di modal
    public function actionGetDataPasien()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restKasir->get('tra-pembayaran-uang-muka/data-pasien?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['pendaftaran_id'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    //action buat triger modal
    public function actionSearch($tipe = NULL)
    {        
        if($tipe == 'pasien') {
            $path = 'search_pasien';
        } elseif($tipe == 'pendaftaran') {
            $path = 'search_pendaftaran';
        } else {
            $path = 'search_norm';
        }

        return $this->renderAjax($path, get_defined_vars());
    }
    //action buat handle list data select2 no rm sama pasien
    public function actionListData(){
        try {
            $request = Yii::$app->request;
            $response = $this->_restKasir->get('tra-pembayaran-uang-muka/get-list-data');
            $row = [];
            $body = json_decode($response->getBody(), true);
            $return = ['data'=>$body['response']['data_pendaftaran'],'data_rm'=>$body['response']['data_rm']];
            return DocoHelpers::response($return);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
        
    }

    //action buat handle data no pendaftaran
    public function actionGetPendaftaran()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restKasir->request('POST', 'tra-pembayaran-uang-muka/get-data-pendaftaran',[
                                'form_params'=>['term'=>$_GET['q']['term']],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id'=>$value['pendaftaran_id'],'text'=>$value['no_pendaftaran'].' - '.$value['no_rekam_medik']. ' - '.$value['nama_pasien']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    //action buat ambil data pembayaran sama pasien ketika select2 pendaftaran berubah
    public function actionGetData($id){
        try {
            if($id == 'null'){
                $body['response']['data_pembayaran'] = 'kosong';
                $body['response']['data_pasien'] = 'kosong';
                return DocoHelpers::response($body['response']);
            }
            $request = Yii::$app->request;
            $id = $_GET['id'];
            $response = $this->_restKasir->get( 'tra-pembayaran-uang-muka/get-data',['query' => ['id' => $id ]]);
            $body = json_decode($response->getBody(), true);

            if(empty($body['response']['data_pembayaran'])){
                $body['response']['data_pembayaran'] = "kosong";
            }
            if(empty($body['response']['data_pasien'])){
                $body['response']['data_pasien'] = $id;
            }

            return DocoHelpers::response($body['response']);

        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPrintKwitansi()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-kwitansi.pdf";
        try {
            $id = $request->get('id',null);
            $id = DocoHelpers::decrypt($id);
            $post = ['id'=>$id];
            $response = $this->_restKasir
                        ->post('tra-pembayaran-uang-muka/print-kwitansi',
                            [
                                'form_params' => $post,
                                'save_to' => $path
                            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {  
            var_dump(json_decode($e->getResponse()->getBody()));exit;
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) { 
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintBkm()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-bkm.pdf";
        try {
            $id = $request->get('id',null);
            $id = DocoHelpers::decrypt($id);
            $post = ['id'=>$id];
            $response = $this->_restKasir
                        ->post('tra-pembayaran-uang-muka/print-bkm',
                            [
                                'form_params' => $post,
                                'save_to' => $path
                            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {  
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) { 
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
