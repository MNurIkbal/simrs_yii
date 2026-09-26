<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LaporanMutasiObatAlkesController extends DocoController
{
	protected $allowAction = ['*'];
    public $_title = "Laporan Mutasi Obat Alkes";
    public $_module = '/gudang/laporan-mutasi-obat-alkes/';

    protected $_restGudang;

    public function init() {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actions() {
        return [
            'index'         => 'Doco\gudang\actions\LaporanMutasiObatAlkes\IndexAction',
            'get-list-data' => 'Doco\gudang\actions\LaporanMutasiObatAlkes\GetListDataAction'
        ];
    }

    public function actionShowPopupExcel()
    {
        $title = Yii::t('fe', 'Cetak Laporan Mutasi Obat Alkes');
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams = $this->filter($yiiRestfulParams);
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->guzzleExec($this->_restGudang, [
            'url' => "lap-mutasi-obat-alkes/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Mutasi Obat Alkes.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->guzzleExec($this->_restGudang,[
            'url' => 'lap-mutasi-obat-alkes/download-file',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $filter['advanced-filter']['tgl_pengiriman_awal'] = date('Y-m-d 00:00:00');
        $filter['advanced-filter']['tgl_pengiriman_akhir'] = date('Y-m-d 23:59:00');

        if(isset($filter['advanced-filter']['tgl_pengiriman'])) {
            $explode = explode(' - ', $filter['advanced-filter']['tgl_pengiriman']);

            $filter['advanced-filter']['tgl_pengiriman_awal'] = date('Y-m-d H:i:s', strtotime($explode[0] . '00:00:00'));
            $filter['advanced-filter']['tgl_pengiriman_akhir'] = date('Y-m-d H:i:s', strtotime($explode[1] . '23:59:59'));
            $filter['advanced-filter']['filter_tgl_pengiriman'] = $filter['advanced-filter']['tgl_pengiriman'];

            unset($filter['advanced-filter']['tgl_pengiriman']);
        }

        if(isset($filter['advanced-filter']['ruangan_pengirim'])) {
            $filter['advanced-filter']['ruanganpengirim_id'] = $filter['advanced-filter']['ruangan_pengirim'];

            unset($filter['advanced-filter']['ruangan_pengirim']);
        }

        if(isset($filter['advanced-filter']['ruangan_penerima'])) {
            $filter['advanced-filter']['ruanganpenerima_id'] = $filter['advanced-filter']['ruangan_penerima'];

            unset($filter['advanced-filter']['ruangan_penerima']);
        }

        if($ruangan_id != DocoConstants::GUDANG_FARMASI) {
            $filter['advanced-filter']['ruanganpengirim_id'] = $ruangan_id;
        }

        try {
            $path = Yii::getAlias("@download") . "/laporan-mutasi-obat-alkes.xlsx";
            $response = Yii::$app->docoRest->gudang->get('lap-mutasi-obat-alkes/export-excel', [
                'save_to' => $path,
                'query' => $filter
            ]);

            return DocoHelpers::downloadFile($path,true);
       } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
       } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
       }
    }

    private function filter($filter)
    {
        $filter['advanced-filter']['tgl_pengiriman_awal'] = date('Y-m-d 00:00:00');
        $filter['advanced-filter']['tgl_pengiriman_akhir'] = date('Y-m-d 23:59:00');

        if(isset($filter['advanced-filter']['tgl_pengiriman'])) {
            $explode = explode(' - ', $filter['advanced-filter']['tgl_pengiriman']);

            $filter['advanced-filter']['tgl_pengiriman_awal'] = date('Y-m-d H:i:s', strtotime($explode[0] . '00:00:00'));
            $filter['advanced-filter']['tgl_pengiriman_akhir'] = date('Y-m-d H:i:s', strtotime($explode[1] . '23:59:59'));

            unset($filter['advanced-filter']['tgl_pengiriman']);
        }

        if(isset($filter['advanced-filter']['ruangan_pengirim'])) {
            $filter['advanced-filter']['ruanganpengirim_id'] = $filter['advanced-filter']['ruangan_pengirim'];

            unset($filter['advanced-filter']['ruangan_pengirim']);
        }

        if(isset($filter['advanced-filter']['ruangan_penerima'])) {
            $filter['advanced-filter']['ruanganpenerima_id'] = $filter['advanced-filter']['ruangan_penerima'];

            unset($filter['advanced-filter']['ruangan_penerima']);
        }

        if(isset($filter['advanced-filter']['jenisobatalkes_nama'])) {
            $filter['advanced-filter']['jenisobatalkes_id'] = $filter['advanced-filter']['jenisobatalkes_nama'];

            unset($filter['advanced-filter']['jenisobatalkes_nama']);
        }

        if(isset($filter['advanced-filter']['nama_obat'])) {
            $filter['advanced-filter']['obatalkes_id'] = $filter['advanced-filter']['nama_obat'];

            unset($filter['advanced-filter']['nama_obat']);
        }

        return $filter;
    }
}
