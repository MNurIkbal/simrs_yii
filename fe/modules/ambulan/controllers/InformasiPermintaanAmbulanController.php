<?php

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
use app\modules\ambulan\models\FormAddPegawai;
use app\modules\ambulan\models\FormProses;
use GuzzleHttp\Exception\RequestException;

class InformasiPermintaanAmbulanController extends DocoController
{
    protected $_title;
    protected $_restAmbulan;
    protected $_module = '/ambulan/informasi-permintaan-ambulan/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Informasi Permintaan Ambulan');
        $this->_restAmbulan = Yii::$app->docoRest->ambulan;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        try {
            $response = $this->_restAmbulan->get('informasi-permintaan-ambulan/get-attributes');
            $response = json_decode($response->getBody(),true);
            $statusPesan = isset($response['response']['status_ambulan']) 
                                ? $response['response']['status_ambulan'] : [];
            $statusPesan = ArrayHelper::map($statusPesan, 'lookup_id', 'lookup_name');
            unset($statusPesan[607]);

        } catch (RequestException $e) {
            $statusPesan = [];
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restAmbulan->get('informasi-permintaan-ambulan/', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesanambulan_id']);
                unset($value['pesanambulan_id']);
                $value['primary'] = $primaryKey;
                $value['tgl_pesanambulan'] = $this->helper->convertDate($value['tgl_pesanambulan'], 'd-m-Y H:i');
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

    public function actionEdit($id,$jenis_pasien_id)
    {
        $model = new PesanAmbulanForm;
        $addTindakan = new FormAddTindakan;
        $modelObat = new FormAddObat;
        $model->scenario = 'luar';
        $idPesan = DocoHelpers::decrypt($id);
        $view = 'pasien-luar-form';
        $title = "Edit Permintaan Ambulan Luar RS";

        if ($jenis_pasien_id != 2) {
            $view = 'pasien-rs-form';
            $title = "Edit Permintaan Ambulan Pasien RS";
            $model->scenario = 'rs';
        }
        
        try {
            $response = $this->_restAmbulan->get('informasi-permintaan-ambulan/get-data-detail', [
                'query' => [
                    'id' => $idPesan
                ]
            ]);

            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $model->attributes = $response['header'];
            $header = $response['header'];
            $pasien = $response['pasien'];
            $detail = $response['detail'];
            $tindakan = $obat = [];
            foreach ($detail as $value) {
                if (!empty($value['daftartindakan_id'])) {
                    $idTindakan = $value['daftartindakan_id'];
                    $tindakan[$idTindakan] = [
                        'daftartindakan_id' => $idTindakan,
                        'daftartindakan_nama' => $value['daftartindakan_nama'],
                        'is_default' => $value['kelompok_biaya'],
                        'qty' => $value['qty_tindakan'],
                        'harga_tariftindakan' => $value['tarif_satuan'],
                        'jumlah_tarif' => $value['qty_tindakan'] * $value['tarif_satuan'],
                    ];
                }
                if (!empty($value['obatalkes_id'])) {
                    $idObat = $value['obatalkes_id'];
                    $obat[$idObat] = [
                        'obatalkes_id' => $idObat,
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'satuan_kecil' => $value['satuan_kecil'],
                        'qty' => $value['qty_obat'],
                        'stok' => 1,
                    ];
                }
            }
            Yii::$app->cache->set("tindakan-ambulan-{$id}",$tindakan);
            Yii::$app->cache->set("obat-ambulan-{$id}",$obat);
        } catch (RequestException $e) {
            $header = $detail = $pasien = [];
            Yii::error($e->getMessage());
        }
        return $this->render($view, get_defined_vars());
    }

    public function actionGetDataTindakan($id)
    {
        $request = Yii::$app->request;
        $cache = Yii::$app->cache->get("tindakan-ambulan-{$id}");
        $draw = $request->get('draw', 1);

        $result = $data = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        if (is_array($cache)) {
            $no = 1;
            foreach ($cache as $value) {
                $row = $value;
                $row['rowNum'] = $no;
                $primaryKey = $row['daftartindakan_id'];
                $row['kelompok_biaya'] = $row['is_default'];
                $row['qty_tindakan_label'] = DocoHelpers::formatNumber($row['qty']);
                $row['tarif_satuan_label'] = DocoHelpers::formatNumber($row['harga_tariftindakan']);
                $row['jumlah_tarif_label'] = DocoHelpers::formatNumber($row['jumlah_tarif']);
                $aksi = '';
                if($value['is_default'] == "Tidak Tetap") {
                    $aksi = Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete-cache-tindakan',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module ."delete-cache-tindakan?parent_id={$id}&id={$primaryKey}"]),
                        ]
                    );
                }
                $qty = !empty($value['harga_tariftindakan']) ? $value['qty'] : 1;
                $row['qty'] = Html::textInput('Tindakan[qty]['.$value['daftartindakan_id'].']', $qty, [
                        'class' => 'form-control doco-number qty', 
                        'data-id' => $value['daftartindakan_id'],
                        'data-val' => $value['qty']
                ]);
                $row['aksi'] = $aksi;
                $data[] = $row;
                $no++;
            }
        }
        
        $result['data'] = $data;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        return DocoHelpers::response($result);
    }

    public function actionGetObat($id)
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->cache->set("obat-ambulan-{$id}",$obat);
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
                            'action' => Url::to([$this->_module ."delete-cache-obat?id={$primaryKey}&parent_id=$id"]),
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

    public function actionDeleteCacheObat($id = null, $parent_id)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $cache = Yii::$app->cache;
        $id = DocoHelpers::decrypt($id);
        $cacheObat = $cache->get("obat-ambulan-{$parent_id}");
        if ($cacheObat !== false) {
            if (isset($cacheObat[$id])) {
                unset($cacheObat[$id]);
                Yii::$app->cache->set("obat-ambulan-{$parent_id}",$cacheObat);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data Obat Alkes berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionExportPdf($id, $jenis_pasien_id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/informasi-permintaan-ambulan.pdf";
        try {
            $cetak = "export-pdf";
            if ($jenis_pasien_id != 2) $cetak = "export-pdf-pasien-rs";
            $response = $this->_restAmbulan->get("informasi-permintaan-ambulan/{$cetak}", [
                'save_to' => $path,
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ]
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restAmbulan->get('informasi-permintaan-ambulan/batal-pesan',[
                'query' => [
                    'id' => $id
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],422);
        }
    }

    public function actionAddTindakan()
    {
        $addTindakan = new FormAddTindakan;
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $id = $request->post('id');
        $pasien_id = $request->post('pasien_id',null);
        $addTindakan->load($request->post());
        if ($addTindakan->validate()) {
            $daftartindakan_id = $addTindakan->daftartindakan_id;
            $cacheTindakan = Yii::$app->cache->get("tindakan-ambulan-{$id}");
            $dataTindakan = $this->getRequest($daftartindakan_id,$pasien_id);
                if (empty($dataTindakan)) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal !',
                            'text' => 'Terjadi kesalah pada sistem'
                        ]
                    ],422);
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
                    'qty' => $addTindakan->qty,
                    'harga_tariftindakan' => $dataTindakan['harga_tariftindakan'],
                    'jumlah_tarif' => $addTindakan->qty * $dataTindakan['harga_tariftindakan'],
                ];

                $cache->set("tindakan-ambulan-{$id}",$cacheTindakan,3600);

                $response['response'] = [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data berhasil di tambah'
                ];

                return DocoHelpers::response($response);
        } else {
            return DocoHelpers::response($addTindakan->errors,422,'FormAddTindakan');
        }
    }

    public function actionAddObat()
    {
        $model = new FormAddObat;
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $id = $request->post('id');
        $model->load($request->post());
        if ($model->validate()) {
            $cacheObat = $cache->get("obat-ambulan-{$id}");
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

            $cache->set("obat-ambulan-{$id}",$cacheObat,3600);

            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($response);
        } else {
            return DocoHelpers::response($model->errors,422,'FormAddObat');
        }
    }

    public function actionGetListObat($id)
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cacheObat = Yii::$app->cache->get("obat-ambulan-{$id}");
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
                            'action' => Url::to([$this->_module ."delete-cache-obat?id={$primaryKey}&parent_id=$id"]),
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

    public function actionListPemesanan($tipe = null)
    {
        $tipe = isset($tipe) ? $tipe : "luar";
        $path = ($tipe == 'rs') ? 'list-pemesanan-rs' : 'list-pemesanan';
        return $this->renderAjax($path, get_defined_vars());
    }

    public function actionUpdateCache($tipe = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $tipe = isset($tipe) ? $tipe : 1;
        $qty = $request->post('qty');
        $id = $request->post('id');

        $daftartindakan_id = $post['daftartindakan_id'];
        $cacheTindakan = Yii::$app->cache->get("tindakan-ambulan-{$id}");

        if(isset($cacheTindakan[$daftartindakan_id])) {
            $harga = $cacheTindakan[$daftartindakan_id]['harga_tariftindakan'];
            $cacheTindakan[$daftartindakan_id]['qty'] = $qty;
            $cacheTindakan[$daftartindakan_id]['jumlah_tarif'] = $harga * $qty;
        } 

        Yii::$app->cache->set("tindakan-ambulan-{$id}",$cacheTindakan,3600);

        $response = [];
        return DocoHelpers::response($response);
    }

    public function actionDeleteCacheTindakan($id = null, $parent_id)
    {
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $cacheTindakan = $cache->get("tindakan-ambulan-{$parent_id}");

        if ($cacheTindakan !== false) {
            if (isset($cacheTindakan[$id])) {
                unset($cacheTindakan[$id]);
                Yii::$app->cache->set("tindakan-ambulan-{$parent_id}",$cacheTindakan);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionAddTindakanDefault($ambulan_id = null, $parent_id, $pasien_id)
    {
        $request = Yii::$app->request;
        $resetCacheTindakan = Yii::$app->cache->get("tindakan-ambulan-{$parent_id}");
        $resetCacheObat = Yii::$app->cache->get("obat-ambulan-{$parent_id}");

        // Clear Tindakan Tetap Ambulan Sebelumnya
        if(!empty($resetCacheTindakan)) {
            foreach ($resetCacheTindakan as $key => $value) {
                if($value['is_default'] == 'Tetap') {
                    unset($resetCacheTindakan[$value['daftartindakan_id']]);
                }
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

        Yii::$app->cache->set("tindakan-ambulan-{$parent_id}",$resetCacheTindakan);
        Yii::$app->cache->set("obat-ambulan-{$parent_id}", $resetCacheObat);
        return DocoHelpers::response([
            'message' => 'Add tindakan default berhasil'
        ]);

    }

    public function actionTambahTarifAmbulan($ambulan_id = null, $parent_id)
    {
        $resetCache = [];
        if(!empty($ambulan_id)) {
            $filter['ambulan_id'] = $ambulan_id;
            $filter['tipe'] = 1;
            $cacheTindakan = Yii::$app->cache->get("tindakan-ambulan-{$parent_id}");
            if(!empty($cacheTindakan)) {
                foreach ($cacheTindakan as $key => $value) {
                    if($value['is_default'] == 'Tetap') {
                        unset($cacheTindakan[$value['daftartindakan_id']]);
                    }
                }
            }

            try {
                $response = $this->_restAmbulan->get('permintaan-ambulan/get-detail-tindakan', [
                    'query' => $filter
                ]);
                $body = json_decode($response->getBody(), true);
                if(!empty($body['response']['data'])) {
                    foreach ($body['response']['data'] as $key => $value) {
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
            } catch (RequestException $e) {
                $result['error'] = $e->getMessage();
                return $result;
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                return $result;
            }
        }
        
        Yii::$app->cache->set("tindakan-ambulan-{$parent_id}", $cacheTindakan);
        return DocoHelpers::response([
            'message' => 'Tindakan berhasil'
        ]);
    }

    private function getDiffDateTime($startDate, $endDate)
    {
        
        $firstDate = date_create($startDate);
        $lastDate = date_create($endDate);
        $diff = date_diff($firstDate, $lastDate);
        $year = $diff->y;
        $mounth = $diff->m;
        $hour = $diff->h; 
        $days = $diff->days; 

        $firstHour = date('H', strtotime($startDate));
        $resHoursfirst = 24 - (int)$firstHour;
        $secondHour = date('H', strtotime($endDate));
        $resHourssecond = 24 - (int)$secondHour;

        $hoursDays = 0 ;
        if ($days > 0) {
            $d = $days - 1;
            if ($d != 0) {
                $hoursDays = 24 * $d; 
            }
        }
        $resHours = $hoursDays + $resHoursfirst + $resHourssecond;

        $results = [
                    'tahun' => ($year > 0 ) ? $year : $year + 1,
                    'bulan' => ($mounth > 0 ) ? $mounth : $mounth + 1,
                    'hari' => ($days > 0 ) ? $days : $days + 1,
                    'jam' => $resHours
                    ];
        return $results;
    }


    public function actionSavePasien()
    {
        $request = Yii::$app->request;
        $post = Yii::$app->request->post();
        $dataPesanAmbulan = $post['PesanAmbulanForm'];
        $dataPemakaian = $post['FormProses'];
        $model = new PesanAmbulanForm;
        $modelProses = new FormProses;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        try {
            $model->load($request->post());
            $jenis = $model->jenis;
            $idParent = $request->post('id_parent');
            $model->scenario = ($jenis == "luar") ? "luar" : "rs";
            $modelProses->scenario = ($jenis == "luar") ? "luar" : "rs";
            $cache = Yii::$app->cache;
            $cacheTindakan = Yii::$app->cache->get("tindakan-ambulan-{$idParent}");
            $cacheObat = Yii::$app->cache->get("obat-ambulan-{$idParent}");
            $cachePegawai = Yii::$app->cache->get("pegawai-{$idParent}");
            $modelProses->load($request->post());
            $tglPemakaian = $modelProses->tgl_pemakaian;
            $expl_tglPemakaian = explode('  s/d  ', $tglPemakaian);
            $modelProses->tgl_pemakaiandari = date('Y-m-d H:i:s', strtotime($expl_tglPemakaian[0] ));
            $modelProses->tgl_pemakaiansampai = date('Y-m-d H:i:s', strtotime($expl_tglPemakaian[1] ));
            $getDiffDate = DocoHelpers::getDiffDateTime($modelProses->tgl_pemakaiandari, $modelProses->tgl_pemakaiansampai);
            $modelProses->durasi_pemakaian = $getDiffDate['jam'];
            if ($modelProses->validate()) {
                
                if(empty($cachePegawai)) {
                    $response['response']['text'] = 'Pegawai Tidak Boleh Kosong.';
                    $response['response']['title'] = 'Proses Gagal!';
                    return DocoHelpers::response($response, 422);
                }

                if ($jenis == 'rs') {
                    if(empty($cacheObat)) {
                        $response['response']['text'] = 'Obat Alkes Tidak Boleh Kosong.';
                        $response['response']['title'] = 'Proses Gagal!';
                        return DocoHelpers::response($response, 422);
                    }
                }
                $dataPost = [
                        'tindakan' => $cacheTindakan,
                        'ruangan_id' => Yii::$app->docoVars->workspace("ruangan_id"),
                        'instalasi_id' => Yii::$app->docoVars->workspace("instalasi_id"),
                        'pegawai' => $cachePegawai,
                        'obat_alkes' => $cacheObat,
                        'pesan_ambulan' => $dataPesanAmbulan,
                        'pemakaian' => $modelProses->attributes,
                        'id_parent' => DocoHelpers::decrypt($idParent)
                    ];
                $result = $this->_restAmbulan->post('informasi-permintaan-ambulan/save-pemakaian',[
                    'form_params' => $dataPost
                ]);
                $result = json_decode($result->getBody(),true);
                AmbulanceNotification::updateNotif();
                return DocoHelpers::response($result, false);
            } else {
                $errors = DocoHelpers::parseError($modelProses->errors,'FormProses');
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

    public function actionApprove($id)
    {

    }

    public function actionProses($id, $jenis_pasien_id)
    {
        $model = new PesanAmbulanForm;
        $modelProses = new FormProses;
        $modelObat = new FormAddObat;
        $modelPegawai = new FormAddPegawai;
        $idPesan = DocoHelpers::decrypt($id);
        $namaBulan = DocoHelpers::getNamaBulan(true);
        $namaHari = DocoHelpers::getNamaHari();

        $model->scenario = 'luar';
        $view = 'proses-luar';
        $title = "Proses Permintaan Ambulan Luar / Umum";
        if (!empty($jenis_pasien_id)) {
            if ($jenis_pasien_id != 2) {
                $view = 'proses-rs';
                $title = "Proses Permintaan Ambulan Pasien Rumah Sakit";
                $model->scenario = 'rs';
            }
        }
        try {
            $response = $this->_restAmbulan->get('informasi-permintaan-ambulan/get-data-detail', [
                'query' => [
                    'id' => $idPesan
                ]
            ]);

            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            
            $model->attributes = $response['header'];
            $model->pasien_id = $response['pasien']['pasien_id'];
            $model->carabayar_nama = $response['pasien']['carabayar_nama'];
            $model->penjamin_nama = $response['pasien']['penjamin_nama'];
            $model->kelaspelayanan_nama = $response['pasien']['kelaspelayanan_nama'];
            $model->kelaspelayanan_id = $response['pasien']['kelaspelayanan_id'];
            $model->penjamin_id = $response['pasien']['penjamin_id'];
            $model->carabayar_id = $response['pasien']['carabayar_id'];
            
            $getPelayananAmbulan = $response['pelayanan_ambulan'];
            $statusPesan = false;
            if ($response['header']['status_pesan'] == 607) {
                $statusPesan = 'disabled';
            }
            $getUmur = DocoHelpers::getUmur($response['header']['tgl_lahir']);
            $substrUmur =  substr($getUmur,5);

            $model->nama_pemesan = $response['header']['nama_pemesan'];
            $model->umur = $substrUmur;
            $model->asal_pasien = $response['header']['asal_pasien'];
            $model->is_sadar = ($model->is_sadar == true ) ? 'Sadar' : 'Tidak Sadar';
            $model->is_nafas = ($model->is_nafas == true ) ? 'Ada' : 'Tidak Ada';
            $model->is_nadi = ($model->is_nadi == true ) ? 'Ada' : 'Tidak Ada';
            $no_pesanambulan = $response['header']['no_pesanambulan'];

            $header = $response['header'];
            $pasien = $response['pasien'];
            $detail = $response['detail'];
            $tindakan = $obat = [];
            foreach ($detail as $value) {
                if (!empty($value['daftartindakan_id'])) {
                    $idTindakan = $value['daftartindakan_id'];
                    $tindakan[$idTindakan] = [
                        'daftartindakan_id' => $idTindakan,
                        'daftartindakan_nama' => $value['daftartindakan_nama'],
                        'is_default' => $value['kelompok_biaya'],
                        'qty' => $value['qty_tindakan'],
                        'harga_tariftindakan' => $value['tarif_satuan'],
                        'jumlah_tarif' => $value['qty_tindakan'] * $value['tarif_satuan'],
                    ];
                }
                if (!empty($value['obatalkes_id'])) {
                    $idObat = $value['obatalkes_id'];
                    $obat[$idObat] = [
                        'obatalkes_id' => $idObat,
                        'obatalkes_nama' => $value['obatalkes_nama'],
                        'satuan_kecil' => $value['satuan_kecil'],
                        'qty' => $value['qty_obat'],
                        'stok' => 1,
                    ];
                }
            }
            $modelProses->pendaftaran_id = $model->pendaftaran_id;
            $modelProses->pasien_id = $model->pasien_id;
            $pelayananAmbulan = ArrayHelper::map($getPelayananAmbulan, 'lookup_id', 'lookup_name');
            Yii::$app->cache->set("tindakan-ambulan-{$id}",$tindakan);
            if ($jenis_pasien_id == 1) {
                Yii::$app->cache->set("obat-ambulan-{$id}",$obat);
            }
            $setPegawai = Yii::$app->cache->set("pegawai-{$id}",[]);
        } catch (RequestException $e) {
            $header = $detail = $pasien = $statusPesan = $pelayananAmbulan = $setPegawai = [];
            $setPegawai = $no_pesanambulan = '';
            Yii::error($e->getMessage());
        }
        return $this->render($view, get_defined_vars());
    }

    public function actionGetDataPegawai($id)
    {
        $request = Yii::$app->request;
        $cache = Yii::$app->cache->get("pegawai-{$id}");
        $draw = $request->get('draw', 1);

        $result = $data = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        if (is_array($cache)) {
            $no = 1;
            foreach ($cache as $value) {
                $row = $value;
                $row['rowNum'] = $no;
                $primaryKey = $row['pegawai_id'];
                $aksi = Html::button(
                    "<i class='fa fa-trash'></i>",[
                        'style' => 'margin-right:5px',
                        'class' => 'btn btn-danger btn-xs delete-cache-pegawai',
                        'style' => 'margin-right:5px; padding-left:10px !important;',
                        'action' => Url::to([$this->_module ."delete-pegawai?parent_id={$id}&id={$primaryKey}"]),
                    ]
                );
                $row['aksi'] = $aksi;
                $data[] = $row;
                $no++;
            }
        }
        
        $result['data'] = $data;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        return DocoHelpers::response($result);
    }

    public function actionAddPegawai()
    {
        $addPegawai = new FormAddPegawai;
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $id = $request->post('id');
        $pasien_id = $request->post('pasien_id',null);
        $addPegawai->load($request->post());
        if ($addPegawai->validate()) {
            $pegawai_id = $addPegawai->pegawai_id;
            $cachePegawai = Yii::$app->cache->get("pegawai-{$id}");
            if (isset($cachePegawai[$pegawai_id])) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal !',
                        'text' => 'Pegawai sudah diinputkan'
                    ]
                ],422);
            }
            $dataPegawai = $this->getRequestPegawai($pegawai_id);
                if (empty($dataPegawai)) {
                    return DocoHelpers::response([
                        'response' => [
                            'title' => 'Proses Gagal !',
                            'text' => 'Terjadi kesalah pada sistem'
                        ]
                    ],422);
                }

                $cachePegawai[$pegawai_id] = [
                    'pegawai_id' => $pegawai_id,
                    'nama_pegawai' => $dataPegawai['nama_pegawai'],
                    'nomorindukpegawai' => $dataPegawai['nomorindukpegawai'],
                    'jabatan_nama' => $dataPegawai['jabatan_nama'],
                ];

                $cache->set("pegawai-{$id}",$cachePegawai,3600);

                $response['response'] = [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data berhasil di tambah'
                ];

                return DocoHelpers::response($response);
        } else {
            return DocoHelpers::response($addPegawai->errors,422,'FormAddPegawai');
        }
    }

    public function actionDeletePegawai($id, $parent_id)
    {
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $cachePegawai = $cache->get("pegawai-{$parent_id}");

        if ($cachePegawai !== false) {
            if (isset($cachePegawai[$id])) {
                unset($cachePegawai[$id]);
                Yii::$app->cache->set("pegawai-{$parent_id}",$cachePegawai);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionSearchPegawai()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restAmbulan->get('informasi-permintaan-ambulan/get-pegawai', [
                            'query' => [
                                'term' => $request->get('term'),
                            ]
                        ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];

            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nomorindukpegawai'] .' - '. $value['nama_pegawai'],
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

    private function getRequestPegawai($pegawai_id = null)
    {
        try {
            $request = $this->_restAmbulan->request('GET', 'informasi-permintaan-ambulan/get-pegawai', [
                'query' => [
                    'pegawai_id' => $pegawai_id
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

    public function actionExportSuratTugasPdf($pemakaianambulan_id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/informasi-permintaan-ambulan.pdf";
        try {
            $cetak = "export-surat-tugas-pdf";
            $response = $this->_restAmbulan->get("informasi-permintaan-ambulan/{$cetak}", [
                'save_to' => $path,
                'query' => [
                    'pemakaianambulan_id' => DocoHelpers::decrypt($pemakaianambulan_id)
                ]
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportSuratTugasUmumPdf($pemakaianambulan_id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/informasi-permintaan-ambulan-umum.pdf";
        try {
            $cetak = "export-surat-tugas-umum-pdf";
            $response = $this->_restAmbulan->get("informasi-permintaan-ambulan/{$cetak}", [
                'save_to' => $path,
                'query' => [
                    'pemakaianambulan_id' => DocoHelpers::decrypt($pemakaianambulan_id)
                ]
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

}