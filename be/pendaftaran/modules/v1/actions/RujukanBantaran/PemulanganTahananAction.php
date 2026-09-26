<?php

namespace app\modules\v1\actions\RujukanBantaran;

use Yii;
use yii\base\Action;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\RujukanBantaran;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\LoginPemakai;
use Doco\components\DocoConstants;

class PemulanganTahananAction extends Action
{
    // PROCESS FLOW
    // Check pembantaran data
    // Create pasien pulang data
    // Update pendaftaran data with pemulangan info

    public function run()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $rujukanbantaran_id = $request->post('rujukanbantaran_id');
        $catatan_pemulangan = trim($request->post('catatan_pemulangan', ''));
        $tgl_pemulangan = $request->post('tgl_pemulangan');
        
        // Validation
        if (empty($rujukanbantaran_id)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Rujukan bantaran ID harus diisi.'];
        }
        
        if (empty($catatan_pemulangan)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'Catatan pemulangan harus diisi.'];
        }
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        
        try {
            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
            $user_id = !empty(Yii::$app->jwt) ? $jwt->loginpemakai_id : null;
            
            $pembantaran = RujukanBantaran::findOne(['rujukanbantaran_id' => $rujukanbantaran_id, 'is_deleted' => false]);
            
            if (!$pembantaran) {
                $transaction->rollBack();
                Yii::$app->response->statusCode = 404;
                return ['message' => 'Rujukan bantaran tidak ditemukan.'];
            }

            $pendaftaran = Pendaftaran::findOne(['pendaftaran_id' => $pembantaran->pendaftaran_id, 'is_deleted' => false]);
            
            if (!$pendaftaran) {
                $transaction->rollBack();
                Yii::$app->response->statusCode = 404;
                return ['message' => 'Data pendaftaran terkait tidak ditemukan.'];
            }
            
            $pasienPulang = new PasienPulang();
            $pasienPulang->pasien_id = $pembantaran->pasien_id;
            $pasienPulang->pendaftaran_id = $pembantaran->pendaftaran_id;
            $pasienPulang->carakeluar_id = 1; // Pemulangan
            $pasienPulang->kondisikeluar_id = 1; // Sehat
            $pasienPulang->tglpasienpulang = date('Y-m-d H:i:s');
            $pasienPulang->lama_rawat = (int) ((strtotime(date('Y-m-d H:i:s')) - strtotime($pendaftaran->tgl_pendaftaran)) / (60 * 60 * 24));
            $pasienPulang->satuan_lamarawat = 'Hari';
            $pasienPulang->is_meninggal = false;
            $pasienPulang->satuan_hariperawatan = 'Hari';
            $pasienPulang->keterangan_keluar = $catatan_pemulangan;
            $pasienPulang->created_by = $user_id;
            $pasienPulang->created_date = date('Y-m-d H:i:s');

            if($pasienPulang->save()) {
                $pendaftaran->status_periksa = 4; // Selesai
                $pendaftaran->pasienpulang_id = $pasienPulang->pasienpulang_id;
                $pendaftaran->last_modified_by = $user_id;
                $pendaftaran->last_modified_date = date('Y-m-d H:i:s');
                
                if(!$pendaftaran->save()) {
                    $transaction->rollBack();
                    Yii::error('Gagal memperbarui data pendaftaran: ' . json_encode($pendaftaran->getErrors()), 'rujukan-bantaran');
                    Yii::$app->response->statusCode = 422;
                    return ['message' => 'Data pendaftaran gagal diperbarui.'];
                }

                $pembantaran->status_pelayanan_bantaran = DocoConstants::SELESAI_PELAYANAN_BANTARAN;
                $pembantaran->last_modified_by = $user_id;
                $pembantaran->last_modified_date = date('Y-m-d H:i:s');
                
                if(!$pembantaran->save()) {
                    $transaction->rollBack();
                    Yii::error('Gagal memperbarui data rujukan bantaran: ' . json_encode($pembantaran->getErrors()), 'rujukan-bantaran');
                    Yii::$app->response->statusCode = 422;
                    return ['message' => 'Data rujukan bantaran gagal diperbarui.'];
                }
            } else {
                $transaction->rollBack();
                Yii::error('Gagal menyimpan data pasien pulang: ' . json_encode($pasienPulang->getErrors()), 'rujukan-bantaran');
                Yii::$app->response->statusCode = 422;
                return ['message' => 'Data pasien pulang gagal disimpan.'];
            }

            $transaction->commit();
            
            return [
                'message' => 'Pemulangan tahanan berhasil diproses.',
                'pendaftaran_id' => $pendaftaran->pendaftaran_id,
                'catatan' => $catatan_pemulangan,
                'rujukanbantaran_id' => $pembantaran->rujukanbantaran_id,
                'tgl_pemulangan' => $pasienPulang->tglpasienpulang,
                'tgl_kembalinya' => $pasienPulang->tglpasienpulang
            ];
            
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error('PemulanganTahananAction - Database Error: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan database: ' . $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('PemulanganTahananAction - Error: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
}
