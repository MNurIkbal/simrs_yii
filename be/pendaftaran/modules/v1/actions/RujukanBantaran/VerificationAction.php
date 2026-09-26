<?php

namespace app\modules\v1\actions\RujukanBantaran;

use Yii;
use yii\base\Action;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\LoginPemakai;
use app\modules\v1\models\RujukanBantaran;



class VerificationAction extends Action
{
    // PROCESS FLOW
    // Check pembantaran has patient data
    // If pembantaran has no patient date, then create patient data
    // Update rujukan bantaran
    
    public function run()
    {
        $data = $this->makeCollectionData();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $dataPembantaran = $this->getPembantaranData($data['bantaran_id']);

            $hasPatientData = $this->checkHasPatienData($dataPembantaran['no_identitas_pasien']);
            $patienId = ArrayHelper::getValue($hasPatientData, 'pasien_id', null);
            $statusPasien = DocoConstants::VAR_PAS_L;

            if(is_null($patienId)) {
                $createPatienData = $this->createPatientData($dataPembantaran);
                $patienId = ArrayHelper::getValue($createPatienData, 'pasien_id', null);
                $statusPasien = DocoConstants::VAR_PAS_B;
            }
            
            $data['pasien_id'] = $patienId;
            $data['pasien_lama'] = $statusPasien == DocoConstants::VAR_PAS_L ? true : false;

            $userVerif = $this->getUserVerification($data['user_id']);
            $data['user_verif'] = ArrayHelper::getValue($userVerif, 'pegawai_id', null);
            
            $updateRujukan = $this->updateRujukanBantaran($data);

            if($updateRujukan) {
                $transaction->commit();

                return [
                    'message' => 'Data Berhasil di simpan',
                    'pengajuan_id' => $dataPembantaran['pengajuan_id'],
                    'nomor_pengajuan' => $dataPembantaran['no_rujukanbantaran'],
                    'status_pengajuan_id' => 4, // Bantaran app approved status
                    'alasan_ditolak' => '',
                    'rencana_tindakan' => $data['rencana_tindakan']
                ];
            } else {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 422;
                return [
                    'message' => 'Data gagal disimpan',
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function makeCollectionData()
    {
        $collectionData = [];
        
        $request = Yii::$app->request;
        $post = $request->post();

        $collectionData['bantaran_id'] = $request->post('bantaran_id');
        $collectionData['instalasi_nama'] = $request->post('instalasi_nama');
        $collectionData['ruangan_nama'] = $request->post('ruangan_nama');
        $collectionData['tgl_kunjungan'] = $request->post('tgl_kunjungan');
        $collectionData['dokter_nama'] = $request->post('dokter_nama', null);
        $collectionData['rencana_tindakan'] = $request->post('rencana_tindakan', null);

        $collectionData['jwt'] = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $collectionData['user_id'] = !empty(Yii::$app->jwt) ? $collectionData['jwt']->loginpemakai_id : null;

        return $collectionData;
    }

    private function getPembantaranData($bantaran_id)
    {
        return RujukanBantaran::find()
                ->andWhere(['rujukanbantaran_id' => $bantaran_id])
                ->asArray()
                ->one();
    }

    private function checkHasPatienData($no_identitas_pasien)
    {
        return (new Pasien())->getPasienByNoIdentitas($no_identitas_pasien);
    }

    private function createPatientData($dataPembantaran)
    {
        $model = new Pasien();
        $model->nama_pasien = ArrayHelper::getValue($dataPembantaran, 'nama_pasien', null);
        $model->jenisidentitas = ArrayHelper::getValue($dataPembantaran, 'jenis_identitas', null);
        $model->no_identitas_pasien = ArrayHelper::getValue($dataPembantaran, 'no_identitas_pasien', null);
        $model->jeniskelamin = ArrayHelper::getValue($dataPembantaran, 'jenis_kelamin', null);
        $model->tanggal_lahir = date('Y-m-d',strtotime(ArrayHelper::getValue($dataPembantaran, 'tgl_lahir', null)));
        $model->tempat_lahir = ArrayHelper::getValue($dataPembantaran, 'tempat_lahir', null);
        
        if(!$model->save(false)) {
            throw new \Exception('Gagal menyimpan data pasien baru: ' . json_encode($model->getErrors()));
        }

        return [
            'pasien_id' => $model->pasien_id,
        ];
    }

    private function getUserVerification($user_id)
    {   
        if(!empty($user_id)) {
            $getLoginPemakai = (new LoginPemakai())->getLoginPemakai($user_id);
        }

        $pegawai_id = ArrayHelper::getValue($getLoginPemakai, 'pegawai_id', null);
        $loginpemakai_id = ArrayHelper::getValue($getLoginPemakai, 'loginpemakai_id', null);

        return [
            'pegawai_id' => $pegawai_id,
            'loginpemakai_id' => $loginpemakai_id
        ];
    }

    private function updateRujukanBantaran($data)
    {
        return RujukanBantaran::updateAll([
                'tgl_kunjungan' => date('Y-m-d', strtotime($data['tgl_kunjungan'])),
                'tgl_verifikasi_bantaran' => date('Y-m-d H:i:s'),
                'instalasi_nama' => $data['instalasi_nama'],
                'ruangan_nama' => $data['ruangan_nama'],
                'dokter_nama' => $data['dokter_nama'],
                'rencana_tindakan' => $data['rencana_tindakan'],
                'carabayar_id' => 1, // make this to default value
                'penjamin_id' => 1, // make this to default value
                'pegawaiverifikasi_id' => $data['user_verif'],
                'last_modified_date' => date('Y-m-d H:i:s'),
                'status_verifikasi_bantaran' => DocoConstants::SUDAH_VERIFIKASI_BANTARAN,
                'pasien_id' => $data['pasien_id'],
                'pasien_lama' => $data['pasien_lama'],
                'status_pelayanan_bantaran' => DocoConstants::MENUNGGU_DIDAFTARKAN_BANTARAN
            ], ['rujukanbantaran_id' => $data['bantaran_id']]);
    }
}