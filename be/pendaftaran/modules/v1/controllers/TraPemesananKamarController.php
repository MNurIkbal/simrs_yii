<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-30 11:13
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\TraPemesananKamar;
use app\modules\v1\models\CetakPemesananKamar;
use app\modules\v1\models\KamarTempatTidur;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;

class TraPemesananKamarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TraPemesananKamar';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new TraPemesananKamar;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter');
        if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $query->andWhere(['between', 'tgl_bookingkamar', $tgl_awal, $tgl_akhir]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    /**
    * @controller actionCetakPemesanan
    * @attribute #no_pemesanan# => no_pemesanan
    * @attribute #tgl_pemesanan# => tgl_pemesanan
    * @attribute #tgl_rawatinap# => tgl_rawatinap
    * @attribute #jenis_kasus_penyakit# => jenis_kasus_penyakit
    * @attribute #kelas_pelayanan# => kelas_pelayanan
    * @attribute #ruangan# => ruangan
    * @attribute #kamar# => kamar
    * @attribute #keterangan# => keterangan
    * @attribute #no_rekam_medik# => no_rekam_medik
    * @attribute #nama_pasien# => nama_pasien
    * @attribute #tanggal_lahir# => tanggal_lahir
    * @attribute #jenis_kelamin# => jenis_kelamin
    * @attribute #no_telepon# => no_telepon
    * @attribute #no_mobile# => no_mobile
    * @attribute #pemesan# => pemesan
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #cetak_oleh# => cetak_oleh
    * @attribute #tanggal# => tanggal
    * @attribute #bed# => bed
    * @attribute #tanggal_cetak# => tanggal_cetak
    **/
    public function actionCetakPemesanan()
    {
        try{
            $model = new CetakPemesananKamar;

            $request = Yii::$app->request;
            $id = $request->get('id');
            if(!$id){
                throw new \yii\base\ErrorException("ID Tidak Ditemukan", 500);
            }

            $data = $model::find()->where(['bookingkamar_id'=>$id])->one();
            if(!$data){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $additional = json_decode($data['additional_data'], true);
            $print = new DocoPrint();
            $print->attributes = [
                '#no_pemesanan#' => isset($data['nomor_pemesanan']) ? $data['nomor_pemesanan'] : '',
                '#tgl_pemesanan#' => isset($data['tanggal_pemesanan']) ? $data['tanggal_pemesanan'] : '',
                '#tgl_rawatinap#' => isset($data['tanggal_rawat_inap']) ? $data['tanggal_rawat_inap'] : '',
                '#jenis_kasus_penyakit#' => isset($data['jenis_kasus_penyakit']) ? $data['jenis_kasus_penyakit'] : '',
                '#kelas_pelayanan#'=> isset($data['kelas_pelayanan']) ? $data['kelas_pelayanan'] : '',
                '#ruangan#'=> isset($data['ruangan']) ? $data['ruangan'] : '',
                '#kamar#' => isset($data['kamar']) ? $data['kamar'] : '',
                '#keterangan#' => isset($data['keterangan_pendaftaran']) ? $data['keterangan_pendaftaran'] : '',
                '#no_rekam_medik#' => isset($additional['no_rekam_medik']) ? $additional['no_rekam_medik'] : '',
                '#nama_pasien#' => isset($additional['nama_pasien']) ? $additional['nama_pasien'] : '',
                '#tanggal_lahir#' => isset($additional['tanggal_lahir']) ? $additional['tanggal_lahir'] : '',
                '#jenis_kelamin#' => isset($data->lookupJenisKelamin) ? $data->lookupJenisKelamin : '',
                '#no_telepon#' => isset($additional['no_telepon_pasien']) ? $additional['no_telepon_pasien'] : '',
                '#no_mobile#' => isset($additional['no_mobile_pasien']) ? $additional['no_mobile_pasien'] : '',
                '#pemesan#' => isset($data['pemesan']) ? $data['pemesan'] : '',
                '#nama_pegawai#' => isset($data['petugas_pendaftaran']) ? $data['petugas_pendaftaran'] : '',
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#tanggal#' => date('d F Y'),
                '#tanggal_cetak#' => date('d F Y H:i:s'),
                '#bed#'=>isset($data['bed']) ? $data['bed'] : '',
            ];
            $print->Output();
        }catch (Exception $e){
            // asd
        }
    }

    public function actionSetujui($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = TraPemesananKamar::findOne($id);
            if ($model && !empty($model)) {
                $model->status_booking = DocoConstants::VAR_STJ;
                if ($model->save()) {
                    $kamartempattidur_id = $post['kamartempattidur_id'];
                    if (!empty($post['jeniskelamin'])) {
                        if ($post['jeniskelamin'] == DocoConstants::VAR_LK) {
                            $kettempattidur_id = DocoConstants::VAR_KTTIL;
                        } elseif ($post['jeniskelamin'] == DocoConstants::VAR_PR) {
                            $kettempattidur_id = DocoConstants::VAR_KTTIP;
                        }
                    }
                    $kamar = KamarTempatTidur::findOne($kamartempattidur_id);
                    $kamar->status_isi = true;
                    $kamar->kettempattidur_id = $kettempattidur_id;
                    $kamar->save();
                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'TraPemesananKamar');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
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

    public function actionDitolak($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = TraPemesananKamar::findOne($id);
            if ($model && !empty($model)) {
                $model->status_booking = ($post['is_batal'] != 1) ? DocoConstants::VAR_DTLK : DocoConstants::VAR_BTL;
                if ($model->save()) {
                    $kamartempattidur_id = $post['kamartempattidur_id'];
                    $kamarruangan_id = $post['kamarruangan_id'];
                    if (!empty($post['jeniskelamin'])) {
                        if ($post['jeniskelamin'] == DocoConstants::VAR_LK) {
                            // status kamar laki2
                            $status_terisi = DocoConstants::VAR_KTTIL;
                            $status_kosong = DocoConstants::VAR_KTTKL;
                        } elseif ($post['jeniskelamin'] == DocoConstants::VAR_PR) {
                            // status kamar perempuan
                            $status_terisi = DocoConstants::VAR_KTTIP;
                            $status_kosong = DocoConstants::VAR_KTTKP;
                        }

                        if (($post['kamarruangan_jenis'] == DocoConstants::VAR_JKF) OR ($post['kamarruangan_jenis'] == DocoConstants::VAR_JKC)) {
                            // $sql = "SELECT count(*) as terisi FROM kamartempattidur_m 
                            //     WHERE kamarruangan_id = {$kamarruangan_id} 
                            //     AND kettempattidur_id = {$status_terisi}
                            //     AND status_isi = true
                            // ";
                            // $checkRoom = $connection->createCommand($sql)->queryOne();
                            // if ($checkRoom['terisi'] > 0) {
                            //     $kettempattidur_id = $status_kosong; 
                            // } else {
                                $kettempattidur_id = DocoConstants::VAR_KTTK;
                            // }
                        } else {
                            $kettempattidur_id = $status_kosong;
                        }
                    }
                    $kamar = KamarTempatTidur::findOne($kamartempattidur_id);
                    $kamar->status_isi = false;
                    $kamar->kettempattidur_id = $kettempattidur_id;
                    $kamar->save();
                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'TraPemesananKamar');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
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
}