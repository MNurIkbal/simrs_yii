<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-19 10:54:30
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-23 16:48:25
 */

namespace Doco\laboratorium\controllers;

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
    protected $_title = "Laporan Pemeriksaan Laboratorium";
    protected $_module = '/laboratorium/lap-pemeriksaan';
    protected $_restLab;
    protected $_master;

    public function init()
    {
        parent::init();
        $this->_restLab = Yii::$app->docoRest->laboratorium;
        $this->_master = Yii::$app->docoRest->master;

    }

    public function actionIndex()
    {
        return Yii::$app->docoPlugin->execute($this,'lap_pemeriksaan_index');
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            if ($request->get('ruangan_default') != "") {
                $yiiRestfulParams['advanced-filter']['ruangan_id'] = $request->get('ruangan_default');
            }
            $response = $this->_restLab->request('get', 'lap-pemeriksaan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $row = [];
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
            $response = $this->_restLab->get($url,[
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
        $path = Yii::getAlias("@download") . "/cetak-laporan-pemeriksaan-lab.xlsx";
        try {
            $response = $this->_restLab->get($url,[
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
            $response = $this->_restLab->request('get', 'allow/get-kelompok-pemeriksaan-lab',[
                            'query'=>['nama_kelompok'=>$_GET['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['kelompokpemeriksaanlab_id'],'text'=>$value['nama_kelompok']];
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
            $response = $this->_restLab->request('get', 'allow/get-jenis-pemeriksaan-lab',[
                            'query'=>['kelompokpemeriksaanlab_id'=>$parent_label],
                        ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['jenispemeriksaanlab_id'], 
                    'name' => $value['jenispemeriksaanlab_nama']
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
            $response = $this->_restLab->request('get', 'allow/get-pemeriksaan-lab',[
                            'query'=>['jenispemeriksaanlab_id'=>$parent_label],
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
            $response = $this->_restLab->request('get', 'allow/get-dokter-lab',[
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

    public function actionShowPopupExcel()
    {
        $title = 'Download Excel Laporan Pemeriksaan Laboratorium';
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
        return $this->guzzleExec($this->_restLab, [
            'url' => "lap-pemeriksaan/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Pemeriksaan Laboratorium.xlsx';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restLab->get('lap-pemeriksaan/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionFilters()
    {
        return $this->guzzleExec($this->_restLab, [
            'url' => 'lap-pemeriksaan/filters',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }
}