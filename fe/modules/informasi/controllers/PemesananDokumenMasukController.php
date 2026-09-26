<?php
// Author : Ardi Pratama

namespace Doco\informasi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\informasi\models\KirimDokRmForm;

class PemesananDokumenMasukController extends DocoController
{
    protected $_title = "Informasi :: Pemesanan Dokumen Rekam Medik";
    protected $_module = 'informasi/pemesanan-dokumen-masuk/';
    protected $_restInformasi;

    public function init()
    {
        parent::init();
        $this->_restInformasi = Yii::$app->docoRest->informasi;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $instalasiReq = $this->_restInformasi->get('allow/list-instalasi');
        $instalasiReq = json_decode($instalasiReq->getBody(),TRUE);
        $list_instalasi = ArrayHelper::map($instalasiReq['response']['data'],'instalasi_id','instalasi_nama');

        $ruanganReq = $this->_restInformasi->get('allow/list-ruangan');
        $ruanganReq = json_decode($ruanganReq->getBody(),TRUE);
        $list_ruangan = ArrayHelper::map($ruanganReq['response']['data'],'ruangan_id','ruangan_nama');

        $statusReq = $this->_restInformasi->get('allow/list-status-pesan');
        $statusReq = json_decode($statusReq->getBody(),TRUE);
        $list_status = ArrayHelper::map($statusReq['response'],'statuspesan_id','statuspesan_nama');

        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        try{
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['advanced-filter']['ruangantujuan_id'] = Yii::$app->docoVars->workspace("ruangan_id");     
            $draw = $request->get('draw', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;
            $response = $this->_restInformasi->get('pemesanan-dokumen-masuk/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesandokrm_id']);
                unset($value['pesandokrm_id']);

                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionDpdListRuangan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = $post['depdrop_parents'][0];
        if ($instalasi_id) {
            $ruanganRequest = $this->_restInformasi->get('allow/list-ruangan?instalasi_id='.$instalasi_id);
            $body = json_decode($ruanganRequest->getBody(),TRUE);
            $responses = $body['response']['data'];
            $responses = ArrayHelper::map($responses, 'ruangan_id', 'ruangan_nama');
        } else {
            $responses = [];
        }

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionGetListRuangan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi_id = $post['instalasi_id'];
        if ($instalasi_id) {
            $ruanganRequest = $this->_restInformasi->get('allow/list-ruangan?instalasi_id='.$instalasi_id);
            $body = json_decode($ruanganRequest->getBody(),TRUE);
            $responses = $body['response']['data'];
            $responses = ArrayHelper::map($responses, 'ruangan_id', 'ruangan_nama');
        } else {
            $responses = [];
        }

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    public function actionGetDataNopesan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){            
            $response = $this->_restInformasi->request('POST', 'pemesanan-dokumen-masuk/data-nopesan', [
                            'form_params'=>['term'=>$_GET['q']['term'], 'ruangantujuan_id' => Yii::$app->docoVars->workspace("ruangan_id")],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                    
            foreach ($body['response'] as $key => $value) {
                //$id = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
                $data[] = [
                    'id' => $value['no_pesandokrm'],
                    'text' => $value['no_pesandokrm'], 
                    'instalasi_pemesan_id' => $value['instalasi_pemesan_id'],
                    'ruanganpemesan_id' => $value['ruanganpemesan_id']
                ];
                
            }          
            $total = count($body['response']);                  
             $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];     
            return DocoHelpers::response($return);
        }
    }

    public function actionDetail($id)
    {
        try{
            $decryptId = DocoHelpers::decrypt($id);
            $res = $this->_restInformasi->get('pemesanan-dokumen-masuk/info-pemesanan',['query'=>['id'=>$decryptId]]);
            $res = json_decode($res->getBody(),TRUE);
            $info_pemesanan = $res['response']['data'];

            return $this->render('detail',get_defined_vars());
        } catch (\Exception $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionKirim($id)
    {
        $request = Yii::$app->request;
        try{
            $decryptId = DocoHelpers::decrypt($id);
            $res = $this->_restInformasi->get('pemesanan-dokumen-masuk/info-pemesanan',[
                'query'=>[
                    'id'=>$decryptId
                ]]);
            $res = json_decode($res->getBody(),TRUE);
            $info_pemesanan = $res['response']['data'];
            
            $disabled = ($info_pemesanan['status_pesan'] == 399) ? true : false;
            $disabled_print = ($info_pemesanan['status_pesan'] == 399) ? false : true;
            if($request->post()){
                $post = $request->post(); 
                $post['pesandokrm_id'] = $decryptId;
                $post['ruanganpengirim_id'] = Yii::$app->docoVars->workspace("ruangan_id"); 
                $post['no_pesandokrm'] = $info_pemesanan['no_pesandokrm'];
                $post['ruanganpemesan_id'] = $info_pemesanan['ruanganpemesan_id'];
                $post['pegawaipengirim_id'] = Yii::$app->user->identity->loginpemakai_id;
                $post['tgl_kirim'] = $post['KirimDokRmForm']['tgl_kirim'];
                $response = $this->_restInformasi->request('POST','pemesanan-dokumen-masuk/create-kirim',['form_params'=>$post]);
                $body = json_decode($response->getBody(), true);
                // return DocoHelpers::response($body);
                return DocoHelpers::response($body,false,true);
            }
            
            $pegawaiReq = $this->_restInformasi->get('allow/list-pegawai', ['query' => ['ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id")]]);
            $pegawaiReq = json_decode($pegawaiReq->getBody(),TRUE);
            $list_pegawai = ArrayHelper::map($pegawaiReq['response'],'pegawai_id','nama_pegawai');
            $model = new KirimDokRmForm;
            if(isset($info_pemesanan['instalasi_pemesan'])){
                $model->instalasi_tujuan = $info_pemesanan['instalasi_pemesan'];
            }
            if(isset($info_pemesanan['ruangan_pemesan']) && isset($info_pemesanan['ruanganpemesan_id'])){
                $model->ruangan_tujuan = $info_pemesanan['ruangan_pemesan'];
                $model->ruanganpemesan_id = $info_pemesanan['ruanganpemesan_id'];
            }
            if(isset($info_pemesanan['tgl_mintakirim'])){
                $model->tgl_kirim = $info_pemesanan['tgl_mintakirim'];
            }

            return $this->render('kirim',get_defined_vars());
        } catch (\Exception $e){
            // return DocoHelpers::responseTemplate(500, $e->getMessage());
            $error = json_decode($e->getResponse()->getBody(),true);
            return DocoHelpers::response($error,422);
        } catch (RequestException $e){
            var_dump(json_decode($e->getResponse()->getBody(),TRUE));exit;
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetDataDetail($id)
    {
        try{
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $decryptId = DocoHelpers::decrypt($id);
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;
            $response = $this->_restInformasi
                            ->get('pemesanan-dokumen-masuk/detail?'.http_build_query($yiiRestfulParams), 
                                [
                                    'query' => ['id'=>$decryptId],
                                    'form_params' => []
                                ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesandokrmdetail_id']);
                unset($value['pesandokrmdetail_id']);

                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionPrintPemesanan($id)
    {
        $request = Yii::$app->request;            
        $decryptId = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/informasi-pemesanan-dokumen-masuk.pdf";
        try {                
            $post = $request->post();               
            $response = $this->_restInformasi
                        ->post('pemesanan-dokumen-masuk/print-pemesanan',
                        [
                            'query' => ['id'=>$decryptId],
                            'form_params' => $post,
                            'save_to' => $path
                        ]);                
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) { 
            // var_dump('expression');exit;
            var_dump(json_decode($e->getResponse()->getBody()));exit();     
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {            
            // var_dump($e);exit;
            // var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintPengiriman($id)
    {
        $request = Yii::$app->request;            
        $decryptId = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/informasi-pengiriman-dokumen-masuk.pdf";
        try {                
            $post = $request->post();               
            $response = $this->_restInformasi
                        ->post('pemesanan-dokumen-masuk/print-pengiriman',
                        [
                            'query' => ['id'=>$decryptId],
                            'form_params' => $post,
                            'save_to' => $path
                        ]);                
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) { 
            // var_dump('expression');exit;
            var_dump(json_decode($e->getResponse()->getBody()));exit();     
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {            
            // var_dump($e);exit;
            // var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}