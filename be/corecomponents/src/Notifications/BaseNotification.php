<?php

namespace Doco\Notifications;

use Yii;

class BaseNotification
{
    /**
     * This function will publish redis
     * 
     * @param String $instanceName
     * @param Array $payload
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function publish($instanceName, $payload)
    {
        $mode = Yii::$app->params['mode'];
        return Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => $instanceName .  '-' . $mode,
            'message' => json_encode($payload)
        ]);
    }
}
