<?php

namespace app\components\Notifications;

use app\components\DocoController;
use app\components\Traits\ControllerHelperTrait;
use Yii;

class AmbulanceNotification extends BaseNotification
{
    use ControllerHelperTrait;
    /**
     * Publish new notification to ambulan
     * 
     * @param String $message
     * @param Array $payload
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function newOrder($payload)
    {
        $dataPayload = [
            'pesanambulan_id' => isset($payload['pesanambulan_id']) ? $payload['pesanambulan_id'] : null,
            'no_pesanambulan' => isset($payload['no_pesanambulan']) ? $payload['no_pesanambulan'] : null,
            'tujuan_pasien' => isset($payload['tujuan_pasien']) ? $payload['tujuan_pasien'] : null,
            'tgl_pesanambulan' => isset($payload['tgl_pesanambulan']) ? $payload['tgl_pesanambulan'] : null,
            'created_date' => isset($payload['created_date']) ? $payload['created_date'] : null,
            'ambulan_id' => isset($payload['ambulan_id']) ? $payload['ambulan_id'] : null,
            'status_pesan' => isset($payload['status_pesan']) ? $payload['status_pesan'] : null,
            'no_polisi' => isset($payload['no_polisi']) ? $payload['no_polisi'] : null
        ];
        $existingAmbulanceNotification = Yii::$app->cache->get('ambulanceNotification', []);
        if (empty($existingAmbulanceNotification)) {
            $existingAmbulanceNotification = [
                'record' => [],
                'totalNotProcess' => 0,
            ];
        }
        $notificationBucket = array_merge([$dataPayload],$existingAmbulanceNotification['record']);
        $mode = Yii::$app->params->mode;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'order-ambulan-' . $mode,
            'message' => json_encode([
                'message' => 'Permintaan Ambulan baru, pemesanan untuk no polisi ' . $dataPayload['no_polisi'] . ' dengan No. Pemesanan : ' . $dataPayload['no_pesanambulan'],
                'notificationBucket' => $notificationBucket,
                'totalNotProcess' => $existingAmbulanceNotification['totalNotProcess'] + 1,
            ])
        ]);
        Yii::$app->cache->set('ambulanceNotification', [
            'record' => $notificationBucket,
            'totalNotProcess' => $existingAmbulanceNotification['totalNotProcess'] + 1
        ]);
        return true;
    }

    /**
     * This function update new notification
     * 
     * @return Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function updateNotif()
    {
        $cacheNotif = Yii::$app->cache;
        $notification = (new self)->guzzleExec(Yii::$app->docoRest->ambulan, [
            'url' => 'allow/ambulance-notification'
        ]);
        $cacheNotif->set('ambulanceNotification', $notification);
        $mode = Yii::$app->params->mode;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'order-ambulan-' . $mode,
            'message' => json_encode([
                'message' => null,
                'flag' => 'update',
                'notificationBucket' => $notification['record'],
                'totalNotProcess' => $notification['totalNotProcess'],
            ])
        ]);
        return true;
    }
}