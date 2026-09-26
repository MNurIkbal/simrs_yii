<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\apotek\models\TransaksiResepForm;

class RiwayatObatPasienController extends DocoController
{

    protected $_title = "Riyawat Obat Pasien";
    protected $_module = '/apotek/riwayat-obat-pasien';
    protected $_restApotek;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $pasien_id = $request->get('pasien_id');
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

            $no = 0;
            $draw = $request->get('draw', 1);
            $response = $this->_restApotek->get('riwayat-obat-pasien/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $list_obat = $body['response']['data'];
            $data = [];
            foreach ($list_obat as $key => $obat) {
                $no++;
                $primaryKey = isset($obat['obatalkespasien_id']) ? $obat['obatalkespasien_id'] : $no;
                $obat['tgl_transaksi'] = date('d M Y', strtotime($obat['tgl_transaksi']));
                $obat['instalasi_ruangan'] = $obat['instalasi_nama']. " - " .$obat['ruangan_nama'];
                $obat['carabayar_penjamin'] = $obat['carabayar_nama']. " - " .$obat['penjamin_nama'];
                $obat['rowNum'] = $no;
                $data[$key] = $obat;
            }

            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionSearchPasien()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('term', '');

        try {
            $response = $this->_restApotek->get('riwayat-obat-pasien/search-pasien?term='.$term, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];

            $list = [];
            foreach ($data as $value) {
                $item = [
                    "id" => $value['pasien_id'],
                    "text" => $value['nama_pasien'] ." / ". $value['no_rekam_medik'] ." / ". date('d M Y', strtotime($value['tanggal_lahir'])),
                ];
                $list[] = $item;
            }

            return [
                'list' => $list            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionSearchObat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('term', '');

        try {
            $response = $this->_restApotek->get('riwayat-obat-pasien/search-obat?term='.$term, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];

            $list = [];
            foreach ($data as $value) {
                $item = [
                    "id" => $value['obatalkes_id'],
                    "text" => $value['obatalkes_nama'],
                ];
                $list[] = $item;
            }

            return [
                'list' => $list            ];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Riwayat Obat Pasien Excel';
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
            'url' => "riwayat-obat-pasien/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'riwayat-obat-pasien.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = Yii::$app->docoRest->apotek->get('riwayat-obat-pasien/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

}