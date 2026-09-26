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

class LapRekapPasienBatalRegistrasiController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-rekap-pasien-batal-registrasi/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Rekapitulasi Pasien Batal Registrasi Rumah Sakit');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get('lap-rekap-pasien-batal-registrasi/generate-api');
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
            $response = $this->_restRm->get('lap-rekap-pasien-batal-registrasi/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // dump($body);die;
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['tgl_registrasi'] = DocoHelpers::display_label($value['tgl_registrasi'], true, 
                    date('d M Y', strtotime($value['tgl_registrasi'])));
                $value['rowNum'] = $no;
                $tgl_batal = explode(" ", date('d-m-Y H:i:s', strtotime($value['tgl_batal'])));
                $value['petugas'] = $value['nama_petugas'].'<br/>'.$tgl_batal[0].'<br/>'.$tgl_batal[1];
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
            $response = $this->_restRm->get('lap-rekap-pasien-batal-registrasi/get-ruangan-by?id='.$parent_label);
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
            $path = Yii::getAlias("@download") . "/lap-rekap-pasien-batal-registrasi.xlsx";
            $response = $this->_restRm->get('lap-rekap-pasien-batal-registrasi/export-excel',[
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
        $path = Yii::getAlias("@download") . "/lap-rekap-pasien-batal-registrasi.pdf";
        try {
            $response = $this->_restRm->get('lap-rekap-pasien-batal-registrasi/export-pdf?',[
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

}