<?php

namespace Doco\rm\actions\LapKunjunganPenunjang;

use Yii;
use yii\base\DynamicModel;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

class ShowPopupExcelAction extends BaseCurrentAction
{
    public function run()
    {
        $model = new DynamicModel(['id']);
        $request = Yii::$app->request;
        $params = $request->get();
        $type = $request->get('type', null);
        $randString = DocoHelpers::generateRandomString();
        $payload = $this->getParamsFiltered();
        $payload['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $payload);
        return $this->controller->renderAjax('partials/export_excel', compact('model', 'randString'));
    }
}
