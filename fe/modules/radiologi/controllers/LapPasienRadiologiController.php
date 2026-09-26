<?php

namespace Doco\radiologi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use kartik\mpdf\Pdf;

class LapPasienRadiologiController extends DocoController
{
    protected $_title = "Laporan Pasien Radiologi";
    protected $_module = '/radiologi/lap-pasien-radiologi';
    protected $_restRad;
    protected $_asal_rujukan = [
        'ORDER' => 'Order',
        'RUJUKAN RS' => 'Rujukan RS',
        'APS'=>'APS'
    ];

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi;
    }

    public function actionIndex()
    {
        $title = $this->_title;

        $cara_bayar = $penjamin = $arrAsalRujukan =  [];

        $status = DocoConstants::$status_radiologi;
        // $asalRujukan = DocoConstants::$asal_rujukan;
        $asalRujukan = $this->_asal_rujukan;
        
        try {
            $response = $this->_restRad->get('lap-pasien-radiologi/get-options');
            $body = json_decode($response->getBody(), true);
            $cara_bayar = $body['response']['cara_bayar'];
            $penjamin = $body['response']['penjamin'];
            
            $dataAsalRujukan = !empty($body['response']['asalRujukan']) ? $body['response']['asalRujukan'] : [];
            $asalrujukan = ArrayHelper::map($dataAsalRujukan, 'asalrujukan_nama', 'asalrujukan_nama');
            $arrAsalRujukan = array_merge($asalRujukan,$asalrujukan);
            
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = $arrAsalRujukan = [];
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restRad->request('get', 'inf-pasien-lab/index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []]);
            // return http_build_query($yiiRestfulParams);
            $row = [];
            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tglmasukpenunjang'] = date('d M Y', strtotime($value['tglmasukpenunjang']));
                $value['status_periksa'] = empty($value['status_periksa']) ? '-' : DocoConstants::$status_lab[$value['status_periksa']];
                $value['tglmasukpenunjang'] = DocoHelpers::convertTo224($value['tglmasukpenunjang'],true);
                $value['tanggal_lahir'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])),false,false);
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataDatatable()
    {
        try {
            $request = Yii::$app->request;
            $type = $request->get('type', null);
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restRad->request('get', 'lap-pasien-radiologi/get-data?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function getAksiListPasien($data)
    {
        $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);

        $return_data = '';
        if(!empty($data['no_antrian'])){
            $return_data .= Html::button(
                '<i class="fa fa-volume-up"></i> '.$data['no_antrian'] ,
                [
                    'class' => 'btn btn-turquoise btn-md antrian',
                    'data-tooltip' => "tooltip",
                    'data-original-title' => Yii::t('fe', 'Panggil antrian'),
                    'data-id' => $data['pendaftaran_id'],
                    'data-antrian' => $data['no_antrian'],
                ]
            );
        }
        $return_data .= '&nbsp;&nbsp;';

        return $return_data;
    }

    public function actionGetAsalRujukan2($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if($get['asal_1'] == "order"){
                $response = $this->_restRad->get('inf-pasien-lab/list-instalasi');
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value){
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if($get['asal_1'] == "rujukanRS"){
                $response = $this->_restRad->get('inf-pasien-lab/list-asal-rujukan');
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            // return $request->get();
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    public function actionGetAsalRujukan3($q = "",$q2="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if ($get['asal_1'] == "order") {
                $response = $this->_restRad->get('inf-pasien-lab/list-ruangan?instalasi_id='.$get['asal_2']);
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if ($get['asal_1'] == "rujukanRS") {
                $response = $this->_restRad->get('inf-pasien-lab/list-asal-rujukan-dari?asalrujukan_id=' . $get['asal_2']);
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            // return $request->get();
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/Laporan-Pasien-Radiologi.pdf";

        try {
            $response = $this->_restRad->get('lap-pasien-radiologi/cetak-pdf', [
                'save_to' => $path,
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        try {
            $path = Yii::getAlias("@download") . "/lap-pasien-radiologi.xlsx";
            $response = $this->_restRad->get('lap-pasien-radiologi/export-excel?' .http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
        
            return DocoHelpers::downloadFile($path,true);
            // $response = $this->_restRad->get('lap-pasien-radiologi/export-excel?'.http_build_query($yiiRestfulParams));
            // $body = json_decode($response->getBody(), True);
            // return $this->downloadFile($body["response"]);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
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

    public function actionShowPopup()
    {
        $request = Yii::$app->request;
        $title = 'Cetak Laporan Pasien Radiologi';
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $session = Yii::$app->session->getFlash($randString);
        return $this->guzzleExec($this->_restRad, [
            'url' => "lap-pasien-radiologi/sync-pdf",
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
        $fileNameDownload = 'Laporan Pasien Radiologi.pdf';
        $path = Yii::getAlias("@download").'/'.$fileNameDownload;
        $response = $this->_restRad->get('lap-pasien-radiologi/download-pdf',
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
        $title = 'Cetak Laporan Pasien Radiologi Excel';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRad, [
            'url' => "lap-pasien-radiologi/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Pasien Radiologi.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRad->get('lap-pasien-radiologi/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

}