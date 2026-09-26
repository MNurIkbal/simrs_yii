<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Penyimpanan Dokumen Rm
 * @copyright 31 Mei 2018 aweutist
 */
namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rm\models\TransaksiPenyimpananDokumenForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class TransaksiPenyimpananController extends DocoController
{
	protected $_title = "Penyimpanan Dokumen Rekam Medis";
    protected $_module = 'rm/transaksi-penyimpanan/';
    protected $_restRm;
    protected $allowAction = [
        'index',
        'get-subrak'
    ];

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex($id)
    {
        $id = DocoHelpers::decrypt($id);
        $model = new TransaksiPenyimpananDokumenForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $model->load($post);
        if ($post) {
            if ($model->validate()) {
                $response = $this->_restRm->request('POST', 'transaksi-penyimpanan/save?id=' . $id, [
                    'form_params' => $post
                ]);
                $response = json_decode($response->getBody(), true);
            } else {
                $response = $model->errors;
            }

            return DocoHelpers::response($response, 422, 'TransaksiPenyimpananDokumenForm');
        } else {
            $data = $this->getData($id);
            // dump($data);exit;
            $data_norm = !empty($data['data_posisi'])
                ? ArrayHelper::map($data['data_posisi'], 'no_rekam_medik', 'no_rekam_medik')
                : [];
            $data_no_kirimdokrm = !empty($data['data_posisi'])
                ? ArrayHelper::map($data['data_posisi'], 'no_kirimdokrm', 'no_kirimdokrm')
                : [];
            $data_rak = !empty($data['data_rak'])
                ? ArrayHelper::map($data['data_rak'], 'lokasirak_id', 'lokasirak_nama')
                : [];
            $data_sub_rak = !empty($data['data_sub_rak'])
                ? ArrayHelper::map($data['data_sub_rak'], 'subrak_id', 'subrak_nama')
                : [];

            $model->attributes = $data['data_posisi_by_id'];
            $model->no_rekam_medik = $data['data_posisi_by_id']['no_rekam_medik'];
            $model->no_pengiriman = $data['data_posisi_by_id']['no_kirimdokrm'];
            $model->no_rak = $data['data_posisi_by_id']['lokasirak_id'];
            $model->no_sub_rak = $data['data_posisi_by_id']['subrak_id'];
            $model->tgl_akhir_masuk = date('d F Y', strtotime($data['data_posisi_by_id']['tglmasukrak']));
            $model->status_indexing = $data['data_posisi_by_id']['is_indexing'];
            $model->status_assembling = $data['data_posisi_by_id']['is_assembling'];
        }

        return $this->render('index', get_defined_vars());
    }

    private function getData($id)
    {
        try {
            $response = $this->_restRm->request('GET', 'transaksi-penyimpanan/get-api?id=' . $id);
            $body = json_decode($response->getBody(),TRUE);

            $return = [
                'data_posisi' => $body['response']['data-posisi'],
                'data_posisi_by_id' => $body['response']['data-posisi-by-id'],
                'data_rak' => $body['response']['data-rak'],
                'data_sub_rak' => $body['response']['data-sub-rak'],
            ];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetSubrak()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('transaksi-penyimpanan/get-subrak?lokasirak_id=' . $parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                'id' => $value['subrak_id'],
                'name' => $value['subrak_nama']
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
}