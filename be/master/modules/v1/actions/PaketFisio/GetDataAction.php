<?php

namespace app\modules\v1\actions\PaketFisio;

use Doco\components\DocoHelpers;
use yii\db\Exception as DBException;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DaftarPaketFisioV;

class GetDataAction extends BaseCurrentAction
{
    public function run()
    {
        $helpers = new DocoHelpers;
        try {
            $model = new DaftarPaketFisioV;
            $query = $model::find()->asArray();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return $this->controller->activeDataProvider($query);
        } catch (DBException $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        } catch (\Exception $e) {
            $helpers->logError($e);
            return $helpers->response($e->getMessage(), 500);
        }
    }
}
