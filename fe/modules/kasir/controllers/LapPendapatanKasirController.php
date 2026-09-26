<?php
/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LapPendapatanKasirController extends DocoController
{
    protected $_title = "Laporan Rekapitulasi Pendapatan";
    protected $_module = 'kasir/lap-pendapatan-kasir/';
    protected $_restKasir;
    protected $allowAction = ['*'];

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
    
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = $this->setFilter();
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restKasir->get('lap-pendapatan-kasir/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $responseData = isset($body['response']) ? $body['response'] : [];
            foreach($responseData as $key => $value) {
                $daftartindakan_kode = isset($value['daftartindakan_kode']) ? $value['daftartindakan_kode'] : '';
                $no++;
                $value['kelompoktindakan_nama'] = strtoupper($value['kelompoktindakan_nama']);
                $value['daftartindakan_kode'] = $daftartindakan_kode;
                $value['rowNum'] = $no; 
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($responseData);
            $result['recordsFiltered'] = count($responseData);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function setFilter()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $tgl_pembayaran = $request->get('tgl_pembayaran', null);
        $tgl_pulang = $request->get('tgl_pulang', null);
        $unit = $request->get('unit', null);
        $kelas = $request->get('kelas', null);
        $kelompok = $request->get('kelompok', null);
        $tindakan = $request->get('tindakan', null);
        $namaKelas = $request->get('namaKelas', null);
        if(!empty($tgl_pembayaran) && $tgl_pembayaran != ' - ') {
            $yiiRestfulParams['advanced-filter']['tgl_pembayaran'] = $tgl_pembayaran;
        }
        if(!empty($tgl_pulang) && $tgl_pulang != ' - ') {
            $yiiRestfulParams['advanced-filter']['tgl_pulang'] = $tgl_pulang;
        }
        if(!empty($unit)) {
            $yiiRestfulParams['advanced-filter']['unit'] = $unit;
        }
        if(!empty($kelas)) {
            $yiiRestfulParams['advanced-filter']['kelas'] = $kelas;
        }
        if(!empty($kelompok)) {
            $yiiRestfulParams['advanced-filter']['kelompok'] = $kelompok;
        }
        if(!empty($tindakan)) {
            $yiiRestfulParams['advanced-filter']['tindakan'] = $tindakan;
        }
        if(!empty($namaKelas)) {
            $yiiRestfulParams['advanced-filter']['nama_kelas'] = $namaKelas;
        }
        return $yiiRestfulParams;
    }

    public function actionFilters()
    {
        return $this->guzzleExec($this->_restKasir, [
            'url' => 'lap-pendapatan-kasir/filters',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    public function actionShowPopup()
    {
        $title = 'Laporan Rekapitulasi Pendapatan';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = $this->setFilter();
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "lap-pendapatan-kasir/export-excel-bg-proses",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $date = date('dmY');
        $fileDownloads = "Laporan Rekapitulasi Pendapatan per Tindakan {$date}.xlsx";
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('lap-pendapatan-kasir/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $yiiRestfulParams = $this->setFilter();
        try {
            $date = date('dmY');
            $path = Yii::getAlias("@download") . "/Laporan Rekapitulasi Pendapatan per Tindakan {$date}.xlsx";
            $response = $this->_restKasir->get('lap-pendapatan-kasir/export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}