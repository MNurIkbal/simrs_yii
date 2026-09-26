<?php

/**
 * @Author: Rizqi Fitrianto
 * @edited : Yaya
 * @Date:   2018-02-26 10:39:51
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-09 11:00:36
 */


namespace Doco\apotek\controllers;

use app\components\DHtml;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\PemakaianObatAlkesForm;
use yii\helpers\ArrayHelper;

class InformasiPemakaianObatalkesController extends DocoController
{
    protected $_title = "Pemakaian Obat Alkes";
    protected $_module = '/apotek/informasi-pemakaian-obatalkes/';
    protected $_restApotek;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
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
        return $this->render('informasi', get_defined_vars()); 
    }

    public function actionGetDataPemakaian() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['ruangan_id'] = $ruangan_id;
        
        $dataPemakaian = $this->guzzleExec($this->_restApotek, [
            'url' => 'inf-pemakaian-obatalkes/index',
            'method' => 'get',
            'payload' => [
                'query' => $yiiRestfulParams
            ],
        ]);
        
        $no = $request->get('start', 1);
        $data = [];
        foreach (ArrayHelper::getValue($dataPemakaian, 'data') as $key => $value) {
            $no++;
            $value['primary'] = DocoHelpers::encrypt($value['pemakaianobat_id']);
            $value['tglpemakaianobat'] = date('d M Y H:i:s', strtotime($value['tglpemakaianobat']));
            $value['rowNum'] = $no;                
            $data[$key] = $value;
        }
        $result['data'] = $data;
        $result['recordsTotal'] = ArrayHelper::getValue($dataPemakaian, '_meta.totalCount');
        $result['recordsFiltered'] = ArrayHelper::getValue($dataPemakaian, '_meta.totalCount');
        return DocoHelpers::response($result);
    }

    public function actionDelete($id)
    {
        try {
            $primary = DocoHelpers::decrypt($id);
            $response = $this->_restApotek->request('POST','inf-pemakaian-obatalkes/hapus-pemakaian', 
                [
                    'query' => [
                        'id' => $primary
                    ]
                ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionView($id) {
        $title = DHtml::getTitleMenu();
        $id = DocoHelpers::decrypt($id);
        $model = new PemakaianObatAlkesForm;
        $detail = $this->guzzleExec($this->_restApotek, [
            'url' => 'inf-pemakaian-obatalkes/view',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'id' => $id
                ]
            ]
        ]);

        $model->attributes = ArrayHelper::getValue($detail, 'pemakaian_obat');
        return $this->render('detail',get_defined_vars());
    }

    public function actionSetListItem($id)
    {
        $request = Yii::$app->request;
        $model = new PemakaianObatAlkesForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $cacheKonv = Yii::$app->cache->get('konvert-satuan');
        $id = DocoHelpers::decrypt($id);

        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        $model->load($request->post());
        $model->instalasi_id = $instalasi_id;
        $model->ruangan_id = $ruangan_id;
        if ($model->validate()) {
            $cacheObatAlkes = Yii::$app->cache->get("pemakaian-obat-{$instalasi_id}-{$ruangan_id}-{$id}");
            $satuan = $model->satuan;
            $qty = $model->qty;
            $satuanKecil = $request->post('satuankecil_id');
            $satuanBesar = $request->post('satuanbesar_id');
            if ($cacheObatAlkes == false) {
                Yii::$app->cache->set("pemakaian-obat-{$instalasi_id}-{$ruangan_id}-{$id}",[]);
                $cacheObatAlkes = [];
            }

            if (!isset($cacheObatAlkes[$model->obatalkes_id])) {
                $cacheObatAlkes[$model->obatalkes_id] = [];
            }

            $permintaan = isset($cacheObatAlkes[$model->obatalkes_id]['permintaan']) 
                            ? $cacheObatAlkes[$model->obatalkes_id]['permintaan'] : 0;
            $totalPermintaan = $permintaan + $model->qty;
            if (isset($cacheKonv[$satuan][$satuanKecil])) {
                $totalPermintaan = ($cacheKonv[$satuan][$satuanKecil] * $model->qty) + $permintaan;
            }

            $satuanBesar = isset($cacheKonv[$satuanBesar][$satuanKecil]) 
                            ? $cacheKonv[$satuanBesar][$satuanKecil] : 0;
            $hasilSatuanBesar = $satuanBesar != 0 ? round($totalPermintaan/$satuanBesar,2) : 0;

            $setCache = [
                'text' => $request->post('text'),
                'kode_obat' => $request->post('kode_obat'),
                'nama_obat' => $request->post('nama_obat'),
                'satuankecil_id' => $request->post('satuankecil_id'),
                'satuankecil_nama' => $request->post('satuankecil_nama'),
                'satuanbesar_id' => $request->post('satuanbesar_id'),
                'satuanbesar_nama' => $request->post('satuanbesar_nama'),
                'jumlah_input' => $hasilSatuanBesar,
                'permintaan' => $totalPermintaan,
                'harga_netto' => $request->post('harga_netto'),
                'tanggal_pemakaian' => $model->tanggal_pemakaian,
                'keterangan_pemakaianobat' => $model->keterangan_pemakaianobat,
            ];

            $cacheObatAlkes[$model->obatalkes_id] = $setCache;

            $cacheObatAlkes = Yii::$app->cache->set("pemakaian-obat-{$instalasi_id}-{$ruangan_id}-{$id}",$cacheObatAlkes,3600);
            
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

    public function actionDeleteListItem($id_parent,$id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $id_parent = DocoHelpers::decrypt($id_parent);
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-obat-{$instalasi_id}-{$ruangan_id}-{$id_parent}");
        if ($cacheObatAlkes !== false) {
            if (isset($cacheObatAlkes[$id])) {
                unset($cacheObatAlkes[$id]);
                Yii::$app->cache->set("pemakaian-obat-{$instalasi_id}-{$ruangan_id}-{$id_parent}",$cacheObatAlkes);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionGetListItemBefore($id)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw',1);
        $data = [];
        $yiiRestfulParams['id'] = $id;
        try {
            $response = $this->_restApotek->get('inf-pemakaian-obatalkes/get-detail-pemakaian', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            $data = [];                                       
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                // $primary = json_encode([$value['pemakaianobatdetail_id'], $value['pemakaianobat_id'], $value['obatalkes_id']]);
                $value['primary'] = DocoHelpers::encrypt($value['pemakaianobatdetail_id']);
                $value['rowNum'] = $no;                
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

    public function actionGetListItem($id)
    {
        $request = Yii::$app->request;
        $id_clean = DocoHelpers::decrypt($id);
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-obat-{$instalasi_id}-{$ruangan_id}-{$id_clean}");
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $konvSatuan = Yii::$app->cache->get('konvert-satuan');
        if ($cacheObatAlkes !== false) {
            $no = $request->get('start',1);
            foreach ($cacheObatAlkes as $key => $value) {
                $no++;
                $satuanBesarId = $value['satuanbesar_id'];
                $satuanKecilId = $value['satuankecil_id'];
                $satuanBesar = isset($konvSatuan[$satuanBesarId][$satuanKecilId]) 
                                ? $konvSatuan[$satuanBesarId][$satuanKecilId] : 0;
                $hasilSatuanBesar = $satuanBesar != 0 ? round($value['permintaan']/$satuanBesar,2) : 0;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'kode_obat' => $value['kode_obat'],
                    'nama_obat' => $value['nama_obat'],
                    'qty_besar' => $hasilSatuanBesar,
                    'satuan_besar' => $value['satuanbesar_nama'],
                    'qty_kecil' => $value['permintaan'],
                    'satuan_kecil' => $value['satuankecil_nama'],
                    'keterangan_pemakaianobat' => $value['keterangan_pemakaianobat'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([
                                $this->_module .'delete-list-item',
                                'id' => $primaryKey,
                                'id_parent' => $id
                            ]),
                        ]
                    )
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
        }

        return DocoHelpers::response($result);
    }

    public function actionSearchObatAlkes()
    {
        $response = [];
        try {
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $request = Yii::$app->request;
            $result = $this->_restApotek->get('allow/list-stok-apotek',[
                            'query' => [
                                'instalasi_id' => $instalasi_id,
                                'ruangan_id' => $ruangan_id,
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $satuan = [];
                if (isset($value['satuankecil_id'])) {
                    $satuan[$value['satuankecil_id']] = $value['satuankecil_nama'];
                }

                if (isset($value['satuanbesar_id'])) {
                    $satuan[$value['satuanbesar_id']] = $value['satuanbesar_nama'];
                }

                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_kode'] . ' - ' . $value['obatalkes_namalain'],
                    'kode_obat' => $value['obatalkes_kode'],
                    'nama_obat' => $value['obatalkes_namalain'],
                    'stok' => $value['qty_tersedia'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuanbesar_nama' => $value['satuanbesar_nama'],
                    'satuan' => $satuan,
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

    public function actionBeforePrint($id)
    {
        $title = 'Print Pemakaian Obat Alkes';
        $parentId = $id;
        $data = [
            'ruangan_nama' => '',
            'nopemakaian_obat' => '',
            'tglpemakaianobat' => ''
        ];
        try {
            $result = $this->_restApotek->get('inf-pemakaian-obatalkes/before-print',[
                            'query' => [
                                'id' => DocoHelpers::decrypt($id),
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = $result['response'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['response']['text'] = 'Terjadi kesalah pada sistem';
        }
        return $this->renderPartial('cetak',get_defined_vars());
    }

    public function actionCetakPdf($id)
    {
        $id_pemakaian = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/pemakaian-obat-alkes.pdf";
        try {
            $response = $this->_restApotek->get('pemakaian-obat-alkes/cetak', [
                'query' => [
                    'id' => $id_pemakaian
                ],
                'save_to' => $path
            ]);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionSave($id)
    {
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $id = DocoHelpers::decrypt($id);
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-obat-{$instalasi_id}-{$ruangan_id}-{$id}");
        $response['response'] = [
            'text' => 'Obat alkes harus terisi',
            'title' => 'Proses Gagal !'
        ];
        $codeHttp = 422;
        if ($cacheObatAlkes) {
            try {
                $result = $this->_restApotek->post('inf-pemakaian-obatalkes/save',[
                    'form_params' => [
                        'instalasi_id' => $instalasi_id,
                        'ruangan_id' => $ruangan_id,
                        'data' => json_encode($cacheObatAlkes)
                    ],
                    'query' => [
                        'id' => $id
                    ]
                ]);
                $result = json_decode($result->getBody(),true);
                $response['response'] = $result;
                $cacheSatuan = isset($result['response']) ? $result['response'] : [];
                Yii::$app->cache->set('konvert-satuan',$cacheSatuan,3600);
                $codeHttp = 200;
                $response['response'] = [
                    'text' => 'Obat alkes berhasil disimpan',
                    'title' => 'Proses berhasil !'
                ];
                Yii::$app->cache->set("pemakaian-obat-{$instalasi_id}-{$ruangan_id}-{$id}",[]);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistem';
            }
        }
        return DocoHelpers::response($response,$codeHttp);
    }
}