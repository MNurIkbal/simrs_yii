<?php
namespace app\modules\v1\actions\InfKartuStok;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\components\ApotekComponent;
use app\modules\v1\models\KartuStokObatFn;
use app\modules\v1\models\ObatAlkes;

class RecalculateAction extends Action
{
    public function run()
    {
        try {
        	$model = $this->controller->getData();
    		return new ActiveDataProvider(['query' => $model::find()]);
    	} catch (\Exception $e){
    		$this->controller->logError($e);
    		return [
    				'message' => $e->getMessage(),
            		'data' => [],
            		'_meta' => ['totalCount'=>0]
            	];
        } catch (\yii\db\Exception $e) {
        	$this->controller->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}