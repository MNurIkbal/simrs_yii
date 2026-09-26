<?php

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\gudang\models\AdjusmenBarangForm;
use app\modules\gudang\models\AdjusmenBarangMasukForm;
use app\modules\gudang\models\AdjusmenBarangKeluarForm;
use GuzzleHttp\Exception\RequestException;

class AdjustmentBarangController extends DocoController
{
    protected $_title = "Adjustment Barang";
    protected $_module = '/gudang/adjustment-barang/';
    protected $_restMaster;
    protected $_restApotek;
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restApotek = Yii::$app->docoRest->apotek;
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
        $title = $this->_title;
        $adjustForm = $this->getFormScenario();
        if ($adjustForm['current_user']) {
            $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $pegawai_nama = Yii::$app->docoVars->user('nama_pegawai');
        }
        $model = new AdjusmenBarangForm;
        $model->scenario = $adjustForm['scenario'];
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = $cache = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('bmhp-operasi',
                [
                    'query' => $yiiRestfulParams
                ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if (!empty($body['response']['data'])) {
                foreach ($body['response']['data'] as $key => $value) {
                    $no++;
                    $primaryKey = DocoHelpers::encrypt($value['paketbmhp_id']);
                    $value['primary'] = $primaryKey;
                    unset($value['paketbmhp_id']);
                    $value['rowNum'] = $no;
                    $value['qty_pemakaian'] = DocoHelpers::formatNumber($value['qty_pemakaian']);
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            } else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;
                return $result;
            }
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionMasuk()
    {
        $request = Yii::$app->request;
        $model = new AdjusmenBarangMasukForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $cacheAdjustmentMasuk = Yii::$app->cache->get("adjus-masuk-barang");
        $countMasuk = ($cacheAdjustmentMasuk == false) ? 0 : count($cacheAdjustmentMasuk);
        $satuan = $this->getRequest();

        $id = null;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('adjustment-barang/create', [
                        'form_params' => $model->attributes
                    ]);
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }

        return $this->renderAjax('masuk', get_defined_vars());
    }

    public function actionKeluar()
    {
        $request = Yii::$app->request;
        $model = new AdjusmenBarangKeluarForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $cacheAdjustmentKeluar = Yii::$app->cache->get("adjus-keluar-barang");
        $countKeluar = ($cacheAdjustmentKeluar == false) ? 0 : count($cacheAdjustmentKeluar);
        $satuan = $this->getRequest();
        $id = null;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('adjustment-barang/create', [
                        'form_params' => $model->attributes
                    ]);
                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }

        return $this->renderAjax('keluar', get_defined_vars());
    }

