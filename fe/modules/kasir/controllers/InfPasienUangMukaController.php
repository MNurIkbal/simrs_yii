<?php
// Author : Ramdhan Nurrachman

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use Doco\kasir\models\PengembalianForm;
use app\modules\kasir\models\BatalBayarUangMukaForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use GuzzleHttp\Exception\RequestException;

class InfPasienUangMukaController extends DocoController
{
    protected $_title = "Informasi Uang Muka Pasien";
    protected $_module = 'kasir/inf-pasien-uang-muka/';
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
        $title = DHtml::getTitleMenu();
        if(empty($title)){
            $title = $this->_title;
        }
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
            $response = $this->_restKasir->get('inf-pasien-uang-muka/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                unset($value['pendaftaran_id']);
                $tglPulang = isset($value['tgl_pulang']) && !empty($value['tgl_pulang']) ? date('d-M-Y', strtotime($value['tgl_pulang'])) : '';
                $str = !empty($tglPulang) ? ' - ' : '';
                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $value['jumlah_uangmuka'] = isset($value['jumlah_uangmuka']) ? DocoHelpers::formatNumber($value['jumlah_uangmuka']) : 0;
                $value['pemakaian_uangmuka'] = isset($value['pemakaian_uangmuka']) ? DocoHelpers::formatNumber($value['pemakaian_uangmuka']): 0;
                $value['sisa'] = isset($value['sisa_uangmuka']) ? $value['sisa_uangmuka']: 0;
                $value['sisa_uangmuka'] = isset($value['sisa_uangmuka']) ? DocoHelpers::formatNumber($value['sisa_uangmuka']): 0;
                $value['pengembalian'] =isset($value['pengembalian']) ? DocoHelpers::formatNumber($value['pengembalian']): 0;
                $value['tgl_pendaftaran'] = !empty($value['tgl_pendaftaran']) ? date('d-M-Y', strtotime($value['tgl_pendaftaran'])) : '-';
                $value['tgl_masuk_keluar'] = date('d-M-Y', strtotime($value['tgl_pendaftaran'])).$str.$tglPulang;
                $value['tgl_pembayaran'] = isset($value['tgl_pembayaran']) && !empty($value['tgl_pembayaran']) ? date('d-M-Y', strtotime($value['tgl_pembayaran'])) : '-';
                $value['tgl_pulang'] = $tglPulang;
                $value['keterangan'] = isset($value['keterangan']) && !empty($value['keterangan']) ? wordwrap($value['keterangan'], 40, "<br />\n") : '';
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
    public function actionGetDetail($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $data = [];
        $decId = DocoHelpers::decrypt($id);
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restKasir->get('inf-pasien-uang-muka/detail?id='.$decId);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['bayaruangmuka_id']);
                unset($value['bayaruangmuka_id']);

                $value['tgl_uangmuka'] = date('d-M-Y', strtotime($value['tgl_uangmuka']));
                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $value['jumlah_uangmuka'] = DocoHelpers::formatNumber($value['jumlah_uangmuka']);
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

    public function actionGetDetailPengembalian($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $data = [];
        $decId = DocoHelpers::decrypt($id);
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restKasir->get('inf-pasien-uang-muka/detail-pengembalian?id='.$decId);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['tandabuktikeluar_id']);
                unset($value['tandabuktikeluar_id']);

                $value['tgl_pengembalian'] = date('d-M-Y', strtotime($value['tgl_buktikeluar']));
                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
                $value['jml_pengembalian'] = DocoHelpers::formatNumber($value['jml_pembayaran']);
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

    public function actionCancel($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restKasir->get('inf-pasien-uang-muka/cancel?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPendaftaran($assign_id="", $q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restKasir->get('pendaftaran?advanced-filter[no_pendaftaran]='.$q);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['results'][] = [
                    'id' => $assign_id? $value['pendaftaran_id'] : $value['no_pendaftaran'], 
                    'text' => $value['no_pendaftaran']
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

    public function actionPrintKwitansi()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-kwitansi.pdf";
        try {
            $id = $request->get('id',null);
            $decryptId = DocoHelpers::decrypt($id);
            $post = ['id'=>$decryptId];
            $response = $this->_restKasir
                        ->post('inf-pasien-uang-muka/print-kwitansi',
                            [
                                'form_params' => $post,
                                'save_to' => $path
                            ]);
            $body = json_decode($response->getBody(), True);
            
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {  
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) { 
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintBkm()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-bkm.pdf";
        try {
            $id = $request->get('id',null);
            $decryptId = DocoHelpers::decrypt($id);
            $post = ['id'=>$decryptId];
            $response = $this->_restKasir
                        ->post('inf-pasien-uang-muka/print-bkm',
                            [
                                'form_params' => $post,
                                'save_to' => $path
                            ]);                
            return DocoHelpers::previewPdf($path);
            // return DocoHelpers::response($body);
        } catch (RequestException $e) {  
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) { 
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintKwitansiKeluar()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-kwitansi.pdf";
        try {
            $id = $request->get('id',null);
            $decryptId = DocoHelpers::decrypt($id);
            $post = ['id'=>$decryptId];
            $response = $this->_restKasir
                        ->post('inf-pasien-uang-muka/print-kwitansi-keluar',
                            [
                                'form_params' => $post,
                                'save_to' => $path
                            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {  
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) { 
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionPrintBkmKeluar()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-bkm.pdf";
        try {
            $id = $request->get('id',null);
            $decryptId = DocoHelpers::decrypt($id);
            $post = ['id'=>$decryptId];
            $response = $this->_restKasir
                        ->post('inf-pasien-uang-muka/print-bkm-keluar',
                            [
                                'form_params' => $post,
                                'save_to' => $path
                            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {  
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) { 
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    //action buat handle data no pendaftaran
    public function actionGetPendaftaran()
    {
        try{
            if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
                $tgl_pendaftaran = '';
                if(isset($_GET['z'])){
                    $tgl_pendaftaran = $_GET['z'];
                }
                $response = $this->_restKasir->request('POST', 'inf-pasien-uang-muka/get-data-pendaftaran',[
                                'form_params'=>['term'=>$_GET['q']['term'], 'date'=>$tgl_pendaftaran],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id'=>$value['pendaftaran_id'],'text'=>$value['no_pendaftaran']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    public function actionDetail($id)
    {
        $title = "Detail Pembayaran Uang Muka Pasien";
        $_title = DHtml::getTitleMenu();
        if(empty($_title)){
            $_title = $this->_title;
        }
        $decId = DocoHelpers::decrypt($id);
        $body = [];
        try {
            $response = $this->_restKasir->get('inf-pasien-uang-muka/view', ['query'=>['id'=>$decId]]);
            $body = json_decode($response->getBody(), true);
            $header = $body['response']['header'];
            $dipakai_uangmuka = ($body['response']['uangmuka_dipakai']) ? 1 : 0;
            $pengembalian_uangmuka = $body['response']['uangmuka_dikembalikan'];
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
        return $this->render('detail', get_defined_vars());
    }
    public function actionPengembalian($id)
    {
        $title = "Pengembalian Uang Muka Pasien";
        $_title = DHtml::getTitleMenu();
        if(empty($title)){
            $_title = $this->_title;
        }
        $decId = DocoHelpers::decrypt($id);
        $model = new PengembalianForm;
        $body = [];
        $request = Yii::$app->request;
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $response = $this->_restKasir->request('POST', 'inf-pasien-uang-muka/pengembalian',[
                                'form_params'=>$model->attributes
                            ]); 
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body,false);
            }else{
                return DocoHelpers::response($model->getErrors(), 422, 'PengembalianForm');
            }
        }

        try {
            $response = $this->_restKasir->get('inf-pasien-uang-muka/pack-pengembalian', ['query'=>['id'=>$decId]]);
            $body = json_decode($response->getBody(), true);
            $header = $body['response']['data']['header'];
            $konfig = isset($body['response']['konfig']) ? $body['response']['konfig'] : [];
            $model->pendaftaran_id = $decId;
            $model->pegawai1_id = Yii::$app->user->id;
            $model->tgl_pengembalian = date('Y-m-d H:i:s');
            $model->ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $model->total_pengembalian = $model->uang_diterima = isset($header['sisa_uangmuka']) ? $header['sisa_uangmuka'] : 0;
            $pemakaian_uangmuka = isset($header['pemakaian_uangmuka']) ? $header['pemakaian_uangmuka'] : 0;
            $jumlah_uangmuka = isset($header['jumlah_uangmuka']) ? $header['jumlah_uangmuka'] : 0;
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
        return $this->render('pengembalian', get_defined_vars());
    }
    public function actionCancelUangMuka()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        if(!empty($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        $pendaftaran_id = $request->get('pendaftaran_id', null); 
        $title     = Yii::t('fe', 'Pembatalan Uang Muka');
        $is_tunai = $this->getJenisPembayaran();
        $batalForm = new BatalBayarUangMukaForm;
        $formName = substr(strrchr(get_class($batalForm), "\\"), 1);
        if ($request->post()) {
            $batalForm->load($request->post());
            
            if ($batalForm->validate()) {
                if(!empty($batalForm->bayaruangmuka_id)) {
                    $id = $batalForm->bayaruangmuka_id;
                }
                if(!empty($batalForm->pendaftaran_id)) {
                    $pendaftaran_id = DocoHelpers::decrypt($batalForm->pendaftaran_id);
                }
                try {
                    $response = $this->_restKasir->get('inf-pasien-uang-muka/batal-uang-muka', [
                        'query' => [
                            'id' => $id,
                            'pendaftaran_id' => $batalForm->pendaftaran_id,
                            'alasan_batal' => $batalForm->alasan_batal,
                            'is_tunai' => $batalForm->is_tunai,
                            'username' => $batalForm->username,
                            'password' => $batalForm->password,
                        ]
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response);

                } catch (RequestException $e) {
                    $response = json_decode($e->getResponse()->getBody(),true);
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    \Yii::$app->response->statusCode =500;
                    return ['response'=>[
                        'title'=>'Terjadi Kesalahan',
                        'message'=>'gagal',
                        'text'=>$response['response']
                    ]];
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(422, 'Error', $e->getMessage());

                }
            }else{
                $errors = DocoHelpers::parseError($batalForm->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }else{
            return $this->renderAjax('_form_batal', get_defined_vars());
        }
    }

    public function actionShowPopup()
    {
        $title = 'Unduh Excel Uang Muka Pasien';
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
            'url' => "inf-pasien-uang-muka/export-excel",
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
        $fileDownloads = "Informasi Uang Muka {$date}.xlsx";
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('inf-pasien-uang-muka/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    private function getJenisPembayaran()
    {
        $jenis_pembayaran = $this->_options['jenis_pembayaran'];
        if(!empty($jenis_pembayaran)) {
            foreach ($jenis_pembayaran as $key => $value) {
                if($key === "") {
                    unset($jenis_pembayaran[$key]);
                }
            }
        }

        return $jenis_pembayaran;
    }
}
