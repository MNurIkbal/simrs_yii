<?php 

namespace Doco\bedah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\assets\CalenderAssets;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Doco\bedah\models\JadwalOperasiForm;

class LaporanPasienController extends DocoController
{
    protected $_title = "Laporan Pasien Bedah Sentral";
    protected $_module = '/bedah/laporan-pasien';
    protected $_restBedah;

    public function init()
    {
        parent::init();
        $this->_restBedah = Yii::$app->docoRest->bedahsentral;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $list_ruangan = $list_penjamin = [];
        try {
            $response = $this->_restBedah->get('laporan-pasien/get-attributes');
            $response = json_decode($response->getBody(),true);

            $getHeader = $this->_restBedah->get('laporan-pasien/get-header-laporan-operasi');
            $getHeader = json_decode($getHeader->getBody(),true);
            $header = $getHeader['response'];
            $list_ruangan = $response['response']['ruangan'];
            $list_penjamin = $response['response']['penjamin'];
        } catch (RequestException $e) {

        } 
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restBedah->get('laporan-pasien', [
                'form_params' => [],
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $header = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;
                $value['no'] = $no;
                $value['tanggal_operasi'] = !empty($value['tanggal_operasi']) && !is_null($value['tanggal_operasi']) ? date('d-M-Y H:i:s', strtotime($value['tanggal_operasi'])) : '-';
                $value['tanggal_pendaftaran'] = !empty($value['tanggal_pendaftaran']) && !is_null($value['tanggal_pendaftaran']) ? date('d-M-Y', strtotime($value['tanggal_pendaftaran'])) : '-';
                $value['subtotal'] = !empty($value['subtotal']) ? Docohelpers::rupiahDisplay($value['subtotal']) : 0;

                foreach($value as $k => $r){
                    if (gettype($value[$k]) == 'boolean'){
                        $value[$k] = ($value[$k] == true ? 'Y' : '');
                    }
                }
                
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/laporan-pasien.xlsx";
            $response = $this->_restBedah->get('laporan-pasien/export-excel',[
                'query' => $filters,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
            $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/laporan-pasien-bedah.pdf";
            $response = $this->_restBedah->get('laporan-pasien/export-pdf', [
                'save_to' => $path,
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    private function generateHeader($header){
        $column = [];
        $column[0] = [
            'title' => "No",
            'data' => 'no',
            'searchable' => false,
        ];
        foreach($header as $value){
            $column_explode = (explode('_', $value) ? explode('_', $value) : $value);
            $visible = (!empty($column_explode[1]) && $column_explode[1] != 'id') ? true : false;
            $title = ucwords(str_replace('_',  ' ', $value));

            $column[] = [
                'title' => $title,
                'data' => $value,
                'searchable' => false,
                'visible' => $visible,
            ];
        }
        return $column;
    }

    public function actionShowPopup()
    {
        $title = 'Laporan Pasien Bedah Sentral';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restBedah, [
            'url' => "laporan-pasien/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Laporan Pasien Bedah Sentral.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restBedah->get('laporan-pasien/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

}