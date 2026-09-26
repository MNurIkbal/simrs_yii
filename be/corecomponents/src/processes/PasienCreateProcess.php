<?php

namespace Doco\processes;

use Yii;

use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PenanggungJawab;
use app\modules\v1\models\KeluargaPasien;
use app\modules\v1\models\SyncPasien;
use app\modules\v1\models\SyncPasienView;
use app\modules\v1\models\PasienUbahData;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungJawabView;
use Doco\Services\Vendors\PendaftaranService;

class PasienCreateProcess extends \Doco\components\DocoBaseProcessExtension 
{
    public function actionView($id) 
    {
        $model = $kunjungan = $pj = $keluarga_pasien = [];
        try {
            $model = Pasien::find()
            ->where(['pasien_id'=>$id])
            ->one();

            if (!empty($model)) {
                $kunjungan = Pendaftaran::find()
                ->where(['pasien_id' => $id])
                ->orderBy(['tgl_pendaftaran' => SORT_DESC])
                ->one();

                if(!empty($kunjungan) && !is_null($kunjungan->penanggungjawab_id)) {
                    $pj = PenanggungJawab::find()
                    ->where(['penanggungjawab_id' => $kunjungan->penanggungjawab_id])
                    ->one();
                } 

                $keluarga_pasien = KeluargaPasien::find()
                ->where(['pasien_id' => $id])
                ->one();

                return [
                    'pasien' => $model,
                    'kunjungan' => $kunjungan,
                    'pj' => $pj,
                    'keluarga_pasien' => $keluarga_pasien
                ];
            }
            throw new \Exception("Data Tidak Di Temukan");
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
    
    protected function processFlow()
    {
        #code...
        try {
            $request = Yii::$app->request;
            $model = new Pasien;
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                $model->tanggal_lahir = date('Y-m-d',strtotime($model->tanggal_lahir));
                $model->tgl_rekam_medik = date('Y-m-d');
                $model->is_aps = ($model->is_aps == 1) ? true : false;
                $golonganumurpasien = $model->getGolonganUmurPasien();
                $model->golonganumur_id = $golonganumurpasien->golonganumur_id;
                if ($model->save()) {
                    $responseMessage = [
                        'message' => Yii::t('app', 'Data Berhasil di simpan'),
                        'data_pasien' => $this->actionView($model->pasien_id)
                    ];
                    return $responseMessage;
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PasienForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            // throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            // var_dump($e);exit;
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // var_dump($e);exit;
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}