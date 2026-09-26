<?php

namespace app\modules\v1\actions\TraPemeriksaan;

use Yii;
use yii\helpers\ArrayHelper;

class OrderRajalAction extends BaseCurrentAction
{
    private static function convertCatatan($posts)
    {
        if (!empty($posts['list_order'])) {
            $posts['list_order'] = json_decode($posts['list_order'], true);
            foreach($posts['list_order'] as $key => $value){
                $orderList = ArrayHelper::getValue($value, 'orders');
                foreach($orderList as $k => $v){
                    $catatan = ArrayHelper::getValue($v, 'catatan');
                    if($catatan){
                        $explode = explode(': ', $posts['list_order'][$key]['orders'][$k]['catatan']);
                        $posts['list_order'][$key]['orders'][$k]['catatan'] = $explode[1];
                    }
                }
            }
            $posts['list_order'] = isset($posts['list_order']) ? json_decode($posts['list_order'], true) : null;
        }
        return $posts;
    }

    private static function getPayloadOrderChilds($listOrders)
    {
        $results = [];
        foreach ($listOrders as $key => $value) {
            unset($value['is_paketfisio']);
            $results[] = $value;
        }
        return $results;
    }

    private static function getPayloadOrderParent($listOrders)
    {
        $parentTarifTindakanId = ArrayHelper::getValue($listOrders, 'tariftindakan_id');
        $parentDaftarTindakanId = ArrayHelper::getValue($listOrders, 'parentdaftartindakan_id');
        $parentOrder = [
            'tariftindakan_id' => $parentTarifTindakanId,
            'daftartindakan_id' => $parentDaftarTindakanId,
            'is_cyto' => null,
            'is_paket' => null,
            'golongan_id' => null,
            'kegiatan_id' => null,
            'is_paketfisio' => true
        ];
        return $parentOrder;
    }

    private static function convertPayload($payload)
    {
        if (!empty($payload['list_order'])) {
            $payload['list_order'] = json_decode($payload['list_order'], true);
            $listOrders = ArrayHelper::getValue($payload, 'list_order');
            foreach($listOrders as $key => $value){
                $payload['list_order'][$key]['is_paketfisio'] = false;
                $orderList = ArrayHelper::getValue($value, 'orders');
                $isPaketChildFirst = ArrayHelper::getValue($orderList, '0.is_paketfisio');
                $payload['list_order'][$key]['is_paketfisio'] = $isPaketChildFirst || ($isPaketChildFirst == 1);
                if ($payload['list_order'][$key]['is_paketfisio']) {
                    $orderParent = self::getPayloadOrderParent($orderList[0]);
                    $orderChilds = self::getPayloadOrderChilds($orderList);
                    $fisioPaketOrder = [
                        'paket' => $orderParent,
                        'childs' => $orderChilds
                    ];
                    $payload['list_order'][$key]['orders'] = $fisioPaketOrder;
                }
            }
            $payload['list_order'] = json_encode($payload['list_order']);
        }
        return $payload;
    }

    private static function hitApiFisio($payload)
    {
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $request = $restFisio->post('order/save-rajal', [
            'form_params' => $payload
        ]);
        $response = json_decode($request->getBody(), true);
        return $response;
    }

    public static function saveOrder($payload)
    {
        // $payload = self::convertCatatan($payload);
        $normalizePayload = self::convertPayload($payload);
        $response = self::hitApiFisio($normalizePayload);
        return $response;
    }
}
