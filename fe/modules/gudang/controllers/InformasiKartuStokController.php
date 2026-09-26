<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Kartu Stok Barang
 * @copyright 17 April 2018 aweutist
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class InformasiKartuStokController extends DocoController
{

    protected $_title = "Kartu Stok Barang";
    protected $_module = '/gudang/informasi-kartu-stok/';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $module = $this->_module;

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan_id;
        if(!isset($yiiRestfulParams['advanced-filter']['tanggal_transaksi'])){
            $date = date('d-M-Y');
            $yiiRestfulParams['advanced-filter']['tanggal_transaksi'] = $date.' - '.$date;
        }
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->guzzleExec($this->_restGudang, [
                'url' => 'inf-kartu-stok/',
                'method' => 'get',
                'payload' => [
                    'query' => $yiiRestfulParams
                ]
            ]);
            $kartuStok = ArrayHelper::getValue($response, 'data', []);
            $no = $request->get('start', 1);
            foreach ($kartuStok as $key => $value) {
                $no++;
                $value['tanggal_transaksi'] = date('d-M-Y', strtotime($value['tanggal_transaksi']));
                $value['tglkadaluarsa'] = !empty($value['tglkadaluarsa']) ? date('d-M-Y', strtotime($value['tglkadaluarsa'])) : "-";
                $value['no_transaksi'] = isset($value["no_transaksi"]) ? $value["no_transaksi"] : "-" ;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $response['_meta']['totalCount'];
            $result['recordsFiltered'] = $response['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetBarang()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restGudang->request('POST', 'allow/ambil-barang', [
                'form_params' => ['term' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['barang_id'], 'text' => $value['barang_nama']];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }

    public function actionUnduhExcel()
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $path = Yii::getAlias("@download") . "/informasi-kartu-stok.xlsx";
            $response = $this->_restGudang->get('inf-kartu-stok/unduh-excel', [
                'query' => $filter,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e);
        } catch (RequestException $e){
            throw new \yii\web\HttpException(500, $e);
        }
    }

}