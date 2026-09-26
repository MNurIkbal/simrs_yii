<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions;

use Yii;
use yii\base\Action;
use Doco\components\DocoConstants;
use app\modules\v1\models\Pendaftaran;

class UpdateProsesAction extends Action
{
    /*$id = pendaftaran_id*/
    public function run($id)
    {
        try{
            $arr_id = explode(',', $id);
            $pendaftaran = Pendaftaran::find()->where(['pendaftaran_id' => $arr_id])->asArray()->all();
            if(count($pendaftaran) == 0) {
                throw new \Exception("Data Pendaftaran Tidak Ditemukan", 1);
            }

            $updStatus = Pendaftaran::updateAll([
                'status_konfirmasi' => DocoConstants::STATUS_KONFIRMASIRM_PROSES
            ], 'pendaftaran_id IN ('.$id.')');

            if(!$updStatus){
                throw new \Exception("Perubahan Status Gagal", 1);
            }
                
            return [
                'message' => 'Berhasil',
                'text' => 'Berhasil',
                'data' => ['pendaftaran_id' => $id]
            ];
        }catch(\Exception $e){
            return [
                'message' => 'Gagal',
                'text' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}