<?php

/**
 * @author Randy Vianda Putra
 * @todo Trasaksi Pemesanan
 * @copyright 23 Maret 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\InfoStokBarang;
use app\modules\v1\models\PesanBarang;
use app\modules\v1\models\PesanBarangDetail;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoPrint;

class TransaksiPemesananController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoStokBarang';
    public $konfig_farmasi;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function init()
    {
        // $this->konfig_farmasi = DocoConstants::konfigFarmasi();
        parent::init();
    }

    /**
     * @todo get all data with ajax
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionAjax()
    {
        // 
    }

    /**
     * @todo save pemesanan barang
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSaveBarang()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $user_login = Yii::$app->user->identity->pegawai_id;
            $request = Yii::$app->request;
            $post = $request->post();
            $tanggal_kirim = $instalasi_id = $ruangan_id = '';
            if (isset($post['list_barang'])) {
                foreach ($post['list_barang'] as $key => $value) {
                    $tanggal_kirim = $value['tanggal_kirim'];
                    $ruangan_id = $value['ruangan_id'];
                }
                $model = new PesanBarang;
                $model->ruangantujuan_id = $request->post('ruangan_id');
                $model->tgl_pesanbarang = $now;
                $model->tgl_mintadikirim = $tanggal_kirim;
                $model->keterangan_pesan = $post['keterangan_pesan'];
                $model->ruanganpemesan_id = $post['ruangan_id_pemesanan'];
                $model->pegawaipemesan_id = $user_login;
                $model->created_by = $user_login;
                if ($model->validate() && $model->save()) {
                    $dataInsert = [];
                    $idParent = $model->pesanbarang_id;
                    foreach ($post['list_barang'] as $key => $value) {
                        $dataInsert[] = [
                            'satuankecil_id' => $value['satuankecil_id'],
                            'pesanbarang_id' => $idParent,
                            'barang_id' => $key,
                            'qty_pesan' => $value['qty_kecil'],
                            'satuanbesar_id' => $value['satuanbesar_id'],
                            'jumlah_input' => $value['qty_besar'],
                            'created_by' => $user_login,
                            'is_active' => true
                        ];
                    }
                    PesanBarangDetail::batchInsert($dataInsert);
                    $transaction->commit();
                    $dataPesan = PesanBarang::find()->where([
                        'pesanbarang_id' => $idParent
                    ])->one();

                    return [
                        'message' => 'Data Berhasil di simpan', 
                        'id' => $idParent,
                        'nomor' => $dataPesan->no_pemesanan
                    ];
                }
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            Yii::error($e->getMessage());
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakPdf 
    * @attribute #nama_ruangan# => Menampilkan Nama Ruangan 
    * @attribute #tanggal_pemesanan# => Menampilkan Tanggal Pemesanan
    * @attribute #tanggal_minta_dikirim# => Menampilkan Tanggal Minta dikirim
    * @attribute #ruangan_tujuan# => Menampilkan Ruangan Tujuan
    * @attribute #instalasi_tujuan# => Menampilkan Instalasi Tujuan
    * @attribute #catatan# => Menampilkan Catatan
    * @attribute #tabel_detail# => Menampilkan Tabel Detail
    **/
    public function actionCetakPdf()
    {
        $model = new PesanBarang;
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        if (isset($get['id'])) {
            $id = $get['id'];
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();

            $pesan_barang = $model->getDataPemesananById($id);
            $pesan_barang_detail = $model->getDetailPemesananById($id);
            
            $print = new DocoPrint();
            $print->attributes = [
                '#nama_ruangan#' => !empty($ruangan->ruangan_nama) ? $ruangan->ruangan_nama : null,
                '#tanggal_pemesanan#' => !empty($pesan_barang['tgl_pesanbarang']) 
                            ? date('d F Y', strtotime($pesan_barang['tgl_pesanbarang'])) : '-',
                '#tanggal_minta_dikirim#' => !empty($pesan_barang['tgl_mintadikirim']) 
                            ? date('d F Y', strtotime($pesan_barang['tgl_mintadikirim'])) : '-',
                '#nomer_pemesanan#' => !empty($pesan_barang['no_pemesanan']) ? $pesan_barang['no_pemesanan'] : '-',
                '#ruangan_tujuan#' => !empty($pesan_barang['ruangan_nama']) ? $pesan_barang['ruangan_nama'] : '-',
                '#instalasi_tujuan#' => !empty($pesan_barang['instalasi_nama']) ? $pesan_barang['instalasi_nama'] : '-',
                '#catatan#' => !empty($pesan_barang['keterangan_pesan']) ? $pesan_barang['keterangan_pesan'] : '-',
                '#tabel_detail#' => $this->renderPartial('index', [
                    'detail' => $pesan_barang_detail,
                ]),            
            ];
            $print->Output();
        }
    }
}
