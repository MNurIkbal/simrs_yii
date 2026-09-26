<?php

namespace Doco\radiologi\controllers;

use app\components\DHtml;
use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;

class LapWaktuTungguPasienRadController extends DocoController
{
    protected $_restRad;
    protected $_title = 'Laporan Waktu Tunggu Pasien Radiologi';

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi; 

    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    // Action index
    public function actionIndex()
    {
        $title = DHtml::getTitleMenu();
        if(empty($title)) {
            $title = $this->_title;
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $response = $this->guzzleExec($this->_restRad, [
            'url' => 'lap-waktu-tunggu-pasien-rad/index',
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $tglMasukPenunjang = ArrayHelper::getValue($value, 'tglmasukpenunjang');
            $tglMasukPenunjang = !empty($tglMasukPenunjang) ? date('d-M-Y H:i:s',strtotime($tglMasukPenunjang)) : '';
            $tglPersetujuan = ArrayHelper::getValue($value, 'tglpersetujuan');
            $tglPersetujuan = !empty($tglPersetujuan) ? date('d-M-Y H:i:s',strtotime($tglPersetujuan)) : '';
            $tglAmbilFoto = ArrayHelper::getValue($value, 'tgl_ambilfoto');
            $tglAmbilFoto = !empty($tglAmbilFoto) ? date('d-M-Y H:i:s',strtotime($tglAmbilFoto)) : '';
            $tglHasil = ArrayHelper::getValue($value, 'tgl_hasilrad');
            $tglHasil = !empty($tglHasil) ? date('d-M-Y H:i:s',strtotime($tglHasil)) : '';
            $tglKirimPasien = ArrayHelper::getValue($value, 'tgl_kirimpasien');
            $tglKirimPasien = !empty($tglKirimPasien) ? date('d-M-Y H:i:s',strtotime($tglKirimPasien)) : '';
            $response['data'][$key]['tglpersetujuan'] = !empty($tglPersetujuan) ? $tglPersetujuan : $tglMasukPenunjang;
            $response['data'][$key]['tgl_kirimpasien'] = DocoHelpers::convDateTime($tglKirimPasien,true,false);
            $response['data'][$key]['waktu_tunggu_tanggal_expertise'] = !empty($tglHasil) ? (new DocoHelpers)->getLamaTunggu($tglMasukPenunjang, $tglHasil) : '-';
            $response['data'][$key]['waktu_tunggu_ambil_foto_expertise'] = !empty($tglAmbilFoto) && !empty($tglHasil) ? (new DocoHelpers)->getLamaTunggu($tglAmbilFoto, $tglHasil) : '-';
        }
        $response['recordsTotal'] = isset($response['_meta']['totalCount']) ? $response['_meta']['totalCount'] : 0;
        $response['recordsFiltered'] = isset($response['_meta']['totalCount']) ? $response['_meta']['totalCount'] : 0;
        return $response;
    }

    public function actionFilters()
    {
        return $this->guzzleExec($this->_restRad, [
            'url' => 'lap-waktu-tunggu-pasien-rad/filters',
            'payload' => [
               'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
         ]);
   
    }

    public function actionShowPopup()
    {
        $request = Yii::$app->request;
        $tipe = $request->get('tipe', 1);
        $title = ($tipe == 1) ? 'Unduh Excel ' : 'Cetak PDF ';
        $title .= $this->_title;
        $randString = DocoHelpers::generateRandomString();
        $payload = DocoDatatableHelper::advancedFilterParam();
        $userIdentity = Yii::$app->session->get('user_identity');
        $nama_pegawai = isset($userIdentity['nama_pegawai']) ? $userIdentity['nama_pegawai'] : null;
        $_GET['nama_pegawai'] = $nama_pegawai;
        $_GET['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $payload);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString, $tipe)
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $session = Yii::$app->session->getFlash($randString);
      $session['randString'] = $randString;
      $session['tipe'] = $tipe;
      return $this->guzzleExec($this->_restRad, [
         'url' => 'lap-waktu-tunggu-pasien-rad/unduh-file',
         'payload' => [
            'query' => $session
         ],
      ]);
    }

    public function actionDownloadFile()
    {
      $request = Yii::$app->request;
      $date = date('dmY');
      $filename = $request->get('fileName', null);
      $tipe = $request->get('tipe', 1);
      $ext = ($tipe == 1) ? '.xlsx' : '.pdf';
      $fileDownloads = $this->_title.' '.$date.$ext;
      $path = Yii::getAlias("@download").'/'.$fileDownloads;
      $response = $this->_restRad->get('lap-waktu-tunggu-pasien-rad/download-file', [
         'save_to' => $path,
         'query' => [
            'filename' => $filename,
            'tipe' => $tipe
         ],
      ]);
      $body = json_decode($response->getBody(), true);
    //   dump($body);die;
      return DocoHelpers::downloadFile($path,true);
    }

    public function actionGetDataPemeriksaan($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-pemeriksaan-view', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasienkirimkeunitlain_id']);
                $value['primary'] = $primaryKey;
                unset($value['pasienkirimkeunitlain_id']);
                $value['rowNum'] = $no;
                $value['is_checkbox'] = '';
                if($value['is_cyto']){
                    $value['is_checkbox'] = '<input type="checkbox" class="checkbox" checked disabled >';
                }
                $data[$key] = $value;
            }

            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDokter($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-dokter?advanced-filter[nama_pegawai]=' . $q);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array(); // array dokter temp
            foreach ($body['response']['data'] as $value)
                if(!array_key_exists($value['pegawai_id'],$temp_dokter)){
                    $result['results'][] = [
                        'id' => $value['nama_pegawai'],
                        'text' => $value['nama_pegawai']
                    ];
                    $temp_dokter[$value['pegawai_id']] = $value['nama_pegawai'];
                }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDokterRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id"); 
        try {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-dokter?advanced-filter[nama_pegawai]=' . $q. '&advanced-filter[ruangan_id]='.$ruanganid);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                'id' => $value['pegawai_id'],
                'text' => $value['nama_pegawai']
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

    public function actionGetPegawaiRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id");
        try {
            $response = $this->_restRad->get('allow/data-pegawai?advanced-filter[nama_pegawai]=' . $q . '&advanced-filter[ruangan_id]=' . $ruanganid);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                'id' => $value['pegawai_id'],
                'text' => $value['nama_pegawai']
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

    // public function actionExportExcel()
    // {
    //     // Convert to json format
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     // Get request
    //     $request = Yii::$app->request;
    //     $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

    //     // Try catch
    //     try {
    //         $path = Yii::getAlias("@download") . "/lap-waktu-tunggu-pasien-radiologi.xlsx";
    //         $response = $this->_restRad->get('lap-waktu-tunggu-pasien-rad/export-excel',[
    //             'query' => $yiiRestfulParams,
    //             'save_to' => $path,
    //         ]);
    //         return DocoHelpers::downloadFile($path,true);
    //     } catch (\Exception $e) {
    //         // Get message
    //         $result['error'] = $e->getMessage();

    //         // Return
    //         return $result;
    //     } catch (RequestException $e) {
    //         // Get message
    //         $result['error'] = $e->getMessage();

    //         // Return
    //         return $result;
    //     }
    // }

    // public function actionExportPdf(){
    //     // Yii::$app->response->format = Response::FORMAT_JSON;
    //     $request = Yii::$app->request;
    //     try {
    //         $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
    //         $path = Yii::getAlias("@download") . "/lap-waktu-tunggu-pasien-radiologi.pdf";
    //         $response = $this->_restRad->get('lap-waktu-tunggu-pasien-rad/export-pdf?' . http_build_query($yiiRestfulParams), [
    //             'save_to' => $path
    //         ]);
    //         $body = json_decode($response->getBody(), true);
    //         // return $path;
    //         return DocoHelpers::previewPdf($path);
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return DocoHelpers::response($result);
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return DocoHelpers::response($result);
    //     }
    // }
}
