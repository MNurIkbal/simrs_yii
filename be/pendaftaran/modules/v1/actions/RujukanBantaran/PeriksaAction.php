<?php

namespace app\modules\v1\actions\RujukanBantaran;

use Yii;
use yii\base\Action;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\LoginPemakai;
use app\modules\v1\models\RujukanBantaran;

class PeriksaAction extends Action
{
    public function run()
    {
        $rujukanBantaranId = Yii::$app->request->post('bantaran_id');

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $rujukanBantaran = RujukanBantaran::find()
                ->andWhere(['rujukanbantaran_id' => $rujukanBantaranId, 'is_deleted' => false])
                ->asArray()
                ->one();

            if($rujukanBantaran['status_pelayanan_bantaran'] != DocoConstants::MENUNGGU_DIDAFTARKAN_BANTARAN) {
                return true;
            }

            if($rujukanBantaran['pasien_lama']) {
                $statusPasien = DocoConstants::VAR_PAS_L;
                $kunjungan = DocoConstants::VAR_K_L;
            } else {
                $statusPasien = DocoConstants::VAR_PAS_B;
                $kunjungan = DocoConstants::VAR_K_B;
            }

            $model = new Pendaftaran();
            $model->tgl_pendaftaran = date('Y-m-d H:i:s');
            $model->penjamin_id = 1; // make this to default value
            $model->instalasi_id = 1; // make this to default value
            $model->jeniskasuspenyakit_id = 23; // make this to default value
            $model->kelaspelayanan_id = 1; // make this to default value
            $model->golonganumur_id = 8; // make this to default value
            $model->status_periksa = 1; // make this to default value
            $model->status_pasien = $statusPasien;
            $model->kunjungan = $kunjungan;
            $model->status_masuk = DocoConstants::VAR_SM_R; // make this to default value
            $model->pasien_id = $rujukanBantaran['pasien_id'];
            $model->created_date = date('Y-m-d H:i:s');

            if(!$model->save(false)) {
                throw new \Exception('Gagal menyimpan data pendaftaran baru: ' . json_encode($model->getErrors()));
            }

            $rujukanBantaranUpdate = RujukanBantaran::updateAll([
                'pendaftaran_id' => $model->pendaftaran_id,
                'last_modified_date' => date('Y-m-d H:i:s'),
                'status_pelayanan_bantaran' => DocoConstants::DALAM_PELAYANAN_BANTARAN
            ], ['rujukanbantaran_id' => $rujukanBantaranId]);

            $transaction->commit();

            return [
                'pendaftaran_id' => $model->pendaftaran_id
            ];
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error('PeriksaAction - Exception: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Gagal menyimpan data pendaftaran: ' . $e->getMessage()];
        }
    }
}