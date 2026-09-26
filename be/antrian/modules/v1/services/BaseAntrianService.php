<?php

namespace app\modules\v1\services;

use Yii;
use yii\helpers\Json;

class BaseAntrianService
{
    protected function publish($data)
    {
        $mode = Yii::$app->params['mode'];
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'display-antrian-' . $mode,
            'message' => Json::encode(['data' => $data])
        ]);
    }
}