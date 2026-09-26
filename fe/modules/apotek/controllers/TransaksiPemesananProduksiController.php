<?php

/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\apotek\models\TransaksiPemesananProduksiForm;
use yii\helpers\ArrayHelper;

class TransaksiPemesananProduksiController extends DocoController
{

    protected $title = "Pemesanan Produksi";
    protected $_module = '/apotek/transaksi-pemesanan-produksi';
    protected $restApotek;
    protected $restMaster;
    protected $ruanganGdf = 25;
    protected $instalasiGdf = DocoConstants::INSTALASI_GUDANG_FARMASI;
    protected $successTitle = 'Proses Berhasil!';
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->restApotek = Yii::$app->docoRest->apotek;
        $this->restMaster = Yii::$app->docoRest->master;
    }

    public function actionRequest($id = null)
    {
        $title = $this->title . ' Obat';
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $instalasi_gdf = $this->instalasiGdf;
        $ruangan_gdf = $this->ruanganGdf;
               
        try {
            $model = new TransaksiPemesananProduksiForm;
            Yii::$app->cache->delete('pemesanan-obat-produksi-' . $id_pegawai. $ruangan_id);

            if (!is_null($id)) {
                $getRequestDetail = $this->guzzleExec($this->restApotek, [
                    'url' => 'transaksi-pemesanan-produksi/get-request-detail',
                    'method' => 'GET',
                    'payload' => [
                        'query' => [
                            'id' => DocoHelpers::decrypt($id),
                        ],
                    ],
                ]);

                if (!is_null($getRequestDetail['request_data'])) {
                    $model->attributes = $getRequestDetail['request_data'];
                    $model->pemesananproduksiobat_id = $id;
                }

                if (!empty($getRequestDetail['obatalkes_data'])) {
                    $cacheObatAlkes = [];
                    $satuan_konversi = null;

                    foreach ($getRequestDetail['obatalkes_data'] as $value) {
                        $additionalData = json_decode(ArrayHelper::getValue($value, 'additional_data'), true);
                        $satuan_konversi = ArrayHelper::getValue($additionalData, 'satuan_konversi');

                        $setCache = [
                            'obatalkes_id' => $value['obatalkes_id'],
                            'text' => $value['obatalkes_nama'],
                            'satuankecil_id' => $value['satuankecil_id'],
                            'satuankecil_nama' => $value['satuankecil_nama'],
                            'satuanbesar_id' => $value['satuanunit_id'],
                            'satuanbesar_nama' => $value['satuanunit_nama'],
                            'qty_besar' => $value['qty'],
                            'qty_kecil' => $value['qty_konversi'],
                            'permintaan' => $value['qty_konversi'],
                            'satuan_konversi' => $satuan_konversi,
                        ];
                        $cacheObatAlkes[$value['obatalkes_id']] = $setCache;
                    }
                    $cacheObatAlkes = Yii::$app->cache->set("pemesanan-obat-produksi-" . $id_pegawai . $ruangan_id, $cacheObatAlkes);
                }

            }

            if (!$model->pemesananproduksiobat_id) {
                $model->instalasi_id = $instalasi_gdf;
            }

            $instalasi = Yii::$app->cache->get('instalasi');
            if (!$instalasi) {
                $response = $this->restMaster->get('instalasi/get-all-data?advanced-filter[is_active]=1');
                $body = json_decode($response->getBody(), true);
                $instalasi_data = ArrayHelper::map($body['response']['data'], 'instalasi_id', 'instalasi_nama');
                Yii::$app->cache->set('instalasi', $instalasi_data, 60);
                $instalasi = $instalasi_data;
            }

            $result = $this->restApotek->get('allow/set-cache-konvert-satuan', []);
            $result = json_decode($result->getBody(), true);
            $cacheSatuan = isset($result['response']) ? $result['response'] : [];
            Yii::$app->cache->set('konversi_satuan', $cacheSatuan, DocoConstants::EXPIRED_CACHE);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        }

        return $this->render('request', get_defined_vars());
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $init_ruangan_id = $request->post('depdrop_all_params')['ruangan_id'];
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        try {
            $response = $this->restApotek->get('allow/get-ruangan-by?id=' . $parent_label);
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

            if ($parent_label == $this->instalasiGdf) {
                $result['selected'] = '25';
            }

            if ($init_ruangan_id) {
                $result['selected'] = $init_ruangan_id;
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
            $get = $request->get();

            $keyword = isset($get['q']) ? $get['q'] : '';
            $page = isset($get['page']) ? $get['page'] : 1;

            $result = $this->restApotek->get('allow/list-obat-alkes', [
                'query' => [
                    'term' => $keyword,
                    'page' => $page,
                    'is_produksi' => true,
                ]
            ]);
            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $value) {
                $nama = empty($value['obatalkes_namalain']) ? $value['obatalkes_nama'] : $value['obatalkes_namalain'];
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $nama,
                    'kode' => !empty($value['obatalkes_kode']) ? $value['obatalkes_kode'] : "-",
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuanbesar_nama' => $value['satuanbesar_nama'],
                    'satuan' => $value['satuan'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response,
        ]);
    }

    public function actionSaveCache()
    {
        $request = Yii::$app->request;
        $model = new TransaksiPemesananProduksiForm();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $this->restApotek->get('allow/reset-cache-konvert-satuan');
        $cacheKonv = Yii::$app->cache->get('konversi_satuan');
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $post = $request->post();
        $model->load($post);

        if ($model->validate()) {
            if(!$cacheKonv) {
                $result = $this->restApotek->get('allow/set-cache-konvert-satuan', []);
                $result = json_decode($result->getBody(), true);
                $cacheSatuan = isset($result['response']) ? $result['response'] : [];
                Yii::$app->cache->set('konversi_satuan', $cacheSatuan, DocoConstants::EXPIRED_CACHE);
            }

            $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-produksi-" . $id_pegawai . $ruangan_id);
            $satuan = $model->satuan;
            if (!$cacheObatAlkes) {
                Yii::$app->cache->set("pemesanan-obat-produksi-" . $id_pegawai . $ruangan_id, []);
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

            $satuanBesar = isset($cacheKonv[$model->obat_alkes][$satuan])
                ? $cacheKonv[$model->obat_alkes][$satuan]
                : 1;
            $hasilSatuanBesar = $satuanBesar != 0 ? round($totalPermintaan / $satuanBesar, 2) : $totalPermintaan;
            $setCache = [
                'obatalkes_id' => $request->post('id'),
                'text' => $request->post('text'),
                'satuankecil_id' => $request->post('satuankecil_id'),
                'satuankecil_nama' => $request->post('satuankecil_nama'),
                'satuanbesar_id' => $request->post('satuan_pesan_id'),
                'satuanbesar_nama' => $request->post('satuan_pesan_nama'),
                'qty_besar' => $hasilSatuanBesar,
                'qty_kecil' => $totalPermintaan,
                'permintaan' => $totalPermintaan,
                'satuan_konversi' => isset($cacheKonv[$model->obat_alkes]) ? $cacheKonv[$model->obat_alkes] : null,
            ];
            $cacheObatAlkes[$model->obat_alkes] = $setCache;
            $cacheObatAlkes = Yii::$app->cache->set("pemesanan-obat-produksi-" . $id_pegawai . $ruangan_id, $cacheObatAlkes);

            $response['response'] = [
                'title' => $this->successTitle,
                'text' => 'Data berhasil di tambah'
            ];

            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $formName);
        }
    }

    public function actionGetListItem()
    {
        $request = Yii::$app->request;

        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-produksi-" . $id_pegawai . $ruangan_id);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if ($cacheObatAlkes !== false) {
            $no = $request->get('start', 0);
            foreach ($cacheObatAlkes as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'nama_obat' => $value['text'],
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

    public function actionDeleteCache($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-produksi-" . $id_pegawai . $ruangan_id);
        if ($cacheObatAlkes !== false && isset($cacheObatAlkes[$id])) {
            unset($cacheObatAlkes[$id]);
            Yii::$app->cache->set("pemesanan-obat-produksi-" . $id_pegawai . $ruangan_id, $cacheObatAlkes);
        }
        $response['response'] = [
            'title' => $this->successTitle,
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionSaveRequest()
    {
        $id_pegawai = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $request = Yii::$app->request;
        $post = $request->post();
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-produksi-" . $id_pegawai . $ruangan_id);
        $response['response'] = [
            'text' => 'Obat alkes harus terisi',
            'title' => 'Proses Gagal !'
        ];
        $codeHttp = 422;

        if ($cacheObatAlkes) {
            try {
                $postData = [
                    'pemesanan_id' => DocoHelpers::decrypt($post['pemesanan_id']),
                    'instalasi_tujuan' => $post['instalasi_tujuan'],
                    'ruangan_tujuan' => $post['ruangan_tujuan'],
                    'catatan_bahanbaku' => $post['catatan'],
                    'list_obat' => $cacheObatAlkes
                ];

                $result = $this->restApotek->request('POST', 'transaksi-pemesanan-produksi/save-request', [
                    'form_params' => $postData
                ]);

                $result = json_decode($result->getBody(), true);
                $codeHttp = 200;
                $response['response'] = [
                    'title' => $this->successTitle,
                    'text' => 'Pemesanan produksi obat alkes berhasil disimpan',
                    'nomor' => isset($result['response']['nomor']) ? $result['response']['nomor'] : '',
                ];
                Yii::$app->cache->delete('pemesanan-obat-produksi-' . $id_pegawai. $ruangan_id);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistem';
            }
        }

        return DocoHelpers::response($response, $codeHttp);
    }

}
