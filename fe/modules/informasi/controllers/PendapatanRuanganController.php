<?php 
// Author : Ardi Pratama

namespace Doco\informasi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;

class PendapatanRuanganController extends DocoController
{
    protected $_title;
    protected $_restRm;
    protected $_module = '/informasi/laporan-pendapatan-ruangan/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Pendapatan Ruangan');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $response = $this->_restRm->get('laporan-pendapatan-ruangan/get-request');
        $response = json_decode($response->getBody(),true);
        $response = $response['response'];
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $yiiRestfulParams['instalasi_id'] = $instalasi_id;
        try {
            $response = $this->_restRm->get('laporan-pendapatan-ruangan/index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $totalPendapatan = ArrayHelper::getValue($body, 'response.additional_data.summary.total', '0');
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $caraBayar = ArrayHelper::getValue($value, 'carabayar_nama');
                $penjamin = ArrayHelper::getValue($value, 'penjamin_nama');
                unset($value['pendaftaran_id']);
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('d M Y', strtotime($value['tgl_pendaftaran']));
                $value['jasa_rumahsakit'] = DocoHelpers::formatNumber($value['jasa_rumahsakit'], 0);
                $value['jasa_layanan'] = DocoHelpers::formatNumber($value['jasa_layanan'], 0);
                $value['total'] = DocoHelpers::formatNumber($value['total'], 0);
                $value['instalasi_ruangan'] = $value['instalasi_nama'].' '.$value['ruangan_nama'];
                $value['carabayar_penjamin'] = "$caraBayar / $penjamin";
                $data[$key] = $value;
            }
            $result['total_pendapatan'] = DocoHelpers::formatNumber($totalPendapatan);
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

    public function actionUpdate($id)
    {
        $module = $this->_module;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new DokRekamMedisForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restRm->put('dok-rekam-medis/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
                $response = $this->_restRm->get('lokasi-rak-rekam-medik');
                $body = json_decode($response->getBody(), true);
                $lokasirak = $body['response']['data'];

                $response = $this->_restRm->get('sub-rak-rekam-medik');
                $body = json_decode($response->getBody(), true);
                $lokasisubrak = $body['response']['data'];

                $response = $this->_restRm->get('warna-dok-rekam-medik');
                $body = json_decode($response->getBody(), true);
                $warnadok = $body['response']['data'];

                $response = $this->_restRm->get('pasien');
                $body = json_decode($response->getBody(), true);
                $pasien = $body['response']['data'];

                $response = $this->_restRm->get('dok-rekam-medis/view?id='.$id);
                $body = json_decode($response->getBody(), true);
                $attributes = $body['response'];
                $model->attributes = $attributes;
                $model->tglrekammedis = date('d M Y', strtotime($model->tglrekammedis));
                return $this->renderAjax('form', get_defined_vars());
        }
    }

    public function actionGetPenjamin($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('allow/get-penjamin'. $params);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['penjamin_id'],
                    'name' => $value['penjamin_nama']
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

    public function actionGetCarabayar($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        // dump($parent_label);die;
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('allow/get-carabayar?id='.$parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['carabayar_id'],
                    'name' => $value['carabayar_nama']
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $yiiRestfulParams['instalasi_id'] = Yii::$app->docoVars->workspace("instalasi_id");

        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_pendaftaran_awal = $tgl_pendaftaran[0];
            $tgl_pendaftaran_awal_akhir = $tgl_pendaftaran[1];
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_pendaftaran_awal;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_pendaftaran_awal_akhir;
        }
        
        $url = 'laporan-pendapatan-ruangan/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/laporan-pendapatan-ruangan.xlsx";
        try {
            $response = $this->_restRm->get($url,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionShowPopupExcel()
    {
        $title = 'Cetak Laporan Pendapatan Ruangan Excel';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $yiiRestfulParams['instalasi_id'] = Yii::$app->docoVars->workspace("instalasi_id");

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    /**
     * @Author: babang
     * 
     * BG-PROCESS PENDAPATAN RUANGAN
     * 
     * --------------------------------------------------
     */

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restRm, [
            'url' => "laporan-pendapatan-ruangan/sync-export-excel-rabbitmq",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Pendapatan Ruangan.xlsx';

        $path = Yii::getAlias("@download") . '/' . $fileDownloads;
        $response = $this->_restRm->get('laporan-pendapatan-ruangan/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path, true);
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/laporan-pendapatan-ruangan.pdf";
            $response = $this->_restRm->get('laporan-pendapatan-ruangan/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actions()
    {
        /**
         * @Author: [Budi][budi@docotel.com]
         * 
         * DATA ATTRIBUTE YANG BISA DIGUKANAN
         * 
         * --------------------------------------------------
         * data_name : nama kolom yang akan dimunculkan pada value text select2 (MAX 4 DATA)
         * keyField : sebagai id pada dropdown select2
         */
        return [
            'list-ruangan' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restRm,
                'serviceAction' => 'laporan-pendapatan-ruangan/get-data-ruangan',
                'data_name' => [
                    'instalasi_nama',
                    'ruangan_nama'
                ],
                'keyField' => 'ruangan_id'
            ],
        ];
    }
}