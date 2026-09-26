<?php

namespace app\modules\v1\actions\LapRekapPurchaseOrderObat;

use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Supplier;

class GetSupplierAction extends Action {
    public function run() {
        try {
            $suppliers = Supplier::find()
                        ->where(['is_active' => true, 'is_deleted' => false])
                        ->select(['supplier_nama', 'supplier_id'])
                        ->all();

            return ArrayHelper::map($suppliers, 'supplier_id', 'supplier_nama');
        } catch (\yii\db\Exception $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}