<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-13 16:53:05
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 14:36:10
 */

namespace Doco\penjaminasuransi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\Services\Penjamin\GetInitFilterService;
use Doco\penjaminasuransi\models\PenerimaanPembayaranForm;
use Doco\penjaminasuransi\models\AlokasiPengajuanFormEdit;

class InformasiPenerimaanPembayaranController extends DocoController
{
    protected $_title = "Informasi Penerimaan Pembayaran Klaim";
    protected $_module = 'penjamin-asuransi/informasi-penerimaan-pembayaran/';
    protected $_restPenjamin;

    public function init()
    {
        parent::init();
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
    }

    public function actionIndex()
    {
        try {
            $resultInitFilter     = (new GetInitFilterService)->execute();
            $response             = $resultInitFilter;
            $response['penjamin'] = [];
            $cara_bayar           = isset($response['cara_bayar']) ? ArrayHelper::map($response['cara_bayar'], 'carabayar_id', 'carabayar_nama') : [];
            $penjamin             = isset($response['penjamin']) ? ArrayHelper::map($response['penjamin'], 'penjamin_id', 'penjamin_nama') : [];
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = $pengajuan = [];
        }
        
        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
            $response = $this->_restPenjamin->request('get', 'inf-penerimaan-pembayaran/index', [
                'query' => $filter
            ]);
            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['terimabayarklaim_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['status_alokasi'] = $value['final_alokasi'];
                $value['final_alokasi'] = isset(DocoConstants::$statusAlokasi[$value['final_alokasi']]) 
                    ? DocoConstants::$statusAlokasi[$value['final_alokasi']] : null ;
                $value['tgl_terimabayarklaim'] = date('d-M-Y', strtotime($value['tgl_terimabayarklaim']));
                $value['total_terimabayar_label'] = DocoHelpers::rupiahDisplay($value['total_terimabayar']);
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    
    public function actionGetPenjamin($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPenjamin->get('allow/get-penjamin'. $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['penjamin_id'],
                    'name' => $value['penjamin_nama']
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

    public function actionGetCarabayar($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPenjamin->get('allow/get-carabayar?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['carabayar_id'],
                    'name' => $value['carabayar_nama']
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

    public function actionEditPenerimaan($id)
    {
        $title = $this->_title;
        $model = new PenerimaanPembayaranForm;
        $modelAjuan = new AlokasiPengajuanFormEdit;
        $carabayar = [];
        $penjamin = [];
        $penerimaDana = [];
        $statusAlokasi = false;
        try {
            $response = $this->_restPenjamin->get('inf-penerimaan-pembayaran/get-attributes',[
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $header = $body['response']['header'];
            $statusAlokasi = $header['final_alokasi'];
            $carabayar = $body['response']['cara_bayar'];
            $model->attributes = $header;
            $penjamin[$header['penjamin_id']]= $header['penjamin_nama'];
            $penerimaDana[$header['pegawaipenerima_id']]= $header['pegawai_penerima'];
            Yii::$app->session['data-penerimaan-edit'] = $body['response']['detail'];
            $tmp = [];
            foreach ($body['response']['detail'] as $value) {
                $tmp[$value['pengajuanklaim_id']] = 1;
            }
            Yii::$app->session['data-penerimaan-list'] = $tmp;
        } catch (RequestException $e) {
            $carabayar = [];
        }
        return $this->render('form', get_defined_vars());
    }

    public function actionBatalPenerimaan($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restPenjamin->post('inf-penerimaan-pembayaran/batal-penerimaan',[
                'query' => [
                    'id' => $id
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'text' => $e->getMessage()
            ],422);
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'inf-penerimaan-pembayaran/export-excel';
        $path = Yii::getAlias("@download") . "/Informasi Penerimaan Pembayaran.xlsx";

        try {
            $this->_restPenjamin->get($url, [
                'save_to' => $path,
                'query'   => $yiiRestfulParams
            ]);

            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/inf-penerimaan-pembayaran.pdf";
            $response = $this->_restPenjamin->get('inf-penerimaan-pembayaran/export-pdf',[
                'save_to' => $path,
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakPenerimaan($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $path = Yii::getAlias("@download") . "/Detail Penerimaan Pembayaran.pdf";
            $response = $this->_restPenjamin->get('inf-penerimaan-pembayaran/cetak-penerimaan',[
                'save_to' => $path,
                'query' => ['id' => $id]
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionGetSession()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $result = [];
        $data = [];
        $draw = $request->get('draw', 1);
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        // $session['data-penerimaan'] = [];
        if(!isset($session['data-penerimaan-edit']) ){
            return DocoHelpers::response($result);
        }

        $rawData = $session['data-penerimaan-edit'];
        
        $no = 1;
        foreach ($rawData as $key => $value) {
            $newData = $value;
            $newData['rowNum'] = $no;
            $newData['total_pengajuan'] = ArrayHelper::getValue($value, 'total_pengajuan', 0);
            $newData['total_terbayar'] = ArrayHelper::getValue($value, 'total_terbayar', 0);
            $newData['pembayaran'] = ArrayHelper::getValue($value, 'pembayaran', 0);
            $newData['total_sisapiutang'] = ArrayHelper::getValue($value, 'total_sisapiutang', 0);
            $newData['no_sep'] = ArrayHelper::getValue($value, 'no_sep');
            $newData['riil_rs'] = ArrayHelper::getValue($value, 'riil_rs', 0);
            $newData['aksi'] = '<button type="button" data-key="'.$key.'" class="btn btn-danger btn-xs btn-hapus"><i class="fa  fa-trash"></i></button>';
            $data[] = $newData;
            $no++;
        }
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        return DocoHelpers::response($result);
    }

    public function actionHapusAjuan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $session = Yii::$app->session;
        if (isset($post['key'])) {
            $sessionData = $session['data-penerimaan-edit'];
            unset($sessionData[$post['key']]);
            Yii::$app->session['data-penerimaan-edit'] = $sessionData;
            return true;
        }
    }

    public function actionAddAjuan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new AlokasiPengajuanFormEdit;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->load($post);
        if($model->validate()){
            $session = Yii::$app->session;
            $sessionData = isset($session['data-penerimaan-edit']) ? $session['data-penerimaan-edit'] : [];
            $sessionData[] = $model->attributes;
            Yii::$app->session['data-penerimaan-edit'] = $sessionData;
            return true;
        }else{
            $errors = DocoHelpers::parseError($model->errors, $formName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }
    }
    
    public function actionSave($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $model = new PenerimaanPembayaranForm;
        $model->load($request->post());
        $session = Yii::$app->session;
        $model->data_pengajuan = !empty($session['data-penerimaan-edit']) ? $session['data-penerimaan-edit'] : null;
        $model->carabayar_id = empty($model->carabayar_id) ? $request->post('cara_bayar') : $model->carabayar_id;
        $model->penjamin_id = empty($model->penjamin_id) ? $request->post('penjamin') : $model->penjamin_id;
        if ($model->validate()) {
            try {
                $model->no_terimabayarklaim = trim($model->no_terimabayarklaim);
                $response = $this->_restPenjamin->post('inf-penerimaan-pembayaran/save',[
                    'form_params' => $model->attributes,
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response,false,'PenerimaanPembayaranForm');
            } catch (RequestException $e) {
                return DocoHelpers::response([
                    'text' => $e->getMessage()
                ],422);
            }
        } else {
            return DocoHelpers::response($model->errors,422,'PenerimaanPembayaranForm');
        }
    }

    public function actionShowPopupDetail()
    {
        $title = 'Unduh Excel';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $userIdentity = Yii::$app->session->get('user_identity');
        $nama_pegawai = isset($userIdentity['nama_pegawai']) ? $userIdentity['nama_pegawai'] : null;
        $_GET['nama_pegawai'] = $nama_pegawai;
        $_GET['randString'] = $randString;
        $get = $request->get();
        Yii::$app->session->setFlash($randString, $get);
        return $this->renderAjax('_modalDetail', get_defined_vars());
    }

    public function actionProcessSyncDetail($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restPenjamin, [
            'url' => "inf-penerimaan-pembayaran/cetak-penerimaan",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionDownloadFileDetail()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $noPembayaran = $request->get('no_pembayaran', null);
        $fileDownloads = 'Detail Penerimaan Pembayaran Klaim '.$noPembayaran.'.xlsx';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restPenjamin->get('inf-penerimaan-pembayaran/download-file-detail', [
            'query' => [
                'filename' => $filename,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopup($type)
    {
        $title = ($type == 1) ? 'Cetak PDF' : 'Unduh Excel';
        $request = Yii::$app->request;
        $advancedFilter = $request->get('advancedFilter', []);
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advancedFilter'] = $advancedFilter;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString, $type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        $session['type'] = $type;
        return $this->guzzleExec($this->_restPenjamin, [
            'url' => "inf-penerimaan-pembayaran/export-file",
            'payload' => [
            'query' => $session
            ],
        ]);
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', null);
        $filename = $request->get('fileName', null);
        $ext = ($type == 1) ? '.pdf' : '.xlsx';
        $fileDownloads = ($type == 1) ? $filename.$ext : 'Penerimaan Pembayaran'.$ext;
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restPenjamin->get('inf-penerimaan-pembayaran/download-file', [
            'query' => [
                'filename' => $filename,
                'type' => $type,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        if($type == 1) {
            return DocoHelpers::previewPdf($path);
        }
        else {
            return DocoHelpers::downloadFile($path,true);
        }
    }
}