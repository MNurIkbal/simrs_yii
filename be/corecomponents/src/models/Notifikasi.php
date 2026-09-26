<?php

namespace Doco\models;

use Yii;

class Notifikasi extends \Doco\components\DocoActiveRecord
{
    /** @var Array $exceptionType */
    protected static $exceptionType = [
        'update-expertise'
    ];

    public static function tableName()
    {
        return 'notifikasi_r';
    }

    /**
     * This function will return rules
     *
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function rules()
    {
        return [
            [
                [
                    'instalasi_id',
                    'modul_id',
                    'tglnotifikasi',
                    'judulnotifikasi',
                    'isi_notifikasi',
                    'type',
                    'user_id',
                    'is_read',
                    'lama_harinotif',
                    'additional_data',
                    'created_date',
                    'created_by',
                    'modified_count',
                    'last_modified_date',
                    'last_modified_by',
                    'is_deleted',
                    'is_active',
                    'deleted_date',
                    'deleted_by',
                ], 'safe',
            ],
        ];
    }

    /**
     * Get latest and bucket notification update expertise
     * 
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static  function notificationUpdateExpertise()
    {
        return [
            'unread' => self::unreadNotificationByType('update-expertise'),
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
                    'type' => 'update-expertise',
                ])
                ->limit(10)
                ->orderBy([
                    'tglnotifikasi' => SORT_DESC
                ])
                ->asArray()
                ->all()
        ];
    }

       /**
     * Get latest and bucket notification update new orderlab
     * 
     * @return Array
     * @author : Maulana Muhammad Rizky
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static  function notificationGetNewOrdeLab()
    {
        return [
            'unread' => self::unreadNotificationByType('new-orderlab'),
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
                    'type' => 'new-orderlab',
                ])
                ->limit(10)
                ->orderBy([
                    'tglnotifikasi' => SORT_DESC
                ])
                ->asArray()
                ->all()
        ];
    }

    public static  function notificationGetNewOrdeRad()
    {
        return [
            'unread' => self::unreadNotificationByType('new-orderrad'),
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
                    'type' => 'new-orderrad',
                ])
                ->limit(10)
                ->orderBy([
                    'tglnotifikasi' => SORT_DESC
                ])
                ->asArray()
                ->all()
        ];
    }

    /**
     * This function return total unread notification
     * 
     * @param String $type
     * @return Integer/Number
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function unreadNotificationByType($type)
    {
        return self::find()
            ->select([
                'notifikasi_id'
            ])
            ->andWhere([
                'type' => $type,
                'is_read' => false
            ])
            ->count();
    }

    /**
     * This function will update cache notification
     * 
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function updateBucketNotificationLab()
    {
        $record = [];
        $unread = 0;
        $notificationRecord = self::notificationUpdateExpertise();
        $notificationOrderlab = self::notificationGetNewOrdeLab();

        $unread += $notificationRecord['unread'];
        $unread += $notificationOrderlab['unread'];

        foreach ($notificationRecord['records'] as $key => $value) {
            $record[] = $value;
        }

        foreach ($notificationOrderlab['records'] as $key => $value) {
            $record[] = $value;
        }

        Yii::$app->cache->set('laboratoriumNotification', [
            'record' => $record,
            'totalUnread' => $unread
        ]);

        return [
            'records' => $record,
            'unread' => $unread
        ];
    }

    public static function updateBucketNotificationRad()
    {
        $record = [];
        $unread = 0;
        $notificationRecord = self::notificationUpdateExpertise();
        $notificationOrderrad = self::notificationGetNewOrdeRad();

        $unread += $notificationRecord['unread'];
        $unread += $notificationOrderrad['unread'];

        foreach ($notificationRecord['records'] as $key => $value) {
            $record[] = $value;
        }

        foreach ($notificationOrderrad['records'] as $key => $value) {
            $record[] = $value;
        }

        Yii::$app->cache->set('radiologiNotification', [
            'record' => $record,
            'totalUnread' => $unread
        ]);

        return [
            'records' => $record,
            'unread' => $unread
        ];
    }

    /**
     * This function will return bucket notification and not read
     * 
     * @param String var
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function generalUserNotification($userId = null)
    {
        if (empty($userId) && empty(Yii::$app->jwt->user->loginpemakai_id)) {
            $bucket = [];
            $unreadNotif = 0;
            \Yii::error([
                "MessageNotification" => "USER ID NOT PROVIDED Notifikasi::generalUserNotification"
            ]);
        } else {
            if (empty($userId)) {
                $userId = Yii::$app->jwt->user->loginpemakai_id;
            }
            $bucket = self::find()
                ->select([
                    'notifikasi_r.notifikasi_id',
                    'notifikasi_r.created_date as tglnotifikasi',
                    'judulnotifikasi',
                    'isi_notifikasi',
                    'type',
                    'modul_k.modul_namalainnya as modul_nama',
                    // new \yii\db\Expression('CASE WHEN notifikasistatus_t.notifikasistatus_id IS NOT NULL THEN true ELSE false END AS is_read'),
                    'is_read',
                    'notifikasi_r.additional_data'
                ])
                ->andWhere(['NOT IN', 'type', self::$exceptionType])
                ->andWhere(['user_id' => $userId])
                ->andWhere('CASE WHEN "type" = \'fasting-reminder\' THEN DATE(tglnotifikasi)<=DATE(NOW()) ELSE true END')
                // ->join('LEFT JOIN', 'notifikasistatus_t', 'notifikasistatus_t.notifikasi_id=notifikasi_r.notifikasi_id AND notifikasistatus_t.loginpemakai_id=' . $userId)
                ->join('JOIN', 'modul_k', 'modul_k.modul_id=notifikasi_r.modul_id')
                ->limit(10)
                ->orderBy(['notifikasi_r.created_date' => SORT_DESC])
                ->asArray()
                ->all();
            $unreadNotif = self::find()
                ->select([
                    'notifikasi_r.notifikasi_id',
                ])
                ->andWhere(['user_id' => $userId])
                ->andWhere(['is_read' => false])
                ->andWhere('CASE WHEN "type" = \'fasting-reminder\' THEN DATE(tglnotifikasi)<=DATE(NOW()) ELSE true END')
                // ->join('LEFT JOIN', 'notifikasistatus_t', 'notifikasistatus_t.notifikasi_id=notifikasi_r.notifikasi_id AND notifikasistatus_t.loginpemakai_id=' . $userId)
                ->asArray()
                ->count();
        }
        return [
            'records' => $bucket,
            'totalUnread' => $unreadNotif
        ];
    }

    /**
     * This function will set is_read
     * 
     * @param String $notifikasi_id
     * @param String $user_id
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function readNotification($notifikasi_id, $user_id = null)
    {
        if (!empty($user_id) || !empty(Yii::$app->jwt->user->loginpemakai_id)) {
            if (empty($user_id)) {
                $user_id = Yii::$app->jwt->user->loginpemakai_id;
            }
            $notificationRecord = self::find()->select(['notifikasi_id', 'user_id', 'is_read', 'tglnotifikasi'])->andWhere(['notifikasi_id' => $notifikasi_id, 'user_id' => $user_id])->one();
            $notificationRecord->is_read = true;
            $notificationRecord->save();
            return true;
        } else {
            return false;
        }
    }
}
