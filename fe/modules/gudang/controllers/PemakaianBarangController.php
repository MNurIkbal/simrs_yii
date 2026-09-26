<?php

/**
** @author yaya
** service :
** - Gudang pemakaian-barang v1
**/

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\gudang\models\PemakaianBarangForm;
use yii\helpers\Html;
use yii\helpers\Url;

class PemakaianBarangController extends DocoController
{
    protected $_title = "Pemakaian Barang";
    protected $_module = '/gudang/pemakaian-barang/';
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $model = new PemakaianBarangForm;
        try {
            $result = $this->_restGudang->get('allow/set-cache-konvert-satuan-barang',[]);
            $result = json_decode($result->getBody(),true);
            $cacheSatuan = isset($result['response']) ? $result['response'] : [];
            Yii::$app->cache->set('konvert-satuan-barang',$cacheSatuan,3600);

            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $uid = Yii::$app->docoVars->user('loginpemakai_id');
            Yii::$app->cache->delete("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}");
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $cacheSatuan = [];
        }

        $cache = json_encode($cacheSatuan);
        return $this->render('form', get_defined_vars());
    }

    public function actionGetListItem()
    {
        $request = Yii::$app->request;

        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $uid = Yii::$app->docoVars->user('loginpemakai_id');
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}");
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $konvSatuan = Yii::$app->cache->get('konvert-satuan-barang');
        if ($cacheObatAlkes !== false) {
            $no = $request->get('start',1);
            foreach ($cacheObatAlkes as $key => $value) {
                $no++;
                $satuanBesarId = $value['satuanbesar_id'];
                $satuanKecilId = $value['satuankecil_id'];
                $satuanBesar = isset($konvSatuan[$value['barang_id']][$value['satuan']])
                                ? $konvSatuan[$value['barang_id']][$value['satuan']] : 0;
                $hasilSatuanBesar = $satuanBesar != 0 ? round($value['permintaan']/$satuanBesar,2) : 0;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'nama_barang' => $value['text'],
                    'qty_besar' => $hasilSatuanBesar == 0 ? $value['permintaan'] : $hasilSatuanBesar,
                    'satuan_besar' => $value['satuanbesar_nama'] != "" ? $value['satuanbesar_nama'] : $value['satuankecil_nama'],
                    'qty_kecil' => $value['permintaan'],
                    'satuan_kecil' => $value['satuankecil_nama'],
                    'keterangan' => $value['keterangan'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-sm delete',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module .'delete-list-item','id' => $primaryKey]),
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

    public function actionSetListItem()
    {
        $request = Yii::$app->request;
        $model = new PemakaianBarangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $cacheKonv = Yii::$app->cache->get('konvert-satuan-barang');

        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $uid = Yii::$app->docoVars->user('loginpemakai_id');

        $model->load($request->post());
        $model->instalasi_id = $instalasi_id;
        $model->ruangan_id = $ruangan_id;
        if ($model->validate()) {
            $cacheObatAlkes = Yii::$app->cache->get("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}");
            $satuan = $model->satuan;
            $qty = $model->qty;
            $satuanKecil = $request->post('satuankecil_id');
            $satuanBesar = $request->post('satuanbesar_id');
            if ($cacheObatAlkes == false) {
                Yii::$app->cache->set("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}",[]);
                $cacheObatAlkes = [];
            }

            if (!isset($cacheObatAlkes[$model->barang_id])) {
                $cacheObatAlkes[$model->barang_id] = [];
            }

            $permintaan = isset($cacheObatAlkes[$model->barang_id]['permintaan'])
                            ? $cacheObatAlkes[$model->barang_id]['permintaan'] : 0;
            $totalPermintaan = $permintaan + $model->qty;
            if (isset($cacheKonv[$model->barang_id][$satuan])) {
                $totalPermintaan = ($cacheKonv[$model->barang_id][$satuan] * $model->qty) + $permintaan;
            }
            $totalSatuanBesar = 0;
            if (!empty($cacheKonv[$model->barang_id][$satuan])) {
                $totalSatuanBesar = $totalPermintaan / $cacheKonv[$model->barang_id][$satuan];
            }
            $setCache = [
                'text' => $request->post('text'),
                'barang_id' => $model->barang_id,
                'satuan' => $model->satuan,
                'satuankecil_id' => $request->post('satuankecil_id'),
                'satuankecil_nama' => $request->post('satuankecil_nama'),
                'satuanbesar_id' => $request->post('satuanbesar_id'),
                'satuanbesar_nama' => $request->post('satuanbesar_nama'),
                'harga_jual' => $request->post('harga_jual'),
                'ppn' => $request->post('ppn'),
                'jumlah_input' => $totalSatuanBesar,
                'permintaan' => $totalPermintaan,
                'harga_netto' => $request->post('harga_netto'),
                'tanggal_pemakaian' => $model->tanggal_pemakaian,
                'keterangan' => $model->keterangan,
            ];

            $cacheObatAlkes[$model->barang_id] = $setCache;

            $cacheObatAlkes = Yii::$app->cache->set("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}",$cacheObatAlkes,3600);

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

    public function actionDeleteListItem($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $uid = Yii::$app->docoVars->user('loginpemakai_id');
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}");
        if ($cacheObatAlkes !== false) {
            if (isset($cacheObatAlkes[$id])) {
                unset($cacheObatAlkes[$id]);
                Yii::$app->cache->set("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}",$cacheObatAlkes);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionSearchBarang()
    {
        $response = [];
        try {
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $request = Yii::$app->request;
            $result = $this->_restGudang->get('allow/list-stok-barang',[
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
                    'id' => $value['barang_id'],
                    'text' => $value['barang_kode'] . ' - ' . $value['barang_nama'],
                    'stok' => $value['qty_tersedia'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuanbesar_nama' => $value['satuanbesar_nama'],
                    'satuan' => $satuan,
                    'ppn' => $value['ppn'],
                    'harga_jual' => $value['harga_jual'],
                    'harga_netto' => $value['harga_netto'],
                    'harga_max' => $value['harga_max'],
                    'harga_min' => $value['harga_min'],
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

    public function actionSave()
    {
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $uid = Yii::$app->docoVars->user('loginpemakai_id');
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}");
        $response['response'] = [
            'text' => 'Barang harus terisi',
            'title' => 'Proses Gagal !'
        ];
        $newCache = [];
        $codeHttp = 422;
        if ($cacheObatAlkes) {
            try {
                $tanggal_pemakaian = Yii::$app->request->post("tanggal_pemakaian");
                $keterangan = Yii::$app->request->post("keterangan");
                $newCache = [
                        'instalasi_id' => $instalasi_id,
                        'tanggal_pemakaian' => date('Y-m-d H:i:s', strtotime($tanggal_pemakaian)),
                        'ruangan_id' => $ruangan_id,
                        'keterangan' => $keterangan,
                        'data' => json_encode($cacheObatAlkes)
                    ];
                $result = $this->_restGudang->post('pemakaian-barang/save',[
                    'form_params' => $newCache
                ]);
                $result = json_decode($result->getBody(),true);
                $response['response'] = $result;
                $cacheSatuan = isset($result['response']) ? $result['response'] : [];
                // Yii::$app->cache->set('konvert-satuan-barang',$cacheSatuan,3600);
                $codeHttp = 200;
                $idParent = isset($result['response']['id_parent'])
                                ? $result['response']['id_parent']
                                : null;
                $newCache['no_pemakaianbarang'] = isset($result['response']['no_pemakaianbarang'])
                                                    ? $result['response']['no_pemakaianbarang']
                                                    : null;
                $newCache['id_parent'] = DocoHelpers::encrypt($idParent);
                $response['response'] = [
                    'text' => 'Obat alkes berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'id_parent' => DocoHelpers::encrypt($idParent)
                ];
                Yii::$app->cache->set("pemakaian-barang-{$instalasi_id}-{$ruangan_id}-{$uid}",[]);
                Yii::$app->cache->set("pemakaian-barang-{$idParent}",$newCache);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistem';
                $response['response']['messageError'] = $e->getMessage();
            }
        }
        return DocoHelpers::response($response,$codeHttp);
    }

    public function actionBeforePrint($id_pemakaian)
    {
        $title = 'Print Pemakaian Barang';
        $id_pemakaian = DocoHelpers::decrypt($id_pemakaian);
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-barang-{$id_pemakaian}");
        $ruangan_name = Yii::$app->docoVars->workspace("ruangan_name");
        $data = [];
        $noPemakaian = $tglPemakaian = $id_pemakaian = null;
        if ($cacheObatAlkes !== false) {
            $noPemakaian = isset($cacheObatAlkes['no_pemakaianbarang'])
                            ? $cacheObatAlkes['no_pemakaianbarang'] : null ;
            $tglPemakaian = isset($cacheObatAlkes['tanggal_pemakaian'])
                            ? $cacheObatAlkes['tanggal_pemakaian'] : null;
            $id_parent = isset($cacheObatAlkes['id_parent'])
                            ? $cacheObatAlkes['id_parent'] : null;
            $data = json_decode($cacheObatAlkes['data'],true);
        }

        return $this->renderPartial('cetak', get_defined_vars());
    }

    public function actionCetakPemakaianBarang($id_pemakaian)
    {
        $id_pemakaian = DocoHelpers::decrypt($id_pemakaian);
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-barang-{$id_pemakaian}");
        $cacheObatAlkes['ruangan_pemakai'] = Yii::$app->docoVars->workspace("ruangan_name");

        $path = Yii::getAlias("@download") . "/gudang-pemakaian-barang.pdf";
        try {
            $response = $this->_restGudang->post('pemakaian-barang/cetak-pemakaian-barang', [
                'form_params' => $cacheObatAlkes,
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}