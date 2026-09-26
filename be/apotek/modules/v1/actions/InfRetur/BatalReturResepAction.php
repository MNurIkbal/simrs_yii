<?php

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\ReturResep;
use SirsCore\models\ReturResepDetail;
use app\modules\v1\models\LogActivityR;
use app\modules\v1\models\Pegawai;
use Doco\models\LoginForm;

class BatalReturResepAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        try {
            $id = $request->get('pendaftaran_id', null);
            $id_retur = $request->get('returresep_id', null);
            $list_log = [];
            
            // update batal retur
            $batal =  ReturResep::updateAll([
                    'is_deleted'   => true,
                    'is_active'    => false,
                    'deleted_date' => date('Y-m-d H:i:s'),
                    'deleted_by'   => Yii::$app->jwt->user->loginpemakai_id,
                    'status_retur'   => DocoConstants::BATAL_RETUR,
                ], ['in', 'returresep_id', $id_retur]);

            // Update detail
            $retur_detail = ReturResepDetail::updateAll([
                    'is_deleted' => true,
                ], ['in', 'returresep_id', $id_retur]);

            
        $Pegawai = Pegawai::find()
            ->select(['pegawai_id', 'nama_pegawai'])
            ->where(['pegawai_id' => Yii::$app->user->identity->pegawai_id])
            ->asArray()->one();

            // Log Activity
            foreach ($id_retur as $value) {
                $list_log[] = [
                    'tgl' => date('Y-m-d H:i:s'),
                    'tipe' => 'RETUR',
                    'aksi' => 'Batal',
                    'keterangan' => $Pegawai['nama_pegawai'],
                    'created_by' => Yii::$app->jwt->user->loginpemakai_id,
                    'transaksi_id' => $value
                ];
            }

            // Insert Log
            LogActivityR::batchInsert($list_log, false);

            if($batal){
                return $this->controller->responseJson(200, 'Berhasil Batal retur resep');
            } else {
                return $this->controller->responseJson(400, 'Gagal Batal retur resep');
            }
        } catch(\yii\base\Exception $e) {
            $this->controller->logError($e);
            return $this->controller->responseJson(400, $e->getMessage(),$e->getLine());
        } catch (\Exception $e) {
            $this->controller->logError($e);
            return $this->controller->responseJson(400, $e->getMessage(),$e->getLine());
        }
    }
}