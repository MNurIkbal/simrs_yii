<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\Services\ExportExcelEngine;

class ExportExcelEngineController extends \Doco\components\DocoActiveController
{
    protected $key;
    protected $status;
    protected $message;
    protected $percentage;

    public $modelClass = '';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionUpdateStatus()
    {
        try {
            $this->paramValidation();

            $exportEngine = new ExportExcelEngine();
            $update = $exportEngine->progressReceiver($this->key, $this->status, $this->message, $this->percentage);
            return ['message' => 'Status updated successfully'];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 400;
            return ['message' => $e->getMessage()];
        }
    }

    private function paramValidation()
    {
        $request = Yii::$app->request;
        
        $this->key = $request->get('key', null);
        $this->status = $request->get('status', null);
        $this->message = $request->get('message', null);
        $this->percentage = $request->get('percentage', null);

        if(is_null($this->key) || $this->key === '') {
            throw new \Exception("Key must be set, Please check your param", 1);
        }

        if(is_null($this->status) || $this->status === '') {
            throw new \Exception("Status must be set, Please check your param", 1);
        }

        if(is_null($this->message) || $this->message === '') {
            throw new \Exception("Message must be set, Please check your param", 1);
        }

        if(is_null($this->percentage) || $this->percentage === '') {
            throw new \Exception("Percentage must be set, Please check your param", 1);
        }
    }
}