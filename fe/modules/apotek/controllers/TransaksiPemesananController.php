<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Pemesanan
 * @copyright 15 January 2018 aweutist
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\apotek\models\TransaksiPemesananForm;
use yii\helpers\ArrayHelper;

class TransaksiPemesananController extends DocoController
{

    protected $_title = "Pemesanan";
    protected $_module = '/apotek/transaksi-pemesanan';
    protected $_restApotek;
    protected $_restMaster;
    protected $_ruangan_gdf = 25;
    protected $_instalasi_gdf = DocoConstants::INSTALASI_GUDANG_FARMASI;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }


    public function actionObatAlkes($state = '')
    {
        $title = $this->_title . ' Obat Alkes';
        $model = new TransaksiPemesananForm;
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi_gdf = $this->_instalasi_gdf;
        $ruangan_gdf = $this->_ruangan_gdf;

        // Init Awal
        $model->instalasi_tujuan = $instalasi_gdf;

        try {
            Yii::$app->cache->delete('pemesanan-obat-' . $id_pegawai. $ruangan_id);
            $instalasi = Yii::$app->cache->get('instalasi');

            if ($instalasi == false) {
                $response = $this->_restMaster->get('instalasi/get-all-data?advanced-filter[is_active]=1');
                $body = json_decode($response->getBody(), true);
                $instalasi_data = ArrayHelper::map($body['response']['data'], 'instalasi_id', 'instalasi_nama');
                Yii::$app->cache->set('instalasi', $instalasi_data, 60);
                $instalasi = $instalasi_data;
            }

            $result = $this->_restApotek->get('allow/set-cache-konvert-satuan', []);
            $result = json_decode($result->getBody(), true);
            $cacheSatuan = isset($result['response']) ? $result['response'] : [];
            Yii::$app->cache->set('konversi_satuan', $cacheSatuan, DocoConstants::EXPIRED_CACHE);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $cacheSatuan = [];
        }
        $cache = json_encode($cacheSatuan);

        return $this->render('obat-alkes', get_defined_vars());
    }

    public function actionGetListItem()
    {
        $request = Yii::$app->request;

        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai . $ruangan_id);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $konvSatuan = Yii::$app->cache->get('konversi_satuan');
        if ($cacheObatAlkes !== false) {
            $no = $request->get('start', 0);
            foreach ($cacheObatAlkes as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'nama_obat' => $value['text'],
                    'kode_obat' => $value['kode'],
                    'qty_besar' => $value['qty_besar']." ".$value['satuanbesar_nama'],
                    'satuan_besar' => $value['satuanbesar_nama'],
                    'qty_kecil' => $value['qty_kecil']." ".$value['satuankecil_nama'],
                    'satuan_kecil' => $value['satuankecil_nama'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",
                        [
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module . '/delete-cache', 'id' => $primaryKey]),
                        ]
                    )
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
        }

        return DocoHelpers::response($result);
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        try {
            $response = $this->_restApotek->get('allow/get-ruangan-by?id=' . $parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) {
                if ($ruangan_id != $value['ruangan_id']) {
                    $result['output'][] = [
                        'id' => $value['ruangan_id'],
                        'name' => $value['ruangan_nama']
                    ];
                }
            }

            if (count($result['output']) == 1) {
                $result['selected'] = $result['output'][0]["id"];
            }

            if ($parent_label == $this->_instalasi_gdf) {
                $result['selected'] = '25';
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

    public function actionSearchObatAlkes()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $ignore_stok = $request->get('ignore_stok') ? $request->get('ignore_stok') : false;
            $konfigPesanStokObat = $this->konfigPesanStokObat();

            if($ignore_stok) {
                $url = $konfigPesanStokObat ? 'allow/list-ketersediaan-stok-obat' : 'allow/list-obat-alkes';
            } else {
                $url = 'allow/list-stok-apotek';
            }

            $get = $request->get();

            $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : null;
            $keyword = isset($get['q']) ? $get['q'] : '';
            $page = isset($get['page']) ? $get['page'] : 1;

            $result = $this->_restApotek->get($url, [
                'query' => [
                    'ruangan_id' => $ruangan_id,
                    'term' => $keyword,
                    'page' => $page
                ]
            ]);
            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $nama = empty($value['obatalkes_namalain']) ? $value['obatalkes_nama'] : $value['obatalkes_namalain'];
                if ($ignore_stok) // untuk pemesanan
                {
                    $disabled = isset($value['qty_tersedia']) && $value['qty_tersedia'] <= 0 && $konfigPesanStokObat ? true : false;
                } else {
                    $disabled = isset($value['qty_tersedia']) && $value['qty_tersedia'] <= 0 ? true : false;
                }
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $nama,
                    'kode' => !empty($value['obatalkes_kode']) ? $value['obatalkes_kode'] : "-",
                    'stok' => $value['qty_tersedia'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuanbesar_nama' => $value['satuanbesar_nama'],
                    'satuan' => $value['satuan'],
                    'harga_netto' => $value['harganetto'],
                    'disabled' => $disabled
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response,
            'payload' => isset($get) ? $get : NULL
        ]);
    }

    public function actionGetStokRuangan($obatalkes_id)
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

            $result = $this->_restApotek->get('allow/get-stok-ruangan', [
                'query' => [
                    'obatalkes_id' => $obatalkes_id,
                    'ruangan_id' => $ruangan_id
                ]
            ]);
            $result = json_decode($result->getBody(), true);
            $response = isset($result['response']) ? $result['response'] : [];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionSaveCache()
    {
        $request = Yii::$app->request;
        $model = new TransaksiPemesananForm();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $reset = $this->_restApotek->get('allow/reset-cache-konvert-satuan', []);
        $cacheKonv = Yii::$app->cache->get('konversi_satuan');
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $post = $request->post();
        $key = ArrayHelper::getValue($post, 'TransaksiPemesananForm.obat_alkes', []);
        $qtyMasuk = ArrayHelper::getValue($post, 'TransaksiPemesananForm.qty', []);
        $stokObat = ArrayHelper::getValue($post, 'TransaksiPemesananForm.stok', []);
        $model->load($post);

        if ($model->validate()) {
            if(!$cacheKonv) {
                $result = $this->_restApotek->get('allow/set-cache-konvert-satuan', []);
                $result = json_decode($result->getBody(), true);
                $cacheSatuan = isset($result['response']) ? $result['response'] : [];
                Yii::$app->cache->set('konversi_satuan', $cacheSatuan, DocoConstants::EXPIRED_CACHE);
            }

            $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai . $ruangan_id);
            // if(!empty($cacheObatAlkes[$key])){
            //     if(isset($cacheObatAlkes[$key]['permintaan'])){
            //         $newTotalPermintaan = $cacheObatAlkes[$key]['permintaan'] + $qtyMasuk;
            //         if($newTotalPermintaan > $stokObat){
            //             $response['response'] = [
            //                 'title' => 'Proses Gagal!',
            //                 'text' => 'Qty Melebihi Stok'
            //             ];
            //             return DocoHelpers::response($response, 422);
            //         }
            //     }
            // }
            $satuan = $model->satuan;
            $qty = $model->qty;
            $satuanKecil = $request->post('satuankecil_id');
            $model->stok = $request->post('stok_asli');
            if ($cacheObatAlkes == false) {
                Yii::$app->cache->set("pemesanan-obat-" . $id_pegawai . $ruangan_id, []);
                $cacheObatAlkes = [];
            }

            if (!isset($cacheObatAlkes[$model->obat_alkes])) {
                $cacheObatAlkes[$model->obat_alkes] = [];
            }

            $permintaan = isset($cacheObatAlkes[$model->obat_alkes]['permintaan'])
                ? $cacheObatAlkes[$model->obat_alkes]['permintaan'] : 0;
            $totalPermintaan = $permintaan + $model->qty;

            if (isset($cacheKonv[$model->obat_alkes][$satuan])) {
                $totalPermintaan = ($cacheKonv[$model->obat_alkes][$satuan] * $model->qty) + $permintaan;
            }

            $satuanBesarId = $request->post('satuan_pesan_id');
            $satuanKecilId = $request->post('satuankecil_id');
            $satuanBesar = isset($cacheKonv[$model->obat_alkes][$satuan])
                ? $cacheKonv[$model->obat_alkes][$satuan]
                : 1;
            $hasilSatuanBesar = $satuanBesar != 0 ? round($totalPermintaan / $satuanBesar, 2) : $totalPermintaan;
            $id_satuan_pesan = $request->post('satuan_pesan_id');
            $id_satuan_besar = $request->post('satuanbesar_id');
            $setCache = [
                'obatalkes_id' => $request->post('id'),
                'text' => $request->post('text'),
                'kode' => $request->post('kode_obat'),
                'satuankecil_id' => $request->post('satuankecil_id'),
                'satuankecil_nama' => $request->post('satuankecil_nama'),
                'satuanbesar_id' => $request->post('satuan_pesan_id'),
                'satuanbesar_nama' => $request->post('satuan_pesan_nama'),
                'instalasi_id' => $model->instalasi_tujuan,
                'ruangan_id' => $model->ruangan_tujuan,
                'qty_pesan' => ($id_satuan_pesan == $id_satuan_besar) ? $hasilSatuanBesar : $totalPermintaan,
                'qty_besar' => $hasilSatuanBesar,
                'qty_kecil' => $totalPermintaan,
                'permintaan' => $totalPermintaan,
                'tanggal_kirim' => $model->tanggal_kirim
            ];
            $cacheObatAlkes[$model->obat_alkes] = $setCache;
            $cacheObatAlkes = Yii::$app->cache->set("pemesanan-obat-" . $id_pegawai . $ruangan_id, $cacheObatAlkes);

            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];

            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $formName);
        }
    }

    public function actionDeleteCache($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai . $ruangan_id);
        if ($cacheObatAlkes !== false) {
            if (isset($cacheObatAlkes[$id])) {
                unset($cacheObatAlkes[$id]);
                Yii::$app->cache->set("pemesanan-obat-" . $id_pegawai . $ruangan_id, $cacheObatAlkes);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionDeleteAllCache()
    {
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        if(!empty($id_pegawai && $ruangan_id)){
            Yii::$app->cache->set("pemesanan-obat-" . $id_pegawai . $ruangan_id, array());
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus',
                'id_pegawai' => $id_pegawai,
                'ruangan_id' => $ruangan_id,
            ];
            return DocoHelpers::response($response);
        }
    }

    /**
     * @todo save pemesanan obat alkes
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSave()
    {
        $id_pegawai = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $request = Yii::$app->request;
        $post = $request->post();
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai . $ruangan_id);
        $response['response'] = [
            'text' => 'Obat alkes harus terisi',
            'title' => 'Proses Gagal !'
        ];
        $codeHttp = 422;

        if ($cacheObatAlkes) {
            try {
                $postData = [
                    'tanggal_kirim' => $post['tanggal_kirim'],
                    'keterangan_pesan' => $post['keterangan'],
                    'ruangan_id_pemesanan' => Yii::$app->docoVars->workspace('ruangan_id'),
                    'list_obat' => $cacheObatAlkes
                ];

                $result = $this->_restApotek->request('POST', 'transaksi-pemesanan/save-obat-alkes', [
                    'form_params' => $postData
                ]);

                $result = json_decode($result->getBody(), true);
                $response['response'] = $result;
                $codeHttp = 200;
                $response['response'] = [
                    'text' => 'Obat alkes berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'id' => isset($result['response']['id']) ? $result['response']['id'] : '',
                    'nomor' => isset($result['response']['nomor']) ? $result['response']['nomor'] : '',
                ];
                Yii::$app->cache->delete('pemesanan-obat-' . $id_pegawai. $ruangan_id);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistemss';
            }
        }
        return DocoHelpers::response($response, $codeHttp);
    }

    public function actionCetakPdf($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/transaksi-pemesanan.pdf";
        try {
            if(Yii::$app->report->enabled) {
                $urlReport = 'permintaan_obat';
                $query = [
                    'id' => $id
                ];
                return Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $query,
                    'manualRender'=>function() use($query,$path){
                        $response = $this->_restApotek->post('transaksi-pemesanan/cetak-pdf',[
                            'query' => $query,
                            'save_to' => $path
                        ]);

                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }
            $response = $this->_restApotek->get('transaksi-pemesanan/cetak-pdf?id=' . $id,[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetKonfigRak($obatalkes_id)
    {
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        try{
            $request = $this->_restApotek->request('POST', 'allow/get-konfig-rak', [
                'query' => [
                    "obatalkes_id" => $obatalkes_id,
                    "ruangan_id" => $ruangan_id
                ]
            ]);

            $returnRequest = json_decode($request->getBody(), true);
        }catch(\Exception $e){
            $returnRequest = null;
        }

        /**
        if($returnRequest['response'] == null) {
            $returnRequest['response'] = [
                "obatalkes_id" => $obatalkes_id,
                "ruangan_id" => $ruangan_id,
                "min_stok" => null,
                "max_stok" => null
            ];
        }

        $returnRequest['response']['min_stok'] = is_null($returnRequest['response']['min_stok']) ? 0 : $returnRequest['response']['min_stok'];
        $returnRequest['response']['max_stok'] = is_null($returnRequest['response']['max_stok']) ? 0 : $returnRequest['response']['max_stok'];
        */

        $min_stok = ArrayHelper::getValue($returnRequest,'response.min_stok',0);
        $max_stok = ArrayHelper::getValue($returnRequest,'response.max_stok',0);

        $result = json_encode([
            'min_stok' => $min_stok,
            'max_stok' => $max_stok
        ]);

        return $result;
    }

    private function konfigPesanStokObat() {
        $result = $this->_restApotek->get('allow/konfig-farmasi');
        $result = json_decode($result->getBody(), true);
        return !empty($result['response']['is_pesanstokobat_0']) ? $result['response']['is_pesanstokobat_0'] : false;
    }
}
