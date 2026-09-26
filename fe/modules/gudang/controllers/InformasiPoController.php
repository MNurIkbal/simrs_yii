<?php

/**
** @author yaya
** service : 
** - Gudang formulir-stok-opname v1
**/

namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\Model;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\modules\gudang\models\InfoPoForm;
use Doco\gudang\models\UploadHasilForm;
use GuzzleHttp\Exception\RequestException;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class InformasiPoController extends DocoController
{
    protected $_title = "Informasi Purchase Order (PO)";
    protected $_module = '/gudang/informasi-po/';
    protected $_restGudang;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $module = $this->_module;
        $request = $this->getRequest();
        $type = DocoHelpers::encrypt("obat");

        return $this->render('index', get_defined_vars());
    }

    public function actionBarang()
    {
        $title = $this->_title.' Barang';
        $module = $this->_module;   
        $request = $this->getRequest();

        $type = DocoHelpers::encrypt("barang");

        return $this->render('barang', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restGudang->get('informasi-po/', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt($value['transaksi_id']);
                $type = DocoHelpers::encrypt($value['type_po']);
                $value['primary'] = $primaryKey;
                $value['type'] = $type;
                $value['tanggal_po'] = date('d M Y', strtotime($value['tanggal_po']));
                $value['tgl_rencanaterima'] = !empty($value['tgl_rencanaterima']) 
                    ? date('d M Y', strtotime($value['tgl_rencanaterima'])) : '-';
                $value["status_verifikasi"] = $value["is_verifikasi"] ? "Belum Verifikasi" : "Bisa melakukan penerimaan";
                $value["total_harga_po"] = DocoHelpers::rupiahDisplay($value["total_harga_po"]);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restGudang->get('informasi-po/barang?' . http_build_query($yiiRestfulParams), [
                'form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt($value['transaksi_id']);
                $type = DocoHelpers::encrypt($value['type_po']);
                $value['primary'] = $primaryKey;
                $value['type'] = $type;
                $value['tanggal_po'] = date('d M Y', strtotime($value['tanggal_po']));
                $value['tgl_rencanaterima'] = !empty($value['tgl_rencanaterima']) 
                    ? date('d M Y', strtotime($value['tgl_rencanaterima'])) : '-';
                $value["status_verifikasi"] = $value["is_verifikasi"] ? "Belum Verifikasi" : "Bisa melakukan penerimaan";
                $value["total_harga_po"] = DocoHelpers::rupiahDisplay($value["total_harga_po"]);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = Yii::$app->docoVars->workspace("ruangan_id");
        try {

            $response = $this->_restGudang->get('allow/get-ruangan',[
                'query' => [
                    'instalasi_id' => $parent_label
                ]
            ]);

            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value) 
                $result['output'][] = [
                    'id' => $value['ruangan_id'], 
                    'name' => $value['ruangan_nama']
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

    private function getRequest()
    {
        $request = $this->_restGudang->request('GET', 'informasi-po/generate-api');
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    public function actionSearchPegawai()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-pegawai',[
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

    public function actionSearchObat()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-obat',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
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

    public function actionSearchBarang()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-barang',[
                            'query' => [
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['barang_id'],
                    'text' => $value['barang_nama'],
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

    public function actionSearchItem($instalasi_id)
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restGudang->get('allow/list-item',[
                'query' => [
                    'term' => $request->get('term'),
                    'instalasi_id' => $instalasi_id,
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                if($instalasi_id == 15) {
                    $response[] = [
                        'id' => $value['barang_id'].'-B',
                        'text' => $value['barang_nama'],
                    ];
                }
                else {
                    $response[] = [
                        'id' => $value['obatalkes_id'].'-O',
                        'text' => $value['obatalkes_nama'],
                    ];
                }
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionGetSatuanKonversiItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
        }

        $exp = explode('-', $parent_label);
        list($item_id, $tipe) = $exp;
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restMaster->get('allow/list-satuan-konversi-item', [
                'query' => [
                    'item_id' => $item_id,
                    'tipe' => $tipe,
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value)
                if($tipe == 'B') {
                    $result['output'][] = [
                        'id' => $value['satuankonversibrg_id'],
                        'name' => '1 '.$value['besar'].' = '.$value['nilai_konversi'].' '.$value['kecil']
                    ];
                }
                else {
                    $result['output'][] = [
                        'id' => $value['satuankonversi_id'],
                        'name' => '1 '.$value['besar'].' = '.$value['nilai_konversi'].' '.$value['kecil']
                    ];
                }
                
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
        $url = 'informasi-po/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/informasi-po.xlsx";
        try {
            $response = $this->_restGudang->get($url,[
                'save_to' => $path
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
            $path = Yii::getAlias("@download") . "/informasi-po.pdf";
            $response = $this->_restGudang->get('informasi-po/export-pdf?'.http_build_query($yiiRestfulParams),[
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
        $url = 'informasi-po/export-excel-barang?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/informasi-po-barang.xlsx";
        try {
            $response = $this->_restGudang->get($url,[
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfBarang()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/informasi-po-barang.pdf";
            $response = $this->_restGudang->get('informasi-po/export-pdf-barang?'.http_build_query($yiiRestfulParams),[
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

    public function actionDetail($id, $type)
    {
        $model = new InfoPoForm;
        $type = DocoHelpers::decrypt($type);
        $title = 'Penerimaan '.ucwords($type).' Supplier';
        try {
            $response = $this->_restGudang->get('informasi-po/get-detail',[
                'query' => [
                    'id' => DocoHelpers::decrypt($id),
                    'type' => $type
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $model->attributes = isset($response['response']['header'])
                    ? $response['response']['header'] : [];

            $model->list_data = isset($response['response']['detail'])
                    ? $response['response']['detail'] : [];
            $isExpired = $model->status_penerimaan == DocoConstants::STATUS_PO_EXPIRED ? true : false;
            $isFullReceived = $model->status_penerimaan == DocoConstants::STATUS_PO_SUDAH_SEMUA_DITERIMA ? true : false;
        } catch (RequestException $e) {
            $detail = [];
        }

        return $this->render('detail',get_defined_vars());
    }

    public function actionSave($id, $type)
    {
        $path = Yii::getAlias('@gudang');
        $request = Yii::$app->request;
        
        $model = new InfoPoForm;
        $model->load($request->post());
        $model->diterima_oleh = $request->post('diterima_oleh');
        $model->list_data = $request->post('data_detail');
        $model->catatan = $request->post('catatan');
        $model->supplier_id = $request->post('supplier_id');

        $modelUpload = new UploadHasilForm;
        $file = UploadedFile::getInstances($modelUpload, "upload_file");

        try {
            if ($model->validate()) {
                $listObject = [];
                $dataUpload = [];
                $postCatatan = $request->post('UploadHasilForm');
                $except = ['pdf','png','jpg', 'xlsx', 'xls'];
                /** Save Temporary File **/
                foreach ($file as $key => $value) {
                    $size = $value->size;
                    $ext = end(explode(".", $value->name));
                    if ($size > DocoConstants::MAX_UPLOAD_LAB)
                    {
                        $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                        $result['response']['text'] = Yii::t('fe', 'File maksimal 100 mb !');
                        return DocoHelpers::response($result, 422);
                    }

                    if (!in_array($ext, $except))
                    {
                        $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                        $result['response']['text'] = Yii::t('fe', 'File harus berbentuk jpg, png dan pdf dengan maksimal 100 mb');

                        return DocoHelpers::response($result, 422);
                    }

                    $value->name = strtotime(date("H:i:s")) ."{$key}-". $value->name;
                    $listObject[] = $value;
                    $dataUpload[] = [
                        'upload_berkas' => $value->name,
                        'catatan_berkas' => isset($postCatatan['catatan'][$key]) 
                            ? $postCatatan['catatan'][$key] : null
                    ];
                }

                $model->file_upload = json_encode($dataUpload);

                /** execute to backend **/
                $response = $this->_restGudang->post('informasi-po/save',[
                    'query' => [
                        'id' => DocoHelpers::decrypt($id),
                        'type' => $type
                    ],
                    'form_params' => $model->attributes,
                ]);

                $response = json_decode($response->getBody(),true);

                /** ketika sukses simpan baru upload **/
                if ($response['metadata']['status'] == 200) {
                    $idFolder = isset($response['response']['no_penerimaan']) 
                            ? $response['response']['no_penerimaan'] : '';
                    if (!file_exists($path."/{$idFolder}")) {
                        mkdir($path."/{$idFolder}", 0777, true);
                    }

                    foreach ($listObject as $value) {
                        $value->saveAs($path."/{$idFolder}/". $value->name);
                    }
                }
                return DocoHelpers::response($response,false,'InfoPoForm');
            } else {
                return DocoHelpers::response($model->errors,422,'InfoPoForm');
            }
        } catch (RequestException $e) {
            return DocoHelpers::response([
                "message" => $e->getMessage()
            ],422);
        }
    }

    public function actionCetakPenerimaan($id, $type)
    {
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/informasi-po-detail.pdf";
            $type = DocoHelpers::decrypt($type);
            if ($type == DocoConstants::JENIS_OBAT) {
                $response = $this->_restGudang->get('informasi-po/cetak-penerimaan',[
                    'query' => [
                        'id' => DocoHelpers::decrypt($id)
                    ],
                    'save_to' => $path
                ]);
            } else {
                $response = $this->_restGudang->get('informasi-po/cetak-penerimaan-barang',[
                    'query' => [
                        'id' => DocoHelpers::decrypt($id)
                    ],
                    'save_to' => $path
                ]);
            }
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetPenerimaanTerakhir($no_po, $type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            $response = $this->_restGudang->get("informasi-po/get-penerimaan-terakhir",[
                "query" => [
                    "no_po" => $no_po,
                    "type" => $type
                ]
            ]);

            $response = json_decode($response->getBody(), true);

            return $response;
        } catch (Exception $e) {
            return false;
        }
    }
}
