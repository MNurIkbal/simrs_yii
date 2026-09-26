<?php

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\gudang\models\PenerimaanSupplierForm;
use app\modules\gudang\models\PenerimaanSupplierDetailForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class PenerimaanObatSupplierController extends DocoController
{
    protected $_title = "Penerimaan Obat Alkes Supplier";
    protected $_module = '/gudang/penerimaan-obat-supplier/';
    protected $_restMaster;
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
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
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $title = $this->_title;
        $model = new PenerimaanSupplierForm;
        $model->scenario = PenerimaanSupplierForm::SCENARIO_GUDANG_FARMASI;
        $modelDetail = new PenerimaanSupplierDetailForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $cachePenerimaan = Yii::$app->cache->set("penerimaan-" . $id_pegawai, []);
        $countPenerimaan  = ($cachePenerimaan == false) ? 0 : count($cachePenerimaan);
        $options = $this->getAttributes();

        $konfig_farmasi = $this->_restGudang->get('konfig-farmasi/get-konfig?id=1');
        $setting = json_decode($konfig_farmasi->getBody(),true);
        $need_verif = $setting['response']['is_verifpenerimaan'];
        $harga_donasi = $setting['response']['harga_donasi'];
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

    public function actionSearchObatAlkes($tipe,$consignment)
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $search = $request->get('search');
            $result = $this->_restMaster->get('allow/get-data-obat-alkes',[
                            'query' => [
                                'limit' => false,
                                'term' => $search['term'],
                                'tipe' => $tipe,
                                'is_consignment' => $consignment
                            ]

                        ]);
                        
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $satuankecil_nama = ($tipe == 0) ? $value['satuan_kecil'] : $value['satuankecil_nama'];
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                    'kode' => $value['obatalkes_kode'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $satuankecil_nama,
                    'is_consigment' => $value['is_consigment'],
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

    public function actionSearchObatAlkesConsig($tipe)
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/get-data-obat-alkes-consig',[
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
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                    'kode' => $value['obatalkes_kode'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $satuankecil_nama,
                    'is_consigment' => $value['is_consigment'],
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

    public function actionGetListItem()
    {
        $request = Yii::$app->request;
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $cachePenerimaan = Yii::$app->cache->get("penerimaan-" . $id_pegawai);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if ($cachePenerimaan !== false) {
            $no = $request->get('start',1);
            $totalNetto = " ";
            foreach ($cachePenerimaan as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'obatalkes_id' => $value['obatalkes_id'],
                    'obatalkes_nama' => $value['obatalkes_nama'],
                    'obatalkes_kode' => $value['obatalkes_kode'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuanunit_nama_kecil' => $value['satuanunit_nama_kecil'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuanunit_nama_besar' => $value['satuanunit_nama_besar'],
                    'qty_kecil' => DocoHelpers::formatNumber($value['qty_kecil'])." ".$value['satuanunit_nama_kecil'],
                    'qty_besar' => DocoHelpers::formatNumber($value['qty_besar'])." ".$value['satuanunit_nama_besar'],
                    'harga_netto' => $value['harga_netto'],
                    'tgl_kadaluarsa' => date('d M Y', strtotime($value['tgl_kadaluarsa'])),
                    'diskon' => $value['diskon'],
                    'no_batch' => isset($value['no_batch']) ? $value['no_batch'] : "",
                    'keterangan' => isset($value['keterangan']) ? $value['keterangan'] : "",
                    'is_consignment' => isset($value['is_consignment']) ? $value['is_consignment'] : false,
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete',
                            'style' => 'margin-right:5px; padding-left:10px !important; margin-bottom: 13px',
                            'action' => Url::to([$this->_module .'delete-list-item','id' => $primaryKey]),
                        ]
                    )
                ];
                $harga_netto = str_replace(".","",$value['harga_netto']);
                $totalNetto += (int)$harga_netto;
            }
            $result['total_netto'] = DocoHelpers::formatNumber($totalNetto);
            $result['data'] = $data;
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
        }

        return DocoHelpers::response($result);
    }

    public function actionSetListItem()
    {
        $request = Yii::$app->request;
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $model = new PenerimaanSupplierDetailForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $model->load($request->post());
        $model->scenario = 'obat';
        $post = $request->post('PenerimaanSupplierDetailForm');

        $model->attributes = $post;
        if ($model->validate()) {
            $satuankonversi_id = $post['satuankonversi_id'];
            $satuanRequest = $this->getRequest($satuankonversi_id);
            $qty_kecil = $satuanRequest['nilai_konversi'];
            $satuanunit_nama_besar = $satuanRequest['satuanunit_nama_besar'];
            $satuanunit_nama_kecil = $satuanRequest['satuanunit_nama_kecil'];

            $cachePenerimaan = Yii::$app->cache->get("penerimaan-" . $id_pegawai);
            if ($cachePenerimaan == false) {
                Yii::$app->cache->set("penerimaan-".$id_pegawai, []);
                $cachePenerimaan = [];
            }

            if (!isset($cachePenerimaan[$model->obatalkes_id])) {
                $cachePenerimaan[$model->obatalkes_id] = [];
            }

            $setCache = [
                'obatalkes_id' => $post['obatalkes_id'],
                'obatalkes_nama' => $post['obatalkes_nama'],
                'obatalkes_kode' => $post['obatalkes_kode'],
                'qty_besar' => $post['qty_besar'],
                'qty_kecil' => $post['qty_besar'] * $qty_kecil,
                'nilai_konversi' => $qty_kecil,
                'satuankonversi_id' => $post['satuankonversi_id'],
                'satuankecil_id' => $satuanRequest['satuankecil_id'],
                'satuanbesar_id' => $satuanRequest['satuanbesar_id'],
                'satuanunit_nama_besar' => $satuanunit_nama_besar,
                'satuanunit_nama_kecil' => $satuanunit_nama_kecil,
                'tgl_kadaluarsa' => date('d M Y', strtotime($post['tgl_kadaluarsa'])),
                'harga_netto' => $post['harga_netto'],
                'diskon' => $post['diskon']==''?0:$post['diskon'],
                'no_batch' => $post['no_batch'],
                'keterangan' => $post['keterangan'],
                'is_consignment' => $post['is_consignment'],
                'is_donasi' => $post['is_donasi']
            ];

            // dump($setCache);die;
            $cachePenerimaan[$model->obatalkes_id] = $setCache;
            $cachePenerimaan = Yii::$app->cache->set("penerimaan-" . $id_pegawai, $cachePenerimaan, 3600);

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
        $post = Yii::$app->request->post();
        $cache = Yii::$app->cache;
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $form = new PenerimaanSupplierForm;
        $form->scenario = PenerimaanSupplierForm::SCENARIO_GUDANG_FARMASI;

        $post['PenerimaanSupplierForm']['is_consigment'] = $post['isConsigmentHeader'];
        $post['PenerimaanSupplierForm']['is_donasi'] = $post['isDonasiHeader'];
        unset($post['isConsigmentHeader']);
        unset($post['isDonasiHeader']);

        $form->load($post);
        if ($form->validate()) {
            $cachePenerimaan = $cache->get("penerimaan-" . $id_pegawai);
            if ($cachePenerimaan) {
                try {
                    $result = $this->_restGudang->post('penerimaan-obat-supplier/save',[
                        'form_params' => [
                            'data' => json_encode($cachePenerimaan),
                            'post' => $post,
                        ]
                    ]);
                    $result = json_decode($result->getBody(),true);
                    if ($result['metadata']['status'] == 200) {
                        $cachePenerimaan = $cache->delete("penerimaan-" . $id_pegawai);
                        \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                    }
                    return DocoHelpers::response($result, false);

                } catch (RequestException $e) {
                    $response = json_decode($e->getResponse()->getBody(), true);
                    $response['response']['text'] = 'Terjadi kesalahan pada sistem';
                    return DocoHelpers::response($response, 422);
                }
            }
        }else{
            $response = $form->errors;
            return DocoHelpers::response($response,422,"PenerimaanSupplierForm");
        }
    }

    public function actionDeleteListItem($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $cachePenerimaan = Yii::$app->cache->get("penerimaan-" . $id_pegawai);
        if ($cachePenerimaan !== false) {
            if (isset($cachePenerimaan[$id])) {
                unset($cachePenerimaan[$id]);
                Yii::$app->cache->set("penerimaan-" . $id_pegawai, $cachePenerimaan);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionCetak($no_penerimaan)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $path = Yii::getAlias("@download") . "/penerimaan-obat-supplier.pdf";
            $response = $this->_restGudang->get('penerimaan-obat-supplier/export-pdf?no_penerimaan='.$no_penerimaan,
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

    public function actionAlertHarga($id)
    {
        $request = Yii::$app->request;
        $id = is_integer($id) ? $id : DocoHelpers::decrypt($id);
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
            $response = $this->_restGudang->post('penerimaan-obat-supplier/save-update-harga?trace=1', [
                "form_params" => $request->post()
            ]);
            $body = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage());
        }

        return DocoHelpers::response($body);
    }

    private function getAttributes($satuankonversi_id = null)
    {
        try {
            $request = $this->_restGudang->get('penerimaan-obat-supplier/get-attribute', [
                'query' => []
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
        } catch (RequestException $e) {
            (new DocoHelpers)->logError($e);
            $attributes = [
                'payterm' => [],
                'ppn' => [],
                'sumber_penerimaan' => [],
            ];
        }

        return $attributes;
    }

    private function getRequest($satuankonversi_id = null)
    {
        try {
            $request = $this->_restGudang->request('GET', 'adjustment-obat-alkes/generate-api', [
                'query' => [
                    'satuankonversi_id' => $satuankonversi_id
                ]
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
        } catch (RequestException $e) {
            $attributes = [];
        }

        return $attributes;
    }

}
