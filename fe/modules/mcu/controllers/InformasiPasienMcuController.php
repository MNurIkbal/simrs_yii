<?php

namespace Doco\mcu\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;   
use GuzzleHttp\Exception\RequestException;

class InformasiPasienMcuController extends DocoController
{
    protected $_title = "Informasi Pasien MCU";
    protected $_module = '/mcu/informasi-pasien-mcu/';
    protected $_restMcu;
    
    public function init()
    {
        parent::init();
        $this->_restMcu = Yii::$app->docoRest->mcu;
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
        $title = !empty(DHtml::getTitleMenu()) ? DHtml::getTitleMenu() : $this->_title;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tglmasukpenunjang'])) {
            $tglmasukpenunjang_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tglmasukpenunjang']);
            $tgl_awal = $tglmasukpenunjang_range[0];
            $tgl_akhir = $tglmasukpenunjang_range[1];

        } else {
            $tgl_awal = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }
        
        $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
        $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
        $yiiRestfulParams['advanced-filter']['tglmasukpenunjang_awal'] = $tgl_awal_format;
        $yiiRestfulParams['advanced-filter']['tglmasukpenunjang_akhir'] = $tgl_akhir_format;
        unset($yiiRestfulParams['advanced-filter']['tglmasukpenunjang']);
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restMcu->get('informasi-pasien-mcu/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $responData = isset($body['response']['data']) ? $body['response']['data'] : [];
            if(!empty($responData)) {
                foreach ($responData as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                    $value['primary'] = $primaryKey;
                    $value['tglmasukpenunjang'] = date('d-M-Y H:i:s', strtotime($value['tglmasukpenunjang']));
                    $value['info_pasien'] = $value['no_rekam_medik'].' / '.$value['nama_pasien'];
                    $value['tanggal_lahir'] = !empty($value['tanggal_lahir']) ? date('d-M-Y', strtotime($value['tanggal_lahir'])) : '-';
                    $value['rowNum'] = $no;
                    $value['tipepaket_nama'] = is_null($value['tipepaket_nama']) ? " - " : $value['tipepaket_nama'];
                    $value['carabayar_nama'] = $value['carabayar_nama'].' - '.$value['penjamin_nama'];
                    $length = count($value['tarif_tindakan']);
                    for ($i = 0; $i < $length; $i++) {
                        $value['tarif_tindakan'][$i] = is_null($value['tarif_tindakan'][$i]) ? 0 : DocoHelpers::formatNumber($value['tarif_tindakan'][$i]);
                    }
                    $data[$key] = $value;
                }
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            
            return $result;
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
        if (isset($yiiRestfulParams['advanced-filter']['tglmasukpenunjang'])) {
            $tglmasukpenunjang_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tglmasukpenunjang']);
            $tgl_awal = $tglmasukpenunjang_range[0];
            $tgl_akhir = $tglmasukpenunjang_range[1];

        } else {
            $tgl_awal = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }

        $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
        $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
        $yiiRestfulParams['advanced-filter']['tglmasukpenunjang_awal'] = $tgl_awal_format;
        $yiiRestfulParams['advanced-filter']['tglmasukpenunjang_akhir'] = $tgl_akhir_format;
        unset($yiiRestfulParams['advanced-filter']['tglmasukpenunjang']);

        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien-mcu.xlsx";
            $query = [];
            $query = array_merge($query,$yiiRestfulParams);
            $response = $this->_restMcu->get('informasi-pasien-mcu/export-excel',[
                'query' => $query,
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

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tglmasukpenunjang'])) {
            $tglmasukpenunjang_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tglmasukpenunjang']);
            $tgl_awal = $tglmasukpenunjang_range[0];
            $tgl_akhir = $tglmasukpenunjang_range[1];

        } else {
            $tgl_awal = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }
        
        $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
        $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
        $yiiRestfulParams['advanced-filter']['tglmasukpenunjang_awal'] = $tgl_awal_format;
        $yiiRestfulParams['advanced-filter']['tglmasukpenunjang_akhir'] = $tgl_akhir_format;
        unset($yiiRestfulParams['advanced-filter']['tglmasukpenunjang']);

        $path = Yii::getAlias("@download") . "/informasi-pasien-mcu.pdf";
        try {
            $response = $this->_restMcu->get('informasi-pasien-mcu/export-pdf?'.http_build_query($yiiRestfulParams),[
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

    public function actionFilters()
    {
        return $this->guzzleExec($this->_restMcu, [
            'url' => 'informasi-pasien-mcu/filters',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }
}