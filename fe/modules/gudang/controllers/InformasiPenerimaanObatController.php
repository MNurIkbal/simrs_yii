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

use app\modules\gudang\models\InfoPoForm;
use app\modules\gudang\models\ReturFormObat;
use app\modules\gudang\models\PenerimaanObat;
use yii\web\UploadedFile;
use Doco\gudang\models\UploadHasilForm;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;

class InformasiPenerimaanObatController extends DocoController
{
    public $_title = "Informasi Penerimaan Obat Alkes Supplier";
    protected $_module = '/gudang/informasi-penerimaan-obat/';
    protected $_restGudang;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_restMaster = Yii::$app->docoRest->master;
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
            $response = $this->_restGudang->get('informasi-penerimaan-obat/', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $primaryKey = DocoHelpers::encrypt($value['penerimaanobat_id']);
                $value['primary'] = $primaryKey;
                $value['qty_diterima'] = DocoHelpers::formatNumber($value['qty_diterima']) ." ".$value["satuan_besar"];

                $value["is_verifikasi"] = $value["status_invoice"];

                switch ($value["status_invoice"]) {
                    case '0':
                        $value["status_invoice"] = "Belum Diverifikasi";
                        break;

                    case '1':
                        $value["status_invoice"] = "Sudah Diverifikasi";
                        break;

                    case '2':
                        $value["status_invoice"] = "Dibatalkan";
                        break;

                    default:
                        $value["status_invoice"] = "Undefined";
                        break;
                }
                $value['tgl_penerimaan'] = !empty($value['tgl_penerimaan'])
                    ? date('d M Y', strtotime($value['tgl_penerimaan'])) : '-';
                $value['harga_total'] = DocoHelpers::rupiahDisplay($value['harga_total']);
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

    public function actionGetDetail($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-obat/get-detail', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), True);
            $data["detail"] = isset($body['response']['detail']) ? $body['response']['detail'] : [];
            $data["document"] = isset($body['response']['document']) ? $body['response']['document'] : [];
            $data["path"] = "/media/gudang/".$header["no_penerimaan"];
        } catch (Exception $e) {
            $data = [];
        }
        return DocoHelpers::response($data);
    }

