<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-23 10:31:53
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-03-23 14:51:32
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class LaporanMutasiBarangController extends DocoController
{
    protected $_title = "Laporan mutasi barang";
    protected $_module = 'gudang/laporan-mutasi-barang/'; //buat fe
    protected $_backUrl = 'lap-mutasi-barang/'; //buat be
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
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
        $title = Yii::t('fe', $this->_title);
        $module = $this->_module;

        $instalasi = \Yii::$app->cache->get('mutasi_instalasi');        
        if(!$instalasi){            
            $response = $this->_restGudang->get('allow/get-instalasi?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), True);     
            $instalasi_data = ArrayHelper::map($body['response']['data'],'instalasi_id','instalasi_nama');
            \Yii::$app->cache->set('mutasi_instalasi', $instalasi_data, 60);
            $instalasi = $instalasi_data;
        }

        return $this->render('index', get_defined_vars());
    }
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        // init ruangan
        if(!isset($yiiRestfulParams['advanced-filter']['ruanganasal_id'])){
            $yiiRestfulParams['advanced-filter']['ruanganasal_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }  
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {            
            $response = $this->_restGudang->get($this->_backUrl.'index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            // return $body['response'];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;                
                $value['tgl_mutasibarang'] = date("j M Y", strtotime($value['tgl_mutasibarang']));                
                $value['rowNum'] = $no; //$value['primary'] = $primaryKey;
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
    public function actionGetNomutasi()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restGudang->request('POST', $this->_backUrl.'get-nomutasi',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                            
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['nomutasi_barang'],'text'=>$value['nomutasi_barang']];
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {           
            if(!isset($yiiRestfulParams['advanced-filter']['ruangan_id'])){
                $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
            }
            $path = Yii::getAlias("@download") . "/laporan-mutasi-barang.xlsx";
            $response = $this->_restGudang->get($this->_backUrl.'export-excel?'.http_build_query($yiiRestfulParams),[
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
}