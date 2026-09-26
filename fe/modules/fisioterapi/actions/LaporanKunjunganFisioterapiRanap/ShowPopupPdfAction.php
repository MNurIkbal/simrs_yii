<?php

namespace Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap;

use app\components\DocoConstants;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use yii\base\DynamicModel;

class ShowPopupPdfAction extends BaseCurrentAction
{
    public function run()
    {
        $model = new DynamicModel(['id']);
        $request = Yii::$app->request;
        $params = $request->get();
        $type = $request->get('type', null);
        $randString = DocoHelpers::generateRandomString();
        $payload = DocoDatatableHelper::convertToRestfulParams($params);
        $payload['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $payload);
        return $this->controller->renderAjax('partials/export_pdf', compact('model', 'randString'));
    }
}
