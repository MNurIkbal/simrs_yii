<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-28 13:31:39
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-13 11:24:57
 */


namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\InformasiForm;
use Doco\apotek\models\PenerimaanObatForm;

class LaporanMutasiObatalkesController extends DocoController
{
	protected $_title = "Laporan Mutasi Obat Alkes";
    protected $_module = '/apotek/informasi-retur';
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
        $instalasi = \Yii::$app->cache->get('instalasi');            
        if(!$instalasi){            
             $response = $this->_restMaster->get('instalasi?advanced-filter[is_active]=1');
            $body = json_decode($response->getBody(), TRUE);                
            $instalasi_data = $body['response']['data'];
            \Yii::$app->cache->set('instalasi', $instalasi_data, 60);
            $instalasi = $instalasi_data;
        }

        return $this->render('obat-alkes', get_defined_vars());
    }
     //fungsi buat ambil data informasi mutasi obat alkes
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;                 
        $get = $request->get();        
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        // if(!isset($yiiRestfulParams['advanced-filter']['instalasi_nama'])){
        //     // $yiiRestfulParams['advanced-filter']['instalasi_nama'] = Yii::$app->docoVars->workspace("instalasi_id");
        //     $yiiRestfulParams['advanced-filter']['instalasi_nama'] = Yii::$app->docoVars->workspace("ruangan_id");
        //     $yiiRestfulParams['advanced-filter']['ruangan_asal'] = Yii::$app->docoVars->workspace("ruangan_name");
        // }

        $draw = $request->get('draw',1);
        $data = [];

        try {                                           
            $response = $this->_restApotek->get('lap-mutasi-obatalkes/index?'.http_build_query($yiiRestfulParams),['form_params'=>[]]);
            $body = json_decode($response->getBody(), true);    
                   
            $no = $request->get('start',1);                  
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
                unset($value['mutasiobatruangan_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tglmutasioa'] = date('Y-m-d', strtotime($value['tglmutasioa']));
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
    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('allow/ruangan?advanced-filter[instalasi_id]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['output'][] = [
                    'id' => $value['ruangan_id'], 
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
    public function actionGetDataNomutasi2(){
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){            
            $response = $this->_restApotek->request('POST', 'lap-mutasi-obatalkes/data-nomutasi2',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                    
            foreach ($body['response'] as $key => $value) {
                //$id = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
                $data[] = ['id'=>$value['nomutasioa'],'text'=>$value['nomutasioa']];
                
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
        // if(!isset($yiiRestfulParams['advanced-filter']['instalasi_nama'])){
        //     $yiiRestfulParams['advanced-filter']['instalasi_nama'] = Yii::$app->docoVars->workspace("instalasi_id");
        //     $yiiRestfulParams['advanced-filter']['ruangan_nama'] = Yii::$app->docoVars->workspace("ruangan_name");
        // }  
        try {
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restApotek->get('lap-mutasi-obatalkes/export-excel?'.http_build_query($yiiRestfulParams));    
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];               
            return $this->downloadFile($url);
           } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                return $result;
           } catch (RequestException $e){
                $result['error'] = $e->getMessage();
                return $result;
           }  
    }
    private function downloadFile($filename)
    {
        $file = basename($filename);
        $fp = fopen($file, 'w');
        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'".xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;
    }

    public function actionExportPrint(){        
        $request = Yii::$app->request;                  
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());        
        $path = Yii::getAlias("@download") . "/lap-mutasi-obatalkes.pdf";
        try {
            $response = $this->_restApotek->get('lap-mutasi-obatalkes/print-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);                                         
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {       
        var_dump($e->getMessage());exit();     
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {            
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}