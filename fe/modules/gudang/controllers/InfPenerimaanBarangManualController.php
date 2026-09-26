<?php

namespace Doco\gudang\controllers;
/**
* @author yaya
* @since 22 March 2018
*/

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\modules\gudang\models\InfoPoForm;
use app\modules\gudang\models\ReturFormBarangManual;
use app\modules\gudang\models\ReturPenerimaanForm;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class InfPenerimaanBarangManualController extends DocoController
{
    public $_title = "Informasi Penerimaan Barang Supplier Manual";
    protected $_module = '/gudang/inf-penerimaan-barang-manual';
    protected $_restGudang;
    protected $allowedAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_title = Yii::t("fe", $this->_title);
    }
    public function actions() {
        return [
            'print-grn'=> 'Doco\gudang\actions\InformasiPenerimaanObat\PrintGrnManualAction',
        ];
    }
    
    public function actionIndex()
    {
        $instalasi = $ruangan = [];
        $title = $this->_title;
        $module = $this->_module;

        $tgl_penerimaan = '';
        $supplier_nama = '';
        $no_penerimaan = '';

        $status_verifikasi = [
            "Belum Verifikasi" => "Belum Verifikasi",
            "Sudah Verifikasi" => "Sudah Verifikasi"
        ];

        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
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
            // $response = $this->_restGudang->get('inf-penerimaan-barang-manual/index', 
            //         [
            //             'query' => http_build_query($yiiRestfulParams)
            //         ]
            // );
            $response = $this->_restGudang->get('penerimaan-barang-manual/index', 
                    [
                        'query' => http_build_query($yiiRestfulParams)
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penerimaansupp_id']);
                unset($value['penerimaansupp_id']);
                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $value['tgl_penerimaan'] = !empty($value['tgl_penerimaan']) ? date("j M Y", strtotime($value['tgl_penerimaan'])) : '';

                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionSave($id)
    {
        $request = Yii::$app->request;
        $model = new ReturPenerimaanForm;
        $model->load($request->post());
        $model->data_retur = $request->post('data_retur');
        $id = DocoHelpers::decrypt($id);
        if ($model->validate()) {
            try {
                $response = $this->_restGudang->post('penerimaan-barang-manual/save-retur', [
                    'form_params' => $model->attributes,
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response,false,'ReturPenerimaanForm');
            } catch (RequestException $e) {
                return DocoHelpers::response([
                    'messages' => $e->getMessage()
                ],422);
            }
        } else {
            return DocoHelpers::response($model->errors,422,'ReturPenerimaanForm');
        }
    }

    public function actionGetDataRetur($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filters['id'] = $id;
        $filters['per-page'] = 100;

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('penerimaan-barang-manual/get-detail-retur', [
                'form_params' => [],
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penerimaansuppdetail_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['qty_besar_in_label'] = DocoHelpers::formatNumber($value['qty_besar']);
                $value['qty_besar_in_label'] = DocoHelpers::formatNumber($value['qty_besar']);
                $value['sisa_penerimaan_label'] = DocoHelpers::formatNumber($value['qty_sisa']);
                $value['tgl_kadaluarsa'] = !empty($value['tgl_kadaluarsa']) ? date('Y-m-d', strtotime($value['tgl_kadaluarsa'])) : null;
                $value['tglkadaluarsa_label'] = !empty($value['tgl_kadaluarsa']) ? date('d-M-Y', strtotime($value['tgl_kadaluarsa'])) : null;
                $value['input_retur'] = Html::input('text', 'username', null, [
                    'class' => 'form-control qty-retur doco-number',
                    'data-key' => $primaryKey,
                    'disabled' => true
                ]);
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

    public function actionGetDataDetail($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
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
            $response = $this->_restGudang->get('penerimaan-barang-manual/get-detail', 
                    [
                        'query' => http_build_query($yiiRestfulParams)
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penerimaansuppdetail_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['harga_input_satuan'] = DocoHelpers::formatNumber($value['harga_input_satuan']);
                $value['tgl_penerimaan'] = date('d-M-Y', strtotime($value['tgl_penerimaan']));
                $value['tgl_kadaluarsa'] = !empty($value['tgl_kadaluarsa']) ? date('d-M-Y', strtotime($value['tgl_kadaluarsa'])) : null;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            // $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            // $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionView($id)
    {
        $_verification_access = "verifikasi-penerimaan";
        $id_parent = DocoHelpers::decrypt($id);
        $data = [
            'no_penerimaan' => null,
            'tgl_penerimaan' => null,
            'no_faktur' => null,
            'supplier_nama' => null
        ];

        try {
            $response = $this->_restGudang->get('penerimaan-barang-manual/view', [
                'form_params' => [],
                'query' => [
                    'id' => $id_parent
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $data = $response['response']['data'];
            $role_pengguna  = Yii::$app->session->get('akses_menu')["/gudang/inf-penerimaan-barang-manual"];
            $have_access = in_array('verifikasi-penerimaan', $role_pengguna);
            $role_verifikasi = !$data['is_verifikasi'] && $have_access;

        } catch (RequestException $e) {
        } catch (\Exception $e) {
        }
        return $this->render('view', get_defined_vars());
    }

    public function actionVerifikasiPenerimaan($id)
    {
        try {
            $response = $this->_restGudang->get('penerimaan-barang-manual/verifikasi-penerimaan', [
                'form_params' => [],
                'query' => [
                    'penerimaansupp_id' => $id
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (\Exception $e) {
            //
        } catch (RequestException $e) {
            //
        }
    }

    public function actionExportPdf($id)
    {
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/gudang-penerimaan-barang-supplier-manual.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->post('penerimaan-barang-manual/export-pdf', [
                'query' => [
                    'id' => $id
                ],
                'form_params' => [],
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->delete('informasi-penerimaan-barang/delete', [
                'query' => [
                    'id' => $id
                ]
            ]);
        return DocoHelpers::response([
            'message' => "data berhasil di delete"
        ]);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        }
    }

    public function actionReturPenerimaan($id)
    {
        $id_parent = DocoHelpers::decrypt($id);
        $model = new ReturPenerimaanForm;
        $model->tanggal_retur = date('d-M-Y');
        $data = [
            'no_penerimaan' => null,
            'tgl_penerimaan' => null,
            'no_faktur' => null,
            'supplier_nama' => null
        ];
        try {
            $response = $this->_restGudang->get('penerimaan-barang-manual/view', [
                'form_params' => [],
                'query' => [
                    'id' => $id_parent
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $data = $response['response']['data'];
        } catch (RequestException $e) {

        }
        return $this->render('retur', get_defined_vars());
    }

    public function actionRetur()
    {
        $request = Yii::$app->request;
        $model = new ReturFormBarangManual;
        $model->attributes = $request->post();
        $model->detail_retur = $request->post('detail_retur');
        // var_dump($model->attributes);die();
        if (empty(json_decode($model->detail_retur, 1)))
        {
            return DocoHelpers::response([
                "response" => [
                    "title" => "Data Barang Kosong!",
                    "text" => "Tidak ada barang yang bisa di proses",
                ]
            ],422, "ReturFormBarangManual");
        }

        try {
            if ($model->validate()) {
                $response = $this->_restGudang->post("inf-penerimaan-barang-manual/retur", [
                    "form_params" => $model->attributes
                ]);

                $body = json_decode($response->getBody(), true);
            }
            else{
                $keys = explode('[', array_keys($model->errors)[0]);
                if (count($keys) <= 1) {
                    $message = "Alasan Tidak Boleh Kosong";
                } else {
                    $message = "Qty retur tidak boleh kurang dari 1";
                }
                return DocoHelpers::response([
                    "response" => [
                        "data" => $model->errors,
                        "text" => $message,
                    ]
                ],422, "ReturFormBarangManual");
            }
            // else{
            //     return DocoHelpers::response([
            //         "response" => [
            //             "data" => $model->errors,
            //             "text" => "Qty retur tidak boleh kurang dari 1",
            //         ]
            //     ],422, "ReturFormBarangManual");
            // }

            return DocoHelpers::response($body,200);
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result, 402);
        }
    }

    public function actionPrintRetur($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/gudang-informasi-retur-penerimaan-barang-manual.pdf";

        try {
            $response = $this->_restGudang->get('penerimaan-barang-manual/print-retur', [
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

    public function actionAlertHarga($id)
    {
        $request = Yii::$app->request;
        $query = [
            "id" => $id,
            "tipe" => "PEN_SUPP"
        ];

        $data = [];

        try {
            $response = $this->_restGudang->get('allow/get-alert-harga', [
                "query" => $query
            ]);
            $body = json_decode($response->getBody(), true);
            $data = isset($body['response']) && $body['response'] != null ? $body['response'] : []; // jika response null, force empty array
            $no = 1;
            $_count = count($data);

            foreach ($data as $row => $value) {
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

            $result['data'] = $data;
            $result['recordsTotal'] = $_count;
            $result['recordsFiltered'] = $_count;
        } catch (Exception $e) {
            $data = [];
            $result['data'] = $data;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
        }
        return DocoHelpers::response($result);
    }

    public function actionUpdateHarga()
    {
        $request = Yii::$app->request;
        try {
            $response = $this->_restGudang->post('penerimaan-obat-supplier/save-update-harga?trace=1', [
                "form_params" => $request->post()
            ]);
            $body = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage());
        }

        return DocoHelpers::response($body);
    }
}