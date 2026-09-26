<?php
/** Laporan Kunjungan Rumah Sakit
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

class LapDetailTagihanPasienController extends DocoController
{
    protected $_title = "Laporan Detail Tagihan Pasien";
    protected $_module = 'kasir/lap-detail-tagihan-pasien/';
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
            $response = $this->_restKasir->get('lap-detail-tagihan-pasien/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            // var_dump($yiiRestfulParams);
            // var_dump($body);die;
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_masuk'] = isset($value['tgl_masuk']) ? date("d-M-Y", strtotime($value['tgl_masuk'])):'';
                $value['tgl_keluar'] = isset($value['tgl_keluar']) ? date("d-M-Y", strtotime($value['tgl_keluar'])):'';
                $value['tgl_masuk_keluar'] = $value['tgl_masuk'] . ' - '. $value['tgl_keluar'];
                $value['no_pembayaran'] = isset($value['no_pembayaran']) ? $value['no_pembayaran']:'-';
                $value['no_rekam_medik'] = isset($value['no_rekam_medik']) ? $value['no_rekam_medik']:'-';
                $value['instalasi'] = isset($value['instalasi']) ? $value['instalasi']:'-';
                $value['ruangan'] = isset($value['ruangan']) ? $value['ruangan']:'-';
                $value['instalasi_nama'] = $value['instalasi'] . ' - '. $value['ruangan'];
                $value['no_pendaftaran'] = isset($value['no_pendaftaran']) ? $value['no_pendaftaran']:'-';
                $value['nama_pasien'] = isset($value['nama_pasien']) ? $value['nama_pasien']:'-';
                $value['nama_pasien_medik'] = $value['nama_pasien'].' - '.$value['no_rekam_medik'];
                $value['cara_bayar'] = isset($value['cara_bayar']) ? $value['cara_bayar']:'-';
                $value['penjamin'] = isset($value['penjamin']) ? $value['penjamin']:'-';
                $value['carabayar_nama'] = $value['cara_bayar'].' - '.$value['penjamin'];
                $value['total_tagihan'] = isset($value['total_tagihan']) ? DocoHelpers::formatNumber($value['total_tagihan']):0;
                $value['total_dijamin'] = isset($value['total_dijamin']) ? DocoHelpers::formatNumber($value['total_dijamin']):0;
                $value['total_dibayar'] = isset($value['total_dibayar']) ? DocoHelpers::formatNumber($value['total_dibayar']):0;

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
        $title = 'Laporan Detail Tagihan Pasien';
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
            'url' => "lap-detail-tagihan-pasien/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Laporan Detail Tagihan Pasien.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('lap-detail-tagihan-pasien/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
    
    public function actionFilters()
    {
        return $this->guzzleExec($this->_restKasir, [
            'url' => 'lap-detail-tagihan-pasien/filters',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }



}
