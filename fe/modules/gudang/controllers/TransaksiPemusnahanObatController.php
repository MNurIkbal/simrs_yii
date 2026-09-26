<?php

/**
 * @Author: DOCOTEL
 * @Date:   2018-12-26 13:30:39
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2020-05-29 13:22:09
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use Doco\gudang\models\PemusnahanForm;

class TransaksiPemusnahanObatController extends DocoController
{
    protected $_title = "Pemusnahan Obat Alkes";
    protected $_module = '/gudang/transaksi-pemusnahan-obat';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $model = new PemusnahanForm;
        $pegMengetahui = $pegMenyetujui = [];
        $pegawai_pelaksana_id = Yii::$app->docoVars->user('id_pegawai');
        $pegawai_pelaksana = Yii::$app->docoVars->user('nama_pegawai');
        $model->tanggal_pemusnahan = date('d-M-Y');
        try {
            $response = $this->_restGudang->get('allow/get-pegawai');
            $body = json_decode($response->getBody(), true);
            $pegMenyetujui = isset($body['response']) ? ArrayHelper::map($body['response'], 'pegawai_id', 'nama_pegawai') : [] ;
            $pegMengetahui = isset($body['response']) ? ArrayHelper::map($body['response'], 'pegawai_id', 'nama_pegawai') : [] ;
        } catch (\Exception $e) {
            $pegMengetahui = $pegMenyetujui = [];
        } catch (\RequestException $e) {
            $pegMengetahui = $pegMenyetujui = [];
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = 61;
        unset($yiiRestfulParams['per-page']); // untuk menampilkan semua tanpa paging

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restGudang->get('pemusnahan-obat/index', [
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt('{'.implode(',', $value['id_stok']).'}');
                $value['tglkadaluarsa'] = date('d-M-Y', strtotime($value['tglkadaluarsa']));
                $value['stok_satuan'] = DocoHelpers::formatNumber($value['stok_exp']).' '.$value['satuan_kecil'];
                $value['jumlah_harganetto'] = DocoHelpers::formatNumber($value['jumlah_harganetto']);
                $value['harganetto'] = DocoHelpers::formatNumber($value['harganetto']);
                $value['qty_pemusnahan'] = "<div class='input-group' style='width: 150px'><input type='text' class='form-control qty_pemusnahan doco-number' data-index='{$key}' style='width: 100px' disabled='true'><span class='input-group-addon' style='border:none; background: none'>{$value['satuan_kecil']}</span></div>";
                $value['subtotal'] = "<span data-index='{$key}' class='total_netto total_netto-{$key}'>0</span>";
                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
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

    public function actionSave()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $model = new PemusnahanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->load($request->post());

        $list = json_decode($request->post('detail', '{}'));
        $_ruanganId = Yii::$app->docoVars->workspace("ruangan_id");
        $model->detail = json_encode($list);
        $model->total_netto = $request->post('totalharga_netto', 0);
        if ($model->validate()) {
            try {
                $result = $this->_restGudang->post('pemusnahan-obat/save',[
                                'form_params' => $model->attributes
                            ]);
                $response = json_decode($result->getBody(),true);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response = ['message' => $e->getMessage()];
            }
        } else {
            $response = $model->errors;
        }

        return DocoHelpers::response($response,422,$formName);
    }

    public function actionExportPrint($id)
    {
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/pemusnahan-obat-alkes.pdf";
        try {
            $response = $this->_restGudang->get('pemusnahan-obat/print', [
                'query' => [
                    'id' => $id
                ],
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
