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
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use app\components\DHtml;

class InfPasienBelumBayarController extends DocoController
{
    public $_title = "Informasi Pasien Belum Bayar";
    public $_module = 'kasir/inf-pasien-belum-bayar/';
    public $_restKasir; 

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
        $this->_restMaster = Yii::$app->docoRest->master; 
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
        $konfig = $this->getNominal();
        $nominalKonfig = isset($konfig['kelola_tagihan']) ? $konfig['kelola_tagihan'] : 0;
        $disableNominal = '';
        if(empty($nominalKonfig) || $nominalKonfig == 0) {
            $disableNominal = 'disabled';
        }
        $isRawatInap = 'no';
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $roleHideBtn = $this->actionAksesPembayaran();
        $roleHideBtn = ($roleHideBtn) ? '' : 'display:none';
        $response = $this->_restKasir->get('allow/get-api');
        $body = json_decode($response->getBody(), TRUE);

        $resMaster = isset($body['response']['master']) ? $body['response']['master'] : [];
        $btnInvoice = Yii::$app->docoPlugin->execute($this, 'button_detail_rincian');

        //get kode ruangan
        /*
        $response_ruangan = $this->_restKasir->get('inf-pasien-belum-bayar/get-kode-ruangan', [
            'query' => [
                'kode' => 'ruangan_kasir_ri',
            ],
        ]);
        $kode_ruangan = json_decode($response_ruangan->getBody(), True);
        if(!empty($kode_ruangan['response']['kode_ruangan'])) {
            $isRawatInap = ($ruangan_id == $kode_ruangan['response']['kode_ruangan']) ? 'yes' : 'no';
        }
        */

