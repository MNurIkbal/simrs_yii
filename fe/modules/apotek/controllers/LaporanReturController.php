<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-08 18:11:38
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-09 13:45:19
 */


namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\InformasiForm;


class LaporanReturController extends DocoController
{
    
    protected $_title = "Laporan retur resep";
    protected $_module = '/apotek/laporan-retur';
    protected $_restApotek;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    
    public function actionIndex()
    {
        $title = $this->_title;
        $model = new InformasiForm;

        $cara_bayar = \Yii::$app->cache->get('carabayar');        
        if(!$cara_bayar){            
            $response = $this->_restMaster->get('cara-bayar/index?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), True);     
            $carabayar_data = ArrayHelper::map($body['response']['data'],'carabayar_nama','carabayar_nama');
            \Yii::$app->cache->set('carabayar', $carabayar_data, 60);
            $cara_bayar = $carabayar_data;
        }       
        
        return $this->render('index', get_defined_vars());
    }
    public function actionGetData(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;                
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());           
        // $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");           
        $draw = $request->get('draw', 1);        
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;        
        try{                      
            $response = $this->_restApotek->get('lap-retur/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);            
            $body = json_decode($response->getBody(), true);            
            $no = $request->get('start',1);                        
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['returresep_id']);
                unset($value['returresep_id']);
                $value['primary'] = $primaryKey;                
                $value['total_retur'] = 0;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        }catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataObat($id){
        $id = DocoHelpers::decrypt($id);
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

        try{                      
            $response = $this->_restApotek->get('lap-retur/get-detail', ['query' => ['id'=>$id]]);
            $body = json_decode($response->getBody(), true);            
            $no = $request->get('start',1);            
            // return $body['response'];
            $total = 0;
            foreach ($body['response'] as $key => $value) {
                $no++;                
                $value['rowNum'] = $no;                
                $total += $value['total'];
                $value['hargasatuan'] = "Rp. ".number_format($value['hargasatuan'], 0, ',','.');
                $value['total'] = "Rp. ".number_format($value['total'], 0, ',','.');
                $data[$key] = $value;
            }
            $result['totalobat'] = "Rp. ".number_format($total, 0,',','.');
            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = count($body['response']);
            $result['recordsFiltered'] = count($body['response']);
            return $result;
        }catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetNoRetur(){
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restApotek->request('POST', 'lap-retur/get-no-retur',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                
            foreach ($body['response'] as $key => $value) {
                
                $data[] = ['id'=>$value['no_returresep'],'text'=>$value['no_returresep']];
                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }
    public function actionGetNoResep(){
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restApotek->request('POST', 'lap-retur/get-no-resep',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                
            foreach ($body['response'] as $key => $value) {
                
                $data[] = ['id'=>$value['noresep'],'text'=>$value['noresep']];
                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }
    public function actionGetPenjamin()
    {       
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('penjamin?advanced-filter[carabayar_m.carabayar_nama]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['output'][] = [
                    'id' => $value['penjamin_nama'], 
                    'name' => $value['penjamin_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/laporan-retur.xlsx";
            $response = $this->_restApotek->get('lap-retur/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
       } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
       } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
       }  
    }  
    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/laporan-retur-resep.pdf";
        try {
            $response = $this->_restApotek->get('lap-retur/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);                       
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }  

}
