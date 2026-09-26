<?php

namespace Doco\ambulan\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;   
use yii\web\UploadedFile;
use GuzzleHttp\Exception\RequestException;

// use app\modules\ambulan\models\BasePrice;

class InformasiAmbulanController extends DocoController
{
    protected $_title = "Informasi Ambulan";
    protected $_module = '/ambulan/informasi-ambulan/';
    protected $_restAmbulan;
    
    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Informasi Ambulan');
        $this->_restAmbulan = Yii::$app->docoRest->ambulan;
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
        $response = $this->getRequest();
        $jenis_ambulan = isset($response['jenis_ambulan']) ? $response['jenis_ambulan'] : [];
        $status_ambulan = isset($response['status_ambulan']) ? ArrayHelper::map($response['status_ambulan'], 'lookup_id', 'lookup_name') : [];
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
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
        try {
            $response = $this->_restAmbulan->get('informasi-ambulan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $responData = $body['response']['data'];
            foreach ($responData as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['ambulan_id']);
                $value['primary'] = $primaryKey;
                unset($value['ambulan_id']);

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

    public function actionGetDataAmbulanDetail($ambulan_id)
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
        try {
            $ambulan_id = DocoHelpers::decrypt($ambulan_id);
            $response = $this->_restAmbulan->get('informasi-ambulan/get-data-ambulan-detail?ambulan_id='.$ambulan_id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $responData = $body['response']['data'];
            foreach ($responData as $key => $value) {
                $total = $value['biaya_tambahan'] + $value['nominal_tagihan']; 
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['ambulan_id']);
                $value['supir'] = !empty($value['supir']) ? $value['supir'] : '-';
                $value['tgl_pemakaiandari'] = !empty($value['tgl_pemakaiandari']) ? date('d-M-Y', strtotime($value['tgl_pemakaiandari'])) : '-' ; 
                $value['tgl_realisasikembali'] = !empty($value['tgl_realisasikembali']) ? date('d-M-Y', strtotime($value['tgl_realisasikembali'])) : '-' ;
                $value['jarak_pemakaian'] = !empty($value['jarak_pemakaian']) ? DocoHelpers::formatNumber($value['jarak_pemakaian']) : 0;
                $value['nominal_tagihan'] = !empty($value['nominal_tagihan']) ? DocoHelpers::formatNumber($value['nominal_tagihan']) : 0;
                $value['biaya_tambahan'] = !empty($value['biaya_tambahan']) ? DocoHelpers::formatNumber($value['biaya_tambahan']) : 0;
                $value['total'] = DocoHelpers::formatNumber($total);
                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
                unset($value['ambulan_id']);

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

    private function getRequest()
    {
        try {
            $request = $this->_restAmbulan->get('informasi-ambulan/generate-api');
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];

            return $attributes;
        } catch (RequestException $e) {
            $result['jenis_ambulan'] = [];
            $result['status_ambulan'] = [];
            return $result;
        } catch (\Exception $e) {
            $result['jenis_ambulan'] = [];
            $result['status_ambulan'] = [];
            return $result;
        }
    }
    
    public function actionDetail($id)
    {
        $title = 'Detail Informasi Ambulan';
        try {
            $ambulan_id = DocoHelpers::decrypt($id);
            $getData = $this->getAmbulan($ambulan_id);
            $encryptAmbulan_id = $id;

            return $this->renderAjax('_detail', get_defined_vars());
        }catch (\Exception $e) {
            return false;
        }
    }

    private function getAmbulan($ambulan_id)
    {
        try {
            $request = $this->_restAmbulan->get('informasi-ambulan/detail', [
                'query' => ['ambulan_id' => $ambulan_id ]
            ]);
            $response = json_decode($request->getBody(), true);
            $result = $response['response'];
            
            return $result;
        }
         catch (\Exception $e) {
            return [];
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-ambulan.xlsx";
            $response = $this->_restAmbulan->get('informasi-ambulan/export-excel?' .http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportExcelDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $id = $request->get('ambulan_id');
            $ambulan_id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/informasi-ambulan-detail-{$id}.xlsx";
            $response = $this->_restAmbulan->get('informasi-ambulan/export-excel-detail?ambulan_id='.$ambulan_id.'&' .http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            
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
        
        $path = Yii::getAlias("@download") . "/informasi-ambulan.pdf";
        try {
            $response = $this->_restAmbulan->get('informasi-ambulan/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportPdfDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        try {
            $id = $request->get('ambulan_id');
            $ambulan_id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/informasi-ambulan-detail-{$id}.pdf";
            $response = $this->_restAmbulan->get('informasi-ambulan/export-pdf-detail?ambulan_id='.$ambulan_id.'&' .http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);

        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }


}