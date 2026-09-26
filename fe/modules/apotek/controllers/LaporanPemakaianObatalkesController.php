<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-27 13:37:26
 * @Edited by: Johndoe
 * @Date:   2018-03-26 13:43:26
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-26 13:50:21
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

class LaporanPemakaianObatalkesController extends DocoController
{
    protected $_title = "Pemakaian Obat Alkes";
    protected $_module = 'apotek/pemakaian-obat-alkes/';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
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
        return $this->render('laporan', get_defined_vars());    
    }

    public function actionGetDataPemakaian()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $filters['ruangan_id'] = $ruangan_id;
        $draw = $request->get('draw',1);
        $data = [];
        try {
            
            $response = $this->_restApotek->get('lap-pemakaian-obatalkes/', [
                'form_params' => [],
                'query' => $filters
            ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);

            $data = [];                                       
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['tglpemakaianobat'] = date("j M Y", strtotime($value['tglpemakaianobat']));
                $value['primary'] = DocoHelpers::encrypt($value['pemakaianobat_id']);
                $value['rowNum'] = $no;
                $value['detail'] = Html::button('<i class="fa fa-plus-square-o"></i>',[
                    'data-source' => '/apotek/laporan-pemakaian-obatalkes/detail?id=' . $value['primary'],
                    'style' => 'padding-left:8px !important',
                    'class' => 'btn btn-info btn-xs',
                    'onclick' => 'docoHelper.detail(this)'
                ]);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionDetail($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restApotek->get('lap-pemakaian-obatalkes/detail',[
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $yiiRestfulParams['ruangan_id'] = $ruangan_id;
        $url = 'lap-pemakaian-obatalkes/export-excel?advanced-filter[ruangan_id]='.$ruangan_id.'&'.http_build_query($yiiRestfulParams);
        try {
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $path = Yii::getAlias("@download") . "/apotek-Laporan-Pemakaian-Obat-Alkes.xlsx";
            $response = $this->_restApotek->get($url,[
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

    public function actionGetDataObat()
    {
        if(isset($_GET['q']) && !empty($_GET['q'])){                        
            $response = $this->_restApotek->request('POST', 'allow/get-data-obatalkes',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);               
            $data = [];                            
            foreach ($body['response'] as $key => $value) {
                
                $data[] = ['id'=>$value['obatalkes_namalain'],'text'=>$value['obatalkes_namalain']];
                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }else{
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw',1);
            $data = [];                    
            try {                
                $response = $this->_restApotek->get('allow/get-data-obatalkes?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
                $body = json_decode($response->getBody(), True);
                $no = $request->get('start',1);
                $data = [];                                       
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;                                                    
                    $value['rowNum'] = $no;
                    $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                        'class' => 'btn btn-success btn-xs data-check',
                        'data-sel_wrap' =>'.filter-form',
                        'data-key' => $value['obatalkes_namalain'],
                        'data-label' => $value['obatalkes_namalain'],
                        'title' => \Yii::t('fe', 'Klik'),
                    ]);
                    $data[$key] = $value;
                }
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return DocoHelpers::response($result);
            } catch (RequestException $e) {
                $result['error'] = $e->getMessage();
                return $result;
            } catch (\Exception $e){
                $result['error'] = $e->getMessage();
                return $result;
            }
        }
    }

    public function actionDataObat()
    {
        return $this->renderAjax('obat-alkes', get_defined_vars());
    }

    public function actionGetObat($q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restApotek->get('obat-alkes?advanced-filter[obatalkes_namalain]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $value['obatalkes_id'], 
                    'text' => $value['obatalkes_namalain']
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

    private function getKonversi($satuan_besar, $satuan_kecil, $qty_satuanpakai)
    {
        $response = $this->_restApotek->get('lap-pemakaian-obatalkes/get-konversi?satuanbesar_id=' . $satuan_besar . '&satuankecil_id=' . $satuan_kecil . '&qty_satuanpakai=' . $qty_satuanpakai, ['form_params' => []]);

        $body = json_decode($response->getBody(), True);

        return $body['response'];
    }

    public function actionExportPrint(){        
        $request = Yii::$app->request;                  
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());        
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $yiiRestfulParams['ruangan_id'] = $ruangan_id;
        $path = Yii::getAlias("@download") . "/lap-pemakaian-obatalkes.pdf";
        try {
            $response = $this->_restApotek->get('lap-pemakaian-obatalkes/print-pdf?'.http_build_query($yiiRestfulParams),[
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