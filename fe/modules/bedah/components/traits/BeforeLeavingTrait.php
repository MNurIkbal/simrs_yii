<?php

namespace app\modules\bedah\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use app\modules\bedah\models\BeforeLeavingForm;
use app\modules\bedah\models\PreAnestesiForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

use function Complex\ln;

trait BeforeLeavingTrait
{
	public function actionBeforeLeaving($id)
	{
		$model = new BeforeLeavingForm();
		$model->pasienmasukpenunjang_id = DocoHelpers::decrypt($id);
		if ($post = Yii::$app->request->post()) {
			$model->load($post);
			if ($model->validate()) {
		        $body = $this->helper->guzzleExec($this->_restBedah, [
		            'url' => 'inf-pasien-anestesi/save-before-leaving-anestesi',
		            'method' => 'POST'
		        ], [
		        	'form_params' => $model->attributes
		        ]);
		        return DocoHelpers::response($body);
			} else {
                return DocoHelpers::response([
                    'response' => [
                        'data' => DocoHelpers::parseError($model->errors, 'BeforeLeavingForm')
                    ]
                ], 422);
			}
		}

        $body = $this->helper->guzzleExec($this->_restBedah, [
            'url' => 'inf-pasien-anestesi/before-leaving',
            'method' => 'GET'
        ], [
        	'query' => [
        		'id' => $model->pasienmasukpenunjang_id
        	]
        ]);

        $model->attributes = $body;
        return $this->renderAjax('partial/_beforeleaving', get_defined_vars());
	}

	public function actionModalBeforeCondition()
    {
        return $this->renderAjax('partial/modal/beforeleaving.php', get_defined_vars());
    }

}