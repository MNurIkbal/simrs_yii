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

class LaporanPtmController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/laporan-ptm/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan PTM');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get('lap-ptm/generate-api');
        $api = json_decode($api->getBody(), True);

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $tgl_awal = null;
        if (isset($yiiRestfulParams['advanced-filter']['tgl_registrasi'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_registrasi']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_awal = date('Y-m-d', strtotime($tgl_awal));
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get('lap-ptm/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // dump($body);die;
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                
                $value['pemeriksaan_ekg'] = 'Tidak';
                $dataDetail = $this->_restRm->get('lap-ptm/get-detail',[
                    'query' => [
                        'pendaftaran_id' => $value['pendaftaran_id'],
                        'pasien_id' => $value['pasien_id'],
                        'instalasi_id' => $value['instalasi_id'],
                        'tgl_registrasi' => $value['tgl_registrasi'],
                        'tgl_awal' => $tgl_awal,
                    ]
                ]);
                $dataDetail = json_decode($dataDetail->getBody(), True);
                $dataDetail = $dataDetail['response']['data'];
                foreach ($dataDetail['ekg'] as $ekg){
                    if($ekg['pemeriksaan_ekg'] == 'YA') {
                        $value['pemeriksaan_ekg'] = 'Ya';
                    }
                }
                $value['jumlah_kunjungan'] = $dataDetail['jumlah_kunjungan'];

                $nik_pasien = json_decode($value['no_identitas_pasien'], true);
                $identitas_pasien = json_decode($value['additional_pasien'], true);
                $no_ktp = '';
                if(is_array($identitas_pasien) && ! empty($identitas_pasien)) {
                    foreach ($identitas_pasien as $identitas) {
                        if($identitas['jenisidentitas'] == '94') {
                            $no_ktp = $identitas['no_identitas_pasien'];
                        }
                    }
                } else {
                    if (!is_array($nik_pasien) && ! empty($nik_pasien)) {
                        $no_ktp = $nik_pasien;
                    }
                }

                $value['tgl_registrasi'] = date('d M Y H:i:s', strtotime($value['tgl_registrasi']));
                $value['tgl_pulang'] = DocoHelpers::display_label($value['tgl_pulang'], true, 
                    date('d M Y H:i:s', strtotime($value['tgl_pulang'])));
                $value['tanggal_lahir'] = date('d M Y', strtotime($value['tanggal_lahir']));

                $value['no_ktp'] = $no_ktp;
                $diag_utama_kode = isset($value['diag_utama_kode'])?$value['diag_utama_kode']:'';
                $diag_utama = isset($value['diag_utama'])?$value['diag_utama']:'';
                $value['diagnosa'] =  $diag_utama_kode. ' - ' .$diag_utama;
                $nama_keluarga = !empty($value['nama_ayah'])?$value['nama_ayah']:$value['nama_ibu'];
                $value['nama_keluarga'] = isset($nama_keluarga)?$nama_keluarga:'';
                //$value['jumlah_kunjungan'] = array_count_values(array_column($body['response']['data'], 'pasien_id'))[$value['pasien_id']];
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

    public function actionGetIcd()
    {
        $request = Yii::$app->request;
        $type_icd = $request->get('type_icd');
        $term = $request->get('term');
        try {
            $response = $this->_restRm->get('allow/get-icd',[
                'query' => [
                    'type' => $type_icd,
                    'term' => $term,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = [];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['diagnosa_id'],
                    'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_namalainnya'],
                ];
            }
        } catch (RequestException $e) {
            $data = [];
        }

        return DocoHelpers::response([
            'result' => $data
        ]);
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/lap-ptm.xlsx";
            $response = $this->_restRm->get('lap-ptm/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/lap-ptm.pdf";
        try {
            $response = $this->_restRm->get('lap-ptm/export-pdf?',[
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

    public function actionShowPopupExcel()
    {
        $title = 'Cetak Laporan PTM';
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
        return $this->guzzleExec($this->_restRm, [
            'url' => "lap-ptm/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'lap-ptm.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRm->get('lap-ptm/download-file-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupPdf()
    {
        $request = Yii::$app->request;
        $title = 'Cetak Laporan PTM';
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalPdf', get_defined_vars());
    }

    public function actionProcessSyncPdf($randString)
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "lap-ptm/sync-export-pdf",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $fileNameDownload = 'lap-ptm.pdf';
        $path = Yii::getAlias("@download").'/'.$fileNameDownload;
        $response = $this->_restRm->get('lap-ptm/download-pdf',
        [
            'query' => [
                'fileName' => $fileName,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }
}