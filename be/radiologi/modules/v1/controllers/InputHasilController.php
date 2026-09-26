<?php

/**
 * @author Randy Vianda Putra
 * @todo Input Hasil Radiologi
 * @copyright 27 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model
use app\modules\v1\models\InfoPasienRadView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\HasilPemeriksaanRad;
use app\modules\v1\models\HasilPemeriksaanRadView;
use app\modules\v1\models\HasilPemeriksaanRadDetail;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PemeriksaanPasienRadiologiView;
use app\modules\v1\models\HasilBridgingRad;
use Doco\models\ProfilRsView;
use yii\helpers\ArrayHelper;

class InputHasilController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienRadView';

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

    private function getPasienRad($id = null, $type = null, $tindakan_id = null)
    {
        $model = PemeriksaanPasienRadiologiView::find();
        if ($type == 'printRadiologi') {
            $model->select([
                'nama_pasien',
                'no_telepon_pasien',
                'umur',
                'j_kelamin',
                'no_masukpenunjang',
                'no_rekam_medik',
                'dokter_perujuk_id',
                'dokter_perujuk_nama',
                'ruangan_nama',
                'tglmasukpenunjang',
                'penjamin_nama',
                'dokter_penunjang',
            ]);
        }
        if ($tindakan_id) {
            $model->andWhere(['daftartindakan_id' => $tindakan_id]);
        }
        if ($id) {
            return $model->andWhere(['pasienmasukpenunjang_id' => $id])->asArray()->one();
        }
        return $model->asArray()->all();
    }

    private function getPegawai($ruangan_id)
    {
        $model = PegawaiView::find()->where(['is_active' => true]);
        if ($ruangan_id) {
            $model->andWhere(['ruangan_id' => $ruangan_id]);
            $data = $model->asArray()->all();
        } else {
            $data = $model->asArray()->all();
        }

        return $data;
    }

    private function getHasilPemeriksaan($id, $tindakan_id, $daftartindakan_id)
    {
        $model = HasilPemeriksaanRadView::find();
        if ($id) {
            $model->where([
                'pasienmasukpenunjang_id' => $id,
                'tindakanpelayanan_id' => $tindakan_id,
                'daftartindakan_id' => $daftartindakan_id
            ]);
            $data = $model->asArray()->one();
        }

        return $data;
    }

    public function actionGenerateApi($id, $ruangan_id, $tindakan_id, $daftartindakan_id)
    {
        $data_pasien = $this->getPasienRad($id);
        // $header_gol_umur = $this->getNilaiPemeriksaan($id);
        // $detail_gol_umur = $this->getDetailNilaiRujukan($id, $sample_id);
        $data_pegawai = $this->getPegawai($ruangan_id);
        $data_hasil = $this->getHasilPemeriksaan($id, $tindakan_id, $daftartindakan_id);
        $data_hasil_rad = $this->getHasilRad($data_hasil['hasilpemeriksaanrad_id']);

        $result = [
            'data-pasien' => $data_pasien,
            'data-hasil-rad' => $data_hasil_rad,
            // 'header-gol-umur' => $header_gol_umur,
            // 'detail-gol-umur' => $detail_gol_umur,
            'data-pegawai' => $data_pegawai,
            'data-hasil' => $data_hasil,
        ];

        return $result;
    }

    public function actionAmbilFoto()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            if (!empty($post['hasilpemeriksaanrad_id'])) {
                $model = HasilPemeriksaanRad::findOne($post['hasilpemeriksaanrad_id']);
            } else {
                $model = new HasilPemeriksaanRad;
                $getHasil = HasilPemeriksaanRad::find()->where([
                    'pasienmasukpenunjang_id' => isset($post['pasienmasukpenunjang_id']) ? $post['pasienmasukpenunjang_id'] : null,
                    'tindakanpelayanan_id' => isset($post['tindakanpelayanan_id']) ? $post['tindakanpelayanan_id'] : null,
                    'daftartindakan_id' => isset($post['daftartindakan_id']) ? $post['daftartindakan_id'] : null
                ])->one();

                if (!empty($getHasil)) {
                    $model = $getHasil;
                }
            }
            if ($request->post()) {
                $model->attributes = $post;
                $model->pemeriksaanrad_id = isset($post['pemeriksaanradiologi_id']) ? $post['pemeriksaanradiologi_id'] : null;
                $model->penanggungjawab_id = isset($post['pegawai_id']) ? $post['pegawai_id'] : null;
                $model->tgl_ambilfoto = date('Y-m-d H:i:s');
                $model->is_ambilfoto = true;
                $model->daftartindakan_id = isset($post['daftartindakan_id']) ? $post['daftartindakan_id'] : null;
                // if hasilbridging is exist, set tgl_ambilfoto to observation_time
                $pasienmasukpenunjang_id = isset($post['pasienmasukpenunjang_id']) ? $post['pasienmasukpenunjang_id'] : null;
                $modelPenunjang = $pasienmasukpenunjang_id ? PasienMasukPenunjangT::findOne($pasienmasukpenunjang_id) : null;
                if ($modelPenunjang) {
                    $tindakanpelayanan_id = isset($post['tindakanpelayanan_id']) ? $post['tindakanpelayanan_id'] : null;
                    $hasilBridging = HasilBridgingRad::find()->where([
                        'order_no' => $modelPenunjang['no_masukpenunjang'] . '-' . $tindakanpelayanan_id
                    ])->one();

                    if($hasilBridging) {
                        //TODO: need confirm
                        $model->tgl_ambilfoto = $hasilBridging->observation_time; // override
                        $model->no_hasilrad = $hasilBridging->order_no;
                        // $model->tgl_uploadhasil = $hasilBridging->observation_time;
                        // $model->tgl_hasilrad = $hasilBridging->observation_time;
                    }   
                }
                // end

                if ($model->save()) {
                    if ($modelPenunjang) {
                        $modelPenunjang->status_periksa = DocoConstants::ST_PERIKSA;
                        $modelPenunjang->save();   
                    }
                    $transaction->commit();

                    return ['message' => 'Data Berhasil di simpan', 'hasilpemeriksaanrad_id' => DocoHelpers::encrypt($model->hasilpemeriksaanrad_id)];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'HasilPemeriksaanRad');
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

    public function actionHasilScan()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            if (!empty($post[0]['hasilpemeriksaanrad_id'])) {
                $hasilpemeriksaanrad_id = $post[0]['hasilpemeriksaanrad_id'];
                $model = HasilPemeriksaanRad::findOne($hasilpemeriksaanrad_id);
            } else {
                $model = new HasilPemeriksaanRad;
            }
            $model->tgl_uploadhasil = date('Y-m-d H:i:s');

            if ($model->save()) {
                HasilPemeriksaanRadDetail::batchInsert($post);
                $transaction->commit();

                return ['message' => 'Data Berhasil di simpan'];
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'HasilPemeriksaanRad');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
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

    private function getHasilRad($id)
    {
        $model = HasilPemeriksaanRadDetail::find()->where([
            'hasilpemeriksaanrad_id' => $id
        ]);
        $data = $model->all();

        return $data;
    }

    public function actionDeleteUpload($id)
    {
        try {
            $data = HasilPemeriksaanRadDetail::find()->where([
                'hasilpemeriksaanraddetail_id' => $id
            ])->one();

            $result = (new HasilPemeriksaanRadDetail)->delete($id);

            return [
                'data' => !empty($data->upload_file) ? $data->upload_file : null
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdateCatatan($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = HasilPemeriksaanRadDetail::findOne($id);
            $model->catatan = $post['catatan'];
            $model->save();

            return ['message' => 'Data berhasil di update'];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionCetakHasilPdf
     * @attribute #tanggal_lahir# => tanggal lahir
     **/
    public function actionCetakHasilPdf()
    {
        return Yii::$app->docoPlugin->execute('cetak_hasil');
    }

    public function actionBatal()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $hasilPemeriksaanId = ArrayHelper::getValue($post, 'hasilpemeriksaanrad_id');
            $penunjangId = ArrayHelper::getValue($post, 'pasienmasukpenunjang_id');
            $pegawaiId = ArrayHelper::getValue($post, 'pegawai_id');
            if ($request->isPost) {
                if (!empty($hasilPemeriksaanId) && !empty($penunjangId) && !empty($pegawaiId)) {
                    $date = date('Y-m-d H:i:s', time());
                    Yii::$app->db->createCommand("
                        UPDATE hasilpemeriksaanrad_t SET is_deleted = true, deleted_by = {$pegawaiId}, deleted_date = '{$date}'
                        WHERE hasilpemeriksaanrad_id = {$hasilPemeriksaanId}
                    ")->queryAll();

                    Yii::$app->db->createCommand("
                        UPDATE hasilpemeriksaanraddetail_t SET is_deleted = true, deleted_by = {$pegawaiId}, deleted_date = '{$date}'
                        WHERE hasilpemeriksaanrad_id = {$hasilPemeriksaanId}
                    ")->queryAll();

                    /** check apakah masih ada tindakan yang mempunyai hasil, untuk merubah status periksa header*/
                    $model = HasilPemeriksaanRad::find();
                    $model->where([
                        'pasienmasukpenunjang_id' => $penunjangId,
                    ]);
                    $data = $model->asArray()->all();
                    $count = count($data);
                    if ($count == 0) {
                        $model = PasienMasukPenunjangT::findOne($penunjangId);
                        $model->status_periksa = DocoConstants::BLM_PERIKSA;
                        $model->save();
                    }
                    $transaction->commit();
                    return ['message' => 'Data Berhasil dibatalkan.'];
                } else {
                    return [
                        'status' => 422,
                        'message' => 'Pembatalan tidak dapat diproses, parameter yang dikirimkan salah!'
                    ];
                }
            } else {
                return [
                    'status' => 422,
                    'message' => 'Tipe Pengiriman data harus Post'
                ];
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
