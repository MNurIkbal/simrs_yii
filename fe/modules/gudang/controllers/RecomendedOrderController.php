<?php

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\BarangForm;
use GuzzleHttp\Exception\RequestException;

class RecomendedOrderController extends DocoController
{
    protected $_title = "Recomended Order";
    protected $_module = '/gudang/recomended-order/';
    protected $_restGudang;
    
    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionObat()
    {
        $title = $this->_title. ' Obat';

        return $this->render('obat', get_defined_vars());
    }

    public function actionBarang()
    {
        $title = $this->_title. ' Barang';

        return $this->render('barang', get_defined_vars());
    }

    public function actionGetDataObat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['status_generate'] = ($request->get('status_generate')) ? true : false;
        $draw = $request->get('draw', 1);
        $data = $cache = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restGudang->get('recomended-order/obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                    $value['primary'] = $primaryKey;
                    $value['nilai_ro'] = DocoHelpers::formatNumber($value['nilai_ro']);
                    $value['sisa_stok'] = DocoHelpers::formatNumber($value['sisa_stok']);
                    $value['rekomendasi'] = DocoHelpers::formatNumber($value['rekomendasi']);
                    unset($value['obatalkes_id']);

                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            }
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
        $yiiRestfulParams['status_generate'] = ($request->get('status_generate')) ? true : false;
        $draw = $request->get('draw', 1);
        $data = $cache = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restGudang->get('recomended-order/barang?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['barang_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['barang_id']);

                    $value['rowNum'] = $no;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionSave()
    {
        $response = $this->_restGudang->post('recomended-order/save', ['form_params' => [
            'pegawai_id' => Yii::$app->user->identity->id
        ]]);

        $result = json_decode($response->getBody(),true);
        return DocoHelpers::response($result, false);
    }

    public function actionSaveBarang()
    {
        $response = $this->_restGudang->post('recomended-order/save-barang', ['form_params' => [
            'pegawai_id' => Yii::$app->user->identity->id
        ]]);

        $result = json_decode($response->getBody(),true);
        return DocoHelpers::response($result, false);
    }

    public function actionCetakObat($no_rekomendasiobat)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/ro-obat.pdf";
            $response = $this->_restGudang->get('recomended-order/cetak-obat?no_rekomendasiobat='.
                $no_rekomendasiobat,
            [
                'save_to' => $path,
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

    public function actionCetakBarang($no_rekomendasibarang)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/ro-barang.pdf";
            $response = $this->_restGudang->get('recomended-order/cetak-barang?no_rekomendasibarang='.
                $no_rekomendasibarang,
            [
                'save_to' => $path,
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
}