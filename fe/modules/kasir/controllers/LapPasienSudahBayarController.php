<?php
// Author : Ramdhan Nurrachman

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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\helpers\ArrayHelper;

class LapPasienSudahBayarController extends DocoController
{
    protected $_title = "Laporan Pasien Sudah Bayar";
    protected $_module = 'kasir/lap-pasien-sudah-bayar/';
    protected $_restKasir; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; $this->_restMaster = Yii::$app->docoRest->master;
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
        $response = $this->_restKasir->get('lap-pasien-sudah-bayar/get-api');
        $body = json_decode($response->getBody(), TRUE);
        $resMaster = $body['response'];

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
            $response = $this->_restKasir->get('lap-pasien-sudah-bayar/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $pembayaranPelayananId = ArrayHelper::getValue($value, 'pembayaranpelayanan_id');
                $biayaAdministrasi = ArrayHelper::getValue($value, 'biaya_administrasi', 0);
                $totalTagihan = ArrayHelper::getValue($value, 'total_tagihan', 0);
                $totalTagihan = $totalTagihan + $biayaAdministrasi;
                $totalDijamin = ArrayHelper::getValue($value, 'subsidi_asuransi', 0);
                $totalDiskon = ArrayHelper::getValue($value, 'total_discountpembayaran', 0);
                $totalDibayar = $totalTagihan - $totalDijamin - $totalDiskon;
                $instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama');
                $ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama');
                $namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
                $noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
                $caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama');
                $penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
                $tglPembayaran = ArrayHelper::getValue($value, 'tgl_pembayaran');
                $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
                $tglPulang = !empty($value['tgl_pulang']) ? $value['tgl_pulang'] : $tglPendaftaran;

                $primaryKey = DocoHelpers::encrypt($pembayaranPelayananId);
                unset($value['pembayaranpelayanan_id']);
                $value['instalasi_nama'] = $instalasiNama. ' - '.$ruanganNama;
                $value['nama_pasien'] = $namaPasien;
                $value['carabayar_nama'] = $caraBayarNama. ' - '.$penjaminNama;
                $value['tgl_pembayaran'] = date("j M Y H:i:s", strtotime($tglPembayaran));
                $value['total_tagihan'] = DocoHelpers::formatNumber($totalTagihan);
                $value['subsidi_asuransi'] = DocoHelpers::formatNumber($totalDijamin);
                $value['total_sudah_dibayarkan'] = DocoHelpers::formatNumber($totalDibayar);
                $value['total_discount'] = DocoHelpers::formatNumber($totalDiskon);
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
                $value['tgl_masuk_keluar'] = date('d-M-Y', strtotime($tglPendaftaran)).' - '.date('d-M-Y', strtotime($tglPulang));
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

    // Beware Section Data Selection
    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $result = [];
        $url = "";
        try {
            $path = Yii::getAlias("@download") . "/lap-pasien-sudah-bayar.xlsx";
            $response = $this->_restKasir->get('lap-pasien-sudah-bayar/export-excel?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
            $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/laporan-pasien-sudah-bayar.pdf";
            $response = $this->_restKasir->get('lap-pasien-sudah-bayar/export-pdf', [
                'save_to' => $path,
                'query' => $filters
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
    private function downloadFile($filename)
    {
        $file = basename($filename);
        $fp = fopen($file, 'w');
        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'".xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;
    }

    public function actionFilters()
    {
        return $this->guzzleExec($this->_restKasir, [
            'url' => 'lap-pasien-sudah-bayar/filters',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    public function actionShowPopup()
    {
        $title = 'Laporan Pasien Sudah Bayar';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "lap-pasien-sudah-bayar/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }
    
    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);

        $fileDownloads = 'Laporan Pasien Sudah Bayar '.date("dmY").'.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('lap-pasien-sudah-bayar/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupPdf()
    {
        $title = 'Laporan Pasien Sudah Bayar';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('partial/_modal_pdf', get_defined_vars());
    }

    public function actionProcessSyncPdf($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "lap-pasien-sudah-bayar/export-pdf-bg-proses",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Laporan Pasien Sudah Bayar '. date("dmY").'.pdf';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('lap-pasien-sudah-bayar/download-file-pdf', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::previewPdf($path);
    }
}
