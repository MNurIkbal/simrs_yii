<?php
// Author : Ardi Pratama
// edited : rizal

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class KunjunganRawatJalanController extends DocoController
{
    protected $_title = "Laporan Kunjungan Rawat Jalan";
    protected $_module = 'pendaftaran/kunjungan-rawat-jalan/';
    protected $_restMaster;
    protected $_restPendaftaran;
    const SINGKATAN_RJ = 'RJ';
    protected $_status_skrining =  [
        "Sudah" => "Sudah",
        "Belum" => "Belum",
    ];

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

        $ruangan = $carabayar = $status_periksa = $status_pulang = [];
        $request = $this->_restPendaftaran->get('allow/get-api-laporan', [
            'query'=>[
                'jenis'=>'rajal'
            ]
        ]);
        $response = json_decode($request->getBody(), True);
        $response = $response['response'] ? : [];
        
        if ($response) {
            $ruangan = ArrayHelper::map($response['master']['ruangan'], 'ruangan_id', 'ruangan_nama');
            $carabayar = ArrayHelper::map($response['master']['carabayar'], 'carabayar_id', 'carabayar_nama');
            $pegawai = ArrayHelper::map($response['master']['pegawai'], 'pegawai_id', 'nama_pegawai');
            $status_periksa = ArrayHelper::map($response['master']['status_periksa'], 'lookup_id', 'lookup_value');
            $status_pulang = ArrayHelper::map($response['master']['carakeluar'], 'carakeluar_id', 'carakeluar_nama');
        }

        asort($status_periksa);
        asort($status_pulang);
        $status_skrining = $this->_status_skrining;

        return $this->render('laporan', get_defined_vars());
    }

    public function listPencarian() 
    {
        return [
            1 => Yii::t('fe', 'Umur'),
            2 => Yii::t('fe', 'Jenis kelamin'),
            3 => Yii::t('fe', 'Status kunjungan'),
            4 => Yii::t('fe', 'Agama'),
            5 => Yii::t('fe', 'Pekerjaan'),
            6 => Yii::t('fe', 'Status pekerjaan'),
            7 => Yii::t('fe', 'Status perkawinan'),
            8 => Yii::t('fe', 'Alamat'),
            9 => Yii::t('fe', 'Kabupaten/kota'),
            10 => Yii::t('fe', 'Cara masuk'),
            11 => Yii::t('fe', 'Rujukan'),
            12 => Yii::t('fe', 'Jenis kasus penyakit'),
            13 => Yii::t('fe', 'Keterangan pulang'),
            14 => Yii::t('fe', 'Dokter pemeriksa'),
            15 => Yii::t('fe', 'Kelas pelayanan'),
        ];
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
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = $body['response']['data'];
            foreach ($data as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                unset($value['pendaftaran_id']);

                $value['rowNum'] = $no;
                if($value['carakeluar_nama'] != '' && $value['kondisikeluar_nama']){
                    $statusPulang = $value['carakeluar_nama'] . ' / ' . $value['kondisikeluar_nama'];
                }elseif(($value['carakeluar_nama'] != '') || ($value['kondisikeluar_nama'] != '')){
                    if($value['carakeluar_nama'] != ''){
                        $statusPulang = $value['carakeluar_nama'];
                    }else{
                        $statusPulang = $value['kondisikeluar_nama'];
                    }
                }
                else{
                    $statusPulang = '';
                } 
                $value['status_pulang'] = $statusPulang;
                $value['info_kunjungan'] = $value['no_pendaftaran'] . '<br>' . $value['no_rekam_medik'] . ' - ' . (($value['namadepan']) ? $value['namadepan'] . " " : '') . $value['nama_pasien'];
                $value['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran']);
                $value['carabayar_penjamin'] = $value['carabayar_nama'] . ' / ' .$value['penjamin_nama'];
                $value['primaryKey'] = $primaryKey;
                $value['toggle'] = '';
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

        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
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
        $path = Yii::getAlias("@download") . "/lap-kunjungan-rawat-jalan.pdf";
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        try {
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
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
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
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
            $path = Yii::getAlias("@download") . "/laporan-kunjungan-rawat-jalan.xlsx";
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-jalan/export-excel',[
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

    public function actionShowPopupExcel()
    {
        $title = Yii::t('fe', 'Cetak Laporan Kunjungan Rawat Jalan');
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        Yii::Error($yiiRestfulParams);
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restPendaftaran, [
            'url' => "lap-kunjungan-rawat-jalan/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Kunjungan Rawat Jalan.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->guzzleExec($this->_restPendaftaran,[
            'url' => 'lap-kunjungan-rawat-jalan/download-file',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path,true);
    }


}
