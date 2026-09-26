<?php

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LaporanHasilStokOpnameController extends DocoController
{
    protected $_title = "Laporan Hasil Stok Opname";
    protected $_module = 'apotek/laporan-hasil-stok-opname/';
    protected $_restApotek; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek; 
        $this->_restMaster = Yii::$app->docoRest->master;
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
        $title = DHtml::getTitleMenu();
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi = [];
        
        $response = $this->guzzleExec($this->_restApotek, [
            'url' => 'laporan-hasil-stok-opname/get-konfig-ruangan',
            'method' => 'get',
        ]);

        $is_disabled = false;
        $ruangan_aktif = '';
        $daftar_ruangan = $response['ruangan'];
        $implementasi = $response['konfig_farmasi']['is_tgl_implementasi_sesuai_verif'];
        $basePrice = $response['konfig_farmasi']['base_price_so'];
        \Yii::$app->cache->set('implementasi-so', $response['konfig_farmasi']['is_tgl_implementasi_sesuai_verif']);

        $result = compact("title", "instalasiId", "ruangan_id", "instalasi", "is_disabled", "ruangan_aktif", "daftar_ruangan", "implementasi", "basePrice");

        return $this->render('index', $result);
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

        $response = $this->guzzleExec($this->_restApotek, [
            'url' => 'laporan-hasil-stok-opname/index',
            'method' => 'get',
            'payload' => [
                'query' => $yiiRestfulParams
            ],
        ]);
            
        $no = $request->get('start',1);
        $implementasi_so = \Yii::$app->cache->get('implementasi-so');
        foreach ($response['data'] as $key => $value) {
            $no++;
            $value['tgl_form_so'] = isset($value['tgl_form_so']) ? date("j M Y H:i:s", strtotime($value['tgl_form_so'])) : '';
            $value['tgl_validasi_so'] = isset($value['tgl_validasi_so']) ? date("j M Y H:i:s", strtotime($value['tgl_validasi_so'])) : '';
            $value['tgl_implementasi'] = isset($value['tgl_implementasi']) ? date("j M Y H:i:s", strtotime($value['tgl_implementasi'])) : '';
            $value['total_harga_fisik'] = DocoHelpers::formatNumber($value['weighted_avg'] * $value['stok_fisik']);
            $value['total_harga_selisi'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'total_harga_selisi',null));
            $value['total_harga_sistem'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'total_harga_sistem',null));
            $value['weighted_avg'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'weighted_avg',null));
            $value['stok_sistem'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'stok_sistem',null), true, false, 3);
            $value['stok_fisik'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'stok_fisik',null), true, false, 3);
            $value['selisih'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'selisih',null), true, false, 3);
            $value['rowNum'] = $no; 
            $data[$key] = $value;
        }

        $generateJumlah = $this->generateJumlah();
        $result['data'] = $data;
        $result['total_fisik'] = DocoHelpers::formatNumber(ArrayHelper::getValue($generateJumlah, 'total_fisik', 0));
        $result['total_selisih'] = DocoHelpers::formatNumber(ArrayHelper::getValue($generateJumlah, 'total_selisih', 0));
        $result['total_sistem'] = DocoHelpers::formatNumber(ArrayHelper::getValue($generateJumlah, 'total_sistem', 0));
        
        $result['recordsTotal'] = $response['_meta']['totalCount'];
        $result['recordsFiltered'] = $response['_meta']['totalCount'];

        return $result;
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = Yii::$app->docoVars->workspace("ruangan_id");

        try {
            $response = $this->_restApotek->get('allow/get-ruangan',[
                'query' => [
                    'instalasi_id' => $parent_label,
                    'state' => 0,
                ]
            ]);

            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
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

    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Hasil Stok Opname Excel';
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
        return $this->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => "laporan-hasil-stok-opname/sync-export-excel-rabbitmq",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan-hasil-stok-opname.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = Yii::$app->docoRest->apotek->get('laporan-hasil-stok-opname/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    private function generateJumlah()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $restParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $response = $this->guzzleExec($this->_restApotek,[
            'url' => 'laporan-hasil-stok-opname/generate-total',
            'method' => 'GET',
            'payload' => [
                'query' => $restParams
            ],
        ]);
        
        return ArrayHelper::getValue($response, 'data', []);
    }
}
