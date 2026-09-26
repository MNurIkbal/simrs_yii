<?php
// Author : Naufal Ziyad L

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class KunjunganRawatDaruratController extends DocoController
{
    protected $_title = "Laporan Kunjungan Rawat Darurat";
    protected $_module = 'pendaftaran/kunjungan-rawat-darurat/';
    protected $_restPendaftaran;
    protected $_restMaster;
    const SINGKATAN_RD = 'RD';

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
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
        $status = $this->_status;   
        return $this->render('index', get_defined_vars());
    }

    public function actionLaporan()
    {
        $ruangan = $carabayar = [];
        $request = $this->_restPendaftaran->get('allow/get-api-laporan', [
            'query'=>['jenis'=>'igd']
        ]);
        $response = json_decode($request->getBody(), True);
        $response = $response['response'] ? : [];

        if ($response) {
            $ruangan = ArrayHelper::map($response['master']['ruangan'], 'ruangan_id', 'ruangan_nama');
            $carabayar = ArrayHelper::map($response['master']['carabayar'], 'carabayar_id', 'carabayar_nama');
            $pegawai = ArrayHelper::map($response['master']['pegawai'], 'pegawai_id', 'nama_pegawai');
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d', strtotime($tgl_awal));
            $tgl_akhir_format = date('Y-m-d 23:59:59', strtotime($tgl_akhir));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }else{
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = date('Y-m-d');
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = date('Y-m-d 23:59:59');
        }
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-darurat/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasien_id']);
                unset($value['pasien_id']);
                $value['is_active'] = DocoHelpers::isActive($value['is_active']);
                $value['info_kunjungan'] = $value['no_pendaftaran'] . '<br>' . $value['no_rekam_medik'] . ' - ' . (($value['namadepan']) ? $value['namadepan'] . " " : '') . $value['nama_pasien'];
                $value['toggle'] = "";
                $value['rowNum'] = $no;
                $value['carabayar_penjamin'] = $value['carabayar_nama'].' / '.$value['penjamin_nama'];
                $value['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran']);
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
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d', strtotime($tgl_awal));
            $tgl_akhir_format = date('Y-m-d 23:59:59', strtotime($tgl_akhir));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }else{
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = date('Y-m-d');
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = date('Y-m-d 23:59:59');
        }
        $toggle = $request->get('toggle', null);
        if(!empty($toggle)){
            $yiiRestfulParams['advanced-filter']['toggle'] = $toggle;
        }
        try {
            $path = Yii::getAlias("@download") . "/Laporan Kunjungan Rawat Darurat.xlsx";
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-darurat/export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (RequestException $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['nama_rs'] = Yii::$app->docoVars->identity('nama_rumahsakit');
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d', strtotime($tgl_awal));
            $tgl_akhir_format = date('Y-m-d 23:59:59', strtotime($tgl_akhir));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }else{
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = date('Y-m-d');
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = date('Y-m-d 23:59:59');
        }
        $toggle = $request->get('toggle', null);
        if(!empty($toggle)){
            $yiiRestfulParams['advanced-filter']['toggle'] = $toggle;
        }
        $path = Yii::getAlias("@download") . "/Laporan Kunjungan Rawat Darurat.pdf";
        try {
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-darurat/export-pdf?' . http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_rest->put('pemesanan-barang/update?id='.$id, [
                'form_params' => ["is_active" => $status]
            ]);

            $data = [
                'title' => \Yii::t('fe', 'Proses berhasil')." !",
                'text' => \Yii::t('fe', "Status berhasil diubah.")
            ];
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", 
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message,  
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionShowPopup()
    {
        $title = Yii::t('fe', 'Cetak Laporan Kunjungan Rawat Darurat');
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d', strtotime($tgl_awal));
            $tgl_akhir_format = date('Y-m-d 23:59:59', strtotime($tgl_akhir));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }else{
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = date('Y-m-d');
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = date('Y-m-d 23:59:59');
        }
        Yii::Error($yiiRestfulParams);
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restPendaftaran, [
            'url' => "lap-kunjungan-rawat-darurat/export-excel-bgproses",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Kunjungan Rawat Darurat.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->guzzleExec($this->_restPendaftaran,[
            'url' => 'lap-kunjungan-rawat-darurat/download-file',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
