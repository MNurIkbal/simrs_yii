<?php
// Author : Budi

namespace Doco\bankdarah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class InfPemesananDarahRuanganController extends DocoController
{
    protected $_title = "Informasi Pemesanan Darah Ruangan";
    protected $_module = '/inf-pemesanan-darah-ruangan/';
    protected $_restBankDarah; 
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_nama_ruangan;

    public function init()
    {
        parent::init();
        $this->_restBankDarah = Yii::$app->docoRest->bankdarah; 
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {        
        $title = $this->_title;
        $module = $this->_module;
        $dataRequest = $this->getRequest();
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        $ruangan_id = $this->_ruangan_id;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pesandarah'])) {
            $tgl_pesandarah_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pesandarah']);
            $tgl_awal = $tgl_pesandarah_range[0];
            $tgl_akhir = $tgl_pesandarah_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pesandarah_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pesandarah_akhir'] = $tgl_akhir_format;
            // unset($yiiRestfulParams['advanced-filter']['tgl_pesandarah']);
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restBankDarah->get('inf-pemesanan-darah-ruangan/index?' .http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesandarah_id']);
                unset($value['pesandarah_id']);
                $value['tgl_pesandarah'] = date("j M Y", strtotime($value['tgl_pesandarah']));
                $value['tgl_mintakirim'] = date("j M Y", strtotime($value['tgl_mintakirim']));
                $value['nama_pasien'] = $value['nama_pasien'].'/'.$value['no_rekam_medik'];
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
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

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'inf-pemesanan-darah/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/inf-pemesanan-darah.xlsx";
        try {
            $path = Yii::getAlias("@download") . "/inf-pemesanan-darah-ruangan.xlsx";
            $response = $this->_restBankDarah->get($url,[
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getRequest()
    {
        $request = $this->_restBankDarah->request('GET', 'inf-pemesanan-darah-ruangan/get-request');
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }
}
