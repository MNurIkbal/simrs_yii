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
use app\components\DHtml;
use app\modules\ambulan\models\PesanAmbulanForm;
use app\modules\ambulan\models\PesanAmbulanDetailForm;
use app\modules\ambulan\models\FormAddTindakan;
use app\modules\ambulan\models\FormAddObat;
use app\modules\ambulan\models\FormAddTindakanPengembalian;
use app\modules\ambulan\models\FormAddObatPengembalian;
use app\modules\ambulan\models\FormPengembalian;
use GuzzleHttp\Exception\RequestException;

class InformasiPemakaianAmbulanController extends DocoController
{
    protected $_title;
    protected $_restAmbulan;
    protected $_module = '/ambulan/informasi-pemakaian-ambulan/';

    public function init()
    {
        parent::init();
        $this->_title = Yii::t('fe', 'Informasi Pemakaian Ambulan');
        $this->_restAmbulan = Yii::$app->docoRest->ambulan;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        try {
            $response = $this->_restAmbulan->get('informasi-pemakaian-ambulan/get-status-ambulan');
            $response = json_decode($response->getBody(),true);
            $statusPesan = isset($response['response']['status_ambulan']) 
                                ? ArrayHelper::map($response['response']['status_ambulan'], 'lookup_id', 'lookup_name') 
                                : [];
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
            $response = $this->_restAmbulan->get('informasi-pemakaian-ambulan/index', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pemakaianambulan_id']);
                unset($value['pemakaianambulan_id']);
                $value['primary'] = $primaryKey;
                $value['jenis_ambulan'] = $value['jenis_ambulan'] . ' - ' . $value['no_polisi'];
                $value['tgl_pesanambulan'] = date('d-M-Y', strtotime($value['tgl_pesanambulan']));
                $value['tgl_pemakaiansampai'] = date('d-M-Y', strtotime($value['tgl_pemakaiansampai']));
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

    public function actionExportSuratTugasPdf($pendaftaran_id, $id)
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $id = $request->get('id');
        try {
            $path = Yii::getAlias("@download") . "/informasi-pemakaian-ambulan-{$id}.pdf";
            if ( $pendaftaran_id == 'null') {
                $cetak = "export-surat-tugas-pdf-pasien-luar";
            }else {
                $cetak = "export-surat-tugas-pdf";
            }
            $response = $this->_restAmbulan->get("informasi-pemakaian-ambulan/{$cetak}", [
                'save_to' => $path,
                'query' => [
                    'pemakaianambulan_id' => DocoHelpers::decrypt($id)
                ]
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-pemakaian-ambulan.xlsx";
            $response = $this->_restAmbulan->get('informasi-pemakaian-ambulan/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);
            
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionFormPengembalian($id)
    {
        $title = "Pengembalian Ambulan";
        $model = new FormPengembalian;
        $id_parent = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restAmbulan->get('informasi-pemakaian-ambulan/pemakaian-ambulan', [
                'query' => [
                    'id' => $id_parent
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $model->attributes = $response['response'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        }

        return $this->renderAjax('form',get_defined_vars());
    }

    public function actionCariObat($id)
    {
        return $this->guzzleExec($this->_restAmbulan, [
            'url' => 'informasi-pemakaian-ambulan/get-obatan',
            'payload' => [
                'query' => array_merge(Yii::$app->request->get('payload', []),['ambulan_id' => $id]),
            ],
            'returnResponse' => true
        ]);
    }

    public function actionCariTindakan($id)
    {
        return $this->guzzleExec($this->_restAmbulan, [
            'url' => 'informasi-pemakaian-ambulan/get-tindakan',
            'payload' => [
                'query' => array_merge(Yii::$app->request->get('payload', []),['ambulan_id' => $id]),
            ],
            'returnResponse' => true
        ]);
    }

    public function actionPengembalian($id)
    {
        Yii::$app->cache->delete("obat-ambulan-{$id}");
        Yii::$app->cache->delete("tindakan-ambulan-{$id}");
        $title = "Pengembalian Ambulan";
        $model = new FormPengembalian;
        $modelObat = new FormAddObatPengembalian;
        $modelTindakan = new FormAddTindakanPengembalian;
        $id_parent = DocoHelpers::decrypt($id);

        $dataLookup = $this->helper->guzzleExec($this->_restAmbulan,
            [
                'url' => 'informasi-pemakaian-ambulan/get-fillter',
                'method' => 'get',
            ]
        );
        $obatAlkes = $dataLookup['obatAlkes'];
        // $tarifAmbulan = $dataLookup['tarifAmbulan'];

        try {
            $response = $this->_restAmbulan->get('informasi-pemakaian-ambulan/pemakaian-ambulan', [
                'query' => [
                    'id' => $id_parent
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $model->attributes = $response['response'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        }

        $dataJarak = $this->helper->guzzleExec($this->_restAmbulan,
            [
                'url' => 'informasi-pemakaian-ambulan/get-tarif-jarak',
                'payload' => [
                    'query' => $response['response']
                ],
            ]
        );
        $jarakDekat  = $dataJarak['jarakDekat'];
        $jarakSedang = $dataJarak['jarakSedang'];
        $jarakJauh   = $dataJarak['jarakJauh'];

        return $this->render('pengembalian', get_defined_vars());
    }
    
    public function actionObatAlkes()
    {
        return $this->guzzleExec($this->_restAmbulan, [
            'url' => 'informasi-pemakaian-ambulan/get-fillter',
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
            'returnResponse' => true
        ]);
    }

    public function actionSimpanPemakaianAmbulan($id)
    {
        $request = Yii::$app->request;
        $model   = new FormPengembalian;
        $model->load($request->post());

        $id_parent                  = DocoHelpers::decrypt($id);
        $post                       = Yii::$app->request->post();
        $cacheTindakan              = Yii::$app->cache->get("tindakan-ambulan-{$id}");
        $cacheObat                  = Yii::$app->cache->get("obat-ambulan-{$id}");
        $model->pemakaianambulan_id = $id_parent;
        $dataPost = [
            'header'   => $model->attributes,
            'tindakan' => $cacheTindakan,
            'obat'     => $cacheObat,
        ];
        
        if ($model->validate()) {
            try {
                $response = $this->_restAmbulan->get('informasi-pemakaian-ambulan/simpan-pemakaian-ambulan', [
                    'query' => [
                        'id' => $id_parent
                    ],
                    'form_params' => $dataPost
                ]);
                $response = json_decode($response->getBody(),true);
                return DocoHelpers::response($response,200);
            } catch (RequestException $e) {
                $message = $e->getMessage();
                Yii::info($e->getMessage());
            }
            return DocoHelpers::response([
                'message' => $message
            ],422);
        }

        return DocoHelpers::response($model->errors,422, 'FormPengembalian');
    }

    /*
    public function actionEdit($id,$jenis_pasien_id)
    {
        $model = new PesanAmbulanForm;
        $addTindakan = new FormAddTindakan;
        $model->scenario = 'luar';
        $idPesan = DocoHelpers::decrypt($id);
        $view = 'pasien-luar-form';
        $title = "Edit Pemakaian Ambulan Luar RS";

        if ($jenis_pasien_id != 2) {
            $view = 'pasien-rs-form';
            $title = "Edit Pemakaian Ambulan Pasien RS";
        }
        
        try {
            $response = $this->_restAmbulan->get('informasi-pemakaian-ambulan/get-data-detail', [
                'query' => [
                    'id' => $idPesan
                ]
            ]);

            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $model->attributes = $response['header'];
            Yii::$app->cache->set("tindakan-ambulan-{$id}",$response['detail']);
        } catch (RequestException $e) {
            Yii::error($e->getMessage());
        }
        return $this->render($view, get_defined_vars());
    }
    */


    public function actionExportPdf($id, $jenis_pasien_id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/informasi-pemakaian-ambulan.pdf";
        try {
            $cetak = "export-pdf";
            if ($jenis_pasien_id != 2) $cetak = "export-pdf-pasien-rs";
            $response = $this->_restAmbulan->get("informasi-pemakaian-ambulan/{$cetak}", [
                'save_to' => $path,
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ]
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restAmbulan->get('informasi-pemakaian-ambulan/batal-pesan',[
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

    public function actionSearchObat()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restAmbulan->get('informasi-pemakaian-ambulan/get-data-obat', [
                'query' => [
                    'term' => $request->get('term'),
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];

            foreach ($data as $key => $value) {
                $response[] = [
                    'id'           => $value['obatalkes_id'],
                    'text'         => $value['obatalkes_nama'],
                    'satuan_kecil' => $value['satuan_kecil'],
                    'harga'        => $value['harganetto'],
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
                    'harga' => $value['harga'],
                    'qty' => $value['qty'],
                    'cyto' => $value['cyto'],
                    'sub_total' => $value['sub_total'],
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

    private function getRequestObat($obatalkes_id = null, $id = null)
    {
        try {
            $request = $this->_restAmbulan->request('GET', 'informasi-pemakaian-ambulan/get-data-obat-ambulan', [
                'query' => [
                    'obatalkes_id' => $obatalkes_id,
                    'id' => $id,
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
        } catch (RequestException $e) {
            $attributes = [];
        }

        return $attributes;
    }
    
    public function actionAddObat()
    {
        $model = new FormAddObatPengembalian;
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $id = $request->post('id');
        $model->load($request->post());
        if ($model->validate()) {
            $cacheObat = $cache->get("obat-ambulan-{$id}");
            $obatalkes_id = $model->obatalkes_id;
            $dataObat = $this->getRequestObat($obatalkes_id,$id);
            if (empty($dataObat)) {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal !',
                        'text' => 'Terjadi kesalah pada sistem'
                    ]
                ],422);
            }

            if ($dataObat['qty_stok'] != 0 && $model->qty > $dataObat['qty_stok']) {
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
                'satuan_kecil' => $dataObat['satuankecil_nama'],
                'harga' => $dataObat['hargaygdipakai'],
                'qty' => $model->qty,
                'cyto' => $model->cyto,
                'sub_total' => $model->qty * $dataObat['hargaygdipakai'],
                'stok' => $dataObat['qty_stok'],
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

    public function actionSearchTindakan()
    {   
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restAmbulan->get('informasi-pemakaian-ambulan/get-data-tindakan-ambulan', [
                'query' => [
                    'term' => $request->get('term'),
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];

            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => $value['daftartindakan_nama'],
                    'harga' => $value['harga_tariftindakan'],
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

    private function getRequestTindakan($daftartindakan_id = null, $id = null)
    {
        try {
            $request = $this->_restAmbulan->request('GET', 'informasi-pemakaian-ambulan/get-data-tindakan-ambulan', [
                'query' => [
                    'daftartindakan_id' => $daftartindakan_id,
                    'id' => $id,
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
        } catch (RequestException $e) {
            $attributes = [];
        }

        return $attributes;
    }

    public function actionAddTindakan($tipe = null)
    {
        $model = new FormAddTindakanPengembalian;
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $id = $request->post('id');
        $model->load($request->post());
        if ($model->validate()) {
            $cacheTindakan = $cache->get("tindakan-ambulan-{$id}");
            $daftartindakan_id = $model->daftartindakan_id;
            $dataTindakan = $this->getRequestTindakan($daftartindakan_id,$id);

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
            $cacheTindakan[$daftartindakan_id] = [
                'daftartindakan_id' => $daftartindakan_id,
                'daftartindakan_nama' => $dataTindakan['daftartindakan_nama'],
                'harga' => $dataTindakan['harga_tariftindakan'],
                'qty' => $model->qty,
                'cyto' => $model->cyto,
                'sub_total' => $model->qty * $dataTindakan['harga_tariftindakan'],
            ];

            $cache->set("tindakan-ambulan-{$id}",$cacheTindakan,3600);

            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($response);
        } else {
            return DocoHelpers::response($model->errors,422,'FormAddTindakan');
        }
    }

    public function actionDeleteCacheTindakan($id = null, $parent_id)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $cache = Yii::$app->cache;
        $id = DocoHelpers::decrypt($id);
        $cacheObat = $cache->get("tindakan-ambulan-{$parent_id}");
        if ($cacheObat !== false) {
            if (isset($cacheObat[$id])) {
                unset($cacheObat[$id]);
                Yii::$app->cache->set("tindakan-ambulan-{$parent_id}",$cacheObat);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data Tindakan berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionGetListTindakan($id)
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $cacheTindakan = Yii::$app->cache->get("tindakan-ambulan-{$id}");
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $no_urut = $request->get('start', 1);
        $resetCache = [];

        if (!empty($cacheTindakan)) {
            foreach ($cacheTindakan as $key => $value) {
                $no_urut++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data = [
                    'rowNum' => $no_urut,
                    'daftartindakan_id' => $value['daftartindakan_id'],
                    'daftartindakan_nama' => $value['daftartindakan_nama'],
                    'harga' => $value['harga'],
                    'qty' => $value['qty'],
                    'cyto' => $value['cyto'],
                    'sub_total' => $value['sub_total'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete-cache-tindakan-rs',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module ."delete-cache-tindakan?id={$primaryKey}&parent_id=$id"]),
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

}