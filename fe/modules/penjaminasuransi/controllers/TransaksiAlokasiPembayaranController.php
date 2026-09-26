<?php

/**
* @author yaya
**/

namespace Doco\penjaminasuransi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\UploadedFile;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use Doco\penjaminasuransi\models\PenerimaanPembayaranForm;
use Doco\penjaminasuransi\models\AlokasiPengajuanForm;
use Doco\penjaminasuransi\models\TransaksiAlokasiForm;
use Doco\penjaminasuransi\models\TransaksiAlokasiImportForm;

class TransaksiAlokasiPembayaranController extends DocoController
{
    protected $_title;
    protected $_restPenjamin;
    protected $_module = '/penjaminasuransi/transaksi-alokasi-pembayaran/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Transaksi Alokasi Pembayaran');
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actions() {
        $actions = parent::actions();
        $action = [
            'index' => 'Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran\IndexAction',
            'download-template' => 'Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran\DownloadTemplateAction',
            'upload-template' => 'Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran\UploadTemplateAction',
            'upload-template-process' => 'Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran\UploadTemplateProcessAction',
            'upload-template-save' => 'Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran\UploadTemplateSaveAction',
            'get-data' => 'Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran\GetDataAction',
            'clear-details-session' => 'Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran\ClearDetailsSessionAction',
        ];
        return array_merge($actions, $action);
    }

    public function actionGetNoPengajuan()
    {
        $request = Yii::$app->request;
        $get = $request->get('q');
        try {
            $response = $this->_restPenjamin->get('transaksi-alokasi-pembayaran/get-no-pengajuan',[
                'query' => $get
            ]);
            $response = json_decode($response->getBody(),true);
            $data = [];
            foreach ($response['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['pengajuanklaim_id'],
                    'text' => $value['no_pengajuanklaim']
                ];
            }
            $return = [
                'result' => $data,
                'total_count' => count($data),
                'incomplete_results' => false,
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
                return DocoHelpers::response([
                    'text' => $e->getMessage()
                ],422);
        }
    }

    public function actionContentDetail()
    {
        $request = Yii::$app->request;
        $id = $request->get('no_pengajuan');
        if (empty($id)) {
            return "<h3 class='text-center'>Mohon Isi Data Filter Diatas !</h3>";
        };
        try {
            $response = $this->_restPenjamin->get('transaksi-alokasi-pembayaran/get-detail',[
                'query' => [
                    'id' => $id
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $header = $response['response']['header'];
            $model = new TransaksiAlokasiForm;
            $model->total_pengajuan = isset($header['total_piutang'])
                ? $header['total_piutang'] : 0;
            $model->total_terbayar = isset($header['total_terbayar'])
                ? $header['total_terbayar'] : 0;
            $model->sisa_piutang = $model->total_pengajuan - $model->total_terbayar;
            $id = DocoHelpers::encrypt($id);
            return $this->renderAjax('detail',get_defined_vars(), false, true);
        } catch (RequestException $e) {
            $header = [];
        }
    }

    public function actionGetNoPembayaran($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $get = $request->get('q');
        $get['id'] = $id;
        try {
            $response = $this->_restPenjamin->get('transaksi-alokasi-pembayaran/get-no-pembayaran',[
                'query' => $get
            ]);
            $response = json_decode($response->getBody(),true);
            $data = $match = [];
            foreach ($response['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['terimabayarklaim_id'],
                    'text' => $value['no_terimabayarklaim'],
                ];
                $match[$value['terimabayarklaim_id']] = $value['pembayaran'];
            }
            $return = [
                'result' => $data,
                'rawInfo' => $match,
                'total_count' => count($data),
                'incomplete_results' => false,
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
                return DocoHelpers::response([
                    'text' => $e->getMessage()
                ],422);
        }
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $model = new TransaksiAlokasiForm;
        $model->load($request->post());
        $model->total_alokasi = $request->post('total_alokasi',0);
        $model->detail_pembayaran = $request->post('data_alokasi',[]);
        if ($model->validate()) {
            try {
                $response = $this->_restPenjamin->post('transaksi-alokasi-pembayaran/save',[
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response,false,'TransaksiAlokasiForm');
            } catch (RequestException $e) {
                return DocoHelpers::response([
                    'messages' => $e->getMessage()
                ], 500);
            }
        } else {
            return DocoHelpers::response($model->errors, 422, 'TransaksiAlokasiForm');
        }
    }
}