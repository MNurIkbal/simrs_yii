<?php

namespace app\modules\master\components\actions;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ViewAction;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;

class DeleteModalAction extends ViewAction
{
	public $serviceName = null;
	public $serviceDeleteAction = null;
    public $serviceMethod = 'DELETE';

	public function run($id)
	{
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->serviceName->request($this->serviceMethod, $this->serviceDeleteAction,[
                            'query' => ['id' => $id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
	}
}