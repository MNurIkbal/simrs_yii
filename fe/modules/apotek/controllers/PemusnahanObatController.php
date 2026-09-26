<?php

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;

use Doco\apotek\models\PemusnahanForm;
use Doco\apotek\models\MutasiPemusnahanForm;

class PemusnahanObatController extends DocoController
{

    public $_title = "Obat Alkes Expired";
    protected $_module = '/apotek/pemusnahan-obat';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $instalasi = $ruangan = [];
        try {
            $response = $this->_restApotek->get('pemusnahan-obat/get-options');
            $response = json_decode($response->getBody(),true);
            $instalasi = $response['response']['instalasi'];
            $ruangan = $response['response']['ruangan'];
            $date_range = $response['response']['date_range'];
        } catch (RequestException $e) {

        } catch (\Exception $e) {

        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $request->get('draw', 1);
        $result['recordsTotal'] = 0;

        try {
            $dataObatExpired = $this->guzzleExec($this->_restApotek, [
                'url' => 'pemusnahan-obat',
                'payload' => [
                    'query' => $yiiRestfulParams
                ]
            ]);

            $no = $request->get('start', 1);
            foreach ($dataObatExpired['data'] as $key => $value) {
                $no++;
                $primaryKey = $no;
                $value['tglkadaluarsa'] = date('d-M-Y', strtotime($value['tglkadaluarsa']));
                $value['stok_display'] = DocoHelpers::formatNumber($value['stok_exp']) . ' '. $value['satuan_kecil'];
                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
                $value['cost_wa_display'] = DocoHelpers::formatNumber($value['cost_wa']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $dataObatExpired['_meta']['totalCount'];
            $result['recordsFiltered'] = $dataObatExpired['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetObatAlkes()
    {
        $return = [];
        try {
            $request = Yii::$app->request;
            $response = $this->_restApotek->post('allow/get-data-obatalkes',[
                'form_params' => [
                    'term' => $_GET['q'],
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $data = [];
            foreach ($response['response'] as $value) {
                $data[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_namalain']
                ];
            }
            $return = [
                'results' => $data,
            ];
        } catch (RequestException $e) {
            $return = ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $return = ['message' => $e->getMessage()];
        }

        return DocoHelpers::response($return);
    }

    public function actionSetStorage($key = '-obat-expired')
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $_ruanganId = Yii::$app->docoVars->workspace("ruangan_id");
        if ($request->post()) {
            $session->set($_ruanganId . $key, $request->post());
        }
        return true;
    }

    public function actionView($id = null)
    {
        $title = 'Pemusnahan Obat Alkes Expired';
        $session = Yii::$app->session;
        $_ruanganId = Yii::$app->docoVars->workspace("ruangan_id");
        $model = new PemusnahanForm;
        $getListObat = $pegMengetahui = $pegMenyetujui = [];
        $model->tanggal_pemusnahan = date('d-M-Y');
        $isDisable = false;
        if (!empty($id)) {
            $isDisable = true;
            $id = DocoHelpers::decrypt($id);
            try {
                $response = $this->_restApotek->get('pemusnahan-obat/view',[
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $response = json_decode($response->getBody(),true);
                $header = isset($response['response']['header']) ? $response['response']['header'] : [];
                $model->tanggal_pemusnahan = date('d-m-Y',strtotime($header['tglpemusnahan']));
                $model->pegawai_meyetujui = $header['pegawaimenyetujui_id'];
                $model->pegawai_mengetahui = $header['pegawaimengetahui_id'];
                $getListObat = $response['response']['detail'];
            } catch (RequestException $e) {
                $pegMengetahui = $pegMenyetujui = [];
            }
            $id = DocoHelpers::encrypt($id);
            return $this->render('view', get_defined_vars());
        } else {
            try {
                $response = $this->_restApotek->get('pemusnahan-obat/get-pegawai');
                $body = json_decode($response->getBody(), true);
                $pegMenyetujui = isset($body['response']) ? ArrayHelper::map($body['response'], 'pegawai_id', 'nama_pegawai') : [] ;
                $pegMengetahui = isset($body['response']) ? ArrayHelper::map($body['response'], 'pegawai_id', 'nama_pegawai') : [] ;
            } catch (Exception $e) {
                $pegMengetahui = $pegMenyetujui = [];
            }
            $getListObat = $session->get($_ruanganId . '-obat-expired');

            if (!empty($getListObat)) {
                return $this->render('view', get_defined_vars());
            } else {
                return $this->redirect(['/apotek/pemusnahan-obat']);
            }
        }
    }

    public function actionGetPegawai()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restApotek->get('pemusnahan-obat/get-pegawai',[
                            'query' => [
                                'term' => $request->get('term'),
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nomorindukpegawai'] . ' - ' . $value['nama_pegawai'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }
    public function actionMutasi()
    {
        $title = "Mutasi Obat Alkes";
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $model = new MutasiPemusnahanForm;
        $_ruanganId = Yii::$app->docoVars->workspace("ruangan_id");
        $getListObat = $session->get($_ruanganId . '-mutasi-pemusnahan');
        $pegMengetahui = [];

        $model->tglmutasi = date('d-M-Y');
        try {
            $response = $this->_restApotek->get('pemusnahan-obat/get-pegawai');
            $body = json_decode($response->getBody(), true);
            $pegMengetahui = isset($body['response']) ? ArrayHelper::map($body['response'], 'pegawai_id', 'nama_pegawai') : [] ;
        } catch (Exception $e) {
            $pegMengetahui = [];
        }
        return $this->render('mutasi', get_defined_vars());
    }

    public function actionSimpanMutasi()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $model = new MutasiPemusnahanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->load($request->post());
        $_ruanganId = Yii::$app->docoVars->workspace("ruangan_id");
        $getListObat = $session->get($_ruanganId . '-mutasi-pemusnahan');
        $model->detail = $getListObat;
        $model->ruangan_id = $_ruanganId;
        if($_ruanganId == DocoConstants::GUDANG_FARMASI) {
            $model->ruangan_penerima_id = DocoConstants::GUDANG_PEMUSNAHAN;
        } else {
            $model->ruangan_penerima_id = DocoConstants::GUDANG_FARMASI;
        }
        if ($model->validate()) {
            try {
                $result = $this->_restApotek->post('pemusnahan-obat/save-mutasi',[
                                'form_params' => $model->attributes
                            ]);
                $response = json_decode($result->getBody(),true);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response = ['message' => $e->getMessage()];
            }
        } else {
            $response = $model->errors;
        }

        return DocoHelpers::response($response,422,$formName);
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $model = new PemusnahanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->load($request->post());
        $_ruanganId = Yii::$app->docoVars->workspace("ruangan_id");
        $getListObat = $session->get($_ruanganId . '-obat-expired');
        $model->detail = $getListObat;
        $model->total_netto = $request->post('total_netto');
        if ($model->validate()) {
            try {
                $result = $this->_restApotek->post('pemusnahan-obat/save',[
                                'form_params' => $model->attributes
                            ]);
                $response = json_decode($result->getBody(),true);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response = ['message' => $e->getMessage()];
            }
        } else {
            $response = $model->errors;
        }

        return DocoHelpers::response($response,422,$formName);
    }

    public function actionExportPrint($id)
    {
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/pemusnahan-obat-alkes.pdf";
        try {
            $response = $this->_restApotek->get('pemusnahan-obat/print', [
                'query' => [
                    'id' => $id
                ],
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcelPopup() {
        $title = Yii::t('fe', 'Cetak Laporan Obatalkes Expired');
        $request = Yii::$app->request;

        $randomStr = DocoHelpers::generateRandomString();

        $params = DocoDatatableHelper::convertToRestfulParams($request->get());
        $params['randomStr'] = $randomStr;

        Yii::$app->session->setFlash($randomStr, $params);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProsesSyncExcel() {
        $request = Yii::$app->request;
        $randomStr = $request->get('randomStr');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec(
            $this->_restApotek, 
            [
                'url' => 'pemusnahan-obat/sync-export-excel',
                'payload' => ['query' => Yii::$app->session->getFlash($randomStr)]
            ]
        );
    }

    public function actionDownloadExcel() {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = "Laporan Obatalkes Expired.xlsx";

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->guzzleExec($this->_restApotek, [
            'url' => 'pemusnahan-obat/download-file-excel',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path, true);
    }

}