    public function actionSearchBarang($tipe)
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/get-data-barang-adjustment', [
                            'query' => [
                                'term' => $request->get('term'),
                                'tipe' => $tipe,
                            ]
                        ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            foreach ($data as $key => $value) {
                $satuankecil_nama = ($tipe == 0) ? $value['satuan_kecil'] : $value['satuankecil_nama'];
                $response[] = [
                    'id' => $value['barang_id'],
                    'text' => $value['barang_nama'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $satuankecil_nama,
                    'qty_tersedia' => ($tipe == 1) ? $value['qty_tersedia'] : 0,
                    'is_kadaluarsa' => $value['is_kadaluarsa'],
                    'harganetto_ygdipakai' => ($tipe == 0) ? $value["harganetto_ygdipakai"] : 0,
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

    public function actionSearchSatuanKonversi()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-satuan-konversi',[
                            'query' => [
                                'term' => $request->get('term'),
                                'barang_id' => $request->get('barang_id'),
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['satuankecil_id'],
                    'text' => $value['satuanunit_nama'],
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

    public function actionSearchSatuan()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-satuan-masuk',[
                            'query' => [
                                'term' => $request->get('term'),
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['satuanunit_id'],
                    'text' => $value['satuanunit_nama'],
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

    public function actionGetListItemMasuk()
    {
        $request = Yii::$app->request;
        $cacheAdjustmentMasuk = Yii::$app->cache->get("adjus-masuk-barang");

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if ($cacheAdjustmentMasuk !== false) {
            $no = $request->get('start',1);
            foreach ($cacheAdjustmentMasuk as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'qty_penerimaan' => DocoHelpers::formatNumber($value['qty_besar']).' '.$value['satuanunit_nama_besar'],
                    'qty_konversi' => DocoHelpers::formatNumber($value['qty_kecil']).' '.$value['satuanunit_nama_kecil'],
                    'rowNum' => $no,
                    'barang_id' => $value['barang_id'],
                    'barang_nama' => $value['barang_nama'],
                    'satuanunit_nama_besar' => $value['satuanunit_nama_besar'],
                    'satuanunit_nama_kecil' => $value['satuanunit_nama_kecil'],
                    'qty_besar' => DocoHelpers::formatNumber($value['qty_besar']),
                    'qty_kecil' => DocoHelpers::formatNumber($value['qty_kecil']),
                    'harga_netto' => DocoHelpers::formatNumber($value['harga_netto']),
                    'tgl_kadaluarsa' => !empty($value['tgl_kadaluarsa']) ? date('d M Y', strtotime($value['tgl_kadaluarsa'])) :
                    '-',
                    'no_batch' => $value['no_batch'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module .'delete-list-item-masuk','id' => $primaryKey]),
                        ]
                    )
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($cacheAdjustmentMasuk);
            $result['recordsFiltered'] = count($cacheAdjustmentMasuk);
        }

        return DocoHelpers::response($result);
    }

    public function actionGetListItemKeluar()
    {
        $request = Yii::$app->request;
        $cacheAdjustmentKeluar = Yii::$app->cache->get("adjus-keluar-barang");
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if ($cacheAdjustmentKeluar !== false) {
            $no = $request->get('start',1);
            foreach ($cacheAdjustmentKeluar as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'qty_pengeluaran' => DocoHelpers::formatNumber($value['qty_besar']).' '.$value['satuanunit_nama_besar'],
                    'qty_konversi' => DocoHelpers::formatNumber($value['qty_kecil']).' '.$value['satuanunit_nama_kecil'],
                    'rowNum' => $no,
                    'barang_id' => $value['barang_id'],
                    'barang_nama' => $value['barang_nama'],
                    'satuanunit_nama_besar' => $value['satuanunit_nama_besar'],
                    'satuanunit_nama_kecil' => $value['satuanunit_nama_kecil'],
                    'qty_besar' => DocoHelpers::formatNumber($value['qty_besar']),
                    'qty_kecil' => DocoHelpers::formatNumber($value['qty_kecil']),
                    'alasan' => $value['alasan'],
                    'no_batch' => $value['no_batch'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete-keluar',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module .'delete-list-item-keluar','id' => $primaryKey]),
                        ]
                    )
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($cacheAdjustmentKeluar);
            $result['recordsFiltered'] = count($cacheAdjustmentKeluar);
        }

        return DocoHelpers::response($result);
    }

    private function getRequest($satuankonversi_id = null)
    {
        $request = $this->_restGudang->request('GET', 'adjustment-barang/generate-api', [
            'query' => [
                'satuankonversi_id' => $satuankonversi_id
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    private function getRequestStok($barang_id)
    {
        $workspace = Yii::$app->session->get('active_workspace');
        $ruangan_id = $workspace['ruangan_id'];

        $request = $this->_restGudang->request('GET', 'adjustment-barang/cek-stok', [
            'query' => [
                'ruangan_id' => $ruangan_id,
                'barang_id' => $barang_id
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];
        return $attributes;
    }

    public function actionSetListItemMasuk()
    {
        $request = Yii::$app->request;
        $model = new AdjusmenBarangMasukForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        $post = $request->post('AdjusmenBarangMasukForm');
        $model->attributes = $post;

        if ($model->validate()) {
            $satuankonversi_id = $post['satuankonversi_id'];
            $satuanRequest = $this->getRequest($satuankonversi_id);

            $qty_kecil = $satuanRequest['nilai_konversi'];
            $satuanunit_nama_besar = $satuanRequest['satuanunit_nama_besar'];
            $satuanunit_nama_kecil = $satuanRequest['satuanunit_nama_kecil'];
            $cacheAdjustmentMasuk = Yii::$app->cache->get("adjus-masuk-barang");
            if ($cacheAdjustmentMasuk == false) {
                Yii::$app->cache->set("adjus-masuk-barang",[]);
                $cacheAdjustmentMasuk = [];
            }

            if (!isset($cacheAdjustmentMasuk[$model->barang_id])) {
                $cacheAdjustmentMasuk[$model->barang_id] = [];
            }

            $setCache = [
                'qty_besar' => $post['qty'],
                'qty_kecil' => $post['qty'] * $qty_kecil,
                'nilai_konversi' => $qty_kecil,
                'satuankonversi_id' => $post['satuankonversi_id'],
                'satuankecil_id' => $satuanRequest['satuankecil_id'],
                'satuanbesar_id' => $satuanRequest['satuanbesar_id'],
                'satuanunit_nama_besar' => $satuanunit_nama_besar,
                'satuanunit_nama_kecil' => $satuanunit_nama_kecil,
                'barang_id' => $post['barang_id'],
                'barang_nama' => $post['barang_nama'],
                'harga_netto' => $post['harga_netto'],
                'tgl_kadaluarsa' => isset($post['tgl_kadaluarsa']) ? date('Y-m-d', strtotime($post['tgl_kadaluarsa'])) : '',
                'no_batch' => $post['no_batch'],
            ];

            $cacheAdjustmentMasuk[$model->barang_id] = $setCache;
            $cacheAdjustmentMasuk = Yii::$app->cache->set("adjus-masuk-barang",$cacheAdjustmentMasuk,3600);

            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionSetListItemKeluar()
    {
        $request = Yii::$app->request;
        $model = new AdjusmenBarangKeluarForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        $post = $request->post('AdjusmenBarangKeluarForm');

        $model->attributes = $post;
        if ($model->validate()) {
            $satuankonversi_id = $post['satuankonversi_id'];
            $satuanRequest = $this->getRequest($satuankonversi_id);
            $qty_kecil = $satuanRequest['nilai_konversi'];
            $satuanunit_nama_besar = $satuanRequest['satuanunit_nama_besar'];
            $satuanunit_nama_kecil = $satuanRequest['satuanunit_nama_kecil'];

            $cacheAdjustmentKeluar = Yii::$app->cache->get("adjus-keluar-barang");
            if ($cacheAdjustmentKeluar == false) {
                Yii::$app->cache->set("adjus-keluar-barang",[]);
                $cacheAdjustmentKeluar = [];
            }

            if (!isset($cacheAdjustmentKeluar[$model->barang_id])) {
                $cacheAdjustmentKeluar[$model->barang_id] = [];
            }

            $cekStok = $this->getRequestStok($post['barang_id']);
            $total_konversi = (int) $post['qty'] * $qty_kecil;
            if($total_konversi > $cekStok) {
                $response['response'] = [
                    'title' => 'Proses Gagal!',
                    'text' => 'Maaf, Stok Barang '.$post['barang_nama'].' tidak mencukupi.'
                ];

                return DocoHelpers::response($response,422);
            }

            $setCache = [
                'qty_besar' => $post['qty'],
                'qty_kecil' => $total_konversi,
                'nilai_konversi' => $qty_kecil,
                'satuankonversi_id' => $post['satuankonversi_id'],
                'satuankecil_id' => $satuanRequest['satuankecil_id'],
                'satuanbesar_id' => $satuanRequest['satuanbesar_id'],
                'satuanunit_nama_besar' => $satuanunit_nama_besar,
                'satuanunit_nama_kecil' => $satuanunit_nama_kecil,
                'barang_id' => $post['barang_id'],
                'barang_nama' => $post['barang_nama'],
                'alasan' => $post['alasan'],
                'no_batch' => $post['no_batch']
            ];

            $cacheAdjustmentKeluar[$model->barang_id] = $setCache;
            $cacheAdjustmentKeluar = Yii::$app->cache->set("adjus-keluar-barang",$cacheAdjustmentKeluar,3600);

            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionSave()
    {
        $adjustForm = $this->getFormScenario();
        $model = new AdjusmenBarangForm;
        $model->scenario = $adjustForm['scenario'];
        $post = Yii::$app->request->post();
        $model->load($post);
        if ($model->validate()) {
            $cache = Yii::$app->cache;
            $cacheAdjustment = ($post['tab-aktif'] == 'masuk') ? $cache->get("adjus-masuk-barang") : $cache->get("adjus-keluar-barang");

            if ($cacheAdjustment == true) {
                try {
                    $result = $this->_restGudang->post('adjustment-barang/save',[
                        'form_params' => [
                            'data' => json_encode($cacheAdjustment),
                            'post' => $post,
                        ]
                    ]);
                    $result = json_decode($result->getBody(),true);
                    $cacheAdjustment = ($post['tab-aktif'] == 'masuk') ?
                    $cache->delete('adjus-masuk-barang') : $cache->delete('adjus-keluar-barang');
                    return DocoHelpers::response($result, false);
                } catch (RequestException $e) {
                    Yii::info($e->getMessage());
                    $response['response']['text'] = 'Terjadi kesalah pada sistem';
                    $response['response']['message'] = $e->getMessage();
                    return DocoHelpers::response($response, 500);
                }
            }
            $response['response']['text'] = 'Tidak ada data.';
            $response['response']['title'] = 'Proses Gagal !';
            return DocoHelpers::response($response, 422);
        } else {
            Yii::error($model->errors);
            return DocoHelpers::response($model->errors, 422, "AdjusmenBarangForm");
        }
    }

    public function actionDeleteListItemMasuk($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $cacheAdjustmentMasuk = Yii::$app->cache->get("adjus-masuk-barang");
        if ($cacheAdjustmentMasuk !== false) {
            if (isset($cacheAdjustmentMasuk[$id])) {
                unset($cacheAdjustmentMasuk[$id]);
                Yii::$app->cache->set("adjus-masuk-barang",$cacheAdjustmentMasuk);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionDeleteListItemKeluar($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $cacheAdjustmentKeluar = Yii::$app->cache->get("adjus-keluar-barang");
        if ($cacheAdjustmentKeluar !== false) {
            if (isset($cacheAdjustmentKeluar[$id])) {
                unset($cacheAdjustmentKeluar[$id]);
                Yii::$app->cache->set("adjus-keluar-barang",$cacheAdjustmentKeluar);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionCetak($no_adjusmen, $tipe)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/adjustment-obat-alkes.pdf";
            $response = $this->_restGudang->get('adjustment-barang/export-pdf?no_adjusmen='.$no_adjusmen.'&tipe='.$tipe,
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

    public function actionGetSatuanKonversi($satuankonversibrg_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
        }

        if($satuankonversibrg_id) {
            $parent_label = $satuankonversibrg_id;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $column = ($satuankonversibrg_id) ? 'satuankonversibrg_id' : 'barang_id';
            $response = $this->_restMaster->get('allow/list-satuan-konversi-barang-adjustment', [
                'query' => [
                    'parent_label' => $parent_label,
                    'column' => $column,
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                if($satuankonversibrg_id) {
                    $result['response'] = [
                        "satuankonversibrg_id" => $value['satuankonversibrg_id'],
                        "nilai_konversi" => $value['nilai_konversi'],
                        "kecil" => $value['kecil'],
                        "besar" => $value['besar'],
                        "satuankecil_id" => $value['satuankecil_id'],
                        "satuanbesar_id" => $value['satuanbesar_id'],
                    ];

                    return $result;
                }
                else {
                    $result['output'][] = [
                        'id' => $value['satuankonversibrg_id'],
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

    protected function getFormScenario()
    {
        return Yii::$app->docoPlugin->execute($this,'adjustment_obat_scenario');
    }
}
