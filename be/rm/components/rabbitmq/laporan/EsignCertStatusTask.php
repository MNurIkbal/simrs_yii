<?php

namespace app\components\rabbitmq\laporan;

use app\modules\v1\models\DokumenSign;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;

use Doco\Notifications\RmNotification;

use Doco\rabbitmq\task\BaseTask;
use Doco\rabbitmq\RabbitBgProcess;

use Doco\Services\Esign\TilakaService;
use Doco\models\Loginpemakai;
use Doco\models\Notifikasi;
use Doco\models\Pegawai;
use Doco\Notifications\BaseNotification;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

use Yii;

class EsignCertStatusTask extends BaseTask
{
    public function processFlow($parameters)
    {
        $pegawai_id = $parameters['pegawai_id'];
        $user_identifier = $parameters['user_identifier'];
        try {
            $cert_status = TilakaService::certificateStatus($user_identifier);
            $data = Pegawai::find()
                ->select([
                    'additional_esign_data'
                ])
                ->where(['pegawai_id' => $pegawai_id])
                ->asArray()->one();
            $additional_esign_data = json_decode($data['additional_esign_data'], true);
            $this->checkChangedData($pegawai_id, $additional_esign_data['cert_status'], $cert_status);
            $additional_esign_data['cert_status'] = $cert_status;

            $status = TilakaService::getCurrentStatus($additional_esign_data);
            $updateData = [];
            if($status == TilakaService::STATUS_ACTIVE) {
                $updateData['useresign_id'] = $user_identifier;
                unset($additional_esign_data['registration_data']['done_reenroll']);
            } else if($status == TilakaService::STATUS_INACTIVE){
                $updateData['useresign_id'] = null;
            }
            $updateData['additional_esign_data'] = json_encode($additional_esign_data);

            Pegawai::updateAll($updateData, [
                'pegawai_id' => $pegawai_id,
            ]);
        } catch (\Exception $e) {
            Yii::error($e);
        }
    }

    private function checkChangedData ($pegawai_id, $old, $new) {
        Yii::error([
            $old, 
            $new['status'],
        ]);
        if(isset($new['status']) && $new['status'] == 4 && (!isset($old['status']) ||  $old['status'] != 4)) {
            $loginpemakai = Loginpemakai::find()->where(['pegawai_id' => $pegawai_id])->one();
            if($loginpemakai) {
                $message = isset($new['message']['info']) ? $new['message']['info'] : "Pengajuan Sertifikat TTE ditolak";

                $date = date("Y-m-d H:i:s");
                $notificationPayload = [
                    'instalasi_id' => DocoConstants::INST_ID_RM,
                    'type' => 'dok-sign-notif',
                    'modul_id' => DocoConstants::RM_MODULE_ID,
                    'tglnotifikasi' => $date,
                    'judulnotifikasi' => 'Sertifikat TTE ditolak',
                    'isi_notifikasi' => $message,
                    'is_read' => false,
                    'user_id' => $loginpemakai->loginpemakai_id,
                    'additional_data' => json_encode($loginpemakai->attributes),
                    'created_date' => $date,
                    'created_by' => 1,
                ];
                Notifikasi::batchInsert([$notificationPayload]);

                $dataPayload = [
                    'message' => $message,
                ];
                $payloadRedis = [
                    'newNotification' => array_merge($dataPayload, $notificationPayload),
                    'users' => [$loginpemakai->loginpemakai_id,],
                ];
                BaseNotification::publish('new-notification', $payloadRedis);
            }
        }
    }
}