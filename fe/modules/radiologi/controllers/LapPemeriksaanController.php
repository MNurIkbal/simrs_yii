<?php

/**
 * @author Randy Vianda Putra
 * @todo Laporan pemeriksaan radiologi
 * @copyright 31 Juli 2018 aweutist
 */

namespace Doco\radiologi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use yii\helpers\ArrayHelper;

class LapPemeriksaanController extends DocoController 
{
    protected $_title = "Laporan Pemeriksaan Radiologi";
    protected $_module = '/radiologi/lap-pemeriksaan';
    protected $_restRad;
    protected $_master;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi;
        $this->_master = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $request = Yii::$app->request;
        $response = $this->_restRad->get('lap-pemeriksaan/kelas-pelayanan');
        $body = json_decode($response->getBody(), TRUE);
        $body = $body['response'];
        $dataKelasPelayanan = empty($body['data']) ? [] : $body['data'];
        $listkelaspelayanan = ArrayHelper::map($body, 'kelaspelayanan_nama', 'kelaspelayanan_nama');
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restRad->request('get', 'lap-pemeriksaan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = $cache = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                $value['primary'] = $primaryKey;
                unset($value['pendaftaran_id']);
                $value['tglmasukpenunjang'] = date('d M Y', strtotime($value['tglmasukpenunjang']));
                $value['rowNum'] = $no;
                $value['tarif_satuan'] = DocoHelpers::formatNumber($value['tarif_satuan']);
                $value['tarifcyto_tindakan'] = !empty($value['tarifcyto_tindakan']) ? DocoHelpers::formatNumber($value['tarifcyto_tindakan']) : 0;
                $value['total'] = DocoHelpers::formatNumber($value['tarif_tindakan']*$value['qty_tindakan']);
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $url = 'lap-pemeriksaan/export-pdf?ruangan_id='.$ruangan_id.'&'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/cetak-laporan-pemeriksaan-lab.pdf";
            $response = $this->_restRad->get($url,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'lap-pemeriksaan/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/cetak-laporan-pemeriksaan-radiologi.xlsx";
        try {

            $response = $this->_restRad->get($url,[
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);

        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    public function actionGetKelompok()
    {
        if(isset($_GET['term']) && !empty($_GET['term'])){
            $response = $this->_restRad->request('get', 'allow/get-kelompok-pemeriksaan-rad',[
                            'query'=>['nama_kelompok'=>$_GET['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['kelompokpemeriksaanrad_id'],'text'=>$value['nama_kelompok']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetJenis()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRad->request('get', 'allow/get-jenis-pemeriksaan-rad',[
                'query'=>['kelompokpemeriksaanrad_id'=>$parent_label],
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['jenispemeriksaanrad_id'], 
                    'name' => $value['jenispemeriksaanrad_nama']
                ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    public function actionGetPemeriksaan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRad->request('get', 'allow/get-pemeriksaan-rad',[
                'query'=>['jenispemeriksaanrad_id'=>$parent_label],
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['daftartindakan_id'], 
                    'name' => $value['daftartindakan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }
    public function actionGetDokter()
    {
        if(isset($_GET['term']) && !empty($_GET['term'])){
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restRad->request('get', 'allow/get-dokter-rad',[
                'query'=>['ruangan_id'=>$ruangan_id, 'nama_pegawai'=>$_GET['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['pegawai_id'],'text'=>$value['nama_pegawai']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionShowPopup()
    {
        $request = Yii::$app->request;
        $tipe = $request->get('tipe', 1);
        $title = ($tipe == 1) ? 'Unduh Excel ' : 'Cetak PDF ';
        $title .= $this->_title;
        $randString = DocoHelpers::generateRandomString();
        $payload = DocoDatatableHelper::convertToRestfulParams($request->get());
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
            'url' => 'lap-pemeriksaan/unduh-file',
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
        $response = $this->_restRad->get('lap-pemeriksaan/download-file', [
            'query' => [
                'filename' => $filename,
                'tipe' => $tipe
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path,true);
    }
}