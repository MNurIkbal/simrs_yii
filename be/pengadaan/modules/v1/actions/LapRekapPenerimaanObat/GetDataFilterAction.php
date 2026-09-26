<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapRekapPenerimaanObat;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use app\components\DocoHelpers;
use app\modules\v1\models\Payterm;
use app\modules\v1\models\Supplier;

class GetDataFilterAction extends Action {
    public function run() {
        try {
            $supplier = Supplier::find()
            ->where(['is_active' => true])
            ->select([
                'supplier_id',
                'supplier_nama'
            ])->all();

            $payterm = Payterm::find()
            ->where(['is_active' => true])
            ->select([
                'payterm_id',
                'payterm_nama'
            ])->all();

            return [
                'supplier' => $supplier,
                'payterm' => $payterm
            ];

        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }
}
