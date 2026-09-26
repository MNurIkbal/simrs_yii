<?php 

namespace Doco\gudang\controllers;

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
use app\components\DHtml;

class LaporanHasilStokOpnameBarangController extends DocoController
{
    protected $_title = "Laporan Hasil Stok Opname Barang";
    protected $_module = '/gudang/laporan-Hasil-stok-opname-barang';
    protected $_restGudang;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
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
        $title = DHtml::getTitleMenu($this->_title);
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi = [];
        $response = $this->guzzleExec($this->_restGudang, [
            'url' => 'laporan-hasil-stok-opname-barang/get-konfig-ruangan',
        ]);
        
        $is_disabled = false;
        $ruangan_aktif = '';
            
        $daftar_ruangan = ArrayHelper::getValue($response,'ruangan',[]);
        $implementasi = ArrayHelper::getValue($response,'konfig_gudang.is_tgl_implementasi_sesuai_verif');
        
        \Yii::$app->cache->set('implementasi-so', $response['konfig_gudang']['is_tgl_implementasi_sesuai_verif']);

        return $this->render('index', get_defined_vars());
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
        
        $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => "laporan-hasil-stok-opname-barang/index",
            'payload' => ['query' => http_build_query($yiiRestfulParams)],
        ]);

        $no = $request->get('start',1);
        $implementasi_so = \Yii::$app->cache->get('implementasi-so');
        foreach ($response['data'] as $key => $value) {
            $no++;
            $value['tgl_form_so'] = isset($value['tgl_form_so']) ? date("j M Y H:i:s", strtotime($value['tgl_form_so'])) : '';
            $value['tgl_validasi_so'] = isset($value['tgl_validasi_so']) ? date("j M Y H:i:s", strtotime($value['tgl_validasi_so'])) : '';
            $value['tgl_implementasi'] = isset($value['tgl_implementasi']) ? date("j M Y H:i:s", strtotime($value['tgl_implementasi'])) : '';
            $value['harganetto'] = DocoHelpers::formatNumber($value['harganetto']);
            $value['stok_sistem'] = DocoHelpers::formatNumber($value['stok_sistem']);
            $value['stok_fisik'] = DocoHelpers::formatNumber($value['stok_fisik']);
            $value['selisih'] = DocoHelpers::formatNumber($value['selisih']);
            $value['stok_akhir'] = DocoHelpers::formatNumber($value['stok_akhir']);
            $value['selisih_akhir'] = DocoHelpers::formatNumber($value['selisih_akhir']);
            $value['total_harga_netto'] = DocoHelpers::formatNumber($value['total_harga_netto']);
            $value['total_harga_selisih'] = DocoHelpers::formatNumber($value['total_harga_selisih']);
            $value['rowNum'] = $no; 
            $data[$key] = $value;
        }

        $result['data'] = $data;
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

        $response = $this->guzzleExec($this->_restGudang, [
            'url' => 'allow/get-ruangan',
            'payload' => [
                'query' => [
                    'instalasi_id' => $parent_label,
                    'state' => 0,
                ]
            ]
        ]);

        foreach ($response['data'] as $value)
            $result['output'][] = [
                'id' => $value['ruangan_id'],
                'name' => $value['ruangan_nama']
            ];
        return $result;
    }

    public function actionShowPopupExcel() {
        $title = 'Download Laporan Hasil Stok Opname Barang';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel() {
        $request = Yii::$app->request;
        $randString = $request->get('randString');

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restGudang, [
            'url' => "laporan-hasil-stok-opname-barang/sync-export-excel",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionDownloadFileExcel() {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan-hasil-stok-opname-barang.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $this->_restGudang->get('laporan-hasil-stok-opname-barang/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
