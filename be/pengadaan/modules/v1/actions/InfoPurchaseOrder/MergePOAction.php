<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use app\modules\v1\entities\PurchaseOrder;
use Doco\Traits\ControllerHelperTrait;

class MergePOAction extends Action {
    use ControllerHelperTrait;

    public function run() {
        $request = Yii::$app->request;
        $po_ids = $request->post('list_po');
        $type_po = $request->post('type_po');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $po = (new PurchaseOrder)->setTipePO($type_po);
            $po->getInfoPO($po_ids);
            $po->merge();
            $transaction->commit();
            return $this->responseJson(200, 'Berhasil digabung ke PO nomor '. $po->getMergedNoPO(), []);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(422, ['message' => $e->getMessage(),'line' => $e->getLine(), 'file' => $e->getFile()], [], ['title' => 'Proses Gagal !']);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }
}
