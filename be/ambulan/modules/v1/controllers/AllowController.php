<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\PesanAmbulan;
use Yii;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstants;

class AllowController extends \Doco\components\DocoActiveController
{
    use ConfigTrait;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["get-konfigantrian-by-jenis"] = ["POST", "GET"];
        $verbs["get-layarantrian-by-jenis"] = ["POST", "GET"];
        $verbs["get-group-konfig"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * This API return 10 latest order ambulance and total unprocess
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionAmbulanceNotification()
    {
        // first get 10 latest record
        $latestRecord = PesanAmbulan::find()
            ->select([
                'pesanambulan_t.pesanambulan_id',
                'pesanambulan_t.no_pesanambulan',
                'pesanambulan_t.tujuan_pasien',
                // 'pesanambulan_t.tgl_pesanambulan',
                'pesanambulan_t.created_date as tgl_pesanambulan',
                'pesanambulan_t.ambulan_id',
                'pesanambulan_t.status_pesan',
                'ambulan_m.no_polisi'
            ])
            ->join('JOIN', 'ambulan_m', 'ambulan_m.ambulan_id=pesanambulan_t.ambulan_id')
            ->orderBy([
                'pesanambulan_t.created_date' => SORT_DESC
            ])
            ->andWhere([
                'status_pesan' => DocoConstants::BELUM_PROSES
            ])
            ->limit(10)
            ->asArray()
            ->all();
        $notProcessedOrder = PesanAmbulan::find()
            ->andWhere([
                'status_pesan' => DocoConstants::BELUM_PROSES
            ])
            ->count();
        return [
            'record' => $latestRecord,
            'totalNotProcess' => $notProcessedOrder
        ];
    }
}
