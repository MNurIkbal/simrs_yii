<?php
// Author : Budi

namespace Doco\bankdarah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\bankdarah\models\TerimaDarahPmiForm;
use app\modules\bankdarah\models\TerimaDarahPmiDetailForm;
use app\modules\bankdarah\models\CustomTerimaDarahPmiDetail;
use yii\helpers\VarDumper;

class InfPemesananDarahController extends DocoController
{
    protected $_title = "Informasi Pemesanan Darah";
    protected $_module = '/inf-pemesanan-darah/';
    protected $_restBankDarah; 
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_nama_ruangan;

    public function init()
    {
        parent::init();
        $this->_restBankDarah = Yii::$app->docoRest->bankdarah; 
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
    }

    public function actionIndex()
    {        
        $title = $this->_title;
        $module = $this->_module;
        
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        $ruangan_id = $this->_ruangan_id;
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
            $response = $this->_restBankDarah->get('inf-pemesanan-darah/index?' .http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesandarahpmi_id']);
                unset($value['pesandarahpmi_id']);
                $value['tgl_pesandarahpmi'] = date("j M Y", strtotime($value['tgl_pesandarahpmi']));
                $value['qty_diterima'] = is_null($value['qty_diterima']) ? 0 : $value['qty_diterima'];
                $value['qty_sisa'] = is_null($value['qty_sisa']) ? 0 : $value['qty_sisa'];
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
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'inf-pemesanan-darah/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/inf-pemesanan-darah.xlsx";
        try {
            $response = $this->_restBankDarah->get($url, ['save_to' => $path]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakPenerimaan($no_terimadarahpmi)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $ruangan_nama = Yii::$app->docoVars->workspace("ruangan_name");
            $path = Yii::getAlias("@download") . "/penerimaan-darah.pdf";
            $response = $this->_restBankDarah->get('inf-pemesanan-darah/export-pdf-penerimaan?no_terimadarahpmi='.$no_terimadarahpmi.'&ruangan_nama='.$ruangan_nama,
            [
                'save_to' => $path,
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

    public function actionExportPdfTransaksi($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        try {
            $namaRs = Yii::$app->docoVars->identity("nama_rumahsakit");
            $alamatRs = Yii::$app->docoVars->identity("alamatlokasi_rumahsakit");
            $path = Yii::getAlias("@download") . "/penerimaan-darah.pdf";
            $response = $this->_restBankDarah->get('inf-pemesanan-darah/export-pdf-transaksi?id='.$id.'&namaRs='.$namaRs.'&alamatRs='.$alamatRs,
            [
                'save_to' => $path,
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

    public function actionPenerimaan($id)
    {
        $id = DocoHelpers::decrypt($id);
        $title = 'Penerimaan Darah PMI';
        $dataPemesanan = $this->getDataPemesanan($id);
        $model = new TerimaDarahPmiForm;
        $modelDetail = new TerimaDarahPmiDetailForm;
        $model->attributes = $dataPemesanan;
        $model->nama_pmi = $dataPemesanan['supplier_nama'];
        $detail = $this->getDataDetail($id);

        return $this->render('penerimaan', get_defined_vars());
    }

    private function getDataPemesanan($id)
    {
        $request = $this->_restBankDarah->request('GET', 'inf-pemesanan-darah/get-data-pemesanan', [
            'query' => [
                'pesandarahpmi_id' => $id
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    private function getDataDetail($id)
    {
        $request = $this->_restBankDarah->request('GET', 'inf-pemesanan-darah/data-detail', [
            'query' => [
                'pesandarahpmi_id' => $id
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    public function actionSearchPetugas()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $penerima_id = $request->get('penerima_id');
            $result = $this->_restBankDarah->get('inf-pemesanan-darah/get-data-petugas', [
                        'query' => [
                            'term' => $request->get('term'),
                            'ruangan_id' => $this->_ruangan_id,
                        ]
                    ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'].' - '.$value['nomorindukpegawai'],
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

    public function actionSavePenerimaan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $model = new TerimaDarahPmiForm;
        $model->load($request->post());
        $model->attributes = $request->post('TerimaDarahPmiForm');
        $model->penerima_id = $request->post('penerima_id');
        $model->ruangan_id = $this->_ruangan_id;
        $model->list_data = $request->post('data_detail');

        try {
            if($model->validate()) {
                $modelDetail = new CustomTerimaDarahPmiDetail;
                $formNameDetail = substr(strrchr(get_class($modelDetail), "\\"), 1);
                $dataJson = $request->post('data_detail',"{}");
                $data = json_decode($dataJson,true);
                $modelDetail->item = $data;
                if($modelDetail->validate()) {
                    try {
                        $response = $this->_restBankDarah->post('inf-pemesanan-darah/save-penerimaan',[
                            'form_params' => [
                                'header' => $model->attributes,
                                'detail' => $modelDetail->item,
                            ],
                        ]);

                        $body = json_decode($response->getBody(),true);
                        return DocoHelpers::response($body,false,'TerimaDarahPmiDetailForm');
                    } catch (RequestException $e) {
                        Yii::info($e->getMessage());
                        $response['response']['text'] = 'Terjadi kesalah pada sistem';
                        $response['response']['message'] = $e->getMessage();
                        return DocoHelpers::response($response, 422);
                    }
                }
                else {
                    return DocoHelpers::response($modelDetail->errors, 422, 'TerimaDarahPmiDetailForm');
                }
            }
            else {
                return DocoHelpers::response($model->errors,422,'TerimaDarahPmiForm');
            }
        } catch (RequestException $e) {
            return DocoHelpers::response([
                "message" => $e->getMessage()
            ],422);
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['namaRs'] = Yii::$app->docoVars->identity("nama_rumahsakit");
            $path = Yii::getAlias("@download") . "/pemesanan-darah.pdf";
            $response = $this->_restBankDarah->get('inf-pemesanan-darah/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
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
}
