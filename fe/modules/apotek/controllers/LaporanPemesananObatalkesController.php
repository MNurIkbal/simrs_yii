<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-08 17:36:36
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-12-11 13:21:17
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LaporanPemesananObatalkesController extends DocoController
{
    protected $_title = "Laporan Pemesanan Obat Alkes";
    protected $_module = 'apotek/laporan-pemesanan-obatalkes/';
    protected $_restApotek; 
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek; 
        $this->_restMaster = Yii::$app->docoRest->master;
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
        try {
            $instalasi = \Yii::$app->cache->get('instalasi');   
            if(!$instalasi){            
                 $response = $this->_restMaster->get('instalasi?advanced-filter[is_active]=1');
                $body = json_decode($response->getBody(), TRUE);                
                $instalasi_data = $body['response']['data'];
                \Yii::$app->cache->set('instalasi', $instalasi_data, 60);
                $instalasi = $instalasi_data;
            }
        } catch (Exception $e) {
            $instalasi = [];
        } catch (RequestException $e){
            $instalasi = [];
        }
        
        return $this->render('index', get_defined_vars());
    }
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        try {
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $path = Yii::getAlias("@download") . "/laporan-pemesanan-obatalkes.xlsx";
            $response = $this->_restApotek->get('lap-pemesanan-obatalkes/export-excel',[
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

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        // init ruangan
        if(!isset($yiiRestfulParams['advanced-filter']['ruangan_pemesan_id'])){
            $yiiRestfulParams['advanced-filter']['ruangan_pemesan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }  
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $instalasi = Yii::$app->docoVars->workspace("instalasi_name");
            $ruangan = Yii::$app->docoVars->workspace("ruangan_name");
            $response = $this->_restApotek->get('lap-pemesanan-obatalkes/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesanobatalkes_id']);
                $value['primary'] = $primaryKey;
                unset($value['pesanobatalkes_id']);
                $value['tglpemesanan'] = date("j M Y", strtotime($value['tglpemesanan']));
                $value['detail'] = Html::button('<i class="fa fa-plus-square-o"></i>',[
                    'data-source' => '/apotek/laporan-pemesanan-obatalkes/detail?id=' . $value['primary'],
                    'style' => 'padding-left:8px !important',
                    'class' => 'btn btn-info btn-xs',
                    'onclick' => 'docoHelper.detail(this)'
                ]);
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
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
    
    public function actionGetRuangan($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('ruangan?advanced-filter[instalasi_m.instalasi_nama]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['output'][] = [
                    'id' => $value['ruangan_nama'], 
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) { 
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPesanBarang($ruangan_pemesan_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_pemesan_id=Yii::$app->docoVars->workspace("ruangan_id");
        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restApotek->get('lap-pemesanan-obatalkes/pesan-barang?advanced-filter[ruangan_pemesan_id]='.$q,[
                'query' => [
                    'ruangan_pemesan_id' => $ruangan_pemesan_id, 
                    'term' => $q
                ]
            ]);
            
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value) 
                $result['results'][] = [
                    // 'id' => $ruangan_pemesan_id ? $value['pesanobatalkes_id'] : $value['nopemesanan'],
                    'id' => $value['nopemesanan'], 
                    'text' => $value['nopemesanan']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }

        // Yii::$app->response->format = Response::FORMAT_JSON;
        // if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            
        //     $response = $this->_restApotek->get('lap-pemesanan-obatalkes/pesan-barang',[
        //                     'form_params'=>['term'=>$_GET['q']['term']],
        //                 ]);            

        //     $body = json_decode($response->getBody(), true);   
        //     $data = [];                            
        //     foreach ($body['response'] as $key => $value) {
                
        //         $data[] = ['id'=>$value['pesanobatalkes_id'],'text'=>$value['nopemesanan']];
                
        //     }          
        //     $total = count($body['response']);      
        //     $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
        //     return DocoHelpers::response($return);
        // }
    }

    public function actionExportPrint(){        
        $request = Yii::$app->request;                  
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());   
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");        
        $path = Yii::getAlias("@download") . "/lap-pemesanan-obatalkes.pdf";
        try {
            $response = $this->_restApotek->get('lap-pemesanan-obatalkes/print-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);                                         
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {       
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {            
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDetail($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            // $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restApotek->get('lap-pemesanan-obatalkes/detail',[
                'query' => [
                    'id' => $id
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $detail = isset($response['response']['data']) ? $response['response']['data'] : [];
       } catch (\RequestException $e) {
            $detail = [];
       }
       return $this->renderPartial('detail',get_defined_vars());
    }
}
