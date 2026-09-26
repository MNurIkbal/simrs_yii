<?php

/**
** @author yaya
** @since 20 mar 2018
**/
namespace Doco\apotek\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use Doco\apotek\components\access\VerifikasiPemesananAccess as VerifikasiPemesanan;
use Doco\apotek\components\access\BatalPemesananAccess as BatalPemesanan;
use Doco\apotek\models\QtyPesanForm;
use Doco\apotek\models\TambahObatForm;
use GuzzleHttp\Exception\RequestException;

class InformasiObatAlkesKeluarController extends DocoController
{
    const STATUS_PESAN = 398;
    public $_title = "Informasi Pemesanan Obat Alkes Keluar";
    protected $_module = '/apotek/informasi-retur';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_title = Yii::t("fe", "Informasi Pemesanan Obat Alkes Keluar");
    }

    public function actionIndex()
    {
        $instalasi = $ruangan = [];
        $title = $this->_title;
        try {
            $response = $this->_restApotek->get('inf-obat-alkes-keluar/get-fillter',[]);
            $response = json_decode($response->getBody(),true);
            $instalasi = $response['response']['instalasi'];
            $ruangan = $response['response']['ruangan'];
            $nopemesanan = $response['response']['nopemesanan'];
            $status = $response['response']['statusdistribusi'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }
        $btn_toolbar = [
                        'search',
                        'reset',
                        'lihat' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Lihat'),
                            'icon' => 'fa fa-eye',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-lihat',
                                'data-target' => Url::home().('apotek/informasi-obat-alkes-keluar/preview?id=')
                            ]
                        ],
                        'pesan' => [
                            'type' => 'button',
                            'title' => 'Pesan',
                            'icon' => 'fa fa-plus',
                            'attributes'=>[
                                'data-target'=>'/apotek/transaksi-pemesanan/obat-alkes',
                                'data-options'=>'link'
                            ]
                        ],
                        'penerimaan' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Penerimaan'),
                            'icon' => 'fa fa-check-square-o',
                            'method' => 'not-exist',
                            'attributes' => [
                                'class' => 'data-penerimaan',
                                'data-target'=>'/apotek/informasi-mutasi/penerimaan-pesanan?id=',
                                'data-conditions'=>'nomutasioa'
                            ]
                        ],
                        'print' => [
                            'title' => 'Cetak Permintaan',
                            'attributes' => [
                                'id' => 'btn-cetak-permintaan',
                                'data-options' => false,
                                'data-pages' => '_blank',
                                'data-target' => '/apotek/informasi-obat-alkes-keluar/export-pdf?id=',
                            ]
                        ],
                    ];
        if((new VerifikasiPemesanan)->checkKonfigOnly() == TRUE){
            $btn_toolbar['edit-pesan']=[
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Edit'),
                            'icon' => 'fa fa-pencil',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-edit',
                                'id' => 'btn-edit-pesan',
                                'data-target' => Url::home().('apotek/informasi-obat-alkes-keluar/edit?id=')
                            ]
                        ];
        }

        return $this->render('index',get_defined_vars());
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_pemesan_id'] = $ruangan_id;
        if(!isset($yiiRestfulParams['advanced-filter']['tglpemesanan'])){
            $yiiRestfulParams['advanced-filter']['tglpemesanan'] = date('d-M-Y').' - '.date('d-M-Y');
        }
        $draw = $request->get('draw', 1);
        $data = [];

        // return DocoHelpers::response($yiiRestfulParams);

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-obat-alkes-keluar/get-data',
                    [
                        'query' => http_build_query($yiiRestfulParams)
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pesanobatalkes_id']);
                unset($value['pesanobatalkes_id']);
                $value['tglpemesanan'] = date("j M Y", strtotime($value['tglpemesanan']));
                if(isset($value['tglmutasioa']))
                    $value['tglmutasioa'] = date("j M Y", strtotime($value['tglmutasioa']));
                if(isset($value['tglterima']))
                    $value['tglterima'] = date("j M Y", strtotime($value['tglterima']));

                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
                $value['status_kirim'] = $value['statuspesan'] == self::STATUS_PESAN ? true : false;
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

    public function actionPreview($id)
    {
        $data = (object) [];
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restApotek->get('inf-obat-alkes-keluar/get-detail',
                [
                    'query' => [
                        'id' => $id
                    ]
                ]
            );
            $response = json_decode($response->getBody(),true);
            $data = (object) $response['response']['data'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }

        $btn_toolbar = [
            'back',
            'pdf' => [
                'type' => 'link',
                'attributes' => [
                    'data-options' => 'link',
                    'class' => 'btn btn-info btn-labeled btn-xs data-print',
                    'id' => 'cetak-pdf',
                    'url' => '/apotek/informasi-obat-alkes-keluar/export-pdf?id=' . DocoHelpers::encrypt($id)
                ]
            ]
        ];

        if((new BatalPemesanan)->check($data->statuspesan, DocoConstants::BATAL_PESAN_PEMESAN)){
            $btn_toolbar['batal'] = [
                'type' => 'button',
                'title' => 'Batal Pesan',
                'icon' => 'fa fa-close',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'batal-pemesanan',
                    'data-url' => '/apotek/informasi-obat-alkes-keluar/batal-pemesanan?id='.DocoHelpers::encrypt($id)
                ]
            ];
        }
        
        if((new VerifikasiPemesanan)->check($data->statuspesan,$data->status_verifikasi)){
            $btn_toolbar['verifikasi'] = [
                'type' => 'button',
                'title' => 'Verifikasi',
                'icon' => 'fa fa-check',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'verifikasi-pemesanan',
                    'data-url' => '/apotek/informasi-obat-alkes-keluar/verifikasi?id='.DocoHelpers::encrypt($id)
                ]
            ];
        }

        return $this->render('preview',get_defined_vars());
    }

    public function actionVerifikasi($id)
    {
        $id = DocoHelpers::decrypt($id);
        return (new VerifikasiPemesanan)->verify($id);
    }

    public function actionBatalPemesanan($id) {
        $id = DocoHelpers::decrypt($id);
        $url = 'inf-obat-alkes-keluar/batal-pemesanan';
        return (new BatalPemesanan)->batal($id, $url);
    }

    public function actionEdit($id)
    {
        $data = (object) [];
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        if ($request->post()) {
            $post = $request->post();
            $qtyForm = new QtyPesanForm;
            $formName = substr(strrchr(get_class($qtyForm), "\\"), 1);
            if ($qtyForm->validateArrayQty($post['listQtyObat'])) {
                $listQtyObat = [];
                foreach ($post['listQtyObat'] as $key => $value) {
                    preg_match('!\d+!', $value['name'], $matches);
                    $obatId = $matches[0];
                    $listQtyObat[$obatId] = $value['value'];
                }

                foreach ($post['listObat'] as $key => $value) {
                    $obatalkes_id = $post['listObat'][$key]['obatalkes_id'];
                    if (array_key_exists($obatalkes_id, $listQtyObat)) {
                        $post['listObat'][$key]['qty_besar'] = $listQtyObat[$obatalkes_id];
                        $post['listObat'][$key]['qty_form'] = $listQtyObat[$obatalkes_id];
                    }
                }

                $response = $this->_restApotek->post('inf-obat-alkes-keluar/edit-pemesanan', [
                    'form_params' => [
                        'formData' => $post
                    ],
                    'query' => ['pesanobatalkes_id' => $id]
                ]);

                $result = json_decode($response->getBody(), true);
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                \Yii::$app->response->statusCode = $result['metadata']['status'];
                if($result['metadata']['status'] == 422){
                    $result['response']['text'] = $result['response']['message'];
                }
                return $result;
            } else {
                return DocoHelpers::response([
                    'response' => [
                        'title' => 'Proses Gagal!',
                        'text' => 'Qty Pemesanan harus lebih dari 0'
                ]], 422);
            }
        }
        try {
            $response = $this->_restApotek->get('inf-obat-alkes-keluar/get-detail',
                [
                    'query' => [
                        'id' => $id
                    ]
                ]
            );
            $response = json_decode($response->getBody(),true);
            $data = (object) $response['response']['data'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }

        $btn_toolbar =  [
                        'back',
                        'pdf' => [
                            'type' => 'link',
                            'attributes' => [
                                'data-options' => 'link',
                                'class' => 'btn btn-info btn-labeled btn-xs data-print',
                                'id' => 'cetak-pdf',
                                'url' => '/apotek/informasi-obat-alkes-keluar/export-pdf?id=' . DocoHelpers::encrypt($id)
                            ]
                        ],
                        'simpan' => [
                            'type' => 'button',
                            'title' => 'Simpan',
                            'icon' => 'fa fa-save',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'save-edit-pesan',
                                'data-url' => '/apotek/informasi-obat-alkes-keluar/edit?id=' . DocoHelpers::encrypt($id)
                            ]
                        ]
                    ];

        return $this->render('edit',get_defined_vars());
    }

    public function actionGetDataPemesanan($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['pesanobatalkes_id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-obat-alkes-keluar/get-data-detail',
                    [
                        'query' => http_build_query($filter)
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $data[] = [
                "rowNum" => "",
                "obatalkes_nama" => "",
                "qty_pemesanan" => "",
                "qty_konversi" => "",
                "qty_terima" => "",
            ];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $data[$key] = [
                    "rowNum" => $no,
                    "obatalkes_nama" => isset($value["obatalkes_nama"]) ? $value["obatalkes_nama"] : "",
                    "qty_pemesanan" => (isset($value["qty_besar"]) && isset($value["satuan_besar"])) ?  $value["qty_besar"]." ".$value["satuan_besar"] : "",
                    "qty_konversi" => (isset($value["jumlah_pesan"]) && isset($value["satuan_kecil"])) ?  $value["jumlah_pesan"]." ".$value["satuan_kecil"] : "",
                    // "qty_terima" => isset($value["jumlah_diterima"]) && $value["jumlah_diterima"] > 0 ? $value["jumlah_diterima"]." ".$value["satuan_kecil"] : "-"
                    "qty_terima" => @$value['jumlah_input_mutasi'].' '.@$value['satuan_besar']
                ];
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

    public function actionSearchObatAlkes()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restApotek->get('allow/list-stok-apotek', [
                'query' => [
                    'ruangan_id' => $request->get('ruangan_id'),
                    'term' => $request->get('term')
                ]
            ]);
            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $nama = empty($value['obatalkes_namalain']) ? $value['obatalkes_nama'] : $value['obatalkes_namalain'];
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $nama,
                    'stok' => $value['qty_tersedia'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuanbesar_nama' => $value['satuanbesar_nama'],
                    'satuan' => $value['satuan'],
                    'harga_netto' => $value['harganetto']
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

    public function actionGetDataPemesananForm($id) {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $filter['pesanobatalkes_id'] = $id;
        $data = [];

        try {
            $response = $this->_restApotek->get('inf-obat-alkes-keluar/get-data-edit',
                    [
                        'query' => http_build_query($filter)
                    ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 0);
            $data[] = [
                "rowNum" => "",
                "pesanobatdetail_id" => "",
                "obatalkes_id" => "",
                "obatalkes_nama" => "",
                "satuankecil_id" => "",
                "satuanbesar_id" => "",
                "qty_kecil" => "",
                "qty_besar" => "",
                "satuan_nama" => "",
                "qty_pemesanan" => "",
                "qty_konversi" => "",
                "nilai_konversi" => "",
                "stok" => "",
                "action" => "",
            ];

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $identifier = $value["obatalkes_id"]."-".$value["satuankecil_id"];

                $data[$key] = [
                    "identifier" => $identifier,
                    "to_delete" => false,
                    "rowNum" => $no,
                    "pesanobatdetail_id" => $value["pesanobatdetail_id"],
                    "obatalkes_id" => $value["obatalkes_id"],
                    "obatalkes_nama" => isset($value["obatalkes_nama"]) ? $value["obatalkes_nama"] : "",
                    "satuankecil_id" => $value["satuankecil_id"],
                    "satuanbesar_id" => $value["satuanbesar_id"],
                    "qty_kecil" => $value['qty_kecil'],
                    "qty_besar" => $value['qty_besar'],
                    "qty_form" => "<div class='col-md-6'>".Html::input('text',
                        "QtyPesan[".$value['obatalkes_id']."]",$value['qty_besar'],
                        [
                            'class'=>'form-control qty-form text-right doco-number',
                            'data-id' => $value['obatalkes_id'],
                        ]
                    )."</div><div class='col-md-6'><p class='form-control-static'>".$value["satuan_besar"]."</p></div>",
                    "qty_konversi" => (isset($value["jumlah_pesan"]) && isset($value["satuan_kecil"])) ? "<span class='qty-konversi-".$value["obatalkes_id"]."'>".$value["jumlah_pesan"]."</span>"." ".$value["satuan_kecil"] : "",
                    "jumlah_pesan" => (isset($value["jumlah_pesan"]) && isset($value["satuan_kecil"])) ? $value["jumlah_pesan"] : $value["qty_kecil"],
                    "nilai_konversi" => $value['qty_kecil'] / ($value['qty_besar'] > 0 ? $value['qty_besar'] : 1),
                    "stok" => $value['stok_pengirim'],
                    "action_column" => "<button class='btn btn-xs btn-danger btn-delete-row' data-key='$identifier'><i class='fa fa-trash'></i></button>",
                ];
            }

            $result['data'] = $data;
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionExportPdf($id)
    {
        $date = date("Y-m-d");
        $path = Yii::getAlias("@download") . "/pemesanan-obat-alkes.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            if(Yii::$app->report->enabled) {
                $urlReport = 'permintaan_obat';
                $query = [
                    'id' => $id
                ];
                return Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $query,
                    'manualRender'=>function() use($query,$path){
                        $response = $this->_restApotek->post('inf-obat-alkes-keluar/cetak-pemesanan-obat',[
                            'query' => $query,
                            'save_to' => $path
                        ]);
    
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }
            $response = $this->_restApotek->post('inf-obat-alkes-keluar/cetak-pemesanan-obat', [
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
            $response = $this->_restApotek->delete('inf-obat-alkes-keluar/delete', [
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

    public function actionTambahObat() {
        $title = \Yii::t('fe', 'Tambah Obat Alkes');
        $request = Yii::$app->request;
        $model = new TambahObatForm;
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        try {
            Yii::$app->cache->delete('pemesanan-obat-' . $id_pegawai. $ruangan_id);
            $instalasi = Yii::$app->cache->get('instalasi');

            // if ($instalasi == false) {
            //     $response = $this->_restMaster->get('instalasi/index?advanced-filter[is_active]=1');
            //     $body = json_decode($response->getBody(), true);
            //     $instalasi_data = ArrayHelper::map($body['response']['data'], 'instalasi_id', 'instalasi_nama');
            //     Yii::$app->cache->set('instalasi', $instalasi_data, 60);
            //     $instalasi = $instalasi_data;
            // }

            $result = $this->_restApotek->get('allow/set-cache-konvert-satuan', []);
            $result = json_decode($result->getBody(), true);
            $cacheSatuan = isset($result['response']) ? $result['response'] : [];
            Yii::$app->cache->set('konvert-satuan', $cacheSatuan, 3600);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $cacheSatuan = [];
        }
        $cache = json_encode($cacheSatuan);

        return $this->renderAjax('form-tambah-obat', get_defined_vars());
    }
}