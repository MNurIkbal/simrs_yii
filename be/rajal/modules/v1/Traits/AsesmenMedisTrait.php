<?php

namespace app\modules\v1\Traits;

use app\modules\v1\models\Anamnesa;
use app\modules\v1\models\PemeriksaanFisik;
use app\modules\v1\models\PemeriksaanFisikDetail;
use Doco\models\InfoKunjunganRajal;
use Yii;

trait AsesmenMedisTrait
{

    /**
     * This function will return data form asesmen medis
     * 
     * @param String $pendaftaran_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionAsesmenMedis($pendaftaran_id)
    {
        // firstly check data periksa fisik
        $record = PemeriksaanFisik::find()
            ->andWhere(compact('pendaftaran_id'))
            ->asArray()
            ->one();
        if (empty($record)) {
            $record = Anamnesa::find()
                ->select([
                    'pendaftaran_id',
                    'riwayat_penyakit_nama as riwayat_penyakit_dahulu',
                    'riwayat_penyakit_keluarga_list as riwayat_penyakit_keluarga',
                    'keluhan_utama',
                    'berat_badan as beratbadan_kg',
                    'tinggi_badan as tinggibadan_cm',
                    'td as tekanandarah',
                    'nadi as detaknadi',
                    'rr as pernapasan',
                    'suhu as suhutubuh',
                ])
                ->andWhere([
                    'pendaftaran_id' => $pendaftaran_id
                ])
                ->asArray()
                ->one();
        } else {
            $record['daftarDiagnosa'] = PemeriksaanFisikDetail::find()
                ->select([
                    'masalah_diagnosa_medis',
                    'rencana_laksana_medis',
                    'pemeriksaanfisik_id',
                ])
                ->andWhere([
                    'pemeriksaanfisik_id' => $record['pemeriksaanfisik_id']
                ])
                ->asArray()
                ->all();
        }
        return $this->responseJson(200, 'Data berhasil diambil', $record);
    }

    /**
     * return data asesmen medis
     * 
     * @param String $pendaftaran_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveAsesmenMedis($pendaftaran_id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $registrationData = InfoKunjunganRajal::find()
                ->select([
                    'pendaftaran_id',
                    'pegawai_id',
                    'pasien_id'
                ])
                ->andWhere(compact('pendaftaran_id'))
                ->asArray()
                ->one();
            if (empty($registrationData)) {
                return $this->responseJson(400, 'Data pendaftaran tidak ditemukan.');
            }
            $payload = Yii::$app->request->post();
            $details = isset($payload['details']) ? $payload['details'] : [];
            $model = PemeriksaanFisik::find()
                ->select([
                    'pendaftaran_id',
                    'pemeriksaanfisik_id',
                ])
                ->andWhere(compact('pendaftaran_id'))
                ->one();
            $updatedData = false;
            if (!empty($model)) {
                $updatedData = true;
                // update data
                $periksaFisikId = $model['pemeriksaanfisik_id'];
                PemeriksaanFisikDetail::deleteAll([
                    'pemeriksaanfisik_id' => $periksaFisikId
                ]);
            } else {
                unset($payload['details']);
                $model = new PemeriksaanFisik;
                // create new data
            }
            $model->attributes = $payload;
            $model->pendaftaran_id = $pendaftaran_id;
            $model->tglperiksafisik = date('Y-m-d H:i:s');
            $model->dokter_id = $registrationData['pegawai_id'];
            $model->pasien_id = $registrationData['pasien_id'];
            if (!$model->save()) {
                $transaction->rollBack();
                Yii::error([
                    'error-data' => $model->getErrors()
                ]);
                return $this->responseJson(500, 'Terjadi Kesalahan pada server');
            }

            if (!$updatedData) {
                $periksaFisikId = $model->getPrimaryKey();
            }
            $bucketDetails = [];
            foreach ($details as $value) {
                if (isset($value['masalah_diagnosa_medis']) && !empty($value['masalah_diagnosa_medis']) && isset($value['rencana_laksana_medis']) && !empty($value['rencana_laksana_medis'])) {
                    $bucketDetails[] = [
                        'masalah_diagnosa_medis' => $value['masalah_diagnosa_medis'],
                        'rencana_laksana_medis' => $value['rencana_laksana_medis'],
                        'pemeriksaanfisik_id' => $periksaFisikId
                    ];
                }
            }
            if (!empty($bucketDetails)) {
                PemeriksaanFisikDetail::batchInsert($bucketDetails);
            }
            $transaction->commit();
            return $this->responseJson(200, 'Asesmen medis berhasil disimpan');
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500);
        }
    }
}
