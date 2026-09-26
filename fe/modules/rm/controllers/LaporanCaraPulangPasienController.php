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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use kartik\mpdf\Pdf;

class LaporanCaraPulangPasienController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/laporan-cara-pulang-pasien/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Cara Pulang Pasien Rumah Sakit');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get('lap-cara-pulang-pasien/generate-api');
        $api = json_decode($api->getBody(), True);

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
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-cara-pulang-pasien/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // dump($body);die;
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['tgl_pulang'] = DocoHelpers::display_label($value['tgl_pulang'], true, 
                    date('d M Y H:i:s', strtotime($value['tgl_pulang'])));
                $value['tgl_pendaftaran'] = DocoHelpers::display_label($value['tgl_pendaftaran'], true, 
                    date('d M Y H:i:s', strtotime($value['tgl_pendaftaran'])));
                $value['rowNum'] = $no;
                $value['primary'] = $value['no_rekam_medik'];

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
            $response = $this->_restRm->get('lap-cara-pulang-pasien/get-ruangan-by?id='.$parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) 
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
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/lap-cara-pulang-pasien.xlsx";
            $response = $this->_restRm->get('lap-cara-pulang-pasien/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/lap-cara-pulang-pasien.pdf";
        try {
            $response = $this->_restRm->get('lap-cara-pulang-pasien/export-pdf?',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            // dump($body);die;
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionShowPopup()
    {
        $request = Yii::$app->request;
        $title = 'Cetak Laporan Cara Pulang Pasien Rumah Sakit';
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $randString = DocoHelpers::generateRandomString();
        $url = $this->_module;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalPdf', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $session = Yii::$app->session->getFlash($randString);
        return $this->guzzleExec($this->_restRm, [
            'url' => "lap-cara-pulang-pasien/sync-pdf",
            'payload' => [
                'query' => [
                    'params' => $session,
                    'randString' => $randString,
                ]
            ],
        ]);
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $path = Yii::getAlias("@download").'/'.$fileName;
        $response = $this->_restRm->get('lap-cara-pulang-pasien/download-pdf',
        [
            'query' => [
                'fileName' => $fileName,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Cetak Laporan Cara Pulang Pasien Rumah Sakit Excel';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        $url = $this->_module;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "lap-cara-pulang-pasien/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Cara Pulang Pasien.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get('lap-cara-pulang-pasien/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}