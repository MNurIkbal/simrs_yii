<?php
/** Informasi Pasien Uang Muka
 * Author : zn
 */
namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DHtml;

class LapUangMukaPasienController extends DocoController
{
    protected $_title = "Laporan Pembayaran Uang Muka";
    protected $_module = 'kasir/lap-uang-muka-pasien/';
    protected $_restKasir;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $restKasir = Yii::$app->docoRest->kasir;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restKasir->get('lap-uang-muka-pasien/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_uangmuka'] = isset($value['tgl_uangmuka']) ?  date("d-M-Y",strtotime($value['tgl_uangmuka'])) : '-';
                $value['no_uangmuka'] = isset($value['no_uangmuka']) ?  $value['no_uangmuka'] : '-';
                $value['no_pendaftaran'] = isset($value['no_pendaftaran']) ?  $value['no_pendaftaran'] : '-';
                $value['no_rekam_medik'] = isset($value['no_rekam_medik']) ?  $value['no_rekam_medik'] : '-';
                $value['nama_pasien'] = isset($value['nama_pasien']) ?  $value['nama_pasien'] : '-';
                $value['pasien_rekam_medik'] = $value['nama_pasien'].' '.$value['no_rekam_medik'];
                $value['jumlah_uangmuka'] = isset($value['jumlah_uangmuka']) ?  DocoHelpers::formatNumber($value['jumlah_uangmuka']) : 0;
                if (isset($value['nama_bank'])){
                    $nama_bank = $value['nama_bank'];
                    $jenis_transaksi = 'NON TUNAI';
                } else{
                    $nama_bank = '-';
                    $jenis_transaksi = 'TUNAI';
                };
                $value['nama_bank'] = $nama_bank;
                $value['jenis_transaksi'] = $jenis_transaksi;
                $data[]= $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionShowPopup()
    {
        $title = 'Laporan Pembayaran Uang Muka';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "lap-uang-muka-pasien/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Laporan Pembayaran Uang Muka.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('lap-uang-muka-pasien/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

}