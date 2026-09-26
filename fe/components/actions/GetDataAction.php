<?php

namespace app\components\actions;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ViewAction;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;

class GetDataAction extends ViewAction
{
	public $serviceName = null;
	public $serviceAction = null;
    public $keyField = null;
    public $data_name = [];

	public function run()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $api = $this->serviceAction;
        $data_id = $this->keyField;
        $data_name = $this->data_name;
        $getRest = $this->serviceName;
        $params = !empty($get) ? $get : [];
        $params['term'] = $request->get('q');
        
        return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, $params);
	}
	
}