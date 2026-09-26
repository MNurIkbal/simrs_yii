<?php 

/**
 * @author Budi
 * @todo Transaksi Pemberian Piutang
 * @copyright 12 Desember 2019
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\kasir\models\PembayaranPiutangForm;
use app\modules\kasir\models\BatalPiutangForm;

class InformasiPemberianPiutangController extends DocoController
{
    protected $_title = "Informasi Pemberian Piutang";
    protected $_module = '/kasir/informasi-pemberian-piutang';
    protected $_restKasir; 
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
    }

    
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $title = $this->_title;
        $status = $this->getStatus();
        $roleBatalBtn = DHtml::cekHakAkses('batal');
        $roleBatalBtn = ($roleBatalBtn) ? '' : 'display:none';
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
            $response = $this->_restKasir->get('informasi-pemberian-piutang/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            if(isset($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['pemberianpiutang_id']);
                    unset($value['pemberianpiutang_id']);
                    $value['primary'] = $primaryKey;
                    $value['rowNum'] = $no; 
                    $info_pasien = !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'].' - '.$value['nama_pasien'] : $value['no_pendaftaran'].' - '.$value['nama_pasien'];
                    $value['tagihan'] = DocoHelpers::formatNumber($value['tagihan']);
                    $value['total_bayarpiutang'] = DocoHelpers::formatNumber($value['total_bayarpiutang']);
                    $value['total_piutang'] = DocoHelpers::formatNumber($value['total_piutang']);
                    $value['total_sisapiutang'] = DocoHelpers::formatNumber($value['total_sisapiutang']);
                    $value['tgl_pemberianpiutang'] = date("j M Y", strtotime($value['tgl_pemberianpiutang']));
                    $value['pegawaidibebankan_nama'] = $value['pegawaidibebankan_nip'].' - '.$value['pegawaidibebankan_nama'];
                    $value['tgl_pendaftaran'] = date("j M Y", strtotime($value['tgl_pendaftaran']));
                    $value['tglpasienpulang'] = !empty($value['tglpasienpulang']) ? date("j M Y", strtotime($value['tglpasienpulang'])) : '-';
                    $value['info_pasien'] = $info_pasien;
                    $data[$key] = $value;
                }
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
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-pemberian-piutang.xlsx";
            $response = $this->_restKasir->get('informasi-pemberian-piutang/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
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

    private function getStatus()
    {
        $listData = [];
        try {
            $requests = $this->_restKasir->get('allow/get-status-lunas');
            $response = json_decode($requests->getBody(), true);
            
            return $response['response'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $listData['message'] = $e->getMessage();
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
            $listData['message'] = $e->getMessage();
        }
    }

    public function actionPembayaranPiutang($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $title = 'Transaksi Pembayaran Piutang';
        $model = new PembayaranPiutangForm;
        $model->tgl_pembayaranpiutang = date('d-m-Y');
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $data = $this->dataPemberianPiutang($id);
        $data['total_piutang'] = $data['total_bayarpiutang'] + $data['total_sisapiutang'];
        $metodeNonTunai = DocoConstants::METODE_NON_TUNAI;
        if($request->post()) {
            $model->load($request->post());
            $model->scenario = ($model->metode_pembayaran == $metodeNonTunai) ? 'non-tunai' : 'default';

            $model->pemberianpiutang_id = $id;
            $model->pendaftaran_id = $data['pendaftaran_id'];
            $model->pegawai_id = $data['pegawai_id'];
            if($model->validate()) {
                try {
                    $response = $this->_restKasir->post('informasi-pemberian-piutang/save-pembayaran', [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(), true);
                    return DocoHelpers::response($body);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            }
            else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }

        
        $data['tanggal_lahir'] = date('j M Y', strtotime($data['tanggal_lahir'])).' / '.DocoHelpers::getUmur($data['tanggal_lahir']);

        $data['tgl_pemberianpiutang'] =  date('j M Y', strtotime($data['tgl_pemberianpiutang']));
        $data['tgl_pendaftaran'] =  date('j M Y', strtotime($data['tgl_pendaftaran']));

        $model->attributes = $data;
        $model->catatan = $data['catatan'];
        return $this->render('pembayaran', get_defined_vars());
    }

    private function dataPemberianPiutang($id)
    {
        $listData = [];
        try {
            $requests = $this->_restKasir->get('informasi-pemberian-piutang/get-data-piutang?id='.$id);
            $response = json_decode($requests->getBody(), true);
            return $response['response'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $listData['message'] = $e->getMessage();
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
            $listData['message'] = $e->getMessage();
        }
    }

    public function actions()
    {
        /**
         * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
         * 
         * DATA ATTRIBUTE YANG BISA DIGUKANAN
         * 
         * --------------------------------------------------
         * data_name : nama kolom yang akan dimunculkan pada value text select2 (MAX 4 DATA)
         * keyField : sebagai id pada dropdown select2
         */
        return [
            'list-karyawan' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'pemberian-piutang/get-data-karyawan',
                'data_name' => [
                    'nomorindukpegawai',
                    'nama_pegawai'
                ],
                'keyField' => 'pegawai_id'
            ],
            'list-jenis' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-jenis',
                'data_name' => [
                    'nama'
                ],
                'keyField' => 'jenisnontunai_id'
            ],
            'list-metode-bayar' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-metode-bayar',
                'data_name' => [
                    'lookup_name'
                ],
                'keyField' => 'lookup_id'
            ]
        ];
    }

    public function actionCetakKwitansi()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        $path = Yii::getAlias("@download")."/cetak-kwitansi-pemberian-piutang.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $urlReport = 'kwitansi-pemberian-piutang';
        try {
            if(Yii::$app->report->isAvailable($urlReport)){
               return Yii::$app->report->exec($urlReport, [
                     'queryParameter' => [
                        'id' => $id,
                        'nama_pegawai' => $userIdentity['nama_pegawai'],
                     ],
               ]);
            }
            $response = $this->_restKasir->get('informasi-pemberian-piutang/cetak-kwitansi', [
                'query' => [
                    'id' => $id,
                    'nama_pegawai' => $userIdentity['nama_pegawai'],
                ],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @Author: [Bambang Hermawan][bambang.hermawan@sirs.co.id]
     * 
     * BG PROSES - DOWNLOAD EXCEL INFORMASI PEMBERIAN PIUTANG
     * 
     * --------------------------------------------------
     */

    public function actionShowPopupExcel()
    {
        $title = 'Cetak Informasi Pemberian Piutang';
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
        return $this->guzzleExec($this->_restKasir, [
            'url' => "informasi-pemberian-piutang/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Informasi Pemberian Piutang.xlsx';

        $path = Yii::getAlias("@download") . '/' . $fileDownloads;
        $response = $this->_restKasir->get('informasi-pemberian-piutang/download-file-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path, true);
    }

    public function actionBatal()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        $title = Yii::t('fe', 'Batal Pemberian Piutang');
        $batalForm = new BatalPiutangForm;
        $formName = substr(strrchr(get_class($batalForm), "\\"), 1);
        $username = Yii::$app->docoVars->user('nama');
        if ($request->post()) {
            $batalForm->load($request->post());
            $batalForm->tanggal_batal = date('Y-m-d H:i:s');
            if($batalForm->username != $username){
                $response['response']['title'] = 'Proses Gagal!';
                $response['response']['text'] = 'User/password tidak valid';
                return DocoHelpers::response($response,422);
            };
            if (!$batalForm->validate()) {
                $errors = DocoHelpers::parseError($batalForm->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
            $response = $this->_restKasir->post('informasi-pemberian-piutang/batal', [
                'form_params' => $batalForm->attributes
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        }
        else {
            return $this->renderAjax('_form_batal', get_defined_vars());
        }
    }
}