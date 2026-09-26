<?php 

/**
 * @author Budi
 */

namespace Doco\rm\controllers;

use Yii;
use yii\web\Response;
use app\components\DHtml;
use app\components\DocoHelpers;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LapRekapitulasiPelayananController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-rekapitulasi-pelayanan/';
    protected $_controllerService = 'lap-rekapitulasi-pelayanan/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Rekapitulasi Per Unit Pelayanan');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title  = $this->_title;
        $api    = $this->_restRm->get($this->_controllerService.'generate-api');
        $api    = json_decode($api->getBody(), True);
        $module = $this->_module;

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request                    = Yii::$app->request;

        try {
            $yiiRestfulParams       = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw                   = $request->get('draw', 1);
            $no                     = $request->get('start', 1);
            $data                   = [];
            $result                 = [];
            $result['data']         = $data;
            $result['draw']         = $draw;
            $result['recordsTotal'] = 0;

            $response = $this->_restRm->get($this->_controllerService.'index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);

            $responseSummary = $this->_restRm->get($this->_controllerService.'summary?'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);

            $body = json_decode($response->getBody(), true);
            $bodySummary = json_decode($responseSummary->getBody(), true);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['primary'] = DocoHelpers::encrypt($value['pegawai_id']);
                $value['jumlah_pasien'] = $value['jumlah_pasien'] ? $value['jumlah_pasien'] : 0;
                $data[$key] = $value;
            }

            $result['data']            = $data;
            $result['recordsTotal']    = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['summary']         = $bodySummary['response']['count'];

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
        $status_bayar = $request->get('status_bayar', null);
        $status_periksa = $request->get('status_periksa', null);
        $tgl_pendaftaran = explode("/", $request->get('tgl_pendaftaran'));  
        $startdate=''; $enddate ='';     
        if($tgl_pendaftaran) {
            $startdate = $tgl_pendaftaran[0];
            $enddate = $tgl_pendaftaran[1];
        }

        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRm->get($this->_controllerService.'get-data-pasien?instalasi_id='.$instalasi_id.'&ruangan_id='.$ruangan_id.'&pegawai_id='.$pegawai_id.'&status_bayar='.$status_bayar.'&status_periksa='.$status_periksa.'&startdate='.$startdate.'&enddate='.$enddate.'&'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), true);
            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('Y-m-d 00:00:01', strtotime($value['tgl_pendaftaran']));
                $value['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran'], false, false);
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
            $request                    = Yii::$app->request;
            $yiiRestfulParams           = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path                       = Yii::getAlias("@download") . "/" . DHtml::getTitleMenu() . ".xlsx";

            $this->_restRm->get($this->_controllerService.'export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];    
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
            $title = Yii::t('fe', 'Detail');
            $pegawai_id = json_decode(DocoHelpers::decrypt($request->get('id')));
            $instalasi_id = $request->get('instalasi_id');
            $ruangan_id = $request->get('ruangan_id');
            $status_bayar = $request->get('status_bayar');
            $status_periksa = $request->get('status_periksa');
            $tgl_pendaftaran = $request->get('tgl_pendaftaran');
            
            $restRm = $this->_restRm->get($this->_controllerService.'get-nama?instalasi_id='.$instalasi_id.'&ruangan_id='.$ruangan_id);
            $instalasi_nama = json_decode($restRm->getBody(), true)['response']['instalasi_nama'];
            $ruangan_nama = json_decode($restRm->getBody(), true)['response']['ruangan_nama'];

            return $this->renderAjax('detail', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }
}