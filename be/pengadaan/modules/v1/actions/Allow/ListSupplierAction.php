<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * this list supplier return with pajak_id
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Supplier;

class ListSupplierAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $payload = $request->get();
        if (isset($payload['page']) && !empty($payload['page']) && is_int((int) $payload['page'])) {
            $result = $this->getSupplier();
            if (isset($payload['term']) && !empty($payload['page'])) {
                $result->andWhere(['ILIKE', 'LOWER(supplier_nama)', strtolower($payload['term'])]);
            }
            return $result->offset(($payload['page'] - 1) * 10)->limit(11)->asArray()->all();
        } else if (!empty($payload['term'])) {
            $result = $this->getSupplier();
            $result->andWhere(['ILIKE', 'LOWER(supplier_nama)', strtolower($payload['term'])]);
            return $result->limit(10)->asArray()->all();
        } else {
            return [];
        }
    }

    private function getSupplier()
    {
        $model = Supplier::find()
            ->select([
                'supplier_id',
                'supplier_nama',
                'pajak_id'
            ])
            ->where(['is_active' => true]);

        return $model;
    }
}
