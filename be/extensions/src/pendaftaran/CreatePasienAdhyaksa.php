<?php

/**
 * 
 * @author : Fajar (fajar.supriadi@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\pendaftaran;

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
use app\modules\v1\models\PasienV;
use app\modules\v1\models\SyPendaftaranView;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\SyPenanggungJawabView;
use Doco\Services\Vendors\PendaftaranService;

class CreatePasienAdhyaksa extends \Doco\components\DocoBaseProcessExtension
{
    protected $_restPendaftaran;

    public function init()
    {
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

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
        $request = Yii::$app->request;
        $pasien_view = PasienV::find()->where([
            'no_rekam_medik' => $request->get('no_rekam_medik')
        ]);
        $count_pasien = $pasien_view->count();
        $pasien_v = $pasien_view->one();
        try {
            if ($count_pasien > 0) {
                # code...
                $model = Pasien::find()
                        ->where([ 'no_rekam_medik' => $request->get('no_rekam_medik'), 'is_deleted' => false, 'is_active' => true ])
                        ->one();
            } else {
                # code...
                $model = new Pasien;
                if (!empty($request->get('no_rekam_medik'))) {
                    # code...
                    $model->no_rekam_medik = $request->get('no_rekam_medik');
                } 
            }
            
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