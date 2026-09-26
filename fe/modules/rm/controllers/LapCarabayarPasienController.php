<?php 

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use kartik\mpdf\Pdf;

class LapCarabayarPasienController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-carabayar-pasien/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Cara Bayar Pasien Rumah Sakit');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get('lap-carabayar-pasien/generate-api');
        $api = json_decode($api->getBody(), True);
        $api = $api['response'];
        $listCarabayar = $api['carabayar'];

        return $this->render('index', get_defined_vars());
    }

    public function actionLap()
    {
    	$title = $this->_title;
        $response = $this->_restRm->get('lap-carabayar-pasien/get-lap');
        $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {
                $data[$key] = $value;
            }

        $result['data'] = $data;
        return json_encode($result, 200);
    }

    public function actionGetDataRajal()
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
        $result['recordsFiltered'] = 0;
        try {
            $response = $this->_restRm->get('lap-carabayar-pasien/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {
                $data[$key] = $value;
            }
            $dataKeys = array_keys($data[1]);
            foreach ($dataKeys as $key => $value) {
                $data[1][$value] = number_format($data[1][$value] * 100, 0) .'%';
            }

            $result['data'] = $data;
            /*$result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];*/
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataIgd()
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
        $result['recordsFiltered'] = 0;
        try {
            $response = $this->_restRm->get('lap-carabayar-pasien/get-data-igd?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {
                $data[$key] = $value;
            }
            $dataKeys = array_keys($data[1]);
            foreach ($dataKeys as $key => $value) {
                $data[1][$value] = number_format($data[1][$value] * 100, 0) .'%';
            }

            $result['data'] = $data;
            /*$result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];*/
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataRanap()
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
        $result['recordsFiltered'] = 0;
        try {
            $response = $this->_restRm->get('lap-carabayar-pasien/get-data-ranap?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {
                $data[$key] = $value;
            }
            $dataKeys = array_keys($data[1]);
            foreach ($dataKeys as $key => $value) {
                $data[1][$value] = number_format($data[1][$value] * 100, 0) .'%';
            }

            $result['data'] = $data;
            /*$result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];*/
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataMcu()
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
        $result['recordsFiltered'] = 0;
        try {
            $response = $this->_restRm->get('lap-carabayar-pasien/get-data-mcu?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {
                $data[$key] = $value;
            }
            $dataKeys = array_keys($data[1]);
            foreach ($dataKeys as $key => $value) {
                $data[1][$value] = number_format($data[1][$value] * 100, 0) .'%';
            }

            $result['data'] = $data;
            /*$result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];*/
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPenunjang()
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
        $result['recordsFiltered'] = 0;
        try {
            $response = $this->_restRm->get('lap-carabayar-pasien/get-data-penunjang?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $key => $value) {
                $data[$key] = $value;
            }
            $dataKeys = array_keys($data[1]);
            foreach ($dataKeys as $key => $value) {
                $data[1][$value] = number_format($data[1][$value] * 100, 0) .'%';
            }

            $result['data'] = $data;
            /*$result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];*/
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
            $path = Yii::getAlias("@download") . "/lap-carabayar-pasien.xlsx";
            $response = $this->_restRm->get('lap-carabayar-pasien/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            // dump($body);die;
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