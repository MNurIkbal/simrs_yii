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

class PengirimanDokumenKeluarController extends DocoController
{
	protected $_title = "Informasi :: Pengiriman Dokumen Rekam Medik";
    protected $_module = 'informasi/pengiriman-dokumen-keluar/';
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
    	// $list_instalasi = ArrayHelper::map($instalasiReq['response']['data'],'instalasi_id','instalasi_nama');

    	$statusReq = $this->_restInformasi->get('allow/list-status-kirim');
    	$statusReq = json_decode($statusReq->getBody(),TRUE);
    	$list_status = ArrayHelper::map($statusReq['response'],'statuskirim_id','statuskirim_nama');

        $ruanganReq = $this->_restInformasi->get('allow/list-ruangan');
        $ruanganReq = json_decode($ruanganReq->getBody(),TRUE);
        // $list_ruangan = ArrayHelper::map($ruanganReq['response']['data'],'ruangan_id','ruangan_nama');

    	return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
    	try{
    		Yii::$app->response->format = Response::FORMAT_JSON;
	        $request = Yii::$app->request;
	        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            // $yiiRestfulParams['advanced-filter']['ruanganpemesan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
	        $draw = $request->get('draw', 1);
	        $data = [];

	        $result = [];
	        $result['data'] = $data;
	        $result['draw'] = $draw;
	        $result['recordsTotal'] = 0;
	        $result['recordsTotal'] = 0;
	        $response = $this->_restInformasi->get('pengiriman-dokumen-keluar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // return DocoHelpers::response($body);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kirimdokrm_id']);
                unset($value['kirimdokrm_id']);

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
        $obj = $request->post('obj');
        $value = $request->post('value');
        if($obj == 'ruangan_id') {
            $ruanganRequest = $this->_restInformasi->get('allow/new-list-instalasi?ruangan_id='.$value);
        }
        else {
            $ruanganRequest = $this->_restInformasi->get('allow/list-ruangan?instalasi_id='.$value);
        }
        
        $body = json_decode($ruanganRequest->getBody(),TRUE);
        // return DocoHelpers::response($body);
        $responses = $body['response']['data'];

        $tagOptions = ['prompt' => "Pilih .."];
        if($obj == 'ruangan_id') {
            return Html::renderSelectOptions([], ArrayHelper::map($responses, 'instalasi_id', 'instalasi_nama'), $tagOptions);
        }
        else {
            return Html::renderSelectOptions([], ArrayHelper::map($responses, 'ruangan_id', 'ruangan_nama'), $tagOptions);
        }
    }

    public function actionGetDataNokirim()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){            
            $response = $this->_restInformasi->request('POST', 'pengiriman-dokumen-keluar/data-nokirim',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                    
            foreach ($body['response'] as $key => $value) {
                //$id = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
                $data[] = ['id'=>$value['no_kirimdokrm'],'text'=>$value['no_kirimdokrm']];
                
            }          
            $total = count($body['response']);                  
             $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];     
            return DocoHelpers::response($return);
        }
    }

    public function actionSearchNomor()
    {
        $response = [];
        $request = Yii::$app->request;
        try {
            $res = $this->_restInformasi->get('pengiriman-dokumen-keluar/list-nomor',[
                'query' => $request->get()
            ]);
            $bod = json_decode($res->getBody(), TRUE);
            $response = $bod['response'];
        } catch (RequestException $e) {

        } catch (\Exception $e) {

        }
        return DocoHelpers::response($response);
    }

    public function actionDetail($id)
    {
    	try{
            $decryptId = DocoHelpers::decrypt($id);
            $res = $this->_restInformasi->get('pengiriman-dokumen-keluar/info-pengiriman',['query'=>['id'=>$decryptId]]);
            $res = json_decode($res->getBody(),TRUE);
            $info_pengiriman = $res['response']['data'];
    		return $this->render('detail',get_defined_vars());
    	} catch (\Exception $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionPrintDetail($id)
    {
        $request = Yii::$app->request;            
        $decryptId = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/informasi-pengiriman-dokumen-keluar.pdf";
        try {                
            $post = $request->post();               
            $response = $this->_restInformasi
                        ->post('pengiriman-dokumen-keluar/print-detail',
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
	        				->get('pengiriman-dokumen-keluar/detail?'.http_build_query($yiiRestfulParams), 
	        					[
	        						'query' => ['id'=>$decryptId],
	        						'form_params' => []
	        					]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kirimdokrmdetail_id']);
                unset($value['kirimdokrmdetail_id']);

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

    public function actionDelete($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restInformasi->request('DELETE', 'pengiriman-dokumen-keluar/delete',[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}