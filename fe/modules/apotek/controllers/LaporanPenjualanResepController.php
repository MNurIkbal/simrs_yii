<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-06 16:51:18
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-19 11:38:50
 */

namespace Doco\apotek\controllers;

use app\modules\apotek\models\Pegawai;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DHtml;

class LaporanPenjualanResepController extends DocoController
{
    protected $_title = "Laporan Penjualan Obat Alkes";
    protected $_module = '/apotek/laporan-penjualan-resep';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = DHtml::titleMenu($this->_title);
        $api = $this->guzzleExec($this->_restApotek, [
            'url' => 'lap-penjualan-resep/generate-api',
            'payload' => [
                'query' => [],
            ]
        ]);
        $api = ArrayHelper::getValue($api, 'data', []);
        $dokter = ArrayHelper::getValue($api, 'dokter', []);
        $ruangan = ArrayHelper::getValue($api, 'ruangan', []);
        $jenis_penjualan = ArrayHelper::getValue($api, 'jenispenjualan', []);
        $jenis_penjualan = array_replace_recursive(ArrayHelper::map($jenis_penjualan, 'lookup_value', 'lookup_value'), DocoConstants::PENJUALAN_BMHP);
        $statusPenjualan = ArrayHelper::getValue($api, 'statusPenjualan', []);
        $statusPenjualan = array_replace_recursive(ArrayHelper::map($statusPenjualan, 'lookup_id', 'lookup_value'), DocoConstants::STATUS_BMHP);

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tanggal_lahir']) && $yiiRestfulParams['advanced-filter']['tanggal_lahir'] == '__-__-____') {
            unset($yiiRestfulParams['advanced-filter']['tanggal_lahir']);
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $response = $this->guzzleExec($this->_restApotek, [
            'url' => 'lap-penjualan-resep/index',
            'payload' => [
                'query' => $yiiRestfulParams,
            ]
        ]);
        
        $body = ArrayHelper::getValue($response, 'data', []);
        $meta = ArrayHelper::getValue($response, '_meta', []);
        $no = $request->get('start',1);
        if (!empty($body)) {
            foreach ($body as $key => $value) {
                $nama_pasien = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '-';
    
                $no++;
                $primaryKey = DocoHelpers::encrypt(ArrayHelper::getValue($value, 'penjualanresep_id'));
                unset($value['penjualanresep_id']);
    
                $nama_pasien = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '-';
    
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgltransaksi'] = isset($value['tgltransaksi']) ? date("j M Y", strtotime($value['tgltransaksi'])) : null;
                $value['nama_pasien'] = $nama_pasien;
                $value['tanggal_lahir'] = isset($value['tanggal_lahir']) ? date("j M Y", strtotime($value['tanggal_lahir'])) : null;
                $value['totaltagihan'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'totaltagihan'));
                $value['no_pendaftaran'] = ArrayHelper::getValue($value, 'no_pendaftaran', '-');
                $value['formularium'] = ArrayHelper::getValue($value, 'is_formularium') ? 'Ya' : 'Tidak';
                $value['is_psycothropica'] = ArrayHelper::getValue($value, 'is_psycothropica') ? 'Ya' : 'Tidak';
                $value['is_narcotic'] = ArrayHelper::getValue($value, 'is_narcotic') ? 'Ya' : 'Tidak';
                $value['user'] = ArrayHelper::getValue($value, 'user');
                unset($value['totalhargajual']);
                $data[$key] = $value;
            }
        }
        
        $result['data'] = $data;
        $result['recordsTotal'] = ArrayHelper::getValue($meta, 'totalCount', 0);
        $result['recordsFiltered'] = ArrayHelper::getValue($meta, 'totalCount', 0);
        return $result;
    }

    public function actionGetPenjamin()
    {       
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->guzzleExec($this->_restApotek,[
                'url' => 'lap-penjualan-resep/get-penjamin',
                'method' => 'GET',
                'payload' => [
                    'query' => ['carabayar_id' => $parent_label],
                ],
            ]);
            $body = ArrayHelper::getValue($response, 'data', []);            
            foreach ($body as $value) 
                $result['output'][] = [
                    'id' => $value['penjamin_nama'], 
                    'name' => $value['penjamin_nama']
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
    public function actionGetNoresep($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restApotek->get('penjualan-resep?advanced-filter[noresep]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $value['noresep'], 
                    'text' => $value['noresep']
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
    
    public function actionGetDataObat()
    {
        $id = DocoHelpers::decrypt($_GET['id']);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());        
        $yiiRestfulParams['advanced-filter']['penjualanresep_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('lap-penjualan-resep/data-obat?'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $totalharga = 0;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['ppn'] = ($value['hargajual_oa'] * $value['ppn_persen']) / 100;
                $total = $value['qty_oa'] * ($value['hargajual_oa'] + $value['ppn']);
                $value['ppn'] = DocoHelpers::formatNumber($value['ppn']);
                $value['totaltagihan'] = DocoHelpers::formatNumber($total);
                $value['hargajual_oa'] = DocoHelpers::formatNumber($value['hargajual_oa']);
                $totalharga += $total;
                $data[$key] = $value;

            }   
            $result['totalobat'] = DocoHelpers::formatNumber($totalharga);
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
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tanggal_lahir']) && $yiiRestfulParams['advanced-filter']['tanggal_lahir'] == '__-__-____') {
            unset($yiiRestfulParams['advanced-filter']['tanggal_lahir']);
        }

        $result = [];
        $url = "";
        try {
            $path = Yii::getAlias("@download") . "/laporan-penjualan-obat-alkes.xlsx";
            $params = array_merge(['ruangan_id' => $ruangan_id], $yiiRestfulParams);
            $response = $this->guzzleExec($this->_restApotek,[
                'url' => 'lap-penjualan-resep/export-excel',
                'method' => 'GET',
                'payload' => [
                    'query' => $params,
                    'save_to' => $path,
                ],
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }    

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());                
        $path = Yii::getAlias("@download") . "/laporan-penjualan-resep.pdf";
        try {
            $response = $this->_restApotek->get('lap-penjualan-resep/print-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);              
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {       
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {            
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionShowPopupExcel()
    {
        $title = Yii::t('fe', 'Cetak Laporan Penjualan Obat Alkes');
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tanggal_lahir']) && $yiiRestfulParams['advanced-filter']['tanggal_lahir'] == '__-__-____') {
            unset($yiiRestfulParams['advanced-filter']['tanggal_lahir']);
        }
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restApotek, [
            'url' => "lap-penjualan-resep/export-excel-bgproses",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Penjualan Obat Alkes.csv';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        
        $response = $this->guzzleExec($this->_restApotek,[
            'url' => 'lap-penjualan-resep/download-file',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);
        return DocoHelpers::downloadFile($path,true);
    }

    public function checkAksesMenu()
    {
        $m = Yii::$app->controller->module->id;
        $c = Yii::$app->controller->id;
        $controller = "/$m/$c";
        $route = "sync-export-excel";

        $menu = Yii::$app->session->get("akses_menu");
        if(!empty($menu)) {
            if(!empty($menu[$controller])) {
                if(in_array($route, $menu[$controller])) {
                    return true;
                }
            }
        }
        return false;
    }
}
