<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use Doco\components\DocoConstants;
use Doco\components\BatchUpdate;
use app\modules\v1\models\PurchaseRequisition;
use app\modules\v1\models\PurchaseRequisitionBarang;
use app\modules\v1\models\PurchaseRequisitionDetail;
use app\modules\v1\models\PurchaseRequisitionBarangDetail;

class ApprovingProcessAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $approved_by = Yii::$app->user->identity->pegawai_id;
        $id = $request->post('id', null);
        $type = $request->post('type', DocoConstants::JENIS_OBAT);
        $list = $request->post('list', []);

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if(strtolower($type) == DocoConstants::JENIS_OBAT) {
                $model = PurchaseRequisition::find()->where(['purchasereq_id' => $id])->one();
                $table_name = PurchaseRequisitionDetail::tableName();
                $primary_col = 'purchasereqdetail_id';
            } else {
                $model = PurchaseRequisitionBarang::find()->where(['purchasereqbrg_id' => $id])->one();
                $table_name = PurchaseRequisitionBarangDetail::tableName();
                $primary_col = 'purchasereqbrgdetail_id';
            }

            $model->status = DocoConstants::VAR_BELUM_PO;
            $model->tgl_approve = date('Y-m-d H:i:s');
            $model->peg_approve_id = $approved_by;
            $model->save();

            $model = (new BatchUpdate($table_name, function($query) use ($list, $primary_col) {
                $listKey = [];
                foreach ($list as $value) {
                    $primaryKey = $value['detail_id'];
                    $query->set([
                        'qty_input' => $value['qty_final'],
                        'qty_konversi' => $value['qty_final'],
                        'status' => DocoConstants::VAR_BELUM_PO
                    ], "{$primary_col} = {$primaryKey}", $primaryKey);
                    array_push($listKey, $primaryKey);
                }
                if(count($listKey) > 0){
                    $id = implode(", ", $listKey);
                    $query->where("{$primary_col} in({$id}) AND status = ".DocoConstants::VAR_BELUM_APPROVED."");
                }
            }
            ))->execute();
            $transaction->commit();
            return $this->controller->responseJson(200, 'Data Purchase Request Berhasil di Approve.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->controller->responseJson(422, 'Terjadi kesalahan pada sistem.', [
                'status' => 422,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->controller->responseJson(500, 'Terjadi kesalahan pada database.');
        }
    }
}
