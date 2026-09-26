<?php 

namespace Doco\pengadaan\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\base\Model;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use app\modules\pengadaan\models\ValidasiPoObatForm;
use app\modules\pengadaan\models\ValidasiPoBarangForm;
use app\modules\pengadaan\models\ValidasiPoObatDetailForm;
use app\modules\pengadaan\models\ValidasiPoBarangDetailForm;
use app\modules\pengadaan\models\CustomPoObatForm;
use app\modules\pengadaan\models\BatalValidasiForm;

use GuzzleHttp\Exception\RequestException;

class InfoRecomendedOrderController extends DocoController
{
    protected $_title;
    protected $_restMaster;
    protected $_restPengadaan;
    protected $_workspace;
    protected $_ruangan_nama;
    protected $_module = '/pengadaan/info-recomended-order/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Informasi Recomended Order ');
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPengadaan = Yii::$app->docoRest->pengadaan;
        $this->_workspace = Yii::$app->session->get('active_workspace');
        $this->_ruangan_nama = $this->_workspace['ruangan_name'];
    }

    public function actionObat()
    {
        $title = $this->_title.'Obat';
        $module = $this->_module;

        return $this->render('obat', get_defined_vars());
    }

    public function actionBarang()
    {
        $title = $this->_title.'Barang';
        $module = $this->_module;
        
        return $this->render('barang', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat'])) {
            $tgl_rekomendasiobat_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat']);
            $tgl_awal = $tgl_rekomendasiobat_range[0];
            $tgl_akhir = $tgl_rekomendasiobat_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_rekomendasiobat']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPengadaan->get('info-recomended-order/obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['rekomendasiobat_id']);
                unset($value['rekomendasiobat_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgl_rekomendasiobat'] = date('d M Y', strtotime($value['tgl_rekomendasiobat']));
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

    public function actionGetDataBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang'])) {
            $tgl_rekomendasibarang_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang']);
            $tgl_awal = $tgl_rekomendasibarang_range[0];
            $tgl_akhir = $tgl_rekomendasibarang_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_rekomendasibarang']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPengadaan->get('info-recomended-order/barang?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['rekomendasibarang_id']);
                unset($value['rekomendasibarang_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgl_rekomendasibarang'] = date('d M Y', strtotime($value['tgl_rekomendasibarang']));
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

    public function actionCreate($id)
    {
        $id = DocoHelpers::decrypt($id);
        $module = $this->_module;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Validasi Obat PO');
        $model = new ValidasiPoObatForm;
        $modelDetail = new ValidasiPoObatDetailForm;

        $response = $this->_restPengadaan->get('info-recomended-order/view-obat?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $header = $body['response'][0];

        $responseDetail = $this->_restPengadaan->get('info-recomended-order/detail-obat?id='.$id);
        $bodyDetail = json_decode($responseDetail->getBody(), TRUE);
        $detail = $bodyDetail['response'];
        
        return $this->render('form', get_defined_vars());
    }

    public function actionCreateBarang($id)
    {
        $idEnc = DocoHelpers::decrypt($id);
        $module = $this->_module;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Validasi Barang PO');
        $model = new ValidasiPoBarangForm;
        $modelDetail = new ValidasiPoBarangDetailForm;

        $response = $this->_restPengadaan->get('info-recomended-order/view-barang?id='.$idEnc);
        $body = json_decode($response->getBody(), TRUE);
        $header = $body['response'][0];

        $responseDetail = $this->_restPengadaan->get('info-recomended-order/detail-barang?id='.$idEnc);
        $bodyDetail = json_decode($responseDetail->getBody(), TRUE);
        $detail = $bodyDetail['response'];
        
        return $this->render('form_barang', get_defined_vars());
    }

    public function actionGetPegawai()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restPengadaan->get('allow/get-list-pegawai',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $url = 'info-recomended-order/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/info-recomended-order.xlsx";
        try {
            $response = $this->_restPengadaan->get($url,[
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/info-recomended-order.pdf";
            $response = $this->_restPengadaan->get('info-recomended-order/cetak-obat?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
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

    public function actionExportExcelBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $url = 'info-recomended-order/export-excel-barang?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/info-recomended-order-barang.xlsx";
        try {
            $response = $this->_restPengadaan->get($url,[
                // 'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($body['response']);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/info-recomended-order-barang.pdf";
            $response = $this->_restPengadaan->get('info-recomended-order/cetak-barang?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
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

    public function actionSearchSupplier()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-supplier',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['supplier_id'],
                    'text' => $value['supplier_nama'],
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

    public function actionSave()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        if(Yii::$app->request->post()) {
            $ruangan_id = $request->post('ruangan_id');
            $pegawai_id = $request->post('pegawai_id');
            $post = $request->post('data');
            $temp = [];
            $supplier = [];
            $arr = [];
            
            $modelDetail = new CustomPoObatForm;
            $formNameDetail = substr(strrchr(get_class($modelDetail), "\\"), 1);
            $modelDetail->qty_po = $post;
            $validObat = [];
            $cek = false;
            if($modelDetail->validate()) {
                try {
                    if(!empty($post)) {
                        foreach ($post as $key => $value) {
                            if (isset($validObat[$value['obatalkes_id']][$value['id_supplier']])) {
                                $cek = true;
                                break;
                            }
                            $validObat[$value['obatalkes_id']][$value['id_supplier']] = 1;
                            if(!empty($value['id_supplier']) && $value['qty'] != 0) {
                                $arr[] = $value['id_supplier'];
                                $temp[$value['id_supplier']][$value['obatalkes_id']] = $value['qty'];
                            }
                        }
                    }
                    
                    if($cek) {
                        $response['response'] = [
                            'title' => 'Terjadi Kesalahan Sistem !',
                            'text' => 'Silahkan periksa inputan, tidak bisa menambah obat di Supplier yang sama.'
                        ];
                        return DocoHelpers::response($response, 422);
                    }

                    $model = new ValidasiPoObatForm;
                    $formName = substr(strrchr(get_class($model), "\\"), 1);
                    $response = $this->_restPengadaan->post('info-recomended-order/save?id='.$id, [
                        'form_params' => [
                            'data' => $temp,
                            'ruangan_id' => $ruangan_id,
                            'pegawai_id' => $pegawai_id,
                            'id' => $id,
                        ]
                    ]);
                    
                    $res = DocoHelpers::responseJsonString($response->getBody(), true);
                    return DocoHelpers::response($res['response']);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            }
            else {
                return DocoHelpers::response($modelDetail->errors, 422, 'ValidasiPoObatDetailForm');
            }
        }
    }

    public function actionSaveBarang()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);
        if($request->post()) {
            $ruangan_id = $request->post('ruangan_id');
            $pegawai_id = $request->post('pegawai_id');
            $post = $request->post('data');
            $temp = [];
            $supplier = [];
            $arr = [];
            $modelDetail = new CustomPoObatForm;
            $formNameDetail = substr(strrchr(get_class($modelDetail), "\\"), 1);
            $modelDetail->qty_po = $post;
            $validObat = [];
            $cek = false;
            if($modelDetail->validate()) {
                try {
                    foreach ($post as $key => $value) {
                        if (isset($validObat[$value['barang_id']][$value['id_supplier']])) {
                            $cek = true;
                            break;
                        }
                        $validObat[$value['barang_id']][$value['id_supplier']] = 1;
                        if(!empty($value['id_supplier']) && $value['qty'] != 0) {
                            $arr[] = $value['id_supplier'];
                            $temp[$value['id_supplier']][$value['barang_id']] = $value['qty'];
                        }
                    }

                    if($cek) {
                        $response['response'] = [
                            'title' => 'Terjadi Kesalahan Sistem !',
                            'text' => 'Silahkan periksa inputan, tidak bisa menambah obat di Supplier yang sama.'
                        ];
                        return DocoHelpers::response($response, 422);
                    }
                    $model = new ValidasiPoBarangForm;
                    $formName = substr(strrchr(get_class($model), "\\"), 1);
                    $response = $this->_restPengadaan->post('info-recomended-order/save-barang?id='.$id, [
                        'form_params' => [
                            'data' => $temp,
                            'ruangan_id' => $ruangan_id,
                            'pegawai_id' => $pegawai_id,
                            'id' => $id,
                        ]
                    ]);
                    
                    $res = DocoHelpers::responseJsonString($response->getBody(), true);
                    return DocoHelpers::response($res['response']);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(422, $e->getMessage());
                }
            }
            else {
                return DocoHelpers::response($modelDetail->errors, 422, 'ValidasiPoBarangDetailForm');
            }
        }
    }

    private function checkDuplikatArray($array){
        $duplikat = false;
        foreach($array as $k=>$i){
            if(!isset($value_{$i})){
                $value_{$i} = true;
            }
            else{
                $duplikat|= true;          
            }
        }
        return ($duplikat);
    }

    public function actionBatal($id)
    {
        try {
            $request = Yii::$app->request;
            $model = new BatalValidasiForm;
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restPengadaan->put('info-recomended-order/batal?id='.$id, [
                    'form_params' => $model->attributes
                ]);

                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response,false,'BatalValidasiForm');
            } else {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal !',
                        'text' => 'Terjadi kesalahan pada sistem',
                        'data' => $model->errors
                    ]
                ],422,'BatalValidasiForm');
            }
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function actionBatalBarang($id)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $request = Yii::$app->request;
            $model = new BatalValidasiForm;
            $model->load($request->post());
            if ($model->validate()) {
                $response = $this->_restPengadaan->put('info-recomended-order/batal-barang?id='.$id, [
                    'form_params' => $model->attributes
                ]);

                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response,false,'BatalValidasiForm');
            } else {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal !',
                        'text' => 'Terjadi kesalahan pada sistem',
                        'data' => $model->errors
                    ]
                ],422,'BatalValidasiForm');
            }
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function actionDetailObat($id)
    {
        $id = DocoHelpers::decrypt($id);
        $module = $this->_module;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Rekomendasi Order Obat');
        $ruangan = $this->_ruangan_nama;
        $response = $this->_restPengadaan->get('info-recomended-order/view-obat?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $header = $body['response'][0];

        return $this->render('form_detail_obat', get_defined_vars());
    }

    public function actionGetDataDetailObat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPengadaan->get('info-recomended-order/get-detail-obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['rekomendasiobat_id']);
                unset($value['rekomendasiobat_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
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

    public function actionDetailBarang($id)
    {
        $id = DocoHelpers::decrypt($id);
        $module = $this->_module;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Rekomendasi Order Barang');
        $ruangan = $this->_ruangan_nama;
        $response = $this->_restPengadaan->get('info-recomended-order/view-barang?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $header = $body['response'][0];

        return $this->render('form_detail_barang', get_defined_vars());
    }

    public function actionGetDataDetailBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPengadaan->get('info-recomended-order/get-detail-barang?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['rekomendasibarang_id']);
                unset($value['rekomendasibarang_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
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

    public function actionFormBatalBarang($id)
    {
        $title = 'Tolak Transakasi RO';
        $model = new BatalValidasiForm;
        return $this->renderpartial('form_batal_barang',get_defined_vars());
    }

    public function actionFormBatalObat($id)
    {
        $title = 'Tolak Transakasi RO';
        $model = new BatalValidasiForm;
        return $this->renderpartial('form_batal_obat',get_defined_vars());
    }
}