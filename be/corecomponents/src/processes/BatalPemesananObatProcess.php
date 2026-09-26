<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use app\modules\v1\models\PesanObatAlkes;
use app\modules\v1\models\PesanObatDetail;
use Doco\components\DocoConstants;

class BatalPemesananObatProcess extends \Doco\components\DocoBaseProcessExtension {
    protected function processFlow() {
        $request = Yii::$app->request;
        $pesanobatalkes_id = $request->post('pesanobatalkes_id', null);
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            $model = PesanObatAlkes::findOne($pesanobatalkes_id);
            if($model->statuspesan == DocoConstants::BATAL_PESAN) 
                throw new \Exception("Pemesanan sudah dibatalkan");
            
            $model->statuspesan = DocoConstants::BATAL_PESAN;
            if(!$model->save()) throw new \Exception("Gagal update status batal pesan");

            $model_detail = (new PesanObatDetail)->delete(['pesanobatalkes_id' => $pesanobatalkes_id]);
            if(!$model_detail) throw new \Exception("Gagal update detail pemesanan");
            $transaction->commit();
            
            return $this->responseJson(200, 'Pemesanan berhasil dibatalkan');
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, $e->getMessage(), [], ['title' => 'Proses Gagal !']);
        }
    }
}