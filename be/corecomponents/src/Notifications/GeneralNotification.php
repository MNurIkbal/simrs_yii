<?php

namespace Doco\Notifications;

use Doco\models\Notifikasi;
use Yii;

class GeneralNotification extends BaseNotification
{
    /**
     * this function will update notification based on user id
     * 
     * @param String $userId
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function updateNotificationByUser($userId = null)
    {
        if (!empty($userId) || !empty(Yii::$app->jwt->user->loginpemakai_id)) {
            if (empty($userId)) {
                $userId = Yii::$app->jwt->user->loginpemakai_id;
            }
            $newNotification = Notifikasi::generalUserNotification();
            self::publish('update-notification', [
                'notificationBucket' => $newNotification['records'],
                'totalUnread' => $newNotification['totalUnread'],
                'userId' => $userId
            ]);
            return true;
        } else {
            return false;
        }
    }
}
