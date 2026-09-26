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

class LapRekapitulasiSoapResumeDokterController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-rekapitulasi-soap-resume-dokter/';
    protected $_controllerService = 'lap-rekapitulasi-soap-resume-dokter/';
    const TITLE_SOAP = 'SOAP';
    const TITLE_RESUME = 'Resume';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Rekapitulasi Soap Dan Resume Dokter Belum Terisi');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get($this->_controllerService.'generate-api');
        $api = json_decode($api->getBody(), True);
        $module = $this->_module;
        $title_soap = self::TITLE_SOAP;
        $title_resume = self::TITLE_RESUME;

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
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['primary'] = DocoHelpers::encrypt($value['pegawai_id']);
                $value['jumlah_pasien'] = $value['jumlah_pasien'] ? $value['jumlah_pasien'] : 0;
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

    public function actionGetDataPasien()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);
        $instalasi_id = $request->get('instalasi_id', null);
        $ruangan_id = $request->get('ruangan_id', null);
        $pegawai_id = $request->get('pegawai_id', null);
        $jenis_laporan = $request->get('jenis_laporan', null);
        $tgl_pendaftaran = $request->get('tgl_pendaftaran');
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRm->get($this->_controllerService.'get-data-pasien?instalasi_id='.$instalasi_id.'&ruangan_id='.$ruangan_id.'&pegawai_id='.$pegawai_id.'&jenis_laporan='.$jenis_laporan.'&tgl_pendaftaran='.$tgl_pendaftaran.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('Y-m-d 00:00:01', strtotime($value['tgl_pendaftaran']));
                $value['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran'], false, false);
                $value['jenis_laporan'] = '';
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
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/Laporan Rekapitulasi Kunjungan Perpoli.xlsx";

            $restRm = $this->_restRm->get($this->_controllerService.'export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/Laporan Rekapitulasi Kunjungan Perpoli.pdf";

            $restRm = $this->_restRm->get($this->_controllerService.'export-pdf?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);
            $body = json_decode($restRm->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionDetail()
    {
        try {
            $request = Yii::$app->request;
            $response = [];
            $title = Yii::t('fe', 'Detail');
            $pegawai_id = json_decode(DocoHelpers::decrypt($request->get('id')));
            $instalasi_id = $request->get('instalasi_id');
            $ruangan_id = $request->get('ruangan_id');
            $jenis_laporan = $request->get('jenis_laporan');
            if($jenis_laporan == DocoConstants::L_T_RESUME_DOKTER) {
                $tipe = self::TITLE_RESUME;
            } else {
                $tipe = self::TITLE_SOAP;
            }
            $tgl_pendaftaran = $request->get('tgl_pendaftaran');

            $restRm = $this->_restRm->get($this->_controllerService.'get-nama?pegawai='. $pegawai_id.'&instalasi_id='.$instalasi_id.'&ruangan_id='.$ruangan_id);
            $response = json_decode($restRm->getBody(), true)['response'];
            $instalasi_nama = $response['instalasi_nama'];
            $ruangan_nama = $response['ruangan_nama'];
            $dokter = $response['dokter'];
            
            return $this->renderAjax('detail', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }
}