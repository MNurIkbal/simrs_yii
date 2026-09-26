<?php

/**
 * @author Rizal
 * @description backend transaksi pasien inap
 **/

namespace app\modules\v1\controllers;

use app\modules\v1\models\KamarTempatTidur;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use Doco\components\DocoConstants;
use Doco\models\bpjs\BpjsAplicare;
use Doco\rabbitmq\RabbitBgProcess;
use yii\helpers\ArrayHelper;

class SyncKamarApplicaresController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET", 'PUT'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    /**
     * Sync kamar applicares.
     * 
     * @author Maulana Muhammad Rizky.
     */
    public function actionSyncKamar()
    {
        $request = Yii::$app->request;
        $chunk = $request->get('chunk', 10);
        $tempatTidur = (new \yii\db\Query())
            ->select([
                'kamarruangan_id'
            ])
            ->from('kamartempattidur_m')
            ->where([
                'is_rekapkinerjaprofesi' => true,
                'is_active' => true
            ])
            ->orderBy([
                'kamarruangan_id' => SORT_ASC
            ]);
        foreach ($tempatTidur->batch($chunk) as $rows) {
            (new RabbitBgProcess())->send([
                'type_sinkron' => "sinkron",
                'data' => $rows
            ], 'sync_applicares', 'sync_kamar_applicares');
        }
        return ['status' => 200, 'message' => "Sync data behasil"];
    }

    public function actionSyncDeleteKamar()
    {
        $request = Yii::$app->request;
        $chunk = $request->get('chunk', 10);
        $query = (new \yii\db\Query())
        ->select([
            'kamarruangan_m.kamarruangan_id',
            'klasifikasikamar_m.kodekelas_aplicare',
            'kamarruangan_m.kamarruangan_kode'
        ])
        ->from('kamarruangan_m')
        ->join(
            'JOIN',
            'klasifikasikamar_m',
            'kamarruangan_m.klasifikasikamar_id = klasifikasikamar_m.klasifikasikamar_id'
        )
        ->leftJoin(
            'kamartempattidur_m',
            'kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id AND kamartempattidur_m.is_deleted = FALSE AND kamartempattidur_m.is_active = TRUE AND kamartempattidur_m.is_rekapkinerjaprofesi = TRUE'
        )
        ->groupBy([
            'kamarruangan_m.kamarruangan_id',
            'klasifikasikamar_m.kodekelas_aplicare',
            'kamarruangan_m.kamarruangan_kode',
            'kamarruangan_m.is_active',
            'kamarruangan_m.is_rekapkinerjaprofesi',
        ])
        ->having('kamarruangan_m.is_active = FALSE OR kamarruangan_m.is_rekapkinerjaprofesi = FALSE OR COUNT(kamartempattidur_m.kamartempattidur_id) = 0')
        ->orderBy('kamarruangan_m.kamarruangan_id ASC');

        foreach ($query->batch($chunk) as $rows) {
            (new RabbitBgProcess())->send([
                'type_sinkron' => "sinkron",
                'data' => $rows
            ], 'sync_delete_applicares', 'sync_delete_kamar_applicares');
        }
        return ['status' => 200, 'message' => "Sync data behasil"];
    }
}
