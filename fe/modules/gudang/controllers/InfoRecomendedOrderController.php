<?php 

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\base\Model;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use app\modules\gudang\models\ValidasiPoObatForm;
use app\modules\gudang\models\ValidasiPoBarangForm;
use app\modules\gudang\models\ValidasiPoObatDetailForm;
use app\modules\gudang\models\ValidasiPoBarangDetailForm;
use app\modules\gudang\models\CustomPoObatForm;

use GuzzleHttp\Exception\RequestException;

class InfoRecomendedOrderController extends DocoController
{
    protected $_title;
    protected $_restMaster;
    protected $_gudang;
    protected $_workspace;
    protected $_ruangan_nama;
    protected $_module = '/gudang/info-recomended-order/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Informasi Recomended Order ');
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_gudang = Yii::$app->docoRest->gudang;
        $this->_workspace = Yii::$app->session->get('active_workspace');
        $this->_ruangan_nama = $this->_workspace['ruangan_name'];
    }

    public function actionObat()
    {
        $title = $this->_title.'Obat';
        $module = $this->_module;
        $result = $this->getAttributes();
        return $this->render('obat', get_defined_vars());
    }

    public function actionBarang()
    {
        $title = $this->_title.'Barang';
        $module = $this->_module;
        $result = $this->getAttributes();
        return $this->render('barang', get_defined_vars());
    }

    protected function getAttributes()
    {
        try {
            $response = $this->_gudang->get('info-recomended-order/get-attributes');
            $response = json_decode($response->getBody(),true);
            $result = isset($response['response']) ? $response['response'] : [];
        } catch (RequestException $e) {
            $result = [
                'data_status' => []
            ];
        }
        return $result;
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat'])) {
            $tgl_rekomendasiobat_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat']);
            $tgl_awal = $tgl_rekomendasiobat_range[0];
            $tgl_akhir = $tgl_rekomendasiobat_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_gudang->get('info-recomended-order/obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['rekomendasiobat_id']);
                unset($value['rekomendasiobat_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgl_rekomendasiobat'] = date('d M Y', strtotime($value['tgl_rekomendasiobat']));
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

    public function actionGetDataBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang'])) {
            $tgl_rekomendasibarang_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang']);
            $tgl_awal = $tgl_rekomendasibarang_range[0];
            $tgl_akhir = $tgl_rekomendasibarang_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_gudang->get('info-recomended-order/barang?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['rekomendasibarang_id']);
                unset($value['rekomendasibarang_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgl_rekomendasibarang'] = date('d M Y', strtotime($value['tgl_rekomendasibarang']));
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
        if (isset($yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat'])) {
            $tgl_rekomendasiobat_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat']);
            $tgl_awal = $tgl_rekomendasiobat_range[0];
            $tgl_akhir = $tgl_rekomendasiobat_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat']);
        }
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $url = 'info-recomended-order/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/info-recomended-order.xlsx";
        try {
            $response = $this->_gudang->get($url,[
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            if (isset($yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat'])) {
                $tgl_rekomendasiobat_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat']);
                $tgl_awal = $tgl_rekomendasiobat_range[0];
                $tgl_akhir = $tgl_rekomendasiobat_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat']);
            }
            $path = Yii::getAlias("@download") . "/info-recomended-order.pdf";
            $response = $this->_gudang->get('info-recomended-order/cetak-obat?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcelBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang'])) {
            $tgl_rekomendasibarang_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang']);
            $tgl_awal = $tgl_rekomendasibarang_range[0];
            $tgl_akhir = $tgl_rekomendasibarang_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang']);
        }
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $url = 'info-recomended-order/export-excel-barang?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/info-recomended-order-barang.xlsx";
        try {
            $response = $this->_gudang->get($url,[
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            if (isset($yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang'])) {
                $tgl_rekomendasibarang_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang']);
                $tgl_awal = $tgl_rekomendasibarang_range[0];
                $tgl_akhir = $tgl_rekomendasibarang_range[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang_awal'] = $tgl_awal_format;
                $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang_akhir'] = $tgl_akhir_format;
                unset($yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang']);
            }
            $path = Yii::getAlias("@download") . "/info-recomended-order-barang.pdf";
            $response = $this->_gudang->get('info-recomended-order/cetak-barang?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDetailObat($id)
    {
        $id = DocoHelpers::decrypt($id);
        $module = $this->_module;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Rekomendasi Order Obat');
        $ruangan = $this->_ruangan_nama;
        $response = $this->_gudang->get('info-recomended-order/view-obat?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $header = $body['response'][0];

        return $this->render('form_detail_obat', get_defined_vars());
    }

    public function actionGetDataDetailObat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_gudang->get('info-recomended-order/get-detail-obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['rekomendasiobat_id']);
                unset($value['rekomendasiobat_id']);
                $value['primary'] = $primaryKey;
                $value['nilai_ro'] = DocoHelpers::formatNumber($value['nilai_ro']);
                $value['qty_tersedia'] = DocoHelpers::formatNumber($value['qty_tersedia']);
                $value['rekomendasi'] = DocoHelpers::formatNumber($value['rekomendasi']);
                $value['on_po'] = DocoHelpers::formatNumber($value['on_po']);
                $value['rowNum'] = $no;
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

    public function actionDetailBarang($id)
    {
        $id = DocoHelpers::decrypt($id);
        $module = $this->_module;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Rekomendasi Order Barang');
        $ruangan = $this->_ruangan_nama;
        $response = $this->_gudang->get('info-recomended-order/view-barang?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $header = $body['response'][0];

        return $this->render('form_detail_barang', get_defined_vars());
    }

    public function actionGetDataDetailBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_gudang->get('info-recomended-order/get-detail-barang?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['rekomendasibarang_id']);
                unset($value['rekomendasibarang_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['nilai_ro'] = DocoHelpers::formatNumber($value['nilai_ro']);
                $value['qty_tersedia'] = DocoHelpers::formatNumber($value['qty_tersedia']);
                $value['rekomendasi'] = DocoHelpers::formatNumber($value['rekomendasi']);
                $value['on_po'] = DocoHelpers::formatNumber($value['on_po']);
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

    public function actionCetakDetailBarang($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/detail-rekomendasi-barang.pdf";
            $response = $this->_gudang->get('info-recomended-order/cetak-detail-barang',[
                'query' => [
                    'id' => $id
                ],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakDetailObat($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/detail-rekomendasi-obat.pdf";
            $response = $this->_gudang->get('info-recomended-order/cetak-detail-obat',[
                'query' => [
                    'id' => $id
                ],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}