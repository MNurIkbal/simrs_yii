<?php

namespace Doco\gudang\controllers;
/**
* @author yaya
*/

use Yii;
use yii\web\Response;

use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;

class InformasiReturObatSupplierController extends DocoController
{
    public $_title = "Informasi Retur Obat";
    protected $_module = '/gudang/informasi-retur-obat-supplier/';
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
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restGudang->get('informasi-retur-obat-supplier/', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = $value['no_returpenerimaanobat'];
                $value['primary'] = $primaryKey;
                $value['qty_retur'] = DocoHelpers::formatNumber($value['qty_input']) .' '. $value['satuanunit_nama'];
                $value['tgl_retur'] = !empty($value['tgl_retur']) ? date('d M Y', strtotime($value['tgl_retur'])) : '-';
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCetakTransaksi($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/detail-retur-obat-suplier.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->get('informasi-retur-obat-supplier/cetak-transaksi', [
                'save_to' => $path,
                'query' => [
                    'id' => $id
                ]
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/informasi-retur-obat-supplier.pdf";

        try {
            $response = $this->_restGudang->get('informasi-retur-obat-supplier/export-pdf', [
                'query' => $filter,
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/informasi-retur-obat-supplier.xlsx";
        try {
            $response = $this->_restGudang->get('informasi-retur-obat-supplier/export-excel',[
                'query' => $filters,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (RequestException $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}