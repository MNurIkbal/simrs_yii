<?php

namespace Doco\Notifications;

use Doco\components\DocoConstants;
use Doco\models\Loginpemakai;
use Doco\models\Notifikasi;
use Doco\models\User;
use Doco\models\Ruangan;
use Yii;

class RmNotification extends BaseNotification
{
    /**
     * Publish new notification of fasting reminder
     * 
     * @param Array $option
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function documentReminder($permintaandokrm)
    {
        $userId = Yii::$app->jwt->user->loginpemakai_id;
        if(!empty($permintaandokrm)) {
            $result = $permintaandokrm;
            foreach ($result as $data) {
                $users = User::usersIdByRuangan($data['ruangan_id']);
                $message = 'Reminder Dokumen Rekam Medik pasien #nama_pasien(#no_pendaftaran) dengan No. Rekam Medik #no_rekam_medik agar segera dikirimkan ke ruangan #ruangan_nama';
                if(
                    $data['status_rekam_medik'] == DocoConstants::DOKRM_ISSUES ||
                    $data['status_rekam_medik'] == DocoConstants::DOKRM_RECEIVE ||
                    $data['status_rekam_medik'] == DocoConstants::DOKRM_RETURN
                ) {
                    $ruangan = Ruangan::find()
                        ->select(['ruangan_nama'])
                        ->where(['instalasi_id'=>DocoConstants::INST_ID_RM])
                        ->asArray()
                        ->one();
                    $data = array_merge($data, $ruangan);
                    $users = User::usersIdByRuangan($data['ruangan_id']);
                } else {
                    $ruangan = Ruangan::find()
                        ->select(['ruangan_id'])
                        ->where(['instalasi_id'=>DocoConstants::INST_ID_RM])
                        ->asArray()
                        ->one();
                    $data = array_merge($data, $ruangan);
                    $users = User::usersIdByRuangan($data['ruangan_id']);
                }
                $message = str_replace('#nama_pasien', $data['nama_pasien'], $message);
                $message = str_replace('#no_pendaftaran', $data['no_pendaftaran'], $message);
                $message = str_replace('#no_rekam_medik', $data['no_rekam_medik'], $message);
                $message = str_replace('#ruangan_nama', $data['ruangan_nama'], $message);
                
                $date = date("Y-m-d H:i:s");
                $defaultNotificationPayload = [
                    'instalasi_id' => DocoConstants::INST_ID_RI,
                    'type' => 'dok-rm-reminder',
                    'modul_id' => DocoConstants::RM_MODULE_ID,
                    'tglnotifikasi' => $date,
                    'judulnotifikasi' => 'Reminder Dok. RM ' . $data['no_rekam_medik'],
                    'isi_notifikasi' => $message,
                    'is_read' => false,
                    'user_id' => null,
                    'additional_data' => json_encode($data),
                    'created_date' => $date,
                    'created_by' => $userId,
                ];

                $payloadNotification = [];
                foreach ($users as $user_id) {
                    $payloadNotification[] = array_merge($defaultNotificationPayload, [
                        'user_id' => $user_id
                    ]);
                    $arrayOfUsers[] = $user_id;
                }
                Notifikasi::batchInsert($payloadNotification);

                $dataPayload = [
                    'message' => $message,
                    'filter_ruangan' => $data['ruangan_id'],
                ];

                $payloadRedis = [
                    'newNotification' => array_merge($dataPayload, $defaultNotificationPayload),
                    'users' => $arrayOfUsers
                ];
                self::publish('new-notification', $payloadRedis);
            }
        }
    }

    public static function documentSignNotification($dokumenSign)
    {
        if(!empty($dokumenSign)) {
            $loginpemakai = Loginpemakai::find()->where(['pegawai_id' => $dokumenSign->pegawai_id])->one();
            if($loginpemakai) {
                $message = 'Ada dokumen yang harus ditandatangani';

                $date = date("Y-m-d H:i:s");
                $notificationPayload = [
                    'instalasi_id' => DocoConstants::INST_ID_RM,
                    'type' => 'dok-sign-notif',
                    'modul_id' => DocoConstants::RM_MODULE_ID,
                    'tglnotifikasi' => $date,
                    'judulnotifikasi' => 'Tanda Tangani Dokumen',
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
                self::publish('new-notification', $payloadRedis);
            }
        }
    }
}
