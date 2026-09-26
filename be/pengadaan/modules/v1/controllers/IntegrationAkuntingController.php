<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\GtAkuntansi;
use app\modules\v1\models\GtPurchaseOrderView;
use app\modules\v1\models\GtPurchaseOrderDetailView;
use app\modules\v1\payload\SerconPayload;

class IntegrationAkuntingController extends IntegrationController
{
    public $modelClass = '';
    const REF_PO = 'no_penerimaan';
    const REF_DETAIL_PO = 'penerimaanobatdetail_id';
    const TYPE_DETAIL_PO = 'penerimaan_po';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['get-purchase-order'] = ["GET"];
        $verbs['callback-purchase-order'] = ["POST"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['access']);
        return $behaviors;
    }

    /**
     * Berfungsi untuk get data PO yang belum dikirim sebagai integrasi akunting
     * @return array $model
     */
    public function actionGetPurchaseOrder()
    {
        $model = GtPurchaseOrderView::find()
            ->where([
                'OR',
                ['is_sending' => null],
                ['is_sending' => false]
            ])->orderBy(['bill_at' => SORT_ASC])
            // ->limit(100)
            ->asArray()
            ->all();

        if (empty($model)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => 'Data tidak ditemukan'
            ]);
        }
    
        // Insert data ke gt_akuntansi_t
        $this->insertIntegration($model, self::REF_PO);

        return $model;
    }

    /**
     * Berfungsi untuk get data detail PO yang belum dikirim sebagai integrasi akunting
     * @return array $model
     */
    public function actionGetPurchaseOrderDetail()
    {
        $request = Yii::$app->request;
        $type_account = self::TYPE_DETAIL_PO;
        // Pembeda antara PO obat dan barang
        $type = $request->get('type');
        if ($type) {
            $type_account = self::TYPE_DETAIL_PO . '_' . $type;
        }
        
        $model = GtPurchaseOrderDetailView::find()
            ->where([
                'OR',
                ['is_sending' => null],
                ['is_sending' => false]
            ])->andWhere([
                'item' => $type
            ])->orderBy(['created_at' => SORT_ASC])
            ->limit(100)
            ->asArray()
            ->all();

        if (empty($model)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => 'Data tidak ditemukan'
            ]);
        }

        // Insert data ke gt_akuntansi_t
        $this->insertIntegration($model, self::REF_DETAIL_PO, $type_account);

        return $model;
    }

    /**
     * Berfungsi untuk callback data integrasi PO
     * @return array $response
     */
    public function actionCallbackPurchaseOrder()
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
                if (!empty($noReferensi)) {
                    $payload = [
                        'is_send' => $isError ? false : true,
                        'sync_respon' => json_encode($syncResponse),

                    ];
                    $condition = [
                        'no_referensi' => $noReferensi
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
     * Berfungsi untuk callback data integrasi detail PO
     * @return array $response
     */
    public function actionCallbackPurchaseOrderDetail()
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
                $type_account = self::TYPE_DETAIL_PO;
                $type = !empty($value['type']) ? $value['type'] : null;
                if ($type) {
                    $type_account = self::TYPE_DETAIL_PO . '_' . $type;
                }
                

                if (!empty($noReferensi)) {
                    $payload = [
                        'is_send' => $isError ? false : true,
                        'sync_respon' => json_encode($syncResponse),

                    ];
                    $condition = [
                        'no_referensi' => $noReferensi,
                        'type_account' => $type_account,
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
     * Berfungsi untuk insert data integrasi ke gt_akuntansi_t
     * @return array $results
     */
    private function insertIntegration($data, $no_referensi, $type_account = null)
    {
        $results = $payload = $ref_numbers = [];
        // Create payload untuk insert atau update ke gt_akuntansi_t
        foreach ($data as $key => $value) {
            if ($value['is_sending'] === null) {
                // Jika data belum ada
                $payload[] = [
                    'no_referensi' => (string) $value[$no_referensi],
                    'type_account' => $type_account,
                    'is_send' => false,
                    'is_sending' => true,
                    'is_deleted' => false,
                    'is_active' => true,
                ];
            } else if ($value['is_sending'] === false) {
                // Jika data sudah ada
                $ref_numbers[] = $value[$no_referensi];
            }
        }

        // Insert new data
        if (!empty($payload)) {
            $results['new_data'] = GtAkuntansi::batchInsert($payload);
        }
        // Update existing data
        if (!empty($ref_numbers)) {
            $results['updated_data'] = GtAkuntansi::updateAll(
                    ['is_sending' => true], 
                    ['AND',
                        ['no_referensi' => $ref_numbers],
                        ['type_account' => $type_account],
                    ]
                );
        }
        
        return $results;
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
