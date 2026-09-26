<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\models;

use Yii;

class NotifikasiFarmasi extends Notifikasi {
    public static  function notificationUpdate() {
        return [
            'unread' => self::unreadNotificationByType('new-reseptur'),
            'records' => self::find()
                ->select([
                    'notifikasi_id',
                    'tglnotifikasi',
                    'judulnotifikasi',
                    'isi_notifikasi',
                    'type',
                    'user_id',
                    'is_read',
                    'additional_data'
                ])
                ->andWhere([
                    'type' => 'new-reseptur',
                ])
                ->limit(10)
                ->orderBy([
                    'tglnotifikasi' => SORT_DESC
                ])
                ->asArray()
                ->all()
        ];
    }

    public static function updateBucketNotificationFarmasi() {
        $notificationRecord = self::notificationUpdate();
        Yii::$app->cache->set('farmasiNotification', [
            'record' => $notificationRecord['records'],
            'totalUnread' => $notificationRecord['unread']
        ]);
        return $notificationRecord;
    }
}