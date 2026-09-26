<?php

namespace app\modules\v1\actions\RujukanBantaran;

use Yii;
use yii\base\Action;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Cppt;
use app\modules\v1\models\RujukanBantaran;
use app\modules\v1\models\LoginPemakai;

class InsertCpptAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $rujukanbantaran_id = $request->post('rujukanbantaran_id');
        $tgl_cppt = $request->post('tgl_cppt');
        $subjektif = trim($request->post('subjektif'));
        $objektif = trim($request->post('objektif'));
        $catatan_dokter = trim($request->post('catatan_dokter'));
        $instruksi = trim($request->post('instruksi', ''));
        $planning = trim($request->post('planning'));
        $diagUtamaInput = $request->post('a_diag_utama', null);
        $diagPenyertaInput = $request->post('a_diag_penyerta', null);
        
        // Validation
        if (empty($rujukanbantaran_id)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Rujukan bantaran ID harus diisi.'];
        }
        
        if (empty($subjektif)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Subjektif harus diisi.'];
        }
        
        if (empty($objektif)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Objektif harus diisi.'];
        }
        
        if (empty($catatan_dokter)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Catatan Dokter (Assessment) harus diisi.'];
        }
        
        if (empty($planning)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Planning harus diisi.'];
        }
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        
        try {
            // Get user info
            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
            $user_id = !empty(Yii::$app->jwt) ? $jwt->loginpemakai_id : null;
            $user = $this->getUser($user_id);
            $pegawai_id = ArrayHelper::getValue($user, 'pegawai_id', null);
            
            // Get rujukan bantaran info
            $rujukan = RujukanBantaran::find()
                ->select(['rujukanbantaran_id', 'pendaftaran_id', 'ruangan_id', 'pasien_id'])
                ->andWhere(['rujukanbantaran_id' => $rujukanbantaran_id, 'is_deleted' => false])
                ->asArray()
                ->one();
            
            if (!$rujukan) {
                $transaction->rollBack();
                Yii::$app->response->statusCode = 404;
                return ['message' => 'Rujukan bantaran tidak ditemukan.'];
            }
            
            // Parse tanggal
            $tgl_cppt_formatted = date('Y-m-d H:i:s');
            if (!empty($tgl_cppt)) {
                try {
                    $tgl_cppt_formatted = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $tgl_cppt)));
                } catch (\Exception $e) {
                    // Use default if parsing fails
                }
            }
            
            $model = new Cppt();
            $model->pendaftaran_id = $rujukan['pendaftaran_id'];
            $model->pasien_id = $rujukan['pasien_id'];
            $model->ruangan_id = !empty($rujukan['ruangan_id']) ? $rujukan['ruangan_id'] : 1;
            $model->tgl_cppt = $tgl_cppt_formatted;
            $model->subject = $subjektif;
            $model->object = $objektif;
            $model->catatan_dokter = $catatan_dokter;
            $model->instruksi = $instruksi;
            $model->planning = $planning;
            $model->a_diag_utama = $this->prepareJsonField($diagUtamaInput);
            $model->a_diag_penyerta = $this->prepareJsonField($diagPenyertaInput);
            $model->pegawai_id = !empty($pegawai_id) ? $pegawai_id : 1;
            $model->created_by = !empty($user_id) ? $user_id : 1;
            $model->created_date = date('Y-m-d H:i:s');
            $model->is_active = true;
            $model->is_deleted = false;

            if (!$model->save(false)) {
                $transaction->rollBack();
                Yii::error('Gagal menyimpan CPPT baru: ' . json_encode($model->getErrors()), 'rujukan-bantaran');
                Yii::$app->response->statusCode = 422;
                return ['message' => 'Data CPPT gagal disimpan.'];
            }
            
            $transaction->commit();
            
            return [
                'message' => 'Data SOAP berhasil disimpan.',
                'cppt_id' => $model->cppt_id
            ];
            
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error('InsertCpptAction - Database Error: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan database: ' . $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('InsertCpptAction - Error: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    private function getUser($user_id)
    {   
        $getLoginPemakai = [];
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

    private function prepareJsonField($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Accept JSON string or already-decoded array
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }

        // Ensure valid JSON stored in DB
        return json_encode($value);
    }
}
