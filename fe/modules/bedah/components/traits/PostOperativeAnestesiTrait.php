<?php

namespace app\modules\bedah\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

use app\modules\bedah\models\PostOperativeAnestesiForm;

trait PostOperativeAnestesiTrait
{
	public function actionPostOperative()
	{
		$id = Yii::$app->request->get('id');
		$model = new PostOperativeAnestesiForm();
		$model->pasienmasukpenunjang_id = DocoHelpers::decrypt($id);
		if ($post = Yii::$app->request->post()) {
			$model->load($post);
			if ($model->validate()) {
		        $body = $this->helper->guzzleExec($this->_restBedah, [
		            'url' => 'inf-pasien-anestesi/save-post-operative',
		            'method' => 'POST'
		        ], [
		        	'form_params' => $model->attributes
		        ]);
		        return DocoHelpers::response($body);
			} else {
                return DocoHelpers::response([
                    'response' => [
                        'data' => DocoHelpers::parseError($model->errors, 'PostOperativeAnestesiForm')
                    ]
                ], 422);
			}
		}

        $lookup = Yii::$app->cache->getOrSet('lookup-post-operative-anestesi', function() {
        	return $this->helper->guzzleExec($this->_restBedah, [
	            'url' => 'allow/lookup-post-operative-anestesi',
	            'method' => 'GET'
	        ]);
        });
        $body = $this->helper->guzzleExec($this->_restBedah, [
            'url' => 'inf-pasien-anestesi/post-operative',
            'method' => 'GET'
        ], [
        	'query' => [
        		'id' => $model->pasienmasukpenunjang_id
        	]
        ]);
        $model->attributes = $body;
        $aldreteScoreSelected = ArrayHelper::index($model->aldretescores, 'score_id');
        return $this->renderAjax('partial/_postoperative', get_defined_vars());
	}

    public function actionModalPostOperativeDrugsupport()
    {
        return $this->renderAjax('partial/modal/postoperative-drugsupport.php', get_defined_vars());
    }
}