    public function actionExportPdf()
    {
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/gudang-informasi-penerimaan-obat.pdf";
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $response = $this->_restGudang->get('informasi-penerimaan-obat/export-pdf', [
                'query' => $filter,
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $path = Yii::getAlias("@download") . "/informasi-penerimaan-obat.xlsx";
            $response = $this->_restGudang->get('informasi-penerimaan-obat/export-excel', [
                'query' => $filter,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
       } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
       } catch (RequestException $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
       }
    }

    public function actionDetail($id)
    {
        $module = $this->_module;
        $title = $this->_title;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $query = [
            "id" => $id
        ];

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-obat/get-detail', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), True);

            $header = isset($body['response']['header']) ? $body['response']['header'] : [];
            $detail = isset($body['response']['detail']) ? $body['response']['detail'] : [];
            $document = isset($body['response']['document']) ? $body['response']['document'] : [];

            $path = "/media/gudang/".$header["no_penerimaan"];

            return $this->render("detail", get_defined_vars());
        } catch (RequestException $e) {
            $header = $detail = $document = [];
        }
    }

    public function actionDetailExportPdf($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/gudang-informasi-detail-penerimaan-obat.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->get('informasi-penerimaan-obat/detail-export-pdf', [
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

    public function actionVerifikasiPenerimaan($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $query = [
            'id' => $id
        ];

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-obat/verifikasi-penerimaan', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), True);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionEditPenerimaanForm($id = '')
    {
        $path_doc = Yii::getAlias('@asset_gudang');
        $title = $this->_title;
        $module = $this->_module;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $query = [
            "id" => $id
        ];

        $model = new InfoPoForm;
        try {
            $response = $this->_restGudang->get('informasi-penerimaan-obat/get-detail', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), True);
            $list_pegawai = $body["response"]["list_pegawai"];
            $header_static = $body["response"]["header"];
            $detail_static = $body["response"]["detail"];
            $list_document = $body["response"]["document"];
            $model->attributes = $header_static;
            $model->list_data = $detail_static;
            $verified = $header_static['is_verifikasi'] >= 1 ? true : false;
        } catch (Exception $e) {
            $header = $header_static = $detail = [];
        }

        return $this->render("edit-penerimaan", get_defined_vars());
    }

    public function actionEditPenerimaan($id)
    {
        $path = Yii::getAlias('@gudang');
        $request = Yii::$app->request;
        $model = new InfoPoForm;
        $model->load($request->post());
        $model->list_data = $request->post('list_data');

        $modelUpload = new UploadHasilForm;
        $file = UploadedFile::getInstances($modelUpload, "upload_file");


        try {
            if($model->validate()){
                $listObject = [];
                $dataUpload = [];
                $postCatatan = $request->post('UploadHasilForm');
                $except = ['pdf','png','jpg', 'xlsx', 'xls'];
                foreach ($file as $key => $value) {

                    $size = $value->size;
                    $arr_ext = explode(".", $value->name);
                    $ext = end($arr_ext);

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

                $response = $this->_restGudang->post("informasi-penerimaan-obat/edit-penerimaan", [
                    "query" => [
                        "id" => $id
                    ],
                    "form_params" => $model->attributes
                ]);

                $body = json_decode($response->getBody(), true);

                if ($body['metadata']['status'] == 200) {
                    $idFolder = isset($body['response']['no_penerimaan'])
                            ? $body['response']['no_penerimaan'] : '';
                    if (!file_exists($path."/{$idFolder}")) {
                        mkdir($path."/{$idFolder}", 0777, true);
                    }

                    foreach ($listObject as $value) {
                        $value->saveAs($path."/{$idFolder}/". $value->name);
                    }

                    \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                    return DocoHelpers::response($body, 200);
                }else{
                    return DocoHelpers::response($body, 422);
                }
            }else{
                return DocoHelpers::response($model->errors,422, "InfoPoForm");
            }
        } catch (Exception $e) {
            return DocoHelpers::response([
                "message" => $e->getMessage()
            ],false,'InfoPoForm');
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
            $response = $this->_restGudang->get('informasi-penerimaan-obat/batal-penerimaan', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), True);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionDeleteDoc($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $query = [
            'id' => $id
        ];

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-obat/delete-doc', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), True);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionReturPenerimaan($id)
    {
        $title = "Retur Obat Alkes";
        $module = $this->_module;

        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $query = ["id" => $id];

        $model = new InfoPoForm;

        $form_model = new ReturFormObat;

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-obat/get-detail', [
                'query' => $query
            ]);

            $body = json_decode($response->getBody(), true);

            $header = $body["response"]["header"];

            $supplier_id = DocoHelpers::encrypt($header["supplier_id"]);
            $pajak_id = DocoHelpers::encrypt($header["pajak_id"]);

            $model->list_data = $body["response"]["detail_penerimaan"];
        } catch (Exception $e) {
            $header = $header_static = $detail = [];
        }

        return $this->render("retur", get_defined_vars());
    }

    public function actionRetur($id)
    {
        $request = Yii::$app->request;
        $model = new ReturFormObat;
        $model->load($request->post());
        $model->detail_retur = $request->post('detail_retur');

        if (empty(json_decode($model->detail_retur, 1)))
        {
            return DocoHelpers::response([
                "response" => [
                    "text" => "Tidak ada obat yang bisa di proses",
                    "title" => "Data Kosong!"
                ]
            ],422, "ReturFormObat");
        }

        try {
            if ($model->validate()) {
                $response = $this->_restGudang->post("informasi-penerimaan-obat/retur", [
                    "form_params" => $model->attributes
                ]);

                $body = json_decode($response->getBody(), true);
            }else{
                return DocoHelpers::response([
                    "response" => [
                        "data" => $model->errors,
                        "text" => array_values($model->getErrors())[0]
                    ]
                ],422, "ReturFormObat");
            }

            return DocoHelpers::response($body,200);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result, 402);
        }
    }

    public function actionGetObatSupplier($supplier_id, $pajak_id)
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
            $response = $this->_restGudang->get('informasi-penerimaan-obat/get-obat-supplier', [
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
                    "obatalkes_nama" => $row["obatalkes_nama"],
                    "obatalkes_id" => $row["obatalkes_id"],
                    "penerimaanobat_id" => $row["penerimaanobat_id"],
                    "validasipoobatdetail_id" => $row["validasipoobatdetail_id"],
                    "satuanbesar_id" => $row["satuanbesar_id"],
                    "satuan_besar" => $row["satuan_besar"],
                    "qty_diterima" => $row["qty_diterima"],
                    "qty_yg_diterima" => $row["qty_diterima"]." ".$row["satuan_besar"],
                    "no_batch" => $row["no_batch"],
                    "tgl_kadaluarsa" => date("d-M-Y", strtotime($row["tgl_kadaluarsa"])),
                    "penerimaanobatdetail_id" => $row["penerimaanobatdetail_id"],
                    "qty_retur" => 0,
                    "nilai_konversi" => isset($row["nilai_konversi"]) ? $row["nilai_konversi"] : 1,
                    "on_retur" => isset($row["on_retur"]) ? $row["on_retur"] : "-",
                    "harga" => $row["harga"]
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

    public function actionReturPdf($no_retur)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/gudang-informasi-retur-penerimaan-obat.pdf";

        try {
            $response = $this->_restGudang->get('informasi-penerimaan-obat/retur-pdf', [
                "query" => [
                    "no_retur" => $no_retur
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

    public function actionAlertHarga($id)
    {
        $id = DocoHelpers::decrypt($id);
        $query = [
            "id" => $id,
            "tipe" => "PO"
        ];

        $data = [];
        try {
            $compareHarga = $this->guzzleExec($this->_restGudang, [
                'url' => 'allow/get-alert-harga',
                'payload' => [
                    'query' => $query
                ],
                'returnResponse' => true
            ]);
            
            $harga = ArrayHelper::getValue($compareHarga, 'data', []);
            $no = 1;
            $_count = count($harga);
            if($_count > 0) {
                foreach ($harga as $row => $value) {
                    $value["rowNum"] = $no;
                    $value["harganetto_ygdipakai"] = $value["harga_netto_sekarang"];
                    $value["harga_sugesstion"] = $value["harga_disarankan"];
                    $value["harga_transaksi"] = $value["harga_netto_transaksi"];
                    $value["disp_harga_sekarang"] = DocoHelpers::rupiahDisplay($value["harga_netto_sekarang"])." /".$value['satuan_disarankan'];
                    $value["disp_harga_sugesstion"] = DocoHelpers::rupiahDisplay($value["harga_disarankan"])." /".$value['satuan_disarankan'];
                    $value["disp_harga_transaksi"] = DocoHelpers::rupiahDisplay($value["harga_netto_transaksi"])." /".$value['satuan_transaksi'];
                    $data[$row] = $value;
                    $no++;
                }
            }
        } catch (\Exception $e) {
            $data = [];
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $_count;
        $result['recordsFiltered'] = $_count;
        return DocoHelpers::response($result);
    }

    public function actionUpdateHarga()
    {
        $request = Yii::$app->request;
        try {
            $response = $this->_restGudang->post('informasi-penerimaan-obat/save-update-harga', [
                "form_params" => $request->post()
            ]);
            $body = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage());
        }

        return DocoHelpers::response($body);
    }
}
