<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2019-01-08 16:42:20
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-31 14:15:31
 */
namespace Doco\ambulan\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\Notifications\AmbulanceNotification;
use app\components\DHtml;
use app\modules\ambulan\models\PesanAmbulanForm;
use app\modules\ambulan\models\PesanAmbulanDetailForm;
use app\modules\ambulan\models\FormAddTindakan;
use app\modules\ambulan\models\FormAddObat;

class PermintaanAmbulanController extends DocoController
{
    protected $_title;
    protected $_restAmbulan;
    protected $_restMaster;
    protected $_module = '/ambulan/permintaan-ambulan/';
    protected $allowAction = [
        'list-pemesanan',
        'get-data-ambulan',
        'search-tindakan',
        'search-obat',
    ];

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Permintaan Ambulan');
        $this->_restAmbulan = Yii::$app->docoRest->ambulan;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        Yii::$app->cache->delete('ambulan-tindakan-luar-'.$user_login);
        Yii::$app->cache->delete('ambulan-tindakan-rs-'.$user_login);
        Yii::$app->cache->delete('ambulan-obat-rs-'.$user_login);
        return $this->render('index', get_defined_vars());
    }

    public function actionPasienLuar()
    {
        $request = Yii::$app->request;
        $model = new PesanAmbulanForm;
        $modelDetail = new PesanAmbulanDetailForm;
        $modelTindakan = new FormAddTindakan;
        $model->scenario = 'luar';
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $formNameDetail = substr(strrchr(get_class($modelDetail), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restAmbulan->post('permintaan-ambulan/create', [
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

        return $this->renderAjax('pasien_luar', get_defined_vars());
    }

    public function actionPasienRs()
    {
        $request = Yii::$app->request;
        $model = new PesanAmbulanForm;
        $modelTindakan = new FormAddTindakan;
        $modelObat = new FormAddObat;
        $model->scenario = 'rs';
        $modelDetail = new PesanAmbulanDetailForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $formNameDetail = substr(strrchr(get_class($modelDetail), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restAmbulan->post('permintaan-ambulan/create', [
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

        return $this->renderAjax('pasien_rs', get_defined_vars());
    }

    public function actionListPemesanan($tipe = null)
    {   
        $tglPesanAmbulan = Yii::$app->request->get('tgl_pesanambulan', date("Y-m-d"));
        $tipe = isset($tipe) ? $tipe : "luar";
        $path = ($tipe == 'rs') ? 'list-pemesanan-rs' : 'list-pemesanan';
        return $this->renderAjax($path, get_defined_vars());
    }

     public function actionGetDataAmbulan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $tgl_pesanambulan = $request->get('tgl_pesanambulan');

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restAmbulan->get('permintaan-ambulan/pemesanan?'.http_build_query($yiiRestfulParams), ['form_params' => ['tgl_pesanambulan' => $tgl_pesanambulan]]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['ambulan_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgl_pesanambulan'] = date('d M Y', strtotime($value['tgl_pemesanan']));
                $value['pilih'] = '';
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

    public function actionSavePasien()
    {
        $post = Yii::$app->request->post();
        $postData = $post['PesanAmbulanForm'];
        $model = new PesanAmbulanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        try {
            $jenis = $postData['jenis'];

            $model->scenario = ($jenis == "luar") ? "luar" : "rs";
            $cache = Yii::$app->cache;
            $userLogin = Yii::$app->user->identity->loginpemakai_id;
            if($jenis == "rs") {
                $cacheTindakan = $cache->get("ambulan-tindakan-rs-".$userLogin);
                $cacheObat = $cache->get("ambulan-obat-rs-".$userLogin);
            } else {
                $cacheTindakan = $cache->get("ambulan-tindakan-luar-".$userLogin);
                // $cacheObat = $cache->get("ambulan-obat-luar-".$userLogin);
                $cacheObat = [];
            }
            $model->load($post);
            if ($model->validate()) {
                $result = $this->_restAmbulan->post('permintaan-ambulan/save',[
                    'form_params' => [
                        'cacheTindakan' => json_encode($cacheTindakan),
                        'cacheObat' => json_encode($cacheObat),
                        'post' => $post,
                    ]
                ]);
                $result = json_decode($result->getBody(),true);
                if($result['metadata']['status'] == 200) {
                    Yii::$app->cache->delete('ambulan-tindakan-luar-'.$userLogin);
                    Yii::$app->cache->delete('ambulan-tindakan-rs-'.$userLogin);
                    Yii::$app->cache->delete('ambulan-obat-rs-'.$userLogin);
                    // set notification and publish new notification
                    AmbulanceNotification::newOrder($result['response']['orderRecord']);
                }

                return DocoHelpers::response($result, false);
            } else {
                $errors = DocoHelpers::parseError($model->errors,'PesanAmbulanForm');
                return DocoHelpers::response([
                    'response' => [
                        'data' => $errors
                    ]
                ],422);
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
            $response['response']['message'] = $e->getMessage();
            return DocoHelpers::response($response, 500);
        }
    }

    public function actionSearchTindakan()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restAmbulan->get('permintaan-ambulan/get-data-tarif-ambulan', [
                'query' => [
                    'term' => $request->get('term'),
                    'ambulan_id' => $request->get('ambulan_id'),
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];

            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => $value['daftartindakan_nama'],
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
            $result = $this->_restAmbulan->get('permintaan-ambulan/get-data-obat', [
                            'query' => [
                                'term' => $request->get('term'),
                            ]
                        ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];

            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                    'satuan_kecil' => $value['satuan_kecil'],
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

    public function actionSearchPasien()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restAmbulan->get('permintaan-ambulan/get-data-pasien', [
                            'query' => [
                                'term' => $request->get('term'),
                            ]
                        ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['pasien_id'],
                    'text' => $value['no_rekam_medik'].' - '.$value['nama_pasien'],
                    'nama_pasien' => $value['nama_pasien'],
                    'tempat_lahir' => $value['tempat_lahir'],
                    'tanggal_lahir' => date('d-M-Y', strtotime($value['tanggal_lahir'])),
                    'jenis_kelamin' => $value['jenis_kelamin'],
                    'ruangan_nama' => $value['ruangan_nama'],
                    'instalasi_nama' => $value['instalasi_nama'],
                    'diagnosa' => ($value['diagnosa']) ? $value['diagnosa'] : '-',
                    'pendaftaran_id' => $value['pendaftaran_id'],
                    'kelas' => $value['kelaspelayanan_nama'],
                    'cara_bayar' => $value['carabayar_nama'] . ' / ' . $value['penjamin_nama'],
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

    public function actionAddTindakanDefault($ambulan_id, $pasien_id)
    {
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $request = Yii::$app->request;
        $resetCacheTindakan = Yii::$app->cache->get('ambulan-tindakan-rs-'.$userLogin);
        $resetCacheObat = Yii::$app->cache->get('ambulan-obat-rs-'.$userLogin);
        // Clear Tindakan Tetap Ambulan Sebelumnya
        if(!empty($resetCacheTindakan)) {
            foreach ($resetCacheTindakan as $key => $value) {
                // if($value['is_default'] == 'Tetap') {
                    unset($resetCacheTindakan[$value['daftartindakan_id']]);
                // }
            }
        }

        try {
            $response = $this->_restAmbulan->get('permintaan-ambulan/get-detail-tindakan', [
                'query' => [
                    'ambulan_id' => $ambulan_id,
                    'tipe' => 2,
                    'pasien_id' => $pasien_id
                ]
            ]);
            $body = json_decode($response->getBody(), true);

            if(!empty($body['response']['tindakan'])) {
                foreach ($body['response']['tindakan'] as $key => $value) {
                    $jumlah_tarif2 = $value['harga_tariftindakan'];
                    $resetCacheTindakan[$value['daftartindakan_id']] = [
                        'daftartindakan_id' => $value['daftartindakan_id'],
                        'daftartindakan_nama' => ($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : '',
                        'is_default' => ($value['is_default'] == true) ? "Tetap" : "Tidak Tetap",
                        'harga_tariftindakan' => $jumlah_tarif2,
                        'jumlah_tarif' => $jumlah_tarif2,
                        'jumlah_tarif2' => $jumlah_tarif2,
                        'qty' => 1,
                    ];
                }
            }
            if (!empty($body['response']['obat'])) {
                foreach ($body['response']['obat'] as $key => $value) {
                    $resetCacheObat[$value['obatalkes_id']] = [
                        'obatalkes_id' => $value['obatalkes_id'],
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'qty' => $value['qty'],
                        'stok' => $value['stok']
                    ];
                }
            }
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],422);
        }

        Yii::$app->cache->set('ambulan-tindakan-rs-'.$userLogin,$resetCacheTindakan);
        Yii::$app->cache->set('ambulan-obat-rs-'.$userLogin, $resetCacheObat);
        return DocoHelpers::response([
            'message' => 'Add tindakan default berhasil'
        ]);

    }

    public function actionTambahTindakan($tipe = null)
    {
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $model = new FormAddTindakan;
        $typeChace = "ambulan-tindakan-luar-".$userLogin;
        $pasien_id = $request->post('pasien_id',null);
        if ($tipe != 1) $typeChace = "ambulan-tindakan-rs-".$userLogin;
        $model->load($request->post());
        if ($model->validate()) {
            if(!empty($model->daftartindakan_id)) {
                $daftartindakan_id = $model->daftartindakan_id;
                $cacheTindakan = $cache->get($typeChace);
                $dataTindakan = $this->getRequest($model->daftartindakan_id,$pasien_id);

                if (empty($dataTindakan)) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal !',
                            'text' => 'Terjadi kesalah pada sistem'
                        ]
                    ],422);
                }

                if ($cacheTindakan == false) {
                    $cache->set($typeChace,[]);
                    $cacheTindakan = [];
                }

                if (!isset($cacheTindakan[$daftartindakan_id])) {
                    $cacheTindakan[$daftartindakan_id] = [];
                }

                $biayaTetap = "Tidak Tetap";
                if (isset($cacheTindakan[$daftartindakan_id]['is_default'])) {
                    $biayaTetap = $cacheTindakan[$daftartindakan_id]['is_default'];
                }

                $cacheTindakan[$daftartindakan_id] = [
                    'daftartindakan_id' => $daftartindakan_id,
                    'daftartindakan_nama' => $dataTindakan['daftartindakan_nama'],
                    'is_default' => $biayaTetap,
                    'qty' => $model->qty,
                    'harga_tariftindakan' => $dataTindakan['harga_tariftindakan'],
                    'jumlah_tarif' => $model->qty * $dataTindakan['harga_tariftindakan'],
                ];

                $cacheTindakan = $cache->set($typeChace,$cacheTindakan,3600);

                $response['response'] = [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data berhasil di tambah'
                ];

                return DocoHelpers::response($response);
            }
        }else{
            return DocoHelpers::response($model->errors,422,'FormAddTindakan');
        }
    }

    public function actionTambahObat($tipe)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $cache = Yii::$app->cache;
        $model = new FormAddObat;
        $model->load($request->post());
        if ($model->validate()) {
            $userLogin = Yii::$app->user->identity->loginpemakai_id;
            $cacheObat = $cache->get("ambulan-obat-rs-".$userLogin);
            $obatalkes_id = $model->obatalkes_id;
            $dataObat = $this->getRequestObat($obatalkes_id);

            if (empty($dataObat)) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal !',
                        'text' => 'Terjadi kesalah pada sistem'
                    ]
                ],422);
            }

            if ($dataObat['stok'] != 0 && $model->qty > $dataObat['stok']) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal !',
                        'text' => 'Stok Obat ' . $dataObat['obatalkes_nama'] . ' tidak mencukupi'
                    ]
                ],422);
            }

            if ($cacheObat == false) {
                $cache->set("ambulan-obat-rs-".$userLogin,[]);
                $cacheObat = [];
            }

            if (!isset($cacheObat[$obatalkes_id])) {
                $cacheObat[$obatalkes_id] = [];
            }

            $cacheObat[$obatalkes_id] = [
                'obatalkes_id' => $obatalkes_id,
                'obatalkes_nama' => $dataObat['obatalkes_nama'],
                'satuan_kecil' => $dataObat['satuan_kecil'],
                'qty' => $model->qty,
                'stok' => $dataObat['stok'],
            ];

            $cache->set("ambulan-obat-rs-".$userLogin,$cacheObat,3600);

            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($response);
        } else {
            return DocoHelpers::response($model->errors,422,'FormAddObat');
        }
    }

    private function getRequest($daftartindakan_id = null, $pasien_id = null)
    {
        try {
            $request = $this->_restAmbulan->request('GET', 'permintaan-ambulan/get-data-tarif-ambulan', [
                'query' => [
                    'daftartindakan_id' => $daftartindakan_id,
                    'pasien_id' => $pasien_id
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
        } catch (RequestException $e) {
            $attributes = [];
        }

        return $attributes;
    }

    private function getRequestObat($obatalkes_id = null)
    {
        try {
            $request = $this->_restAmbulan->request('GET', 'permintaan-ambulan/get-data-obat-ambulan', [
                'query' => [
                    'obatalkes_id' => $obatalkes_id
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
        } catch (RequestException $e) {
            $attributes = [];
        }

        return $attributes;
    }

    public function actionGetListTindakanLuar($ambulan_id = null)
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cacheTindakan = Yii::$app->cache->get("ambulan-tindakan-luar-".$userLogin);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $no_urut = $request->get('start', 1);
        $resetCache = [];

        if(!empty($cacheTindakan)) {
            $no = $request->get('start',1);
            foreach ($cacheTindakan as $key => $value) {
                $qty = ($value['qty']) ? $value['qty'] : null;
                $jumlah_tarif2 = $value['harga_tariftindakan'];
                $no_urut++;
                $primaryKey = DocoHelpers::encrypt($key);
                $aksi = '';
                if($value['is_default'] == "Tidak Tetap") {
                    $aksi = Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete-cache-tindakan-luar',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module .'delete-cache-tindakan?tipe=1&id='.$primaryKey]),
                        ]
                    );
                }
                $qty = !empty($value['harga_tariftindakan']) ? $value['qty'] : 1;
                $data = [
                    'rowNum' => $no_urut,
                    'daftartindakan_id' => $value['daftartindakan_id'],
                    'daftartindakan_nama' => $value['daftartindakan_nama'],
                    'is_default' => $value['is_default'],
                    'qty' => Html::textInput('Tindakan[qty]['.$value['daftartindakan_id'].']', $qty, [
                        'class' => 'form-control doco-number qty',
                        'data-id' => $value['daftartindakan_id'],
                        'data-val' => $value['qty']
                    ]),
                    'harga_tariftindakan' => DocoHelpers::formatNumber($value['harga_tariftindakan']),
                    'jumlah_tarif' => DocoHelpers::formatNumber($value['qty'] * $value['harga_tariftindakan']),
                    'jumlah_tarif2' => $value['qty'] * $jumlah_tarif2,
                    'aksi' => $aksi,
                    'harga' => $value['harga_tariftindakan']
                ];
                $result['data'][] = $data;
            }
        }

        $result['recordsTotal'] = count($result['data']);
        $result['recordsFiltered'] = count($result['data']);
        $result['draw'] = $request->get('draw');
        return $result;

    }

    public function actionGetListTindakanRs()
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cache = Yii::$app->cache;
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cacheTindakan = $cache->get("ambulan-tindakan-rs-".$userLogin);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $no_urut = $request->get('start', 1);
        $resetCache = [];

        if(!empty($cacheTindakan)) {
            foreach ($cacheTindakan as $key => $value) {
                $qty = ($value['qty']) ? $value['qty'] : null;
                $jumlah_tarif2 = $value['harga_tariftindakan'];
                $no_urut++;
                $primaryKey = DocoHelpers::encrypt($key);
                $aksi = '';
                if($value['is_default'] == "Tidak Tetap") {
                    $aksi = Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete-cache-tindakan-rs',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module .'delete-cache-tindakan?tipe=2&id='.$primaryKey]),
                        ]
                    );

                }

                $data = [
                    'rowNum' => $no_urut,
                    'daftartindakan_id' => $value['daftartindakan_id'],
                    'daftartindakan_nama' => $value['daftartindakan_nama'],
                    'is_default' => $value['is_default'],
                    'qty' => Html::textInput('Tindakan[qty]['.$value['daftartindakan_id'].']', $value['qty'], [
                        'class' => 'form-control doco-number qty_tindakan_rs',
                        'data-id' => $value['daftartindakan_id'],
                        'data-val' => $value['qty']
                    ]),
                    'harga_tariftindakan' => DocoHelpers::formatNumber($value['harga_tariftindakan']),
                    'jumlah_tarif' => DocoHelpers::formatNumber($value['jumlah_tarif']),
                    'jumlah_tarif2' => $value['qty'] * $jumlah_tarif2,
                    'aksi' => $aksi,
                    'harga' => $value['harga_tariftindakan']
                ];

                $result['data'][] = $data;
            }
        }


        $result['recordsTotal'] = count($result['data']);
        $result['recordsFiltered'] = count($result['data']);
        $result['draw'] = $request->get('draw');
        return $result;
    }

    public function actionGetListObatRs()
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cacheObat = Yii::$app->cache->get("ambulan-obat-rs-".$userLogin);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $no_urut = $request->get('start', 1);
        $resetCache = [];

        if (!empty($cacheObat)) {
            foreach ($cacheObat as $key => $value) {
                $no_urut++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data = [
                    'rowNum' => $no_urut,
                    'obatalkes_id' => $value['obatalkes_id'],
                    'obatalkes_nama' => $value['obatalkes_nama'],
                    'qty' => $value['qty'],
                    'stok' => $value['stok'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete-cache-obat-rs',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module .'delete-cache-obat?tipe=2&id='.$primaryKey]),
                        ]
                    )
                ];
                $result['data'][] = $data;
            }
        }

        $result['recordsTotal'] = $no_urut;
        $result['recordsFiltered'] = $no_urut;
        return $result;
    }

    public function actionDeleteCacheTindakan($id = null)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $tipe = $get['tipe'];
        $cache = Yii::$app->cache;
        $id = DocoHelpers::decrypt($id);
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cacheTindakan = ($tipe == 1) ? $cache->get("ambulan-tindakan-luar-".$userLogin) :
        $cache->get("ambulan-tindakan-rs-".$userLogin);

        if ($cacheTindakan !== false) {

            if (isset($cacheTindakan[$id])) {
                unset($cacheTindakan[$id]);
                if($tipe == 1) {
                    Yii::$app->cache->set("ambulan-tindakan-luar-".$userLogin,$cacheTindakan);
                } else {
                    Yii::$app->cache->set("ambulan-tindakan-rs-".$userLogin,$cacheTindakan);
                }
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionDeleteCacheObat($id = null)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $tipe = $get['tipe'];
        $cache = Yii::$app->cache;
        $id = DocoHelpers::decrypt($id);
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cacheObat = ($tipe == 1) ? $cache->get("ambulan-obat-luar-".$userLogin) :
        $cache->get("ambulan-obat-rs-".$userLogin);
        if ($cacheObat !== false) {
            if (isset($cacheObat[$id])) {
                unset($cacheObat[$id]);
                if($tipe == 1) {
                    Yii::$app->cache->set("ambulan-obat-luar-".$userLogin,$cacheObat);
                } else {
                    Yii::$app->cache->set("ambulan-obat-rs-".$userLogin,$cacheObat);
                }
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data Obat Alkes berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionUpdateCache($tipe = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $tipe = isset($tipe) ? $tipe : 1;
        $qty = $post['qty'];

        $daftartindakan_id = $post['daftartindakan_id'];
        $setCache = [];

        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        if($tipe == 1) {
            $cacheTindakan = Yii::$app->cache->get("ambulan-tindakan-luar-".$userLogin);
        } else {
            $cacheTindakan = Yii::$app->cache->get("ambulan-tindakan-rs-".$userLogin);
        }

        if(isset($cacheTindakan[$daftartindakan_id])) {
            $harga = $cacheTindakan[$daftartindakan_id]['harga_tariftindakan'];
            $cacheTindakan[$daftartindakan_id]['qty'] = $qty;
            $cacheTindakan[$daftartindakan_id]['jumlah_tarif'] = $harga * $qty;
            $cacheTindakan[$daftartindakan_id]['jumlah_tarif2'] = $harga * $qty;
        }

        if($tipe == 1) {
            $cacheTindakan = Yii::$app->cache->set("ambulan-tindakan-luar-".$userLogin,$cacheTindakan,3600);
        } else {
            $cacheTindakan = Yii::$app->cache->set("ambulan-tindakan-rs-".$userLogin,$cacheTindakan,3600);
        }

        $response = [];
        return DocoHelpers::response($response);
    }

    public function actionCetak($no_pesanambulan)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/pemesanan-ambulan.pdf";
            $response = $this->_restAmbulan->get('permintaan-ambulan/export-pdf?no_pesanambulan='.$no_pesanambulan,
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

    public function actionCetakRs($no_pesanambulan)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/pemesanan-ambulan.pdf";
            $response = $this->_restAmbulan->get('permintaan-ambulan/export-pdf-pasien-rs?no_pesanambulan='.$no_pesanambulan,
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

    public function actionDeleteTindakanLuar($ambulan_id, $daftartindakan_id)
    {
        $ambulan_id = DocoHelpers::decrypt($ambulan_id);
        try {
            $response = $this->_restAmbulan->request('GET', 'permintaan-ambulan/delete-tindakan-luar',[
                'query' => ['ambulan_id' => $ambulan_id, 'daftartindakan_id' => $daftartindakan_id]
            ]);

            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDeleteObatRs($ambulan_id, $obatalkes_id)
    {
        $ambulan_id = DocoHelpers::decrypt($ambulan_id);
        try {
            $response = $this->_restAmbulan->request('GET', 'permintaan-ambulan/delete-obat',[
                'query' => ['ambulan_id' => $ambulan_id, 'obatalkes_id' => $obatalkes_id]
            ]);

            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionTambahTarifAmbulan($ambulan_id = null)
    {
        $resetCache = [];
        if(!empty($ambulan_id)) {
            $filter['ambulan_id'] = $ambulan_id;
            $filter['tipe'] = 1;
            $userLogin = Yii::$app->user->identity->loginpemakai_id;
            $cacheTindakan = Yii::$app->cache->get("ambulan-tindakan-luar-".$userLogin);
            if(!empty($cacheTindakan)) {
                foreach ($cacheTindakan as $key => $value) {
                    // if($value['is_default'] == 'Tetap') {
                        unset($cacheTindakan[$value['daftartindakan_id']]);
                    // }
                }
            }
            try {
                $response = $this->_restAmbulan->get('permintaan-ambulan/get-detail-tindakan', [
                    'query' => $filter
                ]);
                $body = json_decode($response->getBody(), true);
                if(!empty($body['response']['tindakan'])) {
                    foreach ($body['response']['tindakan'] as $key => $value) {
                        $jumlah_tarif2 = $value['harga_tariftindakan'];
                        $cacheTindakan[$value['daftartindakan_id']] = [
                            'daftartindakan_id' => $value['daftartindakan_id'],
                            'daftartindakan_nama' => ($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : '',
                            'is_default' => ($value['is_default'] == true) ? "Tetap" : "Tidak Tetap",
                            'harga_tariftindakan' => $jumlah_tarif2,
                            'jumlah_tarif' => $jumlah_tarif2,
                            'jumlah_tarif2' => $jumlah_tarif2,
                            'qty' => 1,
                        ];
                    }
                }
                if (!empty($body['response']['obat'])) {
                    foreach ($body['response']['obat'] as $key => $value) {
                        $resetCacheObat[$value['obatalkes_id']] = [
                            'obatalkes_id' => $value['obatalkes_id'],
                            'obatalkes_nama' => $value['obatalkes_nama'],
                            'qty' => $value['qty'],
                            'stok' => $value['stok']
                        ];
                    }
                }else{
                    $resetCacheObat = [];
                }
            } catch (RequestException $e) {
                $result['error'] = $e->getMessage();
                return $result;
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                return $result;
            }
        }

        Yii::$app->cache->set("ambulan-tindakan-luar-".$userLogin, $cacheTindakan);
        Yii::$app->cache->set("ambulan-obat-luar-".$userLogin, $resetCacheObat);
        return DocoHelpers::response([
            'message' => 'Tindakan berhasil'
        ]);
    }

    public function actionTambahObatAmbulan($ambulan_id = null)
    {
        if(!empty($ambulan_id) /*&& $pasien_id*/) {
            $yiiRestfulParams['ambulan_id'] = $ambulan_id;
            // $yiiRestfulParams['pasien_id'] = $pasien_id;
            $userLogin = Yii::$app->user->identity->loginpemakai_id;
            $cacheObat = Yii::$app->cache->get("ambulan-obat-rs-".$userLogin);
            if(!empty($cacheObat)) {
                foreach ($cacheObat as $key => $value) {
                    if(isset($cacheObat[$value['obatalkes_id']])) {
                        unset($cacheObat[$value['obatalkes_id']]);
                    }
                }
            }
            try {
                $response = $this->_restAmbulan->get('permintaan-ambulan/get-detail-obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
                $body = json_decode($response->getBody(), true);
                if(!empty($body['response']['data'])) {
                    foreach ($body['response']['data'] as $key => $value) {
                        // if(isset($resetCache[$value['obatalkes_id']])) continue;
                        $resetCache[$value['obatalkes_id']] = [
                            'obatalkes_id' => $value['obatalkes_id'],
                            'obatalkes_nama' => ($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '',
                            'qty' => $value['qty'],
                        ];
                        // $result['data'][] = $data;
                    }
                }
            } catch (RequestException $e) {
                $result['error'] = $e->getMessage();
                return $result;
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                return $result;
            }
        }

        Yii::$app->cache->set("ambulan-obat-rs-".$userLogin, $resetCache);
    }
}