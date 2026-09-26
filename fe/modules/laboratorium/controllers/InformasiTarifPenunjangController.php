<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-12 14:56:40
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-07-23 16:38:11
 */

namespace Doco\laboratorium\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\VarDumper;

class InformasiTarifPenunjangController extends DocoController
{
    protected $_title = "Informasi Tarif Penunjang";
    protected $_module = 'laboratorium/informasi-tarif-penunjang/';
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_instalasi_id;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $this->_instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
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
        $title = $this->_title;
        $instalasi_id = ($this->_instalasi_id == '-') ? '' : $this->_instalasi_id;
        $response = $this->_restMaster->get('informasi-tarif-penunjang/get-request?instalasi_id='.$instalasi_id, ['form_params' => []]);
        $response = json_decode($response->getBody(), True);
        // return DocoHelpers::response($response);
        $response = $response['response'];

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
            $response = $this->_restMaster->get('informasi-tarif-penunjang/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['tariftindakan_id']);
                unset($value['tariftindakan_id']);
                $value['primary'] = $primaryKey;
                $value['harga_tariftindakan'] = DocoHelpers::formatNumber($value['harga_tariftindakan']);
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

    public function actionDetail($id)
    {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Komponen Tarif');
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restMaster->get('informasi-tarif-penunjang/detail?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        // VarDumper::dump($body['response']);die;
        $attributes = $body['response'];
        $jenisPemeriksaan = $body['response']['jenispemeriksaan_nama'];
        return $this->renderPartial('detail', get_defined_vars());
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/informasi-tarif-penunjang.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restMaster->get('informasi-tarif-penunjang/cetak-pdf', [
                'query' => $yiiRestfulParams,
                'form_params' => [],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $result = [];
        $url = "";
        try {
            $response = $this->_restMaster->get('informasi-tarif-penunjang/export-excel?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            return $this->downloadFile($body["response"]);
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

    /* BEGIN ACTION EXPORT EXCEL BGPROCESS */
    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Tarif Penunjang Excel';
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
        return $this->guzzleExec(Yii::$app->docoRest->master, [
            'url' => "informasi-tarif-penunjang/sync-export-excel",
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'flash' => Yii::$app->session->getFlash($randString),
                    'instalasi_id' => $this->_instalasi_id
                ],
            ]
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan-tarif-penunjang.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = Yii::$app->docoRest->master->get('informasi-tarif-penunjang/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
    /* END ACTION EXPORT EXCEL BGPROCESS */

    /* BEGIN ACTION EXPORT PDF BGPROCESS */
    public function actionShowPopupPdf() {
        $title = 'Download Laporan Tarif Penunjang PDF';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $payload = DocoDatatableHelper::convertToRestfulParams($request->get());
        $payload['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $payload);
        return $this->renderAjax('_modalPdf', get_defined_vars());
    }

    public function actionProcessSyncPdf() {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec(Yii::$app->docoRest->master, [
            'url' => 'informasi-tarif-penunjang/process-sync-pdf-bgprocess',
            'payload' => [
                'query' => [
                    'flash' => Yii::$app->session->getFlash($randString),
                    'instalasi_id' => $this->_instalasi_id
                ],
            ],
        ]);
    }

    public function actionDownloadFilePdf() {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Informasi Tarif Penunjang.pdf';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;

        $response = Yii::$app->docoRest->master->get('informasi-tarif-penunjang/download-pdf-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path, true);
    }
    /* END ACTION EXPORT PDF BGPROCESS */
}
