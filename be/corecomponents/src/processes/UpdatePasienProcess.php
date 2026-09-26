<?php 

namespace Doco\processes;
use Yii;
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
use Doco\components\DocoHelpers;
use Doco\Services\InternalService;
use yii\helpers\ArrayHelper;

class UpdatePasienProcess extends \Doco\components\DocoBaseProcessExtension
{
    private static function getPendaftaran()
    {
        return Pendaftaran::find();
    }

    private function setPj($modelPasien)
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $pendaftaranId = isset($dataPost['pendaftaran_id_last']) ? $dataPost['pendaftaran_id_last'] : null;
        $pjId = isset($dataPost['pj_id']) ?$dataPost['pj_id'] : null;
        $isDelete = isset($dataPost['is_deleted_pj']) ? $dataPost['is_deleted_pj']: null;

        if($pendaftaranId && $pjId != '0' && $isDelete == '0') {
            /** Case Edit */
            $pjModel = self::getPenanggungJawab()
            ->where(['penanggungjawab_id' => $pjId])
            ->one();

            $pjModel->pengantar = $dataPost['pj_pengantar'];
            $pjModel->penanggungjawab_nama = $dataPost['pj_nama'];
            $pjModel->penanggungjawab_jeniskelamin = $dataPost['pj_jk'];
            $pjModel->jenisidentitas = $dataPost['pj_jenis_identitas'];
            $pjModel->no_identitas = $dataPost['pj_no_identitas'];
            $pjModel->hubungankeluarga = $dataPost['pj_hubungan'];
            $pjModel->penanggungjawab_tempatlahir = $dataPost['pj_tempat_lahir'];
            $pjModel->penanggungjawab_tgllahir = $dataPost['pj_tanggal_lahir'];
            $pjModel->penanggungjawab_alamat = $dataPost['pj_alamat'];
            $pjModel->penanggungjawab_notelp = $dataPost['pj_no_telepon'];
            if(!$pjModel->save()) {
                return false;
            }
        } else if($pendaftaranId && $pjId == '0') {
            /** Case Insert */
            $pjModel = new PenanggungJawab;
            $pjModel->pengantar = $dataPost['pj_pengantar'];
            $pjModel->penanggungjawab_nama = $dataPost['pj_nama'];
            $pjModel->penanggungjawab_jeniskelamin = $dataPost['pj_jk'];
            $pjModel->jenisidentitas = $dataPost['pj_jenis_identitas'];
            $pjModel->no_identitas = $dataPost['pj_no_identitas'];
            $pjModel->hubungankeluarga = $dataPost['pj_hubungan'];
            $pjModel->penanggungjawab_tempatlahir = $dataPost['pj_tempat_lahir'];
            $pjModel->penanggungjawab_tgllahir = $dataPost['pj_tanggal_lahir'];
            $pjModel->penanggungjawab_alamat = $dataPost['pj_alamat'];
            $pjModel->penanggungjawab_notelp = $dataPost['pj_no_telepon'];
            $pjModel->pasien_id = $modelPasien->pasien_id;
            if($pjModel->save()) {
                $kunjungan = self::getPendaftaran()
                ->where(['pendaftaran_id' =>$pendaftaranId, 
                        'pasien_id' => $modelPasien->pasien_id])
                ->one();

                if($kunjungan) {
                    $pjId = $kunjungan->penanggungjawab_id;
                    $kunjungan->penanggungjawab_id = $pjModel->penanggungjawab_id;
                    if(!$kunjungan->save()){
                        return false;
                    }
                    if($isDelete == '1') {
                        $pjOldModel = self::getPenanggungJawab()
                        ->where(['penanggungjawab_id' => $pjId])
                        ->one();
                        if(!$pjOldModel->delete()){
                            return false;
                        }
                    }
                } else {
                    throw new \Exception("Data Tidak Di Temukan");
                }
            } 
        } else if ($pendaftaranId && empty($pjId) && $isDelete == '1') {
            $kunjungan = self::getPendaftaran()
            ->where(['pendaftaran_id' =>$pendaftaranId, 
                    'pasien_id' => $modelPasien->pasien_id])
            ->one();

            if($kunjungan) {
                $pjId = $kunjungan->penanggungjawab_id;
                $kunjungan->penanggungjawab_id = null;
                if($kunjungan->save()){
                    if(!empty($pjId)) {
                        $pjModel = self::getPenanggungJawab()
                        ->where(['penanggungjawab_id' => $pjId])
                        ->one();
                        if(!$pjModel->delete()){
                            return false;
                        }
                    }
                }
            } else {
                throw new \Exception("Data Tidak Di Temukan");
            }
        }

