<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-02 14:40:59
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-03-20 15:36:01
 * @Last Modified by:   Sunarko
 * @Last Modified time: 2018-08-15 10:28:26
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

class LaporanStokObatalkesController extends DocoController
{
    protected $_title = "Laporan stok dan ketersediaan obat alkes";
    protected $_module = '/apotek/laporan-stok-obatalkes';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }
    public function actionIndex()
    {
        $model = new InformasiForm;
        $title = Yii::t('fe', $this->_title);
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $instalasiName = Yii::$app->docoVars->workspace("instalasi_name");
        $instalasi = [];
        $data_periode = [];
        $obatalkes_namalain = [];
        $response = $this->_restApotek->get('allow/get-instalasi/', 
            [
                'form_params' => [],
                'query' => []
            ]);
        $response = json_decode($response->getBody(),true);
        // print_r($response); die;
        $data_periode = isset($response['response']['periode']) ? $response['response']['periode'] : [];
        $instalasi = isset($response['response']['instalasi']) ? $response['response']['instalasi'] : [];
        // $response = $this->_restApotek->get('instalasi?advanced-filter[is_active]=1');
        // $body = json_decode($response->getBody(), TRUE);
        // $instalasi = $body['response']['data']; 
        // $ruangan = [];
        // $obatalkes_namalain =[]; 

        return $this->render('obat-alkes', get_defined_vars());
    }

    public function actionGetData(){
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
            $response = $this->_restApotek->get('lap-stok-obatalkes/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['periodestok_id']);
                unset($value['periodestok_id']);

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

    public function actionGetObatAlkesNama()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                if(isset($_GET['z'])){
                    $ruangan_id = $_GET['z'];
                }
                $response = $this->_restApotek->request('POST', 'lap-stok-obatalkes/data-obat-alkes-nama',[
                    'form_params'=>['term'=>$_GET['q']['term'], 'idR'=>$ruangan_id], 
                ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id' => $value['obatalkes_namalain'], 'text' => $value['obatalkes_namalain']];
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

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = Yii::$app->docoVars->workspace("ruangan_id");

        try {
            $response = $this->_restApotek->get('allow/get-ruangan',[
                'query' => [
                    'instalasi_id' => $parent_label
                ]
            ]);
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
       
        try {
            $path = Yii::getAlias("@download") . "/laporan-stok-obatalkes.xlsx";
            $response = $this->_restApotek->get('lap-stok-obatalkes/export-excel?ruangan_id='.Yii::$app->docoVars->workspace("ruangan_id").'&'.http_build_query($yiiRestfulParams),[
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
        
        $path = Yii::getAlias("@download") . "/lap-stok-obatalkes.pdf";
        try {
            $response = $this->_restApotek->get('lap-stok-obatalkes/export-pdf?ruangan_id='.Yii::$app->docoVars->workspace("ruangan_id").'&ruangan_nama='.Yii::$app->docoVars->workspace("ruangan_name").'&'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}