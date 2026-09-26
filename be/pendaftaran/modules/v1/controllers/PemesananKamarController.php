<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\KamarRuanganView;
use app\modules\v1\models\InfoTarifAkomodasiView;
use app\modules\v1\models\Bookingkamar;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KasuspenyakitruanganV;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\InfPemesananKamar;
use app\modules\v1\payload\PendaftaranForm;
use Doco\components\DocoConstants;


class PemesananKamarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Bookingkamar';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["PUT"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["simpan-pesan-kamar"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    private function getDataPasien()
    {
        $result = PasienV::find();
        return $result;
    }

    public function actionGetPasienByRm()
    {
        $request = Yii::$app->request;
        $no_rekam_medik = $request->post('no_rekam_medik');
        $result = $this->getDataPasien();
        $result->andWhere(['no_rekam_medik'=>$no_rekam_medik]);
        return $result->one();
    }

    public function actionGetBundleData()
    {
        return [
            'list-penyakit' => $this->getListPenyakit(),
            'list-kelas' => $this->getListKelas(),
            'list-jenis-kelamin' => $this->getListLookup('jenis_kelamin'),
            'list-jenis-identitas' => $this->getListLookup('jenis_identitas'),
            'list-pekerjaan' => $this->getListPekerjaan(),
            'list-agama' => $this->getListLookup('agama'),
            'list-nama-depan' => $this->getListLookup('nama_depan'),
        ];
    }

    private function getListPekerjaan()
    {
        $data = Pekerjaan::find()
        ->andWhere(['is_active' => 't','is_deleted'=>'f'])
        ->orderBy('pekerjaan_nama');
        $items = ArrayHelper::map($data->all(), 'pekerjaan_id', 'pekerjaan_nama');

        return $items;
    }

    private function getListPenyakit()
    {
        $data = JenisKasusPenyakit::find()
        ->andWhere(['is_active' => 't','is_deleted'=>'f'])
        ->orderBy('jeniskasuspenyakit_nama');
        $items = ArrayHelper::map($data->all(), 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');

        return $items;
    }

    private function getListKelas()
    {
        $data = KelasPelayanan::find()
        ->andWhere(['is_active' => 't','is_deleted'=>'f'])
        ->orderBy('kelaspelayanan_nama');
        $items = ArrayHelper::map($data->all(), 'kelaspelayanan_id', 'kelaspelayanan_nama');

        return $items;
    }

    private function getListLookup($type)
    {
        $data = Lookup::find()
        ->andWhere(['is_active' => 't','is_deleted'=>'f','lookup_type'=>$type]);
        $items = ArrayHelper::map($data->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

    public function actionGetDataKamar()
    {
        $request = Yii::$app->request;
        $model = new KamarRuanganView;
        $query = $model::find();
        if($request->get('jeniskasuspenyakit_id')){
            $query->andWhere(['jeniskasuspenyakit_id'=>$request->get('jeniskasuspenyakit_id')]);
        }
        if($request->get('kelaspelayanan_id')){
            $query->andWhere(['kelaspelayanan_id'=>$request->get('kelaspelayanan_id')]);
        }
        if($request->get('ruangan_id')){
            $query->andWhere(['ruangan_id'=>$request->get('ruangan_id')]);
        }

        // $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return [
            'data'=>$query->asArray()->all(),
            'list-ruangan' => $query->select(['ruangan_id','ruangan_nama','kamarruangan_nokamar'])->distinct()->orderBy(['ruangan_nama'=>SORT_ASC,'kamarruangan_nokamar'=>SORT_ASC])->all()
        ];
    }

    public function actionGetListRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $selectedData = '';

        $result = KasuspenyakitruanganV::find();
        $result->select(['ruangan_id','ruangan_nama']);
        if (isset($get['jeniskasuspenyakit_id'])) {
            $result->andWhere(['jeniskasuspenyakit_id' => $get['jeniskasuspenyakit_id']]);
        }

        $items = ArrayHelper::map($result->all(), 'ruangan_id', 'ruangan_nama');

        if (isset($get['bookingkamar_id'])) {
            $data_booking = Bookingkamar::findOne($get['bookingkamar_id']);
            if($data_booking->ruangan_id){
                $selectedData = $data_booking->ruangan_id;
            }
        }

        return [
            'list-ruangan' => $items,
            'selectedData' => $selectedData
        ];
    }

    public function actionSimpanPesanKamar()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $postTransaksi =  $post['TraPemesananKamarForm'];
            $modelBooking = new Bookingkamar;
            $modelBooking->attributes = $postTransaksi;
            $modelBooking->tgl_bookingkamar = $postTransaksi['tgl_rawatinap'];
            $modelBooking->status_booking = DocoConstants::VAR_BK;
            $date_expired = date('d-m-Y H:i', strtotime($postTransaksi['tgl_rawatinap'] . "+ 1 days"));
            $modelBooking->tgl_expired = $date_expired;
            $modelBooking->alamat_pasien = $postTransaksi['alamat_pasien'];
            $modelBooking->no_telepon_pasien = $postTransaksi['no_telepon_pasien'];
            $modelBooking->no_identitas_pasien = $postTransaksi['no_identitas_pasien'];
            $modelBooking->nama_pasien = $postTransaksi['nama_pasien'];
            $modelBooking->nama_bin = $postTransaksi['nama_bin'];
            $modelBooking->tempat_lahir = $postTransaksi['tempat_lahir'];
            
            if($modelBooking->validate()) {
                $data_additional = [];
                foreach ($postTransaksi as $key => $value) {
                    if(!array_key_exists($key, $modelBooking->attributes)){
                        $data_additional[$key] = $value;
                    }
                    $data_additional['tgl_expired'] = $date_expired;
                }

                $modelBooking->additional_data = json_encode($data_additional);
                if($modelBooking->save()) {
                    if(!isset($postTransaksi['kamartempattidur_id'])){
                        throw new \yii\base\ErrorException("Kamar Tempat Tidur Tidak Ada", 500);
                    }

                    $modelKamar = KamarTempatTidur::findOne($postTransaksi['kamartempattidur_id']);
                    if(!isset($modelKamar->kamartempattidur_id)){
                        throw new \yii\base\ErrorException("Kamar Tempat Tidur Tidak Ditemukan", 500);
                    }

                    $modelKamar->kettempattidur_id = 6;
                    if(!$modelKamar->update()){
                        $error = "Gagal Update Status Kamar";
                        if ($modelKamar->hasErrors()) {
                            $error = implode(' ', array_map(function ($errors) {
                                return implode(' ', $errors);
                            }, $modelKamar->getErrors()));
                        }
                        throw new \yii\base\ErrorException($error, 500);
                    }

                    $transaction->commit();
                    return [
                        'message'=>'Proses Berhasil!',
                        'text' => 'Kamar berhasil dipesan!',
                        'id'=> DocoHelpers::encrypt($modelBooking->bookingkamar_id),
                    ];
                }
            }
            else {
                return [
                    'status' => 422,
                    'data' => $modelBooking->errors,
                ];
            }
        } catch(\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo
            ];
        } catch(\yii\base\ErrorException $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        } catch(\yii\base\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        }
    }

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if(!isset($post)){
                throw new \yii\base\ErrorException("Data Tidak Ditemukan", 500);
            }
            $modelBooking = Bookingkamar::findOne($id);
            $kamartempattidur_id = $modelBooking->kamartempattidur_id;
            $kamarruangan_id = $modelBooking->kamarruangan_id;
            $modelBooking->attributes = $post;
            $modelBooking->tgl_bookingkamar = $post['tgl_rawatinap'];
            $modelBooking->status_booking = DocoConstants::VAR_BK;

            $data_additional = [];
            foreach ($post as $key => $value) {
                if(!array_key_exists($key, $modelBooking->attributes)){
                    $data_additional[$key] = $value;
                }
            }
            $modelBooking->additional_data = json_encode($data_additional);
            if(!$modelBooking->validate()){
                $error = "Gagal Validasi Form";
                if ($modelBooking->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $modelBooking->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }

            // case ketika pasien baru belum punya pasien_id
            if (!empty($post['pasien_id'])) {
                $modelPasien = Pasien::findOne($post['pasien_id']);
                $modelPasien->attributes = $post;
                // Save pasien
                if (!$modelPasien->update()){
                    $error = Yii::t('app', 'Gagal Ubah Data Pasien');
                    if ($modelPasien->hasErrors()) {
                        $error = implode(' ', array_map(function ($errors) {
                            return implode(' ', $errors);
                        }, $modelPasien->getErrors()));
                    }
                    throw new \yii\base\ErrorException($error, 500);
                }
            }

            if(!$modelBooking->save()){
                $error = "Gagal Simpan Pesan Kamar";
                if ($modelBooking->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $modelBooking->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }
            if(!isset($post['kamartempattidur_id'])){
                throw new \yii\base\ErrorException("Kamar Tempat Tidur Tidak Ada", 500);
            }
            $modelKamar = KamarTempatTidur::findOne($kamartempattidur_id);
            if(!isset($modelKamar->kamartempattidur_id)){
                throw new \yii\base\ErrorException("Kamar Tempat Tidur Tidak Ditemukan", 500);
            }
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
                $sqlKamarRuangan = "SELECT kamarruangan_jenis FROM kamarruangan_m
                    WHERE kamarruangan_id = {$kamarruangan_id}
                ";
                $checkKamarRuangan = $connection->createCommand($sqlKamarRuangan)->queryOne();

                if (($checkKamarRuangan['kamarruangan_jenis'] == DocoConstants::VAR_JKF) OR ($checkKamarRuangan['kamarruangan_jenis'] == DocoConstants::VAR_JKC)) {
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
            $modelKamar->kettempattidur_id = $kettempattidur_id;
            if(!$modelKamar->update()){
                $error = "Gagal Update Status Kamar";
                if ($modelKamar->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $modelKamar->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }

            $modelKamar = KamarTempatTidur::findOne($post['kamartempattidur_id']);
            if(!isset($modelKamar->kamartempattidur_id)){
                throw new \yii\base\ErrorException("Kamar Tempat Tidur Tidak Ditemukan", 500);
            }
            $modelKamar->kettempattidur_id = 6;
            if(!$modelKamar->update()){
                $error = "Gagal Update Status Kamar";
                if ($modelKamar->hasErrors()) {
                    $error = implode(' ', array_map(function ($errors) {
                        return implode(' ', $errors);
                    }, $modelKamar->getErrors()));
                }
                throw new \yii\base\ErrorException($error, 500);
            }

            $transaction->commit();

            return [
                'message'=>'Proses Berhasil!',
                'text' => 'Kamar berhasil dipesan!',
                'id'=> DocoHelpers::encrypt($modelBooking->bookingkamar_id),
            ];
        } catch(\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo
            ];
        } catch(\yii\base\ErrorException $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        } catch(\yii\base\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan internal',
                'errorInfo'=>$e->getName()
            ];
        }
    }

    public function actionCekKamarFleksibel($kamarruangan_id)
    {
        $db = Yii::$app->db;
        $jenis_fleksibel = DocoConstants::VAR_JKF;
        $status_approve = DocoConstants::VAR_STJ;
        $status_dipesan = DocoConstants::VAR_BK;
        $model = new PendaftaranForm;
        $model->kamarruangan_id = $kamarruangan_id;

        if($model->validate()) {
            if($kamarruangan_id) {
                $sql = "SELECT jeniskelamin
                    FROM infopemesanankamar_v
                    WHERE
                        kamarruangan_id = {$model->kamarruangan_id}
                    AND kamarruangan_jenis = {$jenis_fleksibel}
                    AND (
                        statusbooking = '{$status_approve}' OR statusbooking = '{$status_dipesan}'
                    )
                ";

                $data = $db->createCommand($sql)->queryOne();
                return !empty($data) ? $data : false;
            }
        }
        else {
            return [
                'status' => 422,
                'data' => $model->errors,
            ];
        }
    }
}