        return true;
    }

    private function setKp($modelPasien)
    {
        $request = Yii::$app->request;
        $dataPost = $request->post();
        $keluargapasien_id = isset($dataPost['kp_id']) ?$dataPost['kp_id'] : null;
        $isDelete = isset($dataPost['is_deleted_kp']) ? $dataPost['is_deleted_kp']: null;

        if($isDelete == '0') {
            /** Case Edit */
            $kpModel = KeluargaPasien::find()
            ->where(['pasien_id' => $modelPasien->pasien_id])
            ->one();
            
            if (empty($kpModel)) {
                $kpModel = new KeluargaPasien;
            }

            $kpModel->keluarga_nama = $dataPost['keluarga_nama'];
            $kpModel->keluarga_jk = $dataPost['keluarga_jk'];
            $kpModel->keluarga_hubungan = $dataPost['keluarga_hubungan'];
            $kpModel->keluarga_no_telepon = $dataPost['keluarga_no_telepon'];
            $kpModel->keluarga_alamat = $dataPost['keluarga_alamat'];
            $kpModel->keluarga_namadepan = $dataPost['keluarga_namadepan'];
            $kpModel->keluarga_pekerjaan_id = $dataPost['keluarga_pekerjaan_id'];
            $kpModel->keluarga_propinsi_id = $dataPost['keluarga_propinsi_id'];
            $kpModel->keluarga_kabupaten_id = isset($dataPost['keluarga_kabupaten_id'])?$dataPost['keluarga_kabupaten_id']:null;
            $kpModel->keluarga_kecamatan_id = isset($dataPost['keluarga_kecamatan_id'])?$dataPost['keluarga_kecamatan_id']:null;
            $kpModel->keluarga_kelurahan_id = isset($dataPost['keluarga_kelurahan_id'])?$dataPost['keluarga_kelurahan_id']:null;
            $kpModel->keluarga_rt = $dataPost['keluarga_rt'];
            $kpModel->keluarga_rw = $dataPost['keluarga_rw'];
            $kpModel->pasien_id = $modelPasien->pasien_id;
            if(!$kpModel->save()) {
                return false;
            }
        }  else if (empty($keluargapasien_id) && $isDelete == '1') {
            $kpModel = KeluargaPasien::find()
                ->where(['pasien_id' => $modelPasien->pasien_id])
                ->one();

            if($kpModel) {
                if(!$kpModel->delete()){
                    return false;
                }
            }
        }

        return true;
    }

    private static function getPenanggungJawab()
    {
        return PenanggungJawab::find();
    }
    
	protected function processFlow()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $id = $request->post('pasien_id');

        try {
            $queryPasienUbah = PasienUbahData::find()
              ->where(['pasien_id' => $id])
              ->where(['is_active' => true]);
            $rowPasienUbah = $queryPasienUbah->all();
            $pjId = $request->post('pj_id');
            $isDeletePj = $request->post('is_deleted_pj');
            $model = Pasien::find()->where(['pasien_id'=>$id])->one();
            $tglLahir = $model->tanggal_lahir;
            $isCanEdit = $request->post() && !empty($model);

            if ($isCanEdit) {
                $model->attributes = $request->post();
                $model->jenisidentitas = !empty($request->post('jenisidentitas')) ? $request->post('jenisidentitas') : null;
                $model->no_identitas_pasien = !empty($request->post('no_identitas_pasien')) ? $request->post('no_identitas_pasien') : null;
                $model->nopeserta_bpjs = !empty($request->post('nopeserta_bpjs')) ? $request->post('nopeserta_bpjs') : null;
                $model->rw = !empty($request->post('rw')) ? $request->post('rw') : null;
                $model->rt = !empty($request->post('rt')) ? $request->post('rt') : null;
                $model->anakke = !empty($request->post('anakke')) ? $request->post('anakke') : null;
                $model->jumlah_bersaudara = !empty($request->post('jumlah_bersaudara')) ? $request->post('jumlah_bersaudara') : null;
                $model->propinsi_id = !empty($request->post('propinsi_id')) ? $request->post('propinsi_id') : null;
                $model->kabupaten_id = !empty($request->post('kabupaten_id')) ? $request->post('kabupaten_id') : null;
                $model->kecamatan_id = !empty($request->post('kecamatan_id')) ? $request->post('kecamatan_id') : null;
                $model->kelurahan_id = !empty($request->post('kelurahan_id')) ? $request->post('kelurahan_id') : null;
                $model->suku_id = !empty($request->post('suku_id')) ? $request->post('suku_id') : null;
                

                // History Edit Pasien
                PasienUbahData::updateAll([
                  'is_active' => false,
                ],[
                  'pasien_id' => $model->pasien_id,
                  'is_active' => true
                ]);
                $modelPasienUbahData = new PasienUbahData();
                $modelPasienUbahData->alasan_ubahdata = $request->post('reason');
                $modelPasienUbahData->pasien_id = $model->pasien_id;
                $modelPasienUbahData->tgl_ubahdata = date('Y-m-d');
                $modelPasienUbahData->save();
                $activePasienUbahData = PasienUbahData::find()->where([
                  'pasien_id' => $model->pasien_id,
                  'is_active' => true
                ])->all();
                // End History

                // Implement scenario error update
                $model->scenario = "fix-error-update";
                $model->jenisidentitas = (string) $model->jenisidentitas;
                $additional_pasien = $request->post('additional_identitas');
                $model->additional_pasien = $additional_pasien;

                $dataPdftrn = $this->getPendaftaran()
                    ->select(['pendaftaran_t.pendaftaran_id', 'konsulpoli_t.konsulpoli_id', 'pendaftaran_t.tgl_pendaftaran'])
                    ->leftJoin('konsulpoli_t', 'konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
                    ->where(['pendaftaran_t.pasien_id' => $id])
                    ->asArray()
                    ->all();

                // $additional_pasien = json_decode($additional_pasien, true);
                // foreach ($additional_pasien as $additional) {
                //     if(isset($additional['jenisidentitas']) && isset($additional['no_identitas_pasien'])) {
                //         if($additional['jenisidentitas'] == DocoConstants::IDENTITAS_KTP) {
                //             $model->no_identitas_pasien = $additional['no_identitas_pasien'];
                //         } else if($additional['jenisidentitas'] == DocoConstants::IDENTITAS_BPJS) {
                //             $model->nopeserta_bpjs = $additional['no_identitas_pasien'];
                //         }
                //     }
                // }

                if($tglLahir != $request->post('tanggal_lahir')) {
                    $updateData = [];
                    foreach ($dataPdftrn as $item) {
                        $umur = DocoHelpers::convertDateToAge(
                            date('Y-m-d', strtotime($request->post('tanggal_lahir'))),
                            null,
                            date('Y-m-d', strtotime($item['tgl_pendaftaran']))
                        );
                
                        $updateData[$item['pendaftaran_id']] = $umur;
                    }

                    if (!empty($updateData)) {
                        $caseWhenSql = "UPDATE pendaftaran_t SET umur = CASE pendaftaran_id ";
                        $ids = [];
                
                        foreach ($updateData as $id => $umur) {
                            $caseWhenSql .= "WHEN '{$id}' THEN '{$umur}' ";
                            $ids[] = $id;
                        }
                
                        $caseWhenSql .= "END WHERE pendaftaran_id IN (" . implode(',', array_map('intval', $ids)) . ")";
                        Yii::$app->db->createCommand($caseWhenSql)->execute();
                    }
                }
                
                // Checking User Password
                $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
                $check = $jwt->katakunci_pemakai;
                $valid = Yii::$app->security->validatePassword($request->post('password'), $check);
                if (!$valid) {
                    throw new \Exception("Password Salah.");
                }
                if ($model->validate() && $model->save()) {
                    $setPj = $this->setPj($model);
                    $setKp = $this->setKp($model);

                    (new InternalService)->sendTo([
                        'Lis' => [
                                'BridgingPatient' => [
                                    'pendaftaran_id' => $request->post('pendaftaran_id_last'),
                                    'no_rekamedik' => $request->post('no_rekam_medik'),
                                    'nama_pemakai' => ArrayHelper::getValue($jwt, 'nama_pemakai')
                                ]
                            ]
                        ], true);
                    if($setPj == true && $setKp == true) {
                        $transaction->commit();
                        return [
                            'message' => Yii::t('app', 'Data Berhasil di ubah'),
                            'pasien_id' => $id,
                            'list_pendaftaran' => $dataPdftrn
                        ];
                    }
                } else {
                    $transaction->rollBack();
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                    // $errors = DocoHelpers::parseError($model->errors,'PasienForm');
                    // return [
                    //     'data' => $errors,
                    //     'status' => 422
                    // ];
                }
                return $model->attributes;
            }

            $errorMessage = '';
            if(empty($request->post())) {
                $errorMessage = 'Data update kosong';
            }

            if(empty($model)) {
                $errorMessage = 'Data Tidak Di Temukan';
            }

            return $this->responseJson(200, $errorMessage, $model);
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
