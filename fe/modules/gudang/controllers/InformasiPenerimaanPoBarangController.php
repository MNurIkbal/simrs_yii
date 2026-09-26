<?php

namespace Doco\gudang\controllers;
/**
* @author yaya
*/

use Yii;
use yii\web\Response;

use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use app\modules\gudang\models\InfoPoForm;
use app\modules\gudang\models\ReturFormBarang;
use yii\web\UploadedFile;
use Doco\gudang\models\UploadHasilForm;

class InformasiPenerimaanPoBarangController extends DocoController
{
    public $_title = "Informasi Penerimaan Barang Supplier";
    protected $_module = '/gudang/informasi-penerimaan-po-barang/';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actions() {
        return [
            'print-grn'=> 'Doco\gudang\actions\InformasiPenerimaanObat\PrintGrnAction',
        ];
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $module = $this->_module;

        return $this->render('index', get_defined_vars());
    }


    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt($value['penerimaanbarang_id']);
                $value['primary'] = $primaryKey;
                $value['qty_diterima'] = DocoHelpers::formatNumber($value['qty_diterima'])." ".$value["satuan_besar"];
                $value['harga_total'] = DocoHelpers::rupiahDisplay($value['harga_total']);
                $value['status_invoice'] = isset(DocoConstants::$statusPenerimaan[$value['status_invoice']])
                    ? DocoConstants::$statusPenerimaan[$value['status_invoice']] : null;
                $value['tgl_penerimaan'] = !empty($value['tgl_penerimaan'])
                    ? date('d M Y', strtotime($value['tgl_penerimaan'])) : '-';
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

    public function actionSave($id)
    {
        $path = Yii::getAlias('@gudang');
        $request = Yii::$app->request;
        $model = new InfoPoForm;
        $model->load($request->post());
        $model->diterima_oleh = $request->post('diterima_oleh');
        $model->list_data = $request->post('data_detail');
        $model->catatan = $request->post('catatan');
        $model->supplier_id = $request->post('supplier_id');
        $model->tgl_suratjalan = $request->post('tgl_suratjalan');
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
                    if ($size > DocoConstants::MAX_UPLOAD_LAB) {
                        $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                        $result['response']['text'] = Yii::t('fe', 'File maksimal 100 mb !');
                        return DocoHelpers::response($result, 422);
                    }

                    if (!in_array($ext, $except)) {
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
                $response = $this->_restGudang->post('informasi-penerimaan-po-barang/save',[
                    'query' => [
                        'id' => DocoHelpers::decrypt($id)
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

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/informasi-penerimaan-po-barang.pdf";

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/export-pdf', [
                'query' => $filter,
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $path = Yii::getAlias("@download") . "/informasi-penerimaan-po-barang.xlsx";
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/export-excel',[
                'query' => $filter,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDetail($id)
    {
        $module = $this->_module;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $query = [
            "id" => $id
        ];
        $model = new InfoPoForm;

        try {
            $title = "Penerimaan Barang Supplier";
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/detail', [
                'query' => $query
            ]);
            $response = json_decode($response->getBody(), true);

            $model->attributes = isset($response['response']['header'])
                    ? $response['response']['header'] : [];

            $model->list_data = isset($response['response']['detail'])
                    ? $response['response']['detail'] : [];

            $pegMegetahui[$model->peg_mengetahui] = $model->mengetahui;
            $pengSetuju[$model->peg_menyetujui] = $model->menyetujui;
            $document = isset($response['response']['document']) ? $response['response']['document'] : [];

            $path = "/media/gudang/". $model->no_penerimaan;

            return $this->render("detail", get_defined_vars());
        } catch (RequestException $e) {
            $header = $detail = $document = [];
            $pegMegetahui = $pengSetuju = [];
        }
    }

    public function actionDeleteDoc($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/delete-doc', [
                'query' => [
                    'id' => $id
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        }
    }

    public function actionCetakDetail($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/informasi-penerimaan-po-barang-detail.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/cetak-detail', [
                'query' => [
                    'id' => $id
                ],
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionVerifikasiPenerimaan($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);

        $query = [
            'id' => $id
        ];

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/verifikasi-penerimaan', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionBatalPenerimaan($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);

        $query = [
            'id' => $id
        ];

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/batal-penerimaan', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body,422);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionDetailExportPdf($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/gudang-informasi-penerimaan-po-barang.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/cetak-detail', [
                "query" => [
                    "id" => $id
                ],
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionReturPenerimaan($id)
    {
        $title = "Retur Barang";
        $module = $this->_module;

        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $query = ["id" => $id];

        $model = new InfoPoForm;

        $form_model = new ReturFormBarang;

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/detail', [
                'query' => $query
            ]);

            $body = json_decode($response->getBody(), true);

            $header = $body["response"]["header"];

            $supplier_id = DocoHelpers::encrypt($header["supplier_id"]);
            $pajak_id = DocoHelpers::encrypt(isset($header["pajak_id"]) ? $header["pajak_id"] : 1);

            $model->list_data = $body["response"]["detail_penerimaan"];
        } catch (Exception $e) {
            $header = $header_static = $detail = [];
        }

        return $this->render("retur", get_defined_vars());
    }

    public function actionRetur()
    {
        $request = Yii::$app->request;
        $model = new ReturFormBarang;
        $model->load($request->post());
        $model->detail_retur = $request->post('detail_retur');
        if (empty(json_decode($model->detail_retur, 1)))
        {
            return DocoHelpers::response([
                "response" => [
                    "title" => "Data Barang Kosong!",
                    "text" => "Tidak ada barang yang bisa di proses",
                ]
            ],422, "ReturFormBarang");
        }

        try {
            if ($model->validate()) {
                $response = $this->_restGudang->post("informasi-penerimaan-po-barang/retur", [
                    "form_params" => $model->attributes
                ]);

                $body = json_decode($response->getBody(), true);
            }else{
                return DocoHelpers::response([
                    "response" => [
                        "data" => $model->errors,
                        "text" => "Qty retur tidak boleh kurang dari 1",
                    ]
                ],422, "ReturFormBarang");
            }

            return DocoHelpers::response($body,200);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result, 402);
        }
    }

    public function actionGetBarangSupplier($supplier_id, $pajak_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $supplier_id = DocoHelpers::decrypt($supplier_id);
        $pajak_id = DocoHelpers::decrypt($pajak_id);

        $filter["supplier_id"] = $supplier_id;
        $filter["pajak_id"] = $pajak_id;

        $draw = $request->get('draw',1);

       $data_table = [];

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/get-barang-supplier', [
                'query' => $filter
            ]);

