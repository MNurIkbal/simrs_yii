<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * Powered by Sirs (PT Citra Raya Nusatama)
 */

namespace app\modules\v1\actions\LapPenerimaanObatAlkes;

use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Supplier;

class SupplierAction extends Action {
    public function run() {
        try {
            $supplier = Supplier::find()
                ->select('supplier_nama')
                ->where(['is_active' => true, 'is_deleted' => false])
                ->all();

            return [
                'supplier' => ArrayHelper::map($supplier, 'supplier_nama', 'supplier_nama'),
            ];
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }
}
