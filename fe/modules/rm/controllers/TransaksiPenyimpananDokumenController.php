<?php

namespace Doco\rm\controllers;

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-10 15:04:13
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-24 11:25:46
 * desc: transaksi penyimpanan dokumen rekam medik controller
 */

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rm\models\TransaksiPenyimpananDokumenForm;
use GuzzleHttp\Exception\RequestException;

class TransaksiPenyimpananDokumenController extends DocoController
{
    protected $_title = "Rm :: Penyimpanan Dokumen Rekam Medis";
    protected $_module = 'rm/transaksi-penyimpanan-dokumen/';
    protected $_restRm;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex(){
        try{
            $request = Yii::$app->request;
            $_title = "Penyimpanan Dokumen Rekam Medis";
            $model = new TransaksiPenyimpananDokumenForm;

            if($request->post()){
                $model->load($request->post());
                $response = $this->_restRm->request('POST', 'transaksi-penyimpanan-dokumen/create',[
                                'form_params'=>$model->attributes,
                            ]); 
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response, false, true);
            }else{
                $model->status_indexing=0;
                $model->status_assembling=0;
                $response = $this->_restRm->get('allow/get-rak-data/', 
                    [
                        'form_params' => [],
                        'query' => []
                    ]);
                $response = json_decode($response->getBody(),true);
                // print_r($response); die;
                $data_rak = isset($response['response']['rak_data']) ? $response['response']['rak_data'] : [];
                return $this->render('form', get_defined_vars());
            }


        }catch(\Exception $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }catch(RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionListRm(){
        try {
            $request = Yii::$app->request;
            $title = "List No. Rekam Medik";

            return $this->renderPartial('form-listrm',get_defined_vars());

        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch(RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }       
    }
    public function actionDataRm(){
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restRm->request('POST','transaksi-penyimpanan-dokumen/data-rm',['form_params'=>$post]);
            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->post('start',1);

            $result = [];
            $result['data'] = $row;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasien_id']);
                $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                    'class' => 'btn btn-success btn-xs select-pasien',
                    'data-pasien' => $value['pasien_id'],
                    'data-tooltip' => 'tooltip',
                    'title' => Yii::t('fe', 'Pilih'),
                ]);   
                $value['rowNum'] = $no;
                $row[$key] = $value;       
            }
            $result['data'] = $row;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
            
            return DocoHelpers::response($result);
        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch(RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    public function actionListPengiriman(){
        try {
            $request = Yii::$app->request;
            $title = "List No. Pengiriman";

            return $this->renderPartial('form-listpengiriman',get_defined_vars());

        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch(RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }       
    }
    public function actionDataPengiriman(){
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $response = $this->_restRm->request('POST','transaksi-penyimpanan-dokumen/data-pengiriman',['form_params'=>$post]);
            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->post('start',1);
            $result = [];
            $result['data'] = $row;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['nomor_pengiriman']);
                $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                    'class' => 'btn btn-success btn-xs select-nopengiriman',
                    'data-pengiriman' => $value['nomor_pengiriman'],
                    'data-tooltip' => 'tooltip',
                    'title' => Yii::t('fe', 'Pilih'),
                ]);   
                $value['rowNum'] = $no;
                $row[$key] = $value;       
            }
            $result['data'] = $row;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
            
            return DocoHelpers::response($result);
            
        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch(RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    public function actionGetListData(){
        try{
            $request = Yii::$app->request;
            $response = $this->_restRm->request('GET','transaksi-penyimpanan-dokumen/get-list-data');
            $row = [];
            $body = json_decode($response->getBody(), true);    

            $return = ['data_rm'=>$body['response']['data_rm'],
                       'data_pengiriman'=>$body['response']['data_pengiriman'],
                       'data_rak'=>$body['response']['data_rak'],
                       'data_subrak'=>$body['response']['data_subrak'],
                       'data_warna'=>$body['response']['data_warna'],
                      ];                            
            return DocoHelpers::response($return);
        }catch (\Exception $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }catch(RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    public function actionGetDokumen(){
        try {
            $id = $_GET['id'];
            $request = Yii::$app->request;
            $response = $this->_restRm->request('GET','transaksi-penyimpanan-dokumen/get-dokumen-data',
                                                    ['query' => ['id' => $id ]]
                                                );
            $body = json_decode($response->getBody(), true);
            $return  = ['data_dokumen'=>$body['response']['data_rak']];
            return DocoHelpers::response($return);

        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);   
        }catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    //get autocomplite data no_rm
    public function actionGetNomorRm()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $response = $this->_restRm->request('POST', 'transaksi-penyimpanan-dokumen/data-nomor-rm',[
                    'form_params'=>['term'=>$_GET['q']['term']], 
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['no_rekam_medik'], 'text' => $value['no_rekam_medik']];
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

    public function actionGetAllRm($q = null, $page = null,$is_valueWithText= 0, $id = null) 
    {
        try{
            $limit = 10;
            $offset = ($page-1)*10;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id'=>'','text'=>'']];
            $response = $this->_restRm->get('allow/get-all-rm',[
                'query' => ['keyword'=>$q,'page'=>$page,'offset'=>$offset,'limit'=>$limit]
            ]);
            $response = json_decode($response->getBody(), TRUE);
            
            $results = [];
            if ($response['metadata']['status'] == 200) {
                $list = $response['response'];
                foreach ($list as $key => $each) {
                    $results[] = [
                        'id'=>$each['no_rekam_medik'], 
                        'text'=>$each['no_rekam_medik']." / ".$each['nama_pasien'], 
                    ];
                }
                $out['results'] = $results;
                $out['pagination'] = [ 'more' => !empty($list)?true:false ];
            }

            return $out;
        } catch (RequestException $e) {
            return ['results' => ['id'=>'','text'=>'']];
        } catch (\Exception $e) {
            return ['results' => ['id'=>'','text'=>'']];
        }
    }
    
    public function actionGetAllPengiriman($q = null, $page = null,$is_valueWithText= 0, $id = null) 
    {
        try{
            $limit = 10;
            $offset = ($page-1)*10;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id'=>'','text'=>'']];
            $response = $this->_restRm->get('allow/get-all-pengiriman',[
                'query' => ['keyword'=>$q,'page'=>$page,'offset'=>$offset,'limit'=>$limit]
            ]);
            $response = json_decode($response->getBody(), TRUE);
            
            $results = [];
            if ($response['metadata']['status'] == 200) {
                $list = $response['response'];
                foreach ($list as $key => $each) {
                    $results[] = [
                        'id'=>$each['kirimdokrm_id'],
                        'text'=>$each['no_kirimdokrm'],
                    ];
                }
                $out['results'] = $results;
                $out['pagination'] = [ 'more' => !empty($list)?true:false ];
            }

            return $out;
        } catch (RequestException $e) {
            return ['results' => ['id'=>'','text'=>'']];
        } catch (\Exception $e) {
            return ['results' => ['id'=>'','text'=>'']];
        }
    }

    public function actionGetDataSubrak($lokasirak_id='')
    {   
        try{
            $data = [];
            if(isset($lokasirak_id)){
                $response = $this->_restRm->get('allow/get-subrak?lokasirak_id='.$lokasirak_id);
                $body = json_decode($response->getBody(), True);
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['subrak_id'], 'text' => $value['subrak_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

}