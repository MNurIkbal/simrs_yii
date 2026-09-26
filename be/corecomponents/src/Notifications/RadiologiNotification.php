<?php

namespace Doco\Notifications;

use Doco\models\Radiologi\HasilPemeriksaanRad;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\models\radiologi\InfoOrderanRadView;
use Doco\models\Notifikasi;
use Doco\models\Pegawai;
use Yii;

class RadiologiNotification extends BaseNotification
{
    /**
     * Publish new notification 
     * 
     * @param Array $option
     * @return Boolean
     * @author : Asri Nurul
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function updateSampleWynacom($option)
    {
        $bucketDetails = isset($option['details']) ? $option['details'] : [];
        $orderNumber = isset($option['orderNumber']) ? (isset($option['orderNumber'][0]) ? $option['orderNumber'][0] : $option['orderNumber']) : null;
        if (!empty($bucketDetails) && !empty($orderNumber)) {
            $dataPayload = $detailWithKey = $bucketTestId = [];
            foreach ($bucketDetails as $detail) {
                $bucketTestId[] = $detail['lis_test_id'];
                $detailWithKey['id-' . $detail['lis_test_id']] = $detail;
            }
            $latestRecordResult = HasilPemeriksaanRad::latestRecordByIds($orderNumber, $bucketTestId);
            $baseNotificationArray = [
                'instalasi_id' => DocoConstants::INST_ID_RAD,
                'type' => 'update-expertise',
                'modul_id' => DocoConstants::MODULE_RAD_ID,
                'tglnotifikasi' => date("Y-m-d H:i:s"),
                'is_read' => false,
                'user_id' => null,
                'created_date' => date("Y-m-d H:i:s"),
            ];
            $bucketNotification = [];
            foreach ($latestRecordResult as $eachRecord) {
                if (isset($detailWithKey['id-' . $eachRecord['lis_test_id']])) {
                    $payload = array_merge($detailWithKey['id-' . $eachRecord['lis_test_id']], [
                        'old_result' => $eachRecord['result'],
                        'old_test_name' => $eachRecord['test_name'],
                        'is_read' => false,
                        'created_date' => date("Y-m-d H:i:s"),
                        'is_integrasi' => true
                    ]);
                    $dataPayload[] = $payload;
                    $bucketNotification[] = array_merge($baseNotificationArray, [
                        'judulnotifikasi' => 'Update pemeriksaan ' . $payload['test_name'] . ' ( ' . $payload['his_reg_no'] . ')',
                        'isi_notifikasi' => 'Update hasil pemeriksaan ' . $payload['his_reg_no'] . ' : ' . $payload['old_test_name'] . '(' . $payload['old_result'] . ') Menjadi ' . $payload['test_name'] . '(' . $payload['result'] . ')',
                        'additional_data' => json_encode($payload)
                    ]);
                }
            }
            $existingNotificationBucket = Yii::$app->cache->get('radiologiNotification', []);
            if (empty($existingNotificationBucket)) {
                $existingNotificationBucket = [
                    'record' => [],
                    'totalUnread' => 0,
                ];
            }
            if (!empty($bucketNotification)) {
                Notifikasi::batchInsert($bucketNotification);
                $notificationPayload = Notifikasi::updateBucketNotificationRad();
                $notificationBucket = $notificationPayload['records'];
                $totalUnread = $notificationPayload['unread'];
                $mode = Yii::$app->params['mode'];
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'update-radiologi-' . $mode,
                    'message' => json_encode([
                        'newNotification' => $dataPayload,
                        'notificationBucket' => $notificationBucket,
                        'totalUnread' => $totalUnread,
                    ])
                ]);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update unread notification
     * 
     */
    public static function updateTotalUnread()
    {
        $notificationPayload = Notifikasi::updateBucketNotificationRad();
        $mode = Yii::$app->params['mode'];
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'update-radiologi-' . $mode,
            'message' => json_encode([
                'newNotification' => [],
                'flag' => 'update',
                'notificationBucket' => $notificationPayload['records'],
                'totalUnread' => $notificationPayload['unread'],
            ])
        ]);
        return $notificationPayload;
    }

    /**
     * Add notification from approval
     * 
     * @author : Asri Nurul
     */
    public static function addNotification($payload, $flagging)
    {
        $dataOrderan =  InfoOrderanRadView::find()->where([
            'pasienkirimkeunitlain_id' => $payload['pasienkeunitlain_id'],
            'pendaftaran_id' => $payload['pendaftaran_id']
        ])
        ->asArray()
        ->one();

        $user = Yii::$app->jwt->user->pegawai_id;
        $pegawai = Pegawai::find()->select(['pegawai_id', 'nama_pegawai'])->where(['pegawai_id' => $user])->asArray()->one();

        $baseNotificationArray = [
            'instalasi_id' => DocoConstants::INST_ID_RAD,
            'type' => 'new-orderrad',
            'modul_id' => DocoConstants::MODULE_RAD_ID,
            'tglnotifikasi' => date("Y-m-d H:i:s"),
            'is_read' => false,
            'user_id' => null,
            'created_date' => date("Y-m-d H:i:s"),
        ];

        if($flagging == 'neworder') {
            $message = 'Ada order dengan No '. $dataOrderan['no_rujukan'] . ' Atas nama Pasien ' .strtoupper($dataOrderan['nama_pasien']) .' yang perlu di approve';
        } 

        if($flagging == 'approve') {
            $message = 'Orderan dengan No '. $dataOrderan['no_rujukan'] .' sudah di proses oleh '. $pegawai['nama_pegawai'];
        }

        if($flagging == 'batalapprove') {
            $message = 'Order dengan No '. $dataOrderan['no_rujukan'] .' di batalkan oleh '. $pegawai['nama_pegawai'];
        }

        $payload = [
            'judulnotifikasi' => 'Radiologi',
            'isi_notifikasi' => $message,
            'created_date' => date("Y-m-d H:i:s"),
            'is_read' => false,
            'his_reg_no' => $dataOrderan['no_rujukan'],
            'nama_pasien' => $dataOrderan['nama_pasien'],
            'is_integrasi' => false,
            'tanggal_rujukan' => $dataOrderan['tgl_rujukan'],
            'nama_pegawai' => $pegawai['nama_pegawai'],
            'pegawai_id' => $pegawai['pegawai_id']
        ];

        $dataPayload[] = $payload;

        $bucketNotification[] = array_merge($baseNotificationArray, [
            'judulnotifikasi' => 'Radiologi',
            'isi_notifikasi' => $message,
            'additional_data' => json_encode($payload)
        ]);

        $existingNotificationBucket = Yii::$app->cache->get('radiologiNotification', []);
        if (empty($existingNotificationBucket)) {
            $existingNotificationBucket = [
                'record' => [],
                'totalUnread' => 0,
            ];
        }

        if (!empty($bucketNotification)) {
            Notifikasi::batchInsert($bucketNotification);
            $notificationPayload = Notifikasi::updateBucketNotificationRad();
            $notificationBucket = $notificationPayload['records'];
            $totalUnread = $notificationPayload['unread'];
            $mode = Yii::$app->params['mode'];
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'update-radiologi-' . $mode,
                'message' => json_encode([
                    'newNotification' => $dataPayload,
                    'notificationBucket' => $notificationBucket,
                    'totalUnread' => $totalUnread,
                ])
            ]);
        }
        return true;
    }
}