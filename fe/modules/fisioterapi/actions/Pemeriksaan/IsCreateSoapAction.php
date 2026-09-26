<?php

namespace Doco\fisioterapi\actions\Pemeriksaan;

use Yii;
use \yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

class IsCreateSoapAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request->get();
        $id = ArrayHelper::getValue($request, 'id');
        $status = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'GET',
            'url' => 'soap/status-by-pendaftaran'
        ], [
            'query' => [
                'id' => DocoHelpers::decrypt($id)
            ]
        ]);
        $allowed = $status['is_pulang'] == TRUE ? 0 : ($status['is_create_soap'] == TRUE ? 0 : 1);
        return ['allowed' => $allowed];
    }
}
