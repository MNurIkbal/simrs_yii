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
use app\modules\gudang\models\AdjusmenObatForm;
use app\modules\gudang\models\AdjusmenObatMasukForm;
use app\modules\gudang\models\AdjusmenObatKeluarForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class AdjustmentObatAlkesController extends DocoController
{
    protected $_title = "Adjustment Obat Alkes";
    protected $_module = '/gudang/adjustment-obat-alkes/';
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
        $model = new AdjusmenObatForm;
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
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');

        $model = new AdjusmenObatMasukForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        $cacheAdjustmentMasuk = Yii::$app->cache->get([
            "adjus-masuk",
            "instalasi_id" => $instalasi_id,
            "ruangan_id" => $ruangan_id,
            "pegawai_id" => $pegawai_id
        ]);
        
        $countMasuk = ($cacheAdjustmentMasuk == false) ? 0 : count($cacheAdjustmentMasuk);
        $satuan = $this->getRequest();

        $id = null;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('adjustment-obat-alkes/create', [
                        'form_params' => $model->attributes
                    ]);
                    \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
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
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');

        $model = new AdjusmenObatKeluarForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        $cacheAdjustmentKeluar = Yii::$app->cache->get([
            "adjus-keluar",
            "instalasi_id" => $instalasi_id,
            "ruangan_id" => $ruangan_id,
            "pegawai_id" => $pegawai_id
        ]);
        
        $countKeluar = ($cacheAdjustmentKeluar == false) ? 0 : count($cacheAdjustmentKeluar);
        $satuan = $this->getRequest();
        $id = null;
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->post('adjustment-obat-alkes/create', [
                        'form_params' => $model->attributes
                    ]);
                    \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
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

    public function actionSearchObatAlkes($tipe)
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/get-data-obat-alkes', [
                            'query' => [
                                'term' => $request->get('term'),
                                'tipe' => $tipe,
                            ]
                        ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];

            $response = [];
            foreach ($data as $key => $value) {
                $satuankecil_nama = ($tipe == 0) ? $value['satuan_kecil'] : $value['satuankecil_nama'];

                $suggestion = 0;
                $hyg = !empty($value["hargaygdigunakan"]) ? $value["hargaygdigunakan"] : "MIN";
                $netto = !empty($value["harganetto"]) ? $value["harganetto"] : 0;
                switch ($hyg) {
                    case "MIN" :
                        $suggestion = $value["hargaminimum"];
                    break;
                    case "MAX" :
                        $suggestion = $value["hargamaksimum"];
                    break;
                    case "AVG" :
                        $suggestion = $value["hargaratarata"];
                    break;
                    default:
                        $suggestion = 0;
                }

                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                    'kode' => $value['obatalkes_kode'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $satuankecil_nama,
                    'qty_tersedia' => ($tipe == 1) ? $value['qty_tersedia'] : 0,
                    'harganetto_ygdipakai' => $netto,
                    'harga_sugesstion' => $suggestion,
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
                                'obatalkes_id' => $request->get('obatalkes_id'),
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
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');

        $cacheAdjustmentMasuk = Yii::$app->cache->get([
            "adjus-masuk", 
            'instalasi_id' => $instalasi_id,
            'ruangan_id' => $ruangan_id,
            'pegawai_id' => $pegawai_id
        ]);

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
                    'rowNum' => $no,
                    'obatalkes_id' => $value['obatalkes_id'],
                    'obatalkes_nama' => $value['obatalkes_nama'],
                    'obatalkes_kode' => $value['obatalkes_kode'],
                    'no_batch' => $value['no_batch'],
                    'keterangan' => $value['keterangan'],
                    'qty_penerimaan' => DocoHelpers::formatNumber($value['qty_besar']).' '.$value['satuanunit_nama_besar'],
                    'qty_konversi' => DocoHelpers::formatNumber($value['qty_kecil']).' '.$value['satuanunit_nama_kecil'],
                    'satuanunit_nama_besar' => $value['satuanunit_nama_besar'],
                    'satuanunit_nama_kecil' => $value['satuanunit_nama_kecil'],
                    'qty_besar' => DocoHelpers::formatNumber($value['qty_besar']),
                    'qty_kecil' => DocoHelpers::formatNumber($value['qty_kecil']),
                    'harga_netto' => DocoHelpers::formatNumber($value['harga_netto']),
                    'tgl_kadaluarsa' => date('d M Y', strtotime($value['tgl_kadaluarsa'])),
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
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        
        $cacheAdjustmentKeluar = Yii::$app->cache->get([
            "adjus-keluar",
            "instalasi_id" => $instalasi_id,
            "ruangan_id" => $ruangan_id,
            "pegawai_id" => $pegawai_id
        ]);
        
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if ($cacheAdjustmentKeluar !== false) {
            $no = $request->get('start',1);
            Yii::error($cacheAdjustmentKeluar);
            foreach ($cacheAdjustmentKeluar as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'obatalkes_id' => $value['obatalkes_id'],
                    'obatalkes_nama' => $value['obatalkes_nama'],
                    'obatalkes_kode' => $value['obatalkes_kode'],
                    'qty_pengeluaran' => DocoHelpers::formatNumber($value['qty_besar']).' '.$value['satuanunit_nama_besar'],
                    'qty_konversi' => DocoHelpers::formatNumber($value['qty_kecil']).' '.$value['satuanunit_nama_kecil'],
                    'satuanunit_nama_besar' => $value['satuanunit_nama_besar'],
                    'satuanunit_nama_kecil' => $value['satuanunit_nama_kecil'],
                    'qty_besar' => DocoHelpers::formatNumber($value['qty_besar']),
                    'qty_kecil' => DocoHelpers::formatNumber($value['qty_kecil']),
                    'alasan' => $value['alasan'],
                    'no_batch' => $value['no_batch'],
                    'keterangan' => $value['keterangan'],
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
        $request = $this->_restGudang->request('GET', 'adjustment-obat-alkes/generate-api', [
            'query' => [
                'satuankonversi_id' => $satuankonversi_id
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    private function getRequestStok($obatalkes_id)
    {
        $workspace = Yii::$app->session->get('active_workspace');
        $ruangan_id = $workspace['ruangan_id'];
        $request = $this->_restGudang->request('GET', 'adjustment-obat-alkes/cek-stok', [
            'query' => [
                'ruangan_id' => $ruangan_id,
                'obatalkes_id' => $obatalkes_id
            ]
        ]);
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $attributes;
    }

    public function actionSetListItemMasuk()
    {
        $request = Yii::$app->request;
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        
        $model = new AdjusmenObatMasukForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        $post = $request->post('AdjusmenObatMasukForm');

        $model->attributes = $post;
        if ($model->validate()) {
            $satuankonversi_id = $post['satuankonversi_id'];
            $satuanRequest = $this->getRequest($satuankonversi_id);
            $qty_kecil = $satuanRequest['nilai_konversi'];
            $satuanunit_nama_besar = $satuanRequest['satuanunit_nama_besar'];
            $satuanunit_nama_kecil = $satuanRequest['satuanunit_nama_kecil'];

            $cacheAdjustmentMasuk = Yii::$app->cache->get([
                "adjus-masuk",
                "instalasi_id" => $instalasi_id,
                "ruangan_id" => $ruangan_id,
                "pegawai_id" => $pegawai_id
            ]);

            if ($cacheAdjustmentMasuk == false) {
                Yii::$app->cache->set(
                    [
                        "adjus-masuk",
                        "instalasi_id" => $instalasi_id,
                        "ruangan_id" => $ruangan_id,
                        "pegawai_id" => $pegawai_id
                    ], 
                    []
                );
                
                $cacheAdjustmentMasuk = [];
            }

            if (!isset($cacheAdjustmentMasuk[$model->obatalkes_id])) {
                $cacheAdjustmentMasuk[$model->obatalkes_id] = [];
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
                'obatalkes_id' => $post['obatalkes_id'],
                'obatalkes_nama' => $post['obatalkes_nama'],
                'obatalkes_kode' => $post['obatalkes_kode'],
                'harga_netto' => $post['harga_netto'],
                'no_batch' => $post['no_batch'],
                'keterangan' => $post['keterangan'],
                'tgl_kadaluarsa' => date('Y-m-d', strtotime($post['tgl_kadaluarsa'])),
            ];

            $cacheAdjustmentMasuk[$model->obatalkes_id] = $setCache;
            $cacheAdjustmentMasuk = Yii::$app->cache->set(
                [
                    "adjus-masuk",
                    "instalasi_id" => $instalasi_id,
                    "ruangan_id" => $ruangan_id,
                    "pegawai_id" => $pegawai_id
                ],
                $cacheAdjustmentMasuk,
                3600)
            ;
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
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');

        $model = new AdjusmenObatKeluarForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        $post = $request->post('AdjusmenObatKeluarForm');
        Yii::error($post);
        $model->attributes = $post;
        if ($model->validate()) {
            $satuankonversi_id = $post['satuankonversi_id'];
            $satuanRequest = $this->getRequest($satuankonversi_id);

            $qty_kecil = $satuanRequest['nilai_konversi'];
            $satuanunit_nama_besar = $satuanRequest['satuanunit_nama_besar'];
            $satuanunit_nama_kecil = $satuanRequest['satuanunit_nama_kecil'];

            $cacheAdjustmentKeluar = Yii::$app->cache->get([
                "adjus-keluar",
                "instalasi_id" => $instalasi_id,
                "ruangan_id" => $ruangan_id,
                "pegawai_id" => $pegawai_id
            ]);

            if ($cacheAdjustmentKeluar == false) {
                Yii::$app->cache->set(
                    [
                        "adjus-keluar",
                        "instalasi_id" => $instalasi_id,
                        "ruangan_id" => $ruangan_id,
                        "pegawai_id" => $pegawai_id
                    ],
                    []
                );

                $cacheAdjustmentKeluar = [];
            }

            if (!isset($cacheAdjustmentKeluar[$model->obatalkes_id])) {
                $cacheAdjustmentKeluar[$model->obatalkes_id] = [];
            }

            $cekStok = $this->getRequestStok($post['obatalkes_id']);
            $total_konversi = $post['qty'] * $qty_kecil;
            if($total_konversi > $cekStok) {
                $response['response'] = [
                    'title' => 'Proses Gagal!',
                    'text' => 'Maaf, Stok Obat '.$post['obatalkes_nama'].' tidak mencukupi.'
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
                'obatalkes_id' => $post['obatalkes_id'],
                'obatalkes_nama' => $post['obatalkes_nama'],
                'obatalkes_kode' => $post['obatalkes_kode'],
                'alasan' => $post['alasan'],
                'no_batch' => $post['no_batch'],
                'keterangan' => $post['keterangan'],
            ];

            $cacheAdjustmentKeluar[$model->obatalkes_id] = $setCache;
            $cacheAdjustmentKeluar = Yii::$app->cache->set(
                [
                    "adjus-keluar",
                    "instalasi_id" => $instalasi_id,
                    "ruangan_id" => $ruangan_id,
                    "pegawai_id" => $pegawai_id
                ], 
                $cacheAdjustmentKeluar, 
                3600
            );

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
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');

        $adjustForm = $this->getFormScenario();
        $model = new AdjusmenObatForm;
        $model->scenario = $adjustForm['scenario'];
        $post = Yii::$app->request->post();
        $model->load($post);
        if ($model->validate()) {
            $cache = Yii::$app->cache;
            
            if($post['tab-aktif'] == 'masuk') {
                $cacheAdjustment = $cache->get([
                    "adjus-masuk",
                    "instalasi_id" => $instalasi_id,
                    "ruangan_id" => $ruangan_id,
                    "pegawai_id" => $pegawai_id
                ]);
            } else {
                $cacheAdjustment = $cache->get([
                    "adjus-keluar",
                    "instalasi_id" => $instalasi_id,
                    "ruangan_id" => $ruangan_id,
                    "pegawai_id" => $pegawai_id
                ]);
            }

            if ($cacheAdjustment == true) {
                try {

                    $result = $this->_restGudang->post('adjustment-obat-alkes/save',[
                        'form_params' => [
                            'data' => json_encode($cacheAdjustment),
                            'post' => $post,
                        ]
                    ]);
                    $result = json_decode($result->getBody(),true);

                    \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                    
                    if($post['tab-aktif'] == 'masuk') {
                        $cacheAdjustment = $cache->delete([
                            "adjus-masuk",
                            "instalasi_id" => $instalasi_id,
                            "ruangan_id" => $ruangan_id,
                            "pegawai_id" => $pegawai_id
                        ]);
                    } else {
                        $cacheAdjustment = $cache->delete([
                            "adjus-keluar",
                            "instalasi_id" => $instalasi_id,
                            "ruangan_id" => $ruangan_id,
                            "pegawai_id" => $pegawai_id
                        ]);
                    }

                    return DocoHelpers::response($result, false);
                } catch (RequestException $e) {
                    Yii::info($e->getMessage());
                    $response['response']['text'] = 'Terjadi kesalah pada sistem';
                    $response['response']['message'] = $e->getMessage();
                    return DocoHelpers::response($response, false);
                } catch (\Exception $e) {
                    Yii::info($e->getMessage());
                    $response['response']['text'] = 'Terjadi kesalah pada sistem';
                    $response['response']['message'] = $e->getMessage();
                    return DocoHelpers::response($response, false);
                }
            }
        }else{
            return DocoHelpers::response($model->errors, 422, "AdjusmenObatForm");
        }
    }

    public function actionDeleteListItemMasuk($id = null)
    {
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');

        $id = DocoHelpers::decrypt($id);

        $cacheAdjustmentMasuk = Yii::$app->cache->get([
            "adjus-masuk",
            "instalasi_id" => $instalasi_id,
            "ruangan_id" => $ruangan_id,
            "pegawai_id" => $pegawai_id
        ]);

        if ($cacheAdjustmentMasuk !== false) {
            if (isset($cacheAdjustmentMasuk[$id])) {
                unset($cacheAdjustmentMasuk[$id]);
                Yii::$app->cache->set([
                    "adjus-masuk",
                    "instalasi_id" => $instalasi_id,
                    "ruangan_id" => $ruangan_id,
                    "pegawai_id" => $pegawai_id
                ], $cacheAdjustmentMasuk);
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
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');

        $id = DocoHelpers::decrypt($id);
        
        $cacheAdjustmentKeluar = Yii::$app->cache->get([
            "adjus-keluar",
            "instalasi_id" => $instalasi_id,
            "ruangan_id" => $ruangan_id,
            "pegawai_id" => $pegawai_id
        ]);
        
        if ($cacheAdjustmentKeluar !== false) {
            if (isset($cacheAdjustmentKeluar[$id])) {
                unset($cacheAdjustmentKeluar[$id]);
                Yii::$app->cache->set(
                    [
                        "adjus-keluar",
                        "instalasi_id" => $instalasi_id,
                        "ruangan_id" => $ruangan_id,
                        "pegawai_id" => $pegawai_id
                    ],
                    $cacheAdjustmentKeluar
                );
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
            return Yii::$app->report->exec('gudang/adjustment-obat-alkes/export-pdf?no_adjusmen='.$no_adjusmen.'&tipe='.$tipe);
            $response = $this->_restGudang->get('adjustment-obat-alkes/export-pdf?no_adjusmen='.$no_adjusmen.'&tipe='.$tipe,
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

    public function actionGetSatuanKonversi($satuankonversi_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
        }

        if($satuankonversi_id) {
            $parent_label = $satuankonversi_id;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $column = ($satuankonversi_id) ? 'satuankonversi_id' : 'obatalkes_id';

            $response = $this->_restMaster->get('allow/list-satuan-konversi', [
                'query' => [
                    'parent_label' => $parent_label,
                    'column' => $column,
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                if($satuankonversi_id) {
                    $result['response'] = [
                        "satuankonversi_id" => $value['satuankonversi_id'],
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

    public function actionAlertHarga($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $query = [
            "id" => $id,
            "tipe" => "ADJ_MASUK"
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
        }
        return DocoHelpers::response($result);
    }

    public function actionUpdateHarga()
    {
        $request = Yii::$app->request;
        try {
            $response = $this->_restGudang->post('adjustment-obat-alkes/save-update-harga', [
                "form_params" => $request->post()
            ]);
            $body = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage());
        }

        return DocoHelpers::response($body);
    }

    protected function getFormScenario()
    {
        return Yii::$app->docoPlugin->execute($this,'adjustment_obat_scenario');
    }
}
