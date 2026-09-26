<?php

namespace app\modules\v1\actions\RujukanBantaran;

use Yii;
use yii\base\Action;
use yii\db\Exception;
use app\modules\v1\models\RujukanBantaran;
use Doco\components\DocoConstants;

class RejectBantaranAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $user_id = !empty(Yii::$app->jwt) ? $jwt->loginpemakai_id : null;
        
        $bantaran_id = $request->post('bantaran_id');
        $keteranganTolakRujukan = trim($request->post('keterangan_tolak_rujukan', ''));

        // Validation
        if (empty($bantaran_id)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Bantaran ID harus diisi.'];
        }
        
        if (empty($keteranganTolakRujukan)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Keterangan penolakan harus diisi.'];
        }
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        
        try {
            $rujukanBantaran = RujukanBantaran::findOne(['rujukanbantaran_id' => $bantaran_id, 'is_deleted' => false]);
            
            if (!$rujukanBantaran) {
                $transaction->rollBack();
                Yii::$app->response->statusCode = 404;
                return ['message' => 'Rujukan bantaran tidak ditemukan.'];
            }
            
            // Update status to 4037 (rejected/ditolak)
            $rujukanBantaran->status_verifikasi_bantaran = 4037;
            $rujukanBantaran->keterangan_penolakan_rujukan = $keteranganTolakRujukan;
            $rujukanBantaran->last_modified_date = date('Y-m-d H:i:s');
            $rujukanBantaran->last_modified_by = $user_id;
            
            if ($rujukanBantaran->save()) {
                $transaction->commit();
                
                return [
                    'message' => 'Rujukan bantaran berhasil ditolak.',
                    'pengajuan_id' => $rujukanBantaran->pengajuan_id,
                    'nomor_pengajuan' => $rujukanBantaran->no_rujukanbantaran,
                    'status_pengajuan_id' => 5, // Bantaran app rejected status
                    'alasan_ditolak' => $keteranganTolakRujukan
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
