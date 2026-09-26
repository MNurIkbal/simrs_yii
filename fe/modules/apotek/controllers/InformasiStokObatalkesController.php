<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-01 15:38:40
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-06-28 14:49:42
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\InformasiForm;

class InformasiStokObatalkesController extends DocoController
{
    protected $_title = "Informasi Stok dan Ketersediaan Obat Alkes";
    protected $_module = '/apotek/informasi-stok';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $request = Yii::$app->request;
        $obatalkesNama = $request->get('obatalkes_nama');
        $ruanganIds = $request->get('ruangan_ids');

        $model = new InformasiForm;
        $ruangan = $instalasi = [];
        $instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        $daftar_ruangan = $this->guzzleExec($this->_restApotek,[
            'url' => 'instalasi/list-ruangan',
            'payload' => [
                'query' => []
            ],
        ]);
        $is_disabled = false;
        $ruangan_aktif = '';

        $listRequest = ['data_obat'=>'actionListJenisObat'];
        $data = $this->getAlloLoopAksi($listRequest);
        $data_obat = (isset($data['data_obat']) && count($data['data_obat']) > 0) ? ArrayHelper::map($data['data_obat'], 'jenisobatalkes_id', 'jenisobatalkes_nama') : [];

        return $this->render('obat-alkes', get_defined_vars());
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

        $response = $this->guzzleExec($this->_restApotek,[
            'url' => 'inf-stok-obat-alkes/',
            'method' => 'GET',
            'payload' => [
                'query' => $yiiRestfulParams
            ],
        ]);
        $body = ArrayHelper::getValue($response, 'data', []);
        $meta = ArrayHelper::getValue($response, '_meta', []);

        $no = $request->get('start',1);
        foreach ($body as $key => $value) {
            $no++;
            $value['min_stok'] = DocoHelpers::formatNumber($value['min_stok']);
            $value['max_stok'] = DocoHelpers::formatNumber($value['max_stok']);
            $value['qty_dipesan'] = '<button class="btn btn-link link-dipesan" data-referencefarmasi="'.@$value['reference_resep_farmasi'].'" data-referencemutasi="'.@$value['reference_mutasi'].'" data-referencedokter="'.@$value['reference_resep_dokter'].'" >'.DocoHelpers::formatNumber($value['qty_dipesan'], true, false, 3).'</button>';
            $value['qty_tersedia'] = DocoHelpers::formatNumber($value['qty_tersedia'], true, false, 3);
            $value['qty_stok'] = DocoHelpers::formatNumber($value['qty_stok'], true, false, 3);
            $value['jenisobatalkes_nama'] = isset($value['jenisobatalkes_nama']) ? $value['jenisobatalkes_nama'] : "";
            $value['obatalkes_kode'] = isset($value['obatalkes_kode']) ? $value['obatalkes_kode'] : "";

            $value['rowNum'] = $no;
            $data[$key] = $value;
        }


        $generateJumlah = $this->generateJumlah();
        $result['data'] = $data;
        $result['recordsTotal'] = ArrayHelper::getValue($meta, 'totalCount');
        $result['recordsFiltered'] = ArrayHelper::getValue($meta, 'totalCount');
        $result['total_qty_tersedia'] = DocoHelpers::formatNumber(ArrayHelper::getValue($generateJumlah, 'total_qty_tersedia', 0));
        $result['total_qty_stok'] = DocoHelpers::formatNumber(ArrayHelper::getValue($generateJumlah, 'total_qty_stok', 0));
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
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('allow/get-ruangan-by?id='.$parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) 
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

    public function actionGetObatalkes()
    {
        try {
            if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
                $ruangan_id = !empty($_GET['ruangan_id']) ? $_GET['ruangan_id'] : Yii::$app->docoVars->workspace("ruangan_id");
                $instalasi_id = !empty($_GET['instalasi_id']) ? $_GET['instalasi_id'] : Yii::$app->docoVars->workspace("instalasi_id");
                $response = $this->_restApotek->request('POST', 'inf-stok-obat-alkes/data-obat',[
                                'form_params'=> [
                                    'term' => $_GET['q']['term'], 
                                    'ruangan_id' => $ruangan_id, 
                                    'instalasi_id' => $instalasi_id
                                ],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = [
                        'id' => $value['obatalkes_id'],
                        'text' => $value['obatalkes_namalain']
                    ];
                }
                $total = count($body['response']);
                $return = [
                    'result' => $data,
                    'total_count' => $total,
                    'incomplete_results' => false
                ];
                return DocoHelpers::response($return);
            }
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ]);
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());

        /*
        if(!isset($filters['advanced-filter']['instalasi_id'])){
            $filters['advanced-filter']['instalasi_id'] = Yii::$app->docoVars->workspace("instalasi_id");
            $filters['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }
        if(!isset($filters['advanced-filter']['periodestok_nama'])){
            $filters['advanced-filter']['periodestok_nama'] = date('Y-m-d');
        }
        */
        $path = Yii::getAlias("@download") . "/informasi-stok-dan-ketersedian-obat-alkes.xlsx";
        try {
            $response = $this->_restApotek->get('inf-stok-obat-alkes/export-excel',[
                'query' => $filters,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getAlloLoopAksi($listRequest = [])
    {
        try {
            $request = Yii::$app->docoRest->master->get('allow/loop-aksi', ['form_params'=>$listRequest]);
            $body = json_decode($request->getBody(), true);
            $body = $body['response'];
        } catch (RequestException $e) {
             $body = [];
        }
        return $body;
    }

    private function generateJumlah()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $restParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $response = $this->guzzleExec($this->_restApotek,[
            'url' => 'inf-stok-obat-alkes/generate-total-stok',
            'method' => 'GET',
            'payload' => [
                'query' => $restParams
            ],
        ]);
        return ArrayHelper::getValue($response, 'data', []);
    }

    
    public function actionShowPopupExcel()
    {
        $title = 'Download Informasi Stok Obat Alkes Excel';
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
            'url' => "inf-stok-obat-alkes/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'informasi-stok-dan-ketersedian-obat-alkes.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = Yii::$app->docoRest->apotek->get('inf-stok-obat-alkes/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
