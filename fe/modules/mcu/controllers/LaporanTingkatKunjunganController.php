<?php

namespace Doco\mcu\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;

class LaporanTingkatKunjunganController extends DocoController
{
    protected $_title = "Laporan Tingkat Kunjungan MCU";
    protected $_module = '/mcu/laporan-tingkat=kunjungan/';
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
        $title = $this->_title;

        $response = $this->_restMcu->get('laporan-tingkat-kunjungan-mcu/get-options');
        $body = json_decode($response->getBody(), true);
        $tipePaket = $body['response']['tipe_paket'];
        $penjamin = $body['response']['penjamin'];
        
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

        } else {
            $tgl_awal = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }
        
        $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
        $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
        unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restMcu->get('laporan-tingkat-kunjungan-mcu/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            $responData = $body['response']['data'];
            foreach ($responData as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                unset($value['pendaftaran_id']);
                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
                $value['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran']);
                $value['info_pasien'] = $value['no_rekam_medik'].' / '.$value['nama_pasien'];
                $value['tarif_tindakan'] = DocoHelpers::rupiahDisplay($value['tarif_tindakan']);

                $data[$key] = $value;
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
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];

        } else {
            $tgl_awal = date('Y-m-d');
            $tgl_akhir = date('Y-m-d');
        }
        
        $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
        $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
        $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
        unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);

        $url = 'laporan-tingkat-kunjungan-mcu/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/laporan-tingkat-kunjungan-mcu.xlsx";
        try {
            $response = $this->_restMcu->get($url,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
            
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

}