        return $this->render('index', get_defined_vars());
    }


    public function actionCetakDetailInvoice($id, $invoice_id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        if(!is_numeric($invoice_id)) {
            $invoice_id = DocoHelpers::decrypt($invoice_id);
        }
        $path = Yii::getAlias("@download")."/cetak-detail-invoice.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $response = $this->_restKasir->get('tagihan-pasien/detail-invoice', [
            'query' => [
                'id' => $id,
                'invoice_id' => $invoice_id,
                'nama_pegawai' => $userIdentity['nama_pegawai']
            ],
            'save_to' => $path
        ]);

        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $isPasienTitipan = $request->get('is_pasientitipan', null);
        $caraBayarId = $request->get('caraBayar', null);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $yiiRestfulParams['ruangan_id'] = $ruangan_id;
        $yiiRestfulParams['is_pasientitipan'] = $isPasienTitipan;
        if(!empty($caraBayarId)){
            $yiiRestfulParams['carabayar_id'] = $caraBayarId; 
        }
        try {
            $response = $this->_restKasir->get('inf-pasien-belum-bayar/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['tgl_pendaftaran'] = date("j M Y", strtotime($value['tgl_pendaftaran'])).'<br/>'.date("H:i:s", strtotime($value['tgl_pendaftaran']));
                $value['nama_pasien'] = $value['nama_pasien'].'<br/>'.$value['no_rekam_medik'].'<br/>'.date("j M Y", strtotime($value['tanggal_lahir']));
                $value['penjamin_nama'] = $value['carabayar_nama'].'<br/>'.$value['penjamin_nama'];
                $value['ruangan_nama'] = $value['instalasi_nama'].'<br/>'.$value['ruangan_nama'];
                $value['limit_tagihan'] = DocoHelpers::formatNumber($value['limit_tagihan']);
                $value['total_tagihan'] = DocoHelpers::formatNumber($value['total_tagihan']);
                $value['uang_muka'] = $value['uang_muka'] != Null ? DocoHelpers::formatNumber($value['uang_muka']) : 0;
                $value['uang_masuk'] = DocoHelpers::formatNumber($value['uang_masuk']);
                $value['sisa_tagihan'] = DocoHelpers::formatNumber($value['sisa_tagihan']);
                $value['carabayar_kode_warna'] = $value['carabayar_kode_warna'] ? $value['carabayar_kode_warna'] : null;
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
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

    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "inf-pasien-belum-bayar/export-excel?".http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/inf-pasien-belum-bayar.xlsx";
        try {
            $response = $this->_restKasir->get($url,[
                'save_to' => $path,
            ]);

            $body = json_decode($response->getBody(), True);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }

    public function actionCetakRincian($id, $instalasi_id)
    {
        return Yii::$app->docoPlugin->execute($this, 'cetak_rincian');
    }

    public function actionCetakDetailRincian($id, $instalasi_id)
    {
        $uid = $user_login = Yii::$app->user->identity->loginpemakai_id;
        $params = 'id=' . $id . '&loginpemakai_id=' . $uid;
        return Yii::$app->report->exec('detail-rincian?'.$params);
    }

    private function getNominal()
    {
        try {
            $response = $this->_restKasir->get('inf-pasien-belum-bayar/get-konfig-system');
            $body = json_decode($response->getBody(), True);
            return $body['response'];
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }

    public function actionShowPopup()
    {
        return Yii::$app->docoPlugin->execute($this, 'cetak_detail_rincian');
    }

    public function actionShowPopupDetailDesigner()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $is_detail = (!empty($get['isdetail']) && $get['isdetail'] == 'true') ? 1 : 0;
        $title = ($is_detail == 1) ? 'Jenis Detail Invoice' : 'Jenis Summary';
        $model = new \yii\base\DynamicModel(['jenis_invoice', 'id', 'pendaftaran_id', 'penjamin_id', 'nama_pegawai']);
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model
            ->addRule(['jenis_invoice', 'id', 'pendaftaran_id', 'penjamin_id'], 'integer')
            ->addRule(['jenis_invoice'], 'required');
        $model->attributes = $get;
        $model->jenis_invoice = 1;
        $jenis_invoice = [1 => 'Lengkap', 2 => 'Pasien', 3 => 'Penjamin'];
        $groupUmum = DocoConstants::GROUP_UMUM;
        $pendaftaran_id = $request->get('id', null);
        $instalasi_id = $request->get('instalasi_id', null);
        $listPenjamin = $this->getPenjamin($pendaftaran_id);
        $listPenjamin = !empty($listPenjamin) ? $listPenjamin : [];
        if(empty($groupcarabayar_id)) {
            $groupcarabayar_id = isset($listPenjamin[0]['groupcarabayar_id']) ? $listPenjamin[0]['groupcarabayar_id'] : null;
        }
        if(!empty($listPenjamin)) {
            $listPenjamin = ArrayHelper::map($listPenjamin, 'id_payer', 'nama_payer');
        }
        $is_bgprocess = $request->get('is_bgprocess', true);
        $is_invoice = $request->get('is_invoice', false);
        if($is_invoice){
            $title = 'Jenis Invoice';
        }

        return $this->renderAjax('invoice_detail', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "tagihan-pasien/cetak-detail-rincian",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionShowPopupExcel($action = null)
    {
        $title = 'Informasi Pasien Belum Bayar';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        $url = '/kasir/inf-pasien-belum-bayar/process-sync-excel?randString=' .$randString;
        $pendaftaranId = $request->get('pendaftaran_id', null);
        if(!empty($action) && !empty($pendaftaranId) && $action == 'detail'){
            $url = '/kasir/inf-pasien-belum-bayar/process-sync-excel?randString=' .$randString.'&action=detail&pendaftaran_id=' . $pendaftaranId;
        }
        return $this->renderAjax('_modal_excel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $action = $request->get('action',null);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        $session['params'] = $request->get();
        $url = "inf-pasien-belum-bayar/export-excel-bgprocess";
        if(!empty($action) && $action == 'detail'){
            $url = "inf-pasien-belum-bayar/export-excel-detail-bgprocess";
        }
        return $this->guzzleExec($this->_restKasir, [
            'url' => $url,
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Informasi Pasien Belum Bayar.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('inf-pasien-belum-bayar/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
    
    public function actionGetBulkBiayaAdmin()
    {
        $request = Yii::$app->request;
        $post = $request->post('payload',[]);
        $data =  $this->guzzleExec($this->_restKasir, [
            'url' => "inf-pasien-belum-bayar/get-bulk-biaya-admin",
            'method' => 'post',
            'payload' => [
                'form_params' => $post
            ],
        ]);
        // $data = $this->_restKasir->request('POST', 'inf-pasien-belum-bayar/get-bulk-biaya-admin',[
        //     'form_params'=> [
        //         'payload'=> $post
        //     ],
        // ]);
        return DocoHelpers::responseTemplate(200, '', $data);

    }

    private function getPenjamin($pendaftaran_id)
    {
        return $this->guzzleExec($this->_restKasir, [
            'url' => 'inf-pasien-belum-bayar/get-penjamin',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id
                ]
            ]
        ]);
    }

    public function actionAksesPembayaran(){
        return DHtml::cekHakAkses('update');
    }
}
