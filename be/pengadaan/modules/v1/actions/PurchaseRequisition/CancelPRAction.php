<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\PengadaanComponent;
use app\modules\v1\models\PurchaseRequisitionDetail;
use app\modules\v1\models\PurchaseRequisition;
use app\modules\v1\models\PurchaseRequisitionBarang;
use app\modules\v1\models\PurchaseRequisitionBarangDetail;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class CancelPRAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $purchasereq_id = $request->post('purchasereq_id');
        $details = $request->post('details');
        $type = $request->post('type');
        $tableName = "purchasereqdetail_t";
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $type = strtoupper($type);
            if($type == 'OBAT'){
                $tablePR = new PurchaseRequisition;
                $tablePRdetail = new PurchaseRequisitionDetail;
                $tableName = "purchasereqdetail_t";
                $fieldPRkey = 'purchasereq_id';
                $fieldPRdetailkey = 'purchasereqdetail_id';
            }else{
                $tablePR = new PurchaseRequisitionBarang;
                $tablePRdetail = new PurchaseRequisitionBarangDetail;
                $tableName = "purchasereqbrgdetail_t";
                $fieldPRkey = 'purchasereqbrg_id';
                $fieldPRdetailkey = 'purchasereqbrgdetail_id';
            }

            $purchasereq = $tablePR::find()->where([$fieldPRkey => $purchasereq_id])->one();
            if($purchasereq->status == DocoConstants::VAR_SUDAH_PO) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'PR sudah di generate',
                ];
            }

            $arr_purchasereqdetail_id = array_column($details, 'purchasereqdetail_id');
            $existingDetail = $tablePRdetail::find()->where([
                        'IN', $fieldPRdetailkey, $arr_purchasereqdetail_id
                        ])->asArray()->all();
            $newDetail = [];
            $existSudahPO = false;
            foreach ($existingDetail as $key => $value) {
                if($value['status'] == DocoConstants::VAR_SUDAH_PO) {
                    $existSudahPO = true;
                }
                $newDetail[$value[$fieldPRdetailkey]] = $value;
            }

            $updateData = $updateCondition = [];
            foreach ($details as $key => $value) {
                if($newDetail[$value['purchasereqdetail_id']]['status'] != DocoConstants::VAR_SUDAH_PO) {
                    $updateData['status'][] = DocoConstants::VAR_CANCEL_PR;
                    $updateData['alasan'][] = $value['alasan_cancel'];
                    $updateCondition[$fieldPRdetailkey][] = $value['purchasereqdetail_id'];
                }
            }

            if(count($updateData) <= 0) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Tidak ada data yang bisa dibatalkan',
                ];
            }

            $cancelPR = PengadaanComponent::batchUpdate($tableName, $updateData, $updateCondition);
            if(!$cancelPR) {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Gagal cancel PR detail',
                ];
            }

            // cek jika semua detail di cancel, update header jadi cancel PR
            $newDetail = $tablePRdetail::find()->where([
                        $fieldPRkey => $purchasereq_id
                    ])->asArray()->all();

            $allCanceled = true;
            foreach ($newDetail as $key => $value) {
                if($value['status'] != DocoConstants::VAR_CANCEL_PR && $value['status'] != DocoConstants::VAR_SUDAH_PO){
                    $allCanceled = false;
                }
            }

            if($allCanceled) {
                $header = $tablePR::findOne($purchasereq_id);
                if($header->status == DocoConstants::VAR_PO_SEBAGIAN){
                    $header->status = DocoConstants::VAR_SUDAH_PO;
                } else {
                    $header->status = DocoConstants::VAR_CANCEL_PR;
                }
                if(!$header->save()){
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => 'Gagal cancel PR',
                    ];
                }
            }

            $transaction->commit();

            if($existSudahPO) {
                $message = "Terdapat obat yang Sudah PO";
                $statusCode = 206;
            } else {
                $message = "Data berhasil disimpan";
                $statusCode = 200;
            }

            return [
                'message' => $message,
                'statusCode'  => $statusCode,
                'data' => [
                    'count_cancel' => count($updateData['alasan'])
                ]
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
