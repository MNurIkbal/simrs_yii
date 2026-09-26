<?php

namespace app\modules\v1\actions\RujukanBantaran;

use Yii;
use yii\base\Action;
use yii\db\Exception;
use app\modules\v1\models\RujukanBantaran;
use app\modules\v1\models\Pendaftaran;
use Doco\components\DocoConstants;

class BatalVerifikasiAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $bantaran_id = $request->post('bantaran_id');
        
        // Validation
        if (empty($bantaran_id)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Bantaran ID harus diisi.'];
        }

        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $user_id = !empty(Yii::$app->jwt) ? $jwt->loginpemakai_id : null;
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        
        try {
            $rujukanBantaran = RujukanBantaran::findOne(['rujukanbantaran_id' => $bantaran_id, 'is_deleted' => false]);
            
            if (!$rujukanBantaran) {
                $transaction->rollBack();
                Yii::$app->response->statusCode = 404;
                return ['message' => 'Rujukan bantaran tidak ditemukan.'];
            }
            
            // Cancel pendaftaran if exists
            if (!empty($rujukanBantaran->pendaftaran_id)) {
                $pendaftaran = Pendaftaran::findOne(['pendaftaran_id' => $rujukanBantaran->pendaftaran_id]);
                if ($pendaftaran) {
                    $pendaftaran->is_deleted = true;
                    $pendaftaran->deleted_date = date('Y-m-d H:i:s');
                    $pendaftaran->deleted_by = $user_id;
                    
                    if (!$pendaftaran->save()) {
                        $transaction->rollBack();
                        Yii::$app->response->statusCode = 422;
                        return [
                            'message' => 'Gagal membatalkan pendaftaran.',
                            'errors' => $pendaftaran->getErrors()
                        ];
                    }
                }
            }
            
            // Reset verification data
            $rujukanBantaran->status_verifikasi_bantaran = DocoConstants::BELUM_VERIFIKASI_BANTARAN;
            $rujukanBantaran->tgl_verifikasi_bantaran = null;
            $rujukanBantaran->pegawaiverifikasi_id = null;
            $rujukanBantaran->pendaftaran_id = null;
            $rujukanBantaran->instalasi_nama = null;
            $rujukanBantaran->ruangan_nama = null;
            $rujukanBantaran->dokter_nama = null;
            $rujukanBantaran->status_pelayanan_bantaran = null;
            $rujukanBantaran->last_modified_date = date('Y-m-d H:i:s');
            $rujukanBantaran->last_modified_by = $user_id;
            
            if ($rujukanBantaran->save()) {
                $transaction->commit();
                
                return [
                    'message' => 'Verifikasi bantaran berhasil dibatalkan.',
                    'pengajuan_id' => $rujukanBantaran->pengajuan_id,
                    'nomor_pengajuan' => $rujukanBantaran->no_rujukanbantaran
                ];
            } else {
                $transaction->rollBack();
                Yii::$app->response->statusCode = 422;
                return [
                    'message' => 'Data gagal disimpan.',
                    'errors' => $rujukanBantaran->getErrors()
                ];
            }
            
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Database error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Error: ' . $e->getMessage()];
        }
    }
}
