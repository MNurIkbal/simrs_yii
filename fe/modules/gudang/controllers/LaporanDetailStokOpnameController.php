<?php 

/**
 * @author Randy Vianda Putra
 * @todo Laporan Detail Stok Opname Barang
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

class LaporanDetailStokOpnameController extends DocoController
{

    protected $_title = "Detail Stok Opname Barang";
    protected $_module = '/gudang/laporan-detail-stok-opname';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actionIndex()
    {
        $title = $this->_title;

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan_id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('lap-detail-stok-opname/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['tglstokopname'] = date('d F Y', strtotime($value['tglstokopname']));
                $value['rowNum'] = $no;
                $harga_fisik = $value['volume_fisik'] * $value['harganetto'];
                $harga_sistem = $value['volume_sistem'] * $value['harganetto'];
                $selisih_harga = $value['jmlselisihstok'] * $value['harganetto'];
                $value['harga_fisik'] = "Rp. " . number_format($harga_fisik, 0, ',', '.');
                $value['harga_sistem'] = "Rp. " . number_format($harga_sistem, 0, ',', '.');
                $value['selisih_jumlah'] = $value['jmlselisihstok'];
                $value['selisih_harga'] = "Rp. " . number_format($selisih_harga, 0, ',', '.');
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

    public function actionGetNoSo()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restGudang->request('POST', 'lap-detail-stok-opname/data-detail-so', [
                'form_params' => ['term' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {

                $data[] = ['id' => $value['nostokopname'], 'text' => $value['nostokopname']];

            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/laporan-detail-stokopname-barang.pdf";
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan_id;

        try {
            $response = $this->_restGudang->get('lap-detail-stok-opname/export-pdf', [
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        try {
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            // $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/laporan-detail-stokopname-barang.xlsx";
            $yiiRestfulParams['advanced-filter']['ruangan_id'] = $ruangan_id;
            $response = $this->_restGudang->get('lap-detail-stok-opname/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}