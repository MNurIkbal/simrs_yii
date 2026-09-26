<?php 

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Pemesanan
 * @copyright 23 Maret 2018 aweutist
 */

namespace Doco\gudang\controllers;

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
use Doco\gudang\models\TransaksiPemesananForm;
use yii\helpers\ArrayHelper;

class TransaksiPemesananController extends DocoController
{

    protected $_title = "Pemesanan";
    protected $_module = '/gudang/transaksi-pemesanan';
    protected $_restMaster;
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }


    public function actionBarang()
    {
        $title = $this->_title . ' Barang';
        $model = new TransaksiPemesananForm;
        try {
            $instalasi = Yii::$app->cache->get('instalasi');
            if ($instalasi === false) {
                $response = $this->_restMaster->get('instalasi/get-all-data?advanced-filter[is_active]=1');
                $body = json_decode($response->getBody(), true);
                $instalasi_data = ArrayHelper::map($body['response']['data'], 'instalasi_id', 'instalasi_nama');
                Yii::$app->cache->set('instalasi', $instalasi_data, 60);
                $instalasi = $instalasi_data;
            }
            $result = $this->_restGudang->get('allow/set-cache-konvert-satuan', []);
            $result = json_decode($result->getBody(), true);
            $cacheSatuan = isset($result['response']) ? $result['response'] : [];
            Yii::$app->cache->set('konvert-satuan', $cacheSatuan, 3600);
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $cacheSatuan = [];
        }

        $cache = json_encode($cacheSatuan);
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");

        return $this->render('barang', get_defined_vars());
    }

    public function actionGetListItem()
    {
        $request = Yii::$app->request;

        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $cacheBarang = Yii::$app->cache->get("pemesanan-barang-" . $id_pegawai);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $konvSatuan = Yii::$app->cache->get('konvert-satuan');
        if ($cacheBarang !== false) {
            $no = $request->get('start', 0);
            foreach ($cacheBarang as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'nama_barang' => $value['text'],
                    'qty_besar' => DocoHelpers::formatNumber($value['qty_besar']) . ' ' . $value['satuanbesar_nama'],
                    'qty_kecil' => DocoHelpers::formatNumber($value['qty_kecil']) . ' ' . $value['satuankecil_nama'],
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
        $result['selected'] = DocoConstants::R_GuDANG_BARANG;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        try {
            $response = $this->_restGudang->get('allow/get-ruangan?instalasi_id=' . $parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value) {
                if ($ruangan_id != $value['ruangan_id']) {
                    $result['output'][] = [
                        'id' => $value['ruangan_id'],
                        'name' => $value['ruangan_nama']
                    ];
                }
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

    public function actionSearchBarang()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restGudang->get('allow/get-barang', [
                'query' => [
                    'ruangan_id' => $request->get('ruangan_id'),
                    'term' => $request->get('term'),
                    'is_active' => true,
                ]
            ]);
            $result = json_decode($result->getBody(), true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['barang_id'],
                    'text' => $value['barang_kode'] . ' - ' . $value['barang_nama'],
                    'stok' => 0,
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuan_kecil'],
                    'satuanbesar_id' => null,
                    'satuanbesar_nama' => null,
                    'instalasi_id' => null,
                    'ruangan_id' => null,
                    'satuan' => $value['satuan']
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

    public function actionSaveCache()
    {
        $request = Yii::$app->request;
        $model = new TransaksiPemesananForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $cacheKonv = Yii::$app->cache->get('konvert-satuan');
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $model->load($request->post());
        $model->instalasi_tujuan = true;
        $model->ruangan_tujuan = true;
        if ($model->validate()) {
            $cacheBarang = Yii::$app->cache->get("pemesanan-barang-" . $id_pegawai);
            $satuanKecilId = $request->post('satuankecil_id');
            $listKonversi = $request->post('satuan','{}');
            $dataKonversi = json_decode($listKonversi,true);

            $satuan = $model->satuan;
            $qty = $model->qty;
            $satuanBesarNama = $request->post('satuankecil_nama');

            foreach ($dataKonversi as $value) {
                if ($value['satuanbesar_id'] == $satuan) {
                    $totalPermintaan = $qty * $value['nilai_konversi'];
                    $satuanBesarNama = $value['satuanunit_nama'];
                }
            }

            if ($cacheBarang == false) {
                Yii::$app->cache->set("pemesanan-barang-" . $id_pegawai, []);
                $cacheBarang = [];
            }

            if (!isset($cacheBarang[$model->barang])) {
                $cacheBarang[$model->barang] = [];
            }

            $setCache = [
                'barang_id' => $request->post('id'),
                'text' => $request->post('text'),
                'satuankecil_id' => $satuanKecilId,
                'satuankecil_nama' => $request->post('satuankecil_nama'),
                'satuanbesar_id' => $satuan,
                'satuanbesar_nama' => $satuanBesarNama,
                'instalasi_id' => $model->instalasi_tujuan,
                'ruangan_id' => $model->ruangan_tujuan,
                'qty_besar' => $qty,
                'qty_kecil' => $totalPermintaan,
                'tanggal_kirim' => $model->tanggal_kirim
            ];

            $cacheBarang[$model->barang] = $setCache;

            $cacheBarang = Yii::$app->cache->set("pemesanan-barang-" . $id_pegawai, $cacheBarang);

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
        $cacheBarang = Yii::$app->cache->get("pemesanan-barang-" . $id_pegawai);
        if ($cacheBarang !== false) {
            if (isset($cacheBarang[$id])) {
                unset($cacheBarang[$id]);
                Yii::$app->cache->set("pemesanan-barang-" . $id_pegawai, $cacheBarang);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    /**
     * @todo save pemesanan barang
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSave()
    {
        $id_pegawai = Yii::$app->docoVars->user('id_pegawai');
        $request = Yii::$app->request;
        $post = $request->post();
        $cacheBarang = Yii::$app->cache->get("pemesanan-barang-" . $id_pegawai);
        $response['response'] = [
            'text' => 'Barang harus terisi',
            'title' => 'Proses Gagal !'
        ];
        $codeHttp = 422;
        $model = new TransaksiPemesananForm;
        $model->instalasi_tujuan = $request->post('instalasi_id');
        $model->ruangan_tujuan = $request->post('ruangan_id');
        $model->tanggal_kirim = true;
        $model->barang = true;
        $model->satuan = true;
        $model->qty = 1;

        if (!$model->validate()) return DocoHelpers::response($model->errors,422,'TransaksiPemesananForm');

        if ($cacheBarang) {
            try {
                $postData = [
                    'tanggal_kirim' => $post['tanggal_kirim'],
                    'keterangan_pesan' => $post['keterangan'],
                    'ruangan_id_pemesanan' => Yii::$app->docoVars->workspace('ruangan_id'),
                    'list_barang' => $cacheBarang,
                    'ruangan_id' => $request->post('ruangan_id')
                ];
                $result = $this->_restGudang->request('POST', 'transaksi-pemesanan/save-barang', [
                    'form_params' => $postData
                    ]);
                $result = json_decode($result->getBody(), true);
                $response['response'] = $result;
                $codeHttp = 200;
                $response['response'] = [
                    'text' => 'Barang berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'id' => isset($result['response']['id']) ? DocoHelpers::encrypt($result['response']['id']) : '',
                    'nomor' => isset($result['response']['nomor']) ? $result['response']['nomor'] : '',
                ];
                if ($result['metadata']['status'] == 200) {
                    Yii::$app->cache->delete('pemesanan-barang-' . $id_pegawai);
                }
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistem';
                $response['response']['error'] = $e->getMessage();
            }
        } 
        return DocoHelpers::response($response, $codeHttp);
    }

    public function actionCetakPdf($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/transaksi-pemesanan.pdf";
        try {
            if(Yii::$app->report->enabled) {
                $urlReport = 'permintaan_barang';
                $query = [
                    'id' => $id
                ];
                return Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $query,
                    'manualRender'=>function() use($query,$path){
                        $response = $this->_restGudang->get('transaksi-pemesanan/cetak-pdf',[
                            'query' => $query,
                            'save_to' => $path
                        ]);
    
                        return DocoHelpers::previewPdf($path);
                    }
                ]);
            }
            $response = $this->_restGudang->get('transaksi-pemesanan/cetak-pdf?id=' . $id,[
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

}