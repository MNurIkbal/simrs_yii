<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use yii\web\Response;
use app\components\DocoHelpers;
use app\modules\master\models\PaketFisioForm;

class SavePaketAction extends BaseCurrentAction
{
    private function executeSave()
    {
        $request = Yii::$app->request;
        $model = new PaketFisioForm;
        $formName = 'PaketFisioForm';
        $model->attributes = Yii::$app->request->post('PaketFisioForm', []);
        $model->list_tindakan = json_decode($model->list_tindakan, true);
        if (!$model->validate()) {
            $response = $model->errors;
            $errors = DocoHelpers::parseError($response, $formName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }
        $jsonForm = $model->attributes;
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->master, [
            'url' => 'paket-fisio/save-data',
            'method' => 'post',
            'payload' => [
                'query' => [],
                'form_params' => $jsonForm
            ],
            'with_metadata' => true
        ]);
        return DocoHelpers::response($response, false);
    }

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $sessionId = Yii::$app->docoVars->user("id");
        if ($request->method == 'POST') {
            return $this->executeSave();
        }
        return $this->controller->responseJson(400, 'Must be post method');
    }
}
