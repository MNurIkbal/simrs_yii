<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\RekapanBsl;
use app\modules\v1\payload\BslPayload;

class IntegrationsController extends DocoActiveController
{
    public $modelClass = '';

    /**
     * Untuk Kebutuhan Integerasi BSL
     * di execute after action
     * @var array
     */
    public $messageBroker = [
        'sync-recap-lab-examination' => [
            'services' => [
                'Mhg' => [
                    'BslBilling' => [
                        'payload' => ['pendaftaran_id', 'pasienkirimkeunitlain_id']
                    ]
                ]
            ]
        ],
        'sync-cancel-bill-lab' => [
            'services' => [
                'Mhg' => [
                    'BslCancelBill' => [
                        'payload' => ['id']
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-data-lab-examination"] = ["GET"];
        $verbs["sync-recap-lab-examination"] = ["GET"];
        $verbs["sync-cancel-bill-lab"] = ["GET"];
        $verbs["update-status-lab"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionUpdateStatusLab()
    {
        $request = Yii::$app->request;
        $syncId = $request->post('SyncIdApi');
        $payloadBsl = new BslPayload;
        $payloadBsl->SyncIdApi = $syncId;
        if (!$payloadBsl->validate()) {
            return DocoHelpers::callback(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payloadBsl->errors
            ]);
        }
        $idRekap = [];
        if (is_array($syncId)) {
            foreach ($syncId as $value) {
                if (is_string($value)) {
                    $idEncry = DocoHelpers::decrypt($value);
                    if (is_numeric($idEncry)) {
                        $idRekap[] = $idEncry;
                    }
                }
            }
        } else {
            $idEncry = DocoHelpers::decrypt($syncId);
            if (is_numeric($idEncry)) {
                $idRekap[] = $idEncry;
            }
        }

        if (!empty($idRekap)) {
            RekapanBsl::updateAll([
                'is_sent' => true,
                'is_sending' => true,
            ], ['id' => $idRekap]);
        }

        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM_DATA);
    }

    public function actionGetDataLabExamination()
    {
        $qRekapBsl = RekapanBsl::find()->andWhere([
            'is_sent' => false,
            'is_sending' => false,
        ])->asArray()->all();
        $listRekap = [];
        foreach ($qRekapBsl as $value) {
            $payload = $value['payload'];
            if (empty($payload)) continue;
            $payload = json_decode($payload, true);
            $payload['SyncIdApi'] = DocoHelpers::encrypt($value['id']);
            $listRekap[] = $payload;
        }
        return $listRekap;
    }

    public function actionSyncRecapLabExamination($pendaftaran_id = null, $pasienkirimkeunitlain_id = null)
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }

    public function actionSyncCancelBillLab($id)
    {
        return DocoHelpers::callback(DocoMessages::KEY_SUC_SYSTEM);
    }
}