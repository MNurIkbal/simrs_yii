<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\GtPembayaranView;
use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\SaleOrderLine;
use app\modules\v1\models\IntObatAlkesView;
use app\modules\v1\models\GtAkuntansi;
use app\modules\v1\payload\SerconPayload;
use Doco\components\DocoMessages;
use Doco\components\DocoHelpers;
use Doco\Services\InternalService;

class IntegrasiAkuntingController extends IntegrationController
{

    CONST GT_AKUNTASI_T = "gt_akuntansi_t";

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionTagihan()
    {
        $sModel = SaleOrderLine::find()
            ->where([
                'OR',
                ['is_sending_akuntansi' => null],
                ['is_sending_akuntansi' => false]
            ])
            ->findBySendingBill()
            ->orderByTglProses()
            ->orderBy([
                "type_line" => SORT_DESC
            ])
            ->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => parent::ERROR_MESSAGES
            ]);
        }

        $listData = $listId = [];
        foreach ($sModel as $key => $value) {
            $idRekap = $value['id'];
            $listId[] = $idRekap;
            $row = $value;
            $listData[] = $row;
        }
        $this->updateIsSending(parent::TINDAKAN_REKAP, $listId);
        return $listData;
    }

    private function updateIsSending($tabel, $id, $attrIsSend = 'is_sending')
    {
        $this->updateSync(
            $tabel, 
            [
                $attrIsSend => true,
                'is_sending_akuntansi' => true
            ], 
            ['id' => $id]
        );
    }

    private function updateIsSent($tabel, $id, $attr)
    {
        $this->updateSync(
            $tabel, 
            $attr, 
            ['id' => $id]
        );
    }

    private function updateSync($tabel, $attr = [], $condition = [])
    {
        Yii::$app->db->createCommand()->update($tabel,$attr, $condition)->execute();
    }

    public function actionUangMuka()
    {
        $sModel = GtPembayaranView::find()
        ->where([
            "is_send" => false,
            "is_sending" => false,  
        ])
        ->andWhere(['not', ['no_referensi' => null]])
        ->limit(100)
        ->asArray()
        ->all();   

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => parent::ERROR_MESSAGES
            ]);
        }  

        $listData = $listId = [];
        foreach ($sModel as $key => $value) {
            $idRekap = $value['no_referensi'];
            $listId[] = [
                $idRekap, false, true, $value['edclist_kode']
            ];
            $row = $value;
            $listData[] = $row;
        }

        $this->actionUpdateIsSendingAkuntansi(self::GT_AKUNTASI_T,$listId);
        return $listData;
    }

    public function actionUpdateIsSendingAkuntansi($table, $attr)
    {
        Yii::$app->db->createCommand()->batchInsert($table,["no_referensi","is_send", "is_sending", "edclist_kode"], $attr)->execute();
    }

    public function actionObatPasien()
    {
        $sModel = IntObatAlkesView::find()
            ->where([
                'OR',
                ['is_sending_akuntansi' => null],
                ['is_sending_akuntansi' => false]
            ])
            ->findBySendingBill()
            ->orderByTglProses()
            ->getDataArray();

        if (empty($sModel)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => self::ERROR_MESSAGES
            ]);
        }

        $listId = ArrayHelper::getColumn($sModel, 'id');
        $this->updateIsSending(self::OBAT_REKAP, $listId);

        $cekJumlah = IntObatAlkesView::find()
            ->where([
                'OR',
                ['is_sending_akuntansi' => null],
                ['is_sending_akuntansi' => false]
            ])
            ->findBySendingBill()
            ->orderByTglProses()
            ->count();
        if(!empty($cekJumlah)){
            $listSending = [
                'Odoo' => ['Saleorderlineobat'=>[]]
            ];
            // (new InternalService)->sendTo($listSending);
        }

        return $sModel;
    }

    public function actionCallBackTagihan()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $id = !empty($value['id']) ? $value['id'] : null;
                $isError = !empty($value['is_error']) ? $value['is_error'] : false;
                $syncResponse = !empty($value['response']) ? $value['response'] : null;

                if (!empty($id)) {
                    $payload = [
                        'is_sent' => $isError ? false : true,
                        'is_send_akuntansi' => $isError ? false : true,
                        'id_sync_sercon' => $uidSercon,
                        'sync_respon_akuntansi' => json_encode($syncResponse),

                    ];

                    $this->updateIsSent(parent::TINDAKAN_REKAP, $id, $payload);
                }
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    public function actionCallBackObatPasien()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $uidSercon = $payload->uid;
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $id = !empty($value['id']) ? $value['id'] : null;
                $isError = !empty($value['is_error']) ? $value['is_error'] : false;
                $syncResponse = !empty($value['response']) ? $value['response'] : null;

                if (!empty($id)) {
                    $payload = [
                        'is_sent' => $isError ? false : true,
                        'is_send_akuntansi' => $isError ? false : true,
                        'id_sync_sercon' => $uidSercon,
                        'sync_respon_akuntansi' => json_encode($syncResponse),

                    ];

                    $this->updateIsSent(parent::OBAT_REKAP, $id, $payload);
                }
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    /**
     * Berfungsi untuk callback data integrasi Pembayaran
     * @return array $response
     */
    public function actionCallbackPembayaran()
    {
        $request = Yii::$app->request;
        $payload = new SerconPayload;
        $payload->attributes = $request->post();
        $data = $payload->data;
        if (!empty($data) && is_array($data)) {
            foreach ($data as $value) {
                $noReferensi = !empty($value['no_referensi']) ? $value['no_referensi'] : null;
                $isError = !empty($value['is_error']) ? $value['is_error'] : false;
                $syncResponse = !empty($value['response']) ? $value['response'] : null;
                $payloadRes = !empty($value['payload']) ? $value['payload'] : [];
                $edclist_kode = ArrayHelper::getValue($payloadRes, 'edclist_kode');
                if (!empty($noReferensi)) {
                    $payload = [
                        'is_send' => $isError ? false : true,
                        'sync_respon' => json_encode($syncResponse),

                    ];
                    $condition = [
                        'no_referensi' => $noReferensi,
                        'edclist_kode' => $edclist_kode
                    ];

                    // Update data sync ke gt_akuntansi_t
                    $this->updateIntegration($payload, $condition);
                }
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        }

        return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
            'text' => self::ERROR_MESSAGES
        ]);
    }

    /**
     * Berfungsi untuk update data integrasi di gt_akuntansi_t
     * @return array $results
     */
    private function updateIntegration($data, $condition)
    {
        $results = GtAkuntansi::updateAll($data, $condition);
        
        return $results;
    }
}
