<?php

namespace app\modules\v1\controllers;

use Yii;

class ErrorLogController extends \Doco\components\DocoActiveController
{

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionLogActivity()
    {
        $module = Yii::$app->request->get('module', null);
        $pathFile = Yii::$app->basePath . ($module == 'frontend' ? '/../../' : '/../') . $module . '/runtime/logs/app.log';
        if (empty($module) || (!empty($module) && !file_exists($pathFile))) {
            return [
                'logsArray' => []
            ];
        }
        $allLines = file($pathFile);
        $resultArray = array_slice($allLines, -1000);
        $logsArray = [];
        foreach ($resultArray as $lineArray) {
            if (strpos($lineArray, '[server-error]')) {
                $itemError = [];
                $explodeResult = explode("[server-error]", $lineArray);
                $date = explode(" [", $explodeResult[0])[0];
                $errorInformation = explode("--||--", $explodeResult[1]);
                if (count($errorInformation) == 6) {
                    $itemError = [
                        'date' => $date,
                        'message' => trim(explode(" : ", $errorInformation[0])[1]),
                        'line' => trim(explode(" : ", $errorInformation[1])[1]),
                        'file' => trim(explode(" : ", $errorInformation[2])[1]),
                        'url' => '/' . $module . trim(explode(" : ", $errorInformation[3])[1]),
                        'method' => explode(" : ", $errorInformation[4])[1],
                        'payload' => json_decode(explode(" : ", $errorInformation[5])[1]),
                    ];
                }
                if (!empty($itemError)) {
                    array_push($logsArray, $itemError);
                }
            }
        }
        $logsArray = array_reverse($logsArray);
        return compact('logsArray');
    }
}