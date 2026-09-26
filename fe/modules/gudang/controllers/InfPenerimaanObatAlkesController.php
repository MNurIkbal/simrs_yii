<?php

/**
* @author yaya
**/

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\gudang\models\ReturPenerimaanForm;
use yii\helpers\ArrayHelper;

class InfPenerimaanObatAlkesController extends DocoController
{
    protected $_title = "Informasi Penerimaan Obat Alkes Supplier";
    protected $_module = 'gudang/inf-penerimaan-obat-alkes/';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
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
        $title = Yii::t('fe', $this->_title);
        $module = $this->_module;

        $status_verifikasi = [
            "Belum Verifikasi" => "Belum Verifikasi",
            "Sudah Verifikasi" => "Sudah Verifikasi"
        ];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('inf-penerimaan-obat-alkes', [
                'form_params' => [],
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penerimaansupp_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgl_penerimaan'] = date('d-M-Y', strtotime($value['tgl_penerimaan']));
                $value['tgl_verifikasi'] = !empty($value['tgl_verifikasi']) ? date('d-M-Y', strtotime($value['tgl_verifikasi'])) : '-';
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
            $response = $this->_restGudang->get('inf-penerimaan-obat-alkes/view', [
                'form_params' => [],
                'query' => [
                    'id' => $id_parent
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $data = $response['response']['data'];

            $role_pengguna  = Yii::$app->session->get('akses_menu')["/gudang/inf-penerimaan-obat-alkes"];
            $have_access = in_array('verifikasi-penerimaan', $role_pengguna);
            $role_verifikasi = !$data['is_verifikasi'] && $have_access;

        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            $response['response']['text'] = 'Terjadi kesalahan pada sistem';
            return DocoHelpers::response($response, 422);
        } catch (\Exception $e) {
            $response['response']['text'] = 'Terjadi kesalahan pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }
        return $this->render('view', get_defined_vars());
    }

    public function actionVerifikasiPenerimaan($id)
    {
        try {
            $response = $this->_restGudang->get('inf-penerimaan-obat-alkes/verifikasi-penerimaan', [
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

    public function actionRetur($id)
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
            $response = $this->_restGudang->get('inf-penerimaan-obat-alkes/view', [
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

    public function actionSave($id)
    {
        $request = Yii::$app->request;
        $tgl_retur = date_create($request->post('tanggal_retur'));
        $model = new ReturPenerimaanForm;
        $model->load($request->post());
        $model->tanggal_retur = date_format($tgl_retur, "Y-m-d");
        $model->data_retur = $request->post('data_retur');
        $id = DocoHelpers::decrypt($id);

        if ($model->validate()) {
            try {
                $response = $this->_restGudang->post('inf-penerimaan-obat-alkes/save-retur', [
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
            $response = $this->_restGudang->get('inf-penerimaan-obat-alkes/get-detail-retur', [
                'form_params' => [],
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $subtotal_retur = 0;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penerimaansuppdetail_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['qty_besar_in_label'] = DocoHelpers::formatNumber($value['qty_besar']);
                $value['qty_besar_in_label'] = DocoHelpers::formatNumber($value['qty_besar']);
                $value['sisa_penerimaan_label'] = DocoHelpers::formatNumber($value['qty_sisa']);
                $value['tgl_kadaluarsa'] = date('Y-m-d', strtotime($value['tgl_kadaluarsa']));
                $value['tglkadaluarsa_label'] = date('d-M-Y', strtotime($value['tgl_kadaluarsa']));
                $subtotal = $value['harga_netto_satuan'] * $value['qty_besar'];
                $subtotal_retur = $value['harga_netto_satuan'] * $value['qty_sisa'];
                $value['harga_netto_satuan'] = DocoHelpers::formatNumber($value['harga_netto_satuan']);
                $value['subtotal'] = DocoHelpers::formatNumber($subtotal);
                $value['input_retur'] = Html::input('text', 'username', null, [
                    'class' => 'form-control qty-retur text-right',
                    'style' => 'width: 55px;',
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
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filters['id'] = $id;

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restGudang->get('inf-penerimaan-obat-alkes/get-detail', [
                'form_params' => [],
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $totalNetto = 0;
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['penerimaansuppdetail_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['harga_input_satuan'] = DocoHelpers::formatNumber($value['harga_input_satuan']);
                $value['tgl_penerimaan'] = date('d-M-Y', strtotime($value['tgl_penerimaan']));
                $value['tgl_kadaluarsa'] = date('d-M-Y', strtotime($value['tgl_kadaluarsa']));
                $value['keterangan'] = !empty($value['keterangan']) ? $value['keterangan'] : '-';
                $data[$key] = $value;
                $totalNetto += $value['harga_netto_satuan'];
            }

            $result['total_netto'] = DocoHelpers::formatNumber($totalNetto);
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

    public function actionPrintPdf($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        try {
            $filters = [];
            $filters['id'] = $id;
            $filters['ruangan_name'] = Yii::$app->docoVars->workspace("ruangan_name");
            $path = Yii::getAlias("@download") . "/informasi-penerimaan-obat-alkes.pdf";
            $response = $this->_restGudang->get('inf-penerimaan-obat-alkes/print-pdf', [
                'save_to' => $path,
                'query' => $filters
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

    public function actionPrintRetur($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        try {
            $filters = [];
            $filters['id'] = $id;
            $path = Yii::getAlias("@download") . "/retur-penerimaan-obat-alkes.pdf";
            $response = $this->_restGudang->get('inf-penerimaan-obat-alkes/print-retur', [
                'save_to' => $path,
                'query' => $filters
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

    public function actionPrintGrn() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $filters = [];
            $filters['id'] = DocoHelpers::decrypt($request->get('id'));
            $filters['type'] = $request->get('type');
            
            $path = Yii::getAlias("@download") . "/informasi-penerimaan-obat-alkes.pdf";
            
            if(Yii::$app->report->enabled){
                $id = $request->get('id', null);
                $query = [
                    'id' => $filters['id'],
                    'type' => $filters['type']
                ];
    
                $urlReport = 'grn-manual';
                $path = !empty($optGuzzle['save_to']) ? $optGuzzle['save_to'] : null;
    
                return Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $query,
                    'manualRender'=>function() use($query,$path){
                        $response = $this->_restGudang->get('inf-penerimaan-obat-alkes/print-grn',[
                            'query' => $query,
                            'save_to' => $path
                        ]);
    
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }

            $response = $this->_restGudang->get('inf-penerimaan-obat-alkes/print-grn', [
                'save_to' => $path,
                'query' => $filters
            ]);
            
            $body = json_decode($response->getBody(), true);

            // return print_r($body);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionGetPegawai()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restGudang->get('allow/get-pegawai',[
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

    public function actionGetSupplier()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restGudang->get('allow/list-supplier',[
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

    public function actionAlertHarga($id)
    {
        $request = Yii::$app->request;
        $query = [
            "id" => $id,
            "tipe" => "PEN_SUPP"
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

            $result['data'] = $data;
            $result['recordsTotal'] = $_count;
            $result['recordsFiltered'] = $_count;
        } catch (\Exception $e) {
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