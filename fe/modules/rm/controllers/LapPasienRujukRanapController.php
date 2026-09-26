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

class LapPasienRujukRanapController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-pasien-rujuk-ranap/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Pasien Rujuk Rawat Inap Rumah Sakit');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;

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
        $result['recordsTotal'] = 1;
        $result['recordsFiltered'] = 1;
        try {
            $response = $this->_restRm->get('lap-pasien-rujuk-ranap/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // dump($body);die;
            foreach ($body['response']['data'] as $key => $value) {
                $data[0]['primary'] = http_build_query($yiiRestfulParams);
                $data[0][$key] = $value;
            }

            $result['data'] = $data;
            //$result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            //$result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
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
            $path = Yii::getAlias("@download") . "/lap-pasien-rujuk-ranap.xlsx";
            $response = $this->_restRm->get('lap-pasien-rujuk-ranap/export-excel',[
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
        $path = Yii::getAlias("@download") . "/lap-pasien-rujuk-ranap.pdf";
        try {
            $response = $this->_restRm->get('lap-pasien-rujuk-ranap/export-pdf?',[
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

    public function actionDetailRajal()
    {
        try {
            $request = Yii::$app->request;
            $response = [];
            $title = Yii::t('fe', 'Rawat Jalan');
            $tgl_pulang = date('Y-m-d 00:00:00').' - '.date('Y-m-d 23:59:59');
            if (isset($_GET['advanced-filter']['tglpasienpulang'])) {
                $tgl_pulang = $_GET['advanced-filter']['tglpasienpulang'];
            }
            $instalasi_id = 1;
            
            return $this->renderAjax('detail', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionDetailIgd()
    {
        try {
            $request = Yii::$app->request;
            $response = [];
            $title = Yii::t('fe', 'Rawat Darurat');
            $tgl_pulang = date('Y-m-d 00:00:00').' - '.date('Y-m-d 23:59:59');
            if (isset($_GET['advanced-filter']['tglpasienpulang'])) {
                $tgl_pulang = $_GET['advanced-filter']['tglpasienpulang'];
            }
            $instalasi_id = 2;
            
            return $this->renderAjax('detail', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionGetDataDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);
        $instalasi_id = $request->get('instalasi_id');
        $tgl_pulang = $request->get('tgl_pulang');
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRm->get('lap-pasien-rujuk-ranap/get-data-detail?instalasi_id='.$instalasi_id.'&tgl_pulang='.$tgl_pulang.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $tgl_kunjungan = explode(" ", date('Y-m-d H:i:s', strtotime($value['tgl_kunjungan'])));
                $value['tgl_kunjungan'] = DocoHelpers::convDateTime($value['tgl_kunjungan'], false, false) .'<br/>'. $tgl_kunjungan[1];
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

}