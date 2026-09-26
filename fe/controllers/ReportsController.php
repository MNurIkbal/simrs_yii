<?php

namespace app\controllers;

use Yii;
use app\components\DocoController;

class ReportsController extends DocoController
{
    public $layout = false;
    public $enableCsrfValidation = false;

    /**
     * Backend link
     */
    public function actionViewer()
    {
        $default = ['modules','controller','action'];
        $get = Yii::$app->request->get();
        $query_param = array_diff_key($get,array_flip($default));
        if(empty($query_param)){
            $query_param = [];
        }

        $json_query_param = json_encode($query_param);

        $modules =Yii::$app->request->get('modules');
        $controller =Yii::$app->request->get('controller');
        $action =Yii::$app->request->get('action');

        $link = $modules.'/'.$controller.'/'.$action.'?'.http_build_query($query_param);
        return $this->renderContent(Yii::$app->report->embedViewer($link));
    }

    public function actionByCode()
    {
        $default = ['kode'];
        $get = Yii::$app->request->get();
        $query_param = array_diff_key($get,array_flip($default));
        if(empty($query_param)){
            $query_param = [];
        }

        $json_query_param = json_encode($query_param);

        $kode = Yii::$app->request->get('kode');
        
        $link = $kode.'?'.http_build_query($query_param);
        return $this->renderContent(Yii::$app->report->embedViewer($link));
    }

    public function actionDesigner()
    {
        $request = Yii::$app->request;
        $is_edit = 0;
        $json = '{}';

        $kode = Yii::$app->request->get('kode');
        return $this->renderContent(Yii::$app->report->embedDesigner($kode));
    }
}