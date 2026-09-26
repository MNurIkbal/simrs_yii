<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Extensions\reseptur;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\exceptions\ValidationException;

use app\modules\v1\models\Reseptur;

class SerahkanObatApproval extends \Doco\processes\SerahkanObatProcess {
    protected function updateStatusReseptur() {
        $update = true;
        if(!empty($this->ruanganAsalId)) {
            $reseptur = Reseptur::find()->where(['reseptur_id' => $this->resep->reseptur_id])->one();
            $reseptur->status_reseptur = DocoConstants::RESEPTUR_DISERAHKAN;
            $update = $reseptur->update();
        }

        if(!$update) {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_error, [
                'text' => 'Gagal update status reseptur'
            ]);
        }
    }

    protected function serahkanResep() {
        $this->setLogUser($this->resep);
        $this->resep->log_user = $this->logUser;
        $this->resep->status_reseptur = DocoConstants::RESEPTUR_DISERAHKAN;
        $this->resep->pegawai_menyerahkan_id = is_null($this->pegawaiMenyerahkan) ? Yii::$app->jwt->user->pegawai_id : $this->pegawaiMenyerahkan;
        $this->resep->tgl_menyerahkan = date('Y-m-d H:i:s');
        $this->resep->save();
    }

    protected function processFlow() {
        $this->validasiPayload();
        $this->getKonfigWorklist();
        if($this->bypassWorklist) {
            $this->setPayloadWorklist();
            $this->startDBTransaction();
            $this->updateStatusWorklist();
            $this->commitDBTransaction();
        } 
        
        $this->startDBTransaction();
        $this->validasiResep();
        $this->serahkanResep();
        $this->updateStatusReseptur();
        $this->commitDBTransaction();

        return [
            'message' => 'Berhasil',
            'text' => 'Status Berhasil Diubah'
        ];
    }
}