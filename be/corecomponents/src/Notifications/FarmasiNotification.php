<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\Notifications;

use Yii;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\models\NotifikasiFarmasi;

class FarmasiNotification extends BaseNotification {
    public static function newResep($payload) {
        $dataPayload = [
            'nama_pegawai' => isset($payload['nama_pegawai']) ? $payload['nama_pegawai'] : null,
            'tglreseptur' => isset($payload['tglreseptur']) ? $payload['tglreseptur'] : null,
            'reseptur_id' => isset($payload['reseptur_id']) ? $payload['reseptur_id'] : null,
            'ruangan_tujuan' => isset($payload['ruangan_tujuan']) ? $payload['ruangan_tujuan'] : null,
            'noresep' => isset($payload['noresep']) ? $payload['noresep'] : null,
            'enc_reseptur_id' => isset($payload['reseptur_id']) ? DocoHelpers::encrypt($payload['reseptur_id']) : null,
        ];

        $baseNotificationArray = [
            'instalasi_id' => DocoConstants::INSTALASI_FARMASI,
            'type' => 'new-reseptur',
            'modul_id' => DocoConstants::MODULE_FARMASI_ID,
            'tglnotifikasi' => date("Y-m-d H:i:s"),
            'is_read' => false,
            'user_id' => null,
            'created_date' => date("Y-m-d H:i:s"),
        ];

        $existingFarmasiNotification = Yii::$app->cache->get('farmasiNotification', []);
        if (empty($existingFarmasiNotification)) {
            $existingFarmasiNotification = [
                'record' => [],
                'totalUnread' => 0,
            ];
        }

        $notificationBucket = array_merge([$dataPayload], $existingFarmasiNotification['record']);
        $message = 'Terdapat reseptur baru dari dokter <strong>'.@$dataPayload['nama_pegawai'].'</strong> dengan No Reseptur: <strong>' . $dataPayload['noresep'].'</strong> di ruangan <strong>'.@$dataPayload['ruangan_tujuan'].'</strong>';
        $bucketNotification[] = array_merge($baseNotificationArray, [
            'judulnotifikasi' => 'Farmasi',
            'isi_notifikasi' => $message,
            'additional_data' => json_encode($dataPayload)
        ]);

        NotifikasiFarmasi::batchInsert($bucketNotification);
        $notification = NotifikasiFarmasi::updateBucketNotificationFarmasi();
        $payloadFarmasi = [
            'newNotification' => $dataPayload,
            'notificationBucket' => $notification['records'],
            'totalUnread' => $notification['unread']
        ];

        self::publish('order-farmasi', $payloadFarmasi);
        return true;
    }

    public static function updateNotif() {
        $notification = NotifikasiFarmasi::updateBucketNotificationFarmasi();
        $payloadFarmasi = [
            'newNotification' => [],
            'message' => null,
            'flag' => 'update',
            'notificationBucket' => $notification['records'],
            'totalUnread' => $notification['unread']
        ];

        self::publish('order-farmasi', $payloadFarmasi);
        return true;
    }
}