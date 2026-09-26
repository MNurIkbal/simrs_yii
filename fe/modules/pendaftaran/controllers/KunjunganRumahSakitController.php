<?php
// Author : Ardi Pratama
// edited : rizal

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

class KunjunganRumahSakitController extends DocoController
{
    protected $_title = "Laporan Kunjungan Rumah Sakit";
    protected $_module = 'pendaftaran/kunjungan-rumah-sakit/';
    protected $_restMaster;
    protected $_restPendaftaran;

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

    public function actionLaporan()
    {
        // Init
        // $status = $this->_status; $options = $this->_options;
        $title = $this->_title;

        $instalasiRequest = $this->_restMaster->get('instalasi/list-instalasi');
        $body = json_decode($instalasiRequest->getBody(),TRUE);
        $instalasi = $body['response'];
        
        return $this->render('laporan', get_defined_vars());
    }

    public function actionListRuangan($instalasi_id) {
        $ruanganRequest = $this->_restMaster->get('ruangan/list-ruangan?instalasi_id='.$instalasi_id);
        $body = json_decode($ruanganRequest->getBody(),TRUE);
        echo json_encode($body['response']);
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
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }

        if (isset($yiiRestfulParams['advanced-filter']['ruangan_id'])) {
            $ruangan_id = $yiiRestfulParams['advanced-filter']['ruangan_id'];
            $yiiRestfulParams['advanced-filter']['list_ruangan_id'] = $ruangan_id;
            unset($yiiRestfulParams['advanced-filter']['ruangan_id']);
        }
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('lap-kunjungan-rumah-sakit/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $titipan = '-';
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $titipan = '-';
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                unset($value['pendaftaran_id']);
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('d-F-Y H:i:s',strtotime($value['tgl_pendaftaran']));
                $value['nama_pasien'] = (($value['namadepan']) ? $value['namadepan'] . " " : '') . $value['nama_pasien'];
                
                 if (!empty($value['is_pasientitipan_pk'])) {
                    if($value['is_pasientitipan_pk'] == true && $value['is_stoppasientitipan'] == false){
                        $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$value['kelas_ditagihkan_nama'];
                    } else {
                        $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
                    }
                } else if (empty($value['is_pasientitipan_pk'])) {
                    if($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false){
                        $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$value['kelas_ditagihkan_nama'];
                    } else {
                        $value['kelaspelayanan_nama'] = $value['kelaspelayanan_nama'].' / '.$titipan;
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

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        $yiiRestfulParams['advanced-filter']['ruangan_ids'] = $ruangan_id;
        // echo "<pre>";var_dump(http_build_query($yiiRestfulParams));die();
        $path = Yii::getAlias("@download") . "/lap-kunjungan-rumah-sakit.pdf";
        try {
            $response = $this->_restPendaftaran->get('lap-kunjungan-rumah-sakit/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            return $e->getMessage();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        $yiiRestfulParams['advanced-filter']['ruangan_ids'] = $ruangan_id;
        try {
            $path = Yii::getAlias("@download") . "/laporan-kunjungan-rumah-sakit.xlsx";
            $response = $this->_restPendaftaran->get('lap-kunjungan-rumah-sakit/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionShowPopup()
    {
        $title = Yii::t('fe', 'Cetak Laporan Kunjungan Rumah Sakit');
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }
        $yiiRestfulParams['advanced-filter']['ruangan_ids'] = $ruangan_id;
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
            'url' => "lap-kunjungan-rumah-sakit/export-excel-bgproses",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Kunjungan Rumah Sakit.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->guzzleExec($this->_restPendaftaran,[
            'url' => 'lap-kunjungan-rumah-sakit/download-file',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

}
