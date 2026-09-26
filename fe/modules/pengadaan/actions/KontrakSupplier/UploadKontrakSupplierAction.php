<?php

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
class UploadKontrakSupplierAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        $payload = Yii::$app->cache->get("upload-kontrak-supplier-".$user_login);
        return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'kontrak-supplier/upload-kontrak-supplier',
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'payload' => json_encode($payload, true)
                ],
            ],
            'returnResponse' => true
        ]);
    }
}
