<?php

/**
 * @Author  : M.ilhamsyah.P
 * @Date    : 2020-08-10 13:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan pasien konsul
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class LapPasienKonsulController extends DocoController
{
    protected $_title = "Laporan Pasien Konsul";
    protected $_restRm;
    protected $_module = '/rm/lap-pasien-konsul/';
    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_restMaster = Yii::$app->docoRest->master;

    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $response = $this->_restRm->get('laporan-pasien-konsul/ruangan?id='.$instalasi_id);
        $body = json_decode($response->getBody(), TRUE);
        $data_ruangan = $body['response'];
        return $this->render('index', get_defined_vars());
    }

    public function actionGetRuangan()
    {
        $restRm = Yii::$app->docoRest->rm;
        $response = $this->_restRm->get('laporan-pasien-konsul/ruangan');
        $body = json_decode($response->getBody(), TRUE);
        $data_ruangan = $body['response'];
        return $data_ruangan;
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $restRm = Yii::$app->docoRest->rm;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
       
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $counter=0;

        try {
            $response = $this->_restRm->get('laporan-pasien-konsul/index?id='.$instalasi_id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            // print_r($body); die;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $data[$key] = $value;
                $data[$counter]['rowNum'] = $no;
                $data[$counter]['tgl_pendaftaran'] = date('d/m/Y H:i:s',strtotime($value['tgl_pendaftaran']));
                $data[$counter]['tgl_konsulpoli'] = date('d/m/Y H:i:s',strtotime($value['tgl_konsulpoli']));
                $counter++;

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

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $restRm = Yii::$app->docoRest->rm;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_konsulpoli'])) {
            $tgl_masuk_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_konsulpoli']);
            $tgl_awal = $tgl_masuk_range[0];
            $tgl_akhir = $tgl_masuk_range[1];

            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            
            $yiiRestfulParams['advanced-filter']['tgl_masuk_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_masuk_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_konsulpoli']);
        }
        $path = Yii::getAlias("@download") . "/laporan-pasien-konsul.pdf";
        try {
            $query = [
                'instalasi_id' => Yii::$app->docoVars->workspace("instalasi_id"),
            ];
            $query = array_merge($query,$yiiRestfulParams);

            $response = $this->_restRm->get('laporan-pasien-konsul/export-pdf',[
                'query' => $query,
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
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $restRm = Yii::$app->docoRest->rm;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        if (isset($yiiRestfulParams['advanced-filter']['tgl_konsulpoli'])) {
            $tgl_masuk_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_konsulpoli']);
            $tgl_awal = $tgl_masuk_range[0];
            $tgl_akhir = $tgl_masuk_range[1];

            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));

            $yiiRestfulParams['advanced-filter']['tgl_masuk_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_masuk_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_konsulpoli']);
        }
        try {
            $path = Yii::getAlias("@download") . "/laporan-pasien-konsul.xlsx";
            $query = [
                'instalasi_id' => Yii::$app->docoVars->workspace("instalasi_id"),
            ];
            $query = array_merge($query,$yiiRestfulParams);

            $response = $this->_restRm->get('laporan-pasien-konsul/export-excel',[
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

}
