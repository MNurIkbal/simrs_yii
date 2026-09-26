<?php

/**
 * @author Randy Vianda Putra
 * @todo Trasaksi Retur
 * @copyright 8 Maret 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\components\ApotekComponent;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\PesanObatAlkes;
use Doco\components\DocoPrint;

class TransaksiPemesananController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoStokObatAlkesView';
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
     * @todo save pemesanan obat alkes
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSaveObatAlkes()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $user_login = Yii::$app->user->identity->pegawai_id;
            $request = Yii::$app->request;
            $post = $request->post();
            $tanggal_kirim = $instalasi_id = $ruangan_id = '';
            if (isset($post['list_obat'])) {
                foreach ($post['list_obat'] as $key => $value) {
                    $tanggal_kirim = $value['tanggal_kirim'];
                    $ruangan_id = $value['ruangan_id'];
                }
                $model = new PesanObatAlkes;
                $model->ruangan_id = $ruangan_id;
                $model->tglpemesanan = $now;
                $model->tglmintadikirim = $now;
                $model->keterangan_pesan = $post['keterangan_pesan'];
                $model->ruanganpemesan_id = $post['ruangan_id_pemesanan'];
                $model->pegawaipemesan_id = $user_login;
                $model->created_by = $user_login;
                $model->status_verifikasi = DocoConstants::OBAT_BELUM_DIVERIFIKASI;

                if ($model->validate() && $model->save()) {
                    $dataInsert = [];
                    $idParent = $model->pesanobatalkes_id;
                    foreach ($post['list_obat'] as $key => $value) {
                        $dataInsert[] = [
                            'satuankecil_id' => $value['satuankecil_id'],
                            'pesanobatalkes_id' => $idParent,
                            'obatalkes_id' => $key,
                            'jumlah_pesan' => $value['qty_kecil'],
                            'satuanbesar_id' => $value['satuanbesar_id'],
                            'jumlah_input' => $value['qty_besar'],
                            'created_by' => $user_login
                        ];
                    }
                    ApotekComponent::insertMultiple('pesanobatdetail_t', $dataInsert);
                    $transaction->commit();
                    $getPemesanan = PesanObatAlkes::findOne($idParent);
                    return ['message' => 'Data Berhasil di simpan', 'id' => $idParent, 'nomor'=>isset($getPemesanan['nopemesanan']) ? $getPemesanan['nopemesanan'] : null];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionCetakPdf
    * @attribute #cetak_pemesanan# => table
    **/
    public function actionCetakPdf()
    {
        $model = new PesanObatAlkes;
        $request = Yii::$app->request;
        $get = $request->get();
        if (isset($get['id'])) {
            $id = $get['id'];
            $pesan_obat = $model->getDataPemesananById($id);
            $pesan_obat_detail = $model->getDetailPemesananById($id);

            $header = [
                'tanggal_pemesanan' => !empty($pesan_obat['tglpemesanan']) ? date('d-M-Y', strtotime($pesan_obat['tglpemesanan'])) : '-',
                'tanggal_minta' => !empty($pesan_obat['tglmintadikirim']) ? date('d-M-Y', strtotime($pesan_obat['tglmintadikirim'])) : '-',
                'no_pemesanan' => !empty($pesan_obat['nopemesanan']) ? $pesan_obat['nopemesanan'] : '-',
                'ruangan_tujuan' => !empty($pesan_obat['ruangan_nama']) ? $pesan_obat['ruangan_nama'] : '-',
                'keterangan_pesan'=>!empty($pesan_obat['keterangan_pesan']) ? $pesan_obat['keterangan_pesan'] : '-',
                'ruangan_pemesan'=>!empty($pesan_obat['ruangan_pemesan']) ? $pesan_obat['ruangan_pemesan'] : '-',
            ];
            $print = new DocoPrint();
            $print->attributes = [
                '#cetak_pemesanan#' => $this->renderPartial('index', [
                    'header'=> $header,
                    'detail' => $pesan_obat_detail,
                ]),
            ];
            $print->Output();
        }
    }
}