            $body = json_decode($response->getBody(), true);

            $result = $body["response"];

            $data = [];

            foreach ($result["data"] as $key => $row) {
                $data_table[$key] = [
                    "no_penerimaan" => $row["no_penerimaan"],
                    "no_faktur" => $row["no_faktur"],
                    "tgl_penerimaan" => $row["tgl_penerimaan"],
                    "nomor_po" => $row["nomor_po"],
                    "barang_nama" => $row["barang_nama"],
                    "barang_id" => $row["barang_id"],
                    "penerimaanbarang_id" => $row["penerimaanbarang_id"],
                    "validasipobarangdetail_id" => $row["validasipobarangdetail_id"],
                    "satuanbesar_id" => $row["satuanbesar_id"],
                    "satuan_besar" => $row["satuan_besar"],
                    "qty_diterima" => $row["qty_diterima"],
                    "qty_yg_diterima" => $row["qty_diterima"]." ".$row["satuan_besar"],
                    "no_batch" => $row["no_batch"],
                    "tgl_kadaluarsa" => date("d-M-Y", strtotime($row["tgl_kadaluarsa"])),
                    "penerimaanbarangdetail_id" => $row["penerimaanbarangdetail_id"],
                    "qty_retur" => 0,
                    "nilai_konversi" => isset($row["nilai_konversi"]) ? $row["nilai_konversi"] : 0,
                    "on_retur" => isset($row["on_retur"]) ? $row["on_retur"] : 0,
                ];
            }

            $data["data"] = $data_table;
            $data["recordsTotal"] = $result["_meta"]["totalCount"];
            $data["recordsFiltered"] = $result["_meta"]["totalCount"];
        } catch (Exception $e) {
            $data_table = [];
        }

        return $data;
    }

    public function actionReturPdf($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/gudang-informasi-retur-penerimaan-barang.pdf";

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-po-barang/retur-pdf', [
                "query" => [
                    "id" => DocoHelpers::decrypt($id)
                ],
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}