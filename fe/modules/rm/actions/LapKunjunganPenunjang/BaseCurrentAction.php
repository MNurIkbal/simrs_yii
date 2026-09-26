<?php

namespace Doco\rm\actions\LapKunjunganPenunjang;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoDatatableHelper;
use app\components\Traits\ControllerHelperTrait;

class BaseCurrentAction extends Action
{
    use ControllerHelperTrait;

    protected function getParamsFiltered()
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $advancedFilter = ArrayHelper::getValue($filter, 'advanced-filter');
        if (empty($filter['advanced-filter'])) {
            unset($filter['advanced-filter']);
        }
        return $filter;
    }
}

?>