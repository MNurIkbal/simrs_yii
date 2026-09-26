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
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class LapPersalinanController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-persalinan/';
    protected $_controllerService = 'lap-persalinan/';
    protected $_controllerAllow = 'allow/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Laporan Persalinan');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get($this->_controllerService.'generate-api');
        $api = json_decode($api->getBody(), True);
        $response = $api['response'];
        $jenisPersalinan = isset($response['jenisPersalinan']) ? $response['jenisPersalinan'] : [];
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get($this->_controllerService.'index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_lahir_bayi'] = date('d/m/Y H:i', strtotime($value['tgl_lahir_bayi']));
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['rowJumlah'] = $this->generateJumlah($yiiRestfulParams);
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
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") .'/'.$this->_title.'.xlsx';

            $response = $this->_restRm->get($this->_controllerService.'export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    private function generateJumlah($yiiRestfulParams = null) 
    {
        $response = $this->_restRm->get($this->_controllerService.'get-total-jenis-persalinan?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        return $body['response'];
    }
    public function actionExportExcelSerconn()
    {
        $request = Yii::$app->request;
        $title = 'Excel Laporan Persalinan';
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => $this->_controllerService."sync-export-excel",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('filename', null);

        $fileDownloads = 'Laporan Persalinan.xlsx';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get($this->_controllerService. 'download-file',
        [
            'query' => [
                'no_request' => $no_request,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionDownloadExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $uid = $request->get('uid');
        $uid = DocoHelpers::decrypt($uid);
        try {
            $response = $this->_restRm->get($this->_controllerService.'get-excel-url?uid='.$uid);
            $response = json_decode($response->getBody(), True);
            $results = $response['response']['result'];

            return $results;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}