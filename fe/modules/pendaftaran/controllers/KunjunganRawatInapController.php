<?php
// Author : Naufal Ziyad L

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class KunjunganRawatInapController extends DocoController
{
    protected $_title = "Laporan Kunjungan Rawat Inap";
    protected $_module = 'pendaftaran/kunjungan-rawat-inap/';
    protected $_restPendaftaran;
    protected $_restMaster;
    const SINGKATAN_RI = 'RI';

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
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);

        return $this->render('index', get_defined_vars());
    }

    public function actionLaporan()
    {
        $title = $this->_title;

        $ruangan = $carabayar = [];
        $request = $this->_restPendaftaran->get('allow/get-api-laporan', [
            'query'=>[
                'jenis'=>'ranap'
            ]
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

    public function actionGetDataSerconn()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payload = $request->get();
        
        $randString = DocoHelpers::generateRandomString();
        
        $yiiRestfulParams['randString'] = $randString;

        $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-inap/generate-data-serconn',[
            'query' => [
                'advance_filter' => $payload['advance_filter'],
                'unique_str' => $randString
            ]
        ]);
        $res = json_decode($response->getBody(), true);
        
        $data = ArrayHelper::getValue($res, 'response');
        return $data;
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
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-inap/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            $titipan = '-';

            foreach($body['response']['data'] as $key => $value) {
                    $no++;
                    $value['no'] = $no;
                    $no_pendaftaran = !empty(ArrayHelper::getValue($value,'no_pendaftaran')) ? ArrayHelper::getValue($value,'no_pendaftaran') : '-';
                    $no_rekam_medik = !empty(ArrayHelper::getValue($value,'no_rekam_medik')) ? ArrayHelper::getValue($value,'no_rekam_medik') : '-';
                    $nama_pasien = !empty(ArrayHelper::getValue($value,'nama_pasien')) ? ArrayHelper::getValue($value,'nama_pasien') : '-';
                    $info_kunjungan = $no_pendaftaran .' - '.$no_rekam_medik.' - '.$nama_pasien;
                    $value['info_kunjungan'] = $info_kunjungan;
                    $status_titipan = ' - ';
                    if($value['carabayar_id'] == 6){
                        $status_titipan = ' - ';
                    } else if (!empty(ArrayHelper::getValue($value,'is_pasientitipan_pk'))) {
                        if(ArrayHelper::getValue($value,'is_pasientitipan_pk') == true && ArrayHelper::getValue($value,'is_stoppasientitipan') == false){
                            $status_titipan = ArrayHelper::getValue($value,'kelas_ditagihkan_nama');
                        }
                    } else if (empty(ArrayHelper::getValue($value,'is_pasientitipan_pk'))) {
                        if($value['is_pasientitipan'] == true && ArrayHelper::getValue($value,'is_stoppasientitipan') == false){
                            $status_titipan = ArrayHelper::getValue($value,'kelas_ditagihkan_nama');
                        }
                    }
                    $kelaspelayanan_nama =  ArrayHelper::getValue($value,'kelaspelayanan_nama').' / '.$status_titipan;
                    $value['kelaspelayanan_nama'] = $kelaspelayanan_nama;
                    $value['carabayar_penjamin'] = ArrayHelper::getValue($value,'carabayar_nama').' / '.ArrayHelper::getValue($value,'penjamin_nama');
                    $value['kamar_bed'] = ArrayHelper::getValue($value,'kamarruangan_nokamar').' / '.ArrayHelper::getValue($value,'no_tempattidur');
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
    
    public function actionGetKamar()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $ruangan_id = empty($post['depdrop_parents'][0]) ? null : $post['depdrop_parents'][0];
        if(!empty($ruangan_id)){
            try {
                $getData = $this->_restPendaftaran->get('allow/get-kamar-by-ruangan', [
                    'form_params' => [
                        'ruangan_id' => $ruangan_id
                    ]
                ]);
                $body = json_decode($getData->getBody(),TRUE);
                $responses = $body['response'];

                $out = [];
                foreach($responses as $key => $response) {
                    $out[] = [
                        'id' => $response['kamarruangan_id'],
                        'name' => $response['kamarruangan_nokamar']
                    ];
                }

                echo json_encode(['output'=>$out, 'selected'=>'']);
                return;
            } catch (\Exception $e) {
                echo json_encode(['output'=>[], 'selected'=>'']);
                return;
            } catch (\RequestException $e) {
                echo json_encode(['output'=>[], 'selected'=>'']);
                return;
            }
        }else{
            echo json_encode(['output'=>[], 'selected'=>'']);
            return;
        }
    }
    
    public function actionGetTempatTidur()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $kamarruangan_id = empty($post['depdrop_parents'][0]) ? null : $post['depdrop_parents'][0];
        if(!empty($kamarruangan_id)){
            try {
                $getData = $this->_restPendaftaran->get('allow/list-tempat-tidur', [
                    'form_params' => [
                        'kamarruangan_id' => $kamarruangan_id
                    ]
                ]);
                $body = json_decode($getData->getBody(),TRUE);
                $responses = $body['response'];

                $out = [];
                foreach($responses as $key => $response) {
                    $out[] = [
                        'id' => $response['kamartempattidur_id'],
                        'name' => $response['no_tempattidur']
                    ];
                }

                echo json_encode(['output'=>$out, 'selected'=>'']);
                return;
            } catch (\Exception $e) {
                echo json_encode(['output'=>[], 'selected'=>'']);
                return;
            } catch (\RequestException $e) {
                echo json_encode(['output'=>[], 'selected'=>'']);
                return;
            }
        }else{
            echo json_encode(['output'=>[], 'selected'=>'']);
            return;
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
        $path = Yii::getAlias("@download") . "/lap-kunjungan-rawat-inap.pdf";
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        try {
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-inap/export-pdf?'.http_build_query($yiiRestfulParams),[
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
            $path = Yii::getAlias("@download") . "/laporan-kunjungan-rawat-inap.xlsx";
            $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-inap/export-excel',[
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
        $request = Yii::$app->request;
        $tipe = $request->get('tipe', 'pdf');
        $jenisFile = ($tipe == 'pdf') ? 'PDF' : 'Excel';

        $title = 'Unduh Kunjungan Rawat Inap';
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['filters_name'] = $request->get('filters_name', []);

        $randString = DocoHelpers::generateRandomString();
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString, $tipe)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        return $this->guzzleExec($this->_restPendaftaran, [
            'url' => "lap-kunjungan-rawat-inap/export-excel-bgproses",
            'payload' => [
                'query' => array_merge($session, [
                    'randString' => $randString,
                    'tipe' => $tipe,
                ])
            ],
        ]);
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $tipe = $request->get('tipe', null);
        $fileName = $request->get('fileName', null);
        $fileDownloads = ($tipe == 'excel') ? 'Laporan Kunjungan Rawat Inap.xlsx' : $fileName;
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restPendaftaran->get('lap-kunjungan-rawat-inap/download-file',
        [
            'query' => [
                'fileName' => $fileName,
                'tipe' => $tipe,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        if($tipe == 'excel') {
            return DocoHelpers::downloadFile($path,true);
        }
        else {
            return DocoHelpers::previewPdf($path);
        }
    }

}
