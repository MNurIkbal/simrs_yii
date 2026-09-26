<?php

namespace app\modules\bedah\components\traits;

use Yii;
use DateTime;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

use app\modules\bedah\models\IntraOperativeAnestesiForm;

trait IntraOperativeAnestesiTrait
{
	public function actionIntraOperative()
	{
		$id = Yii::$app->request->get('id');
		$model = new IntraOperativeAnestesiForm();
		$model->pasienmasukpenunjang_id = DocoHelpers::decrypt($id);
		$model->start_induction = date('H:00:00');
		$model->end_induction = date('H:00:00', strtotime('+ 1 HOUR'));

		$model->length_anesthesia = 60;
		$model->length_surgery = 0;

		if ($post = Yii::$app->request->post()) {
			$model->load($post);
			if ($model->validate()) {
		        $body = $this->helper->guzzleExec($this->_restBedah, [
		            'url' => 'inf-pasien-anestesi/save-intra-operative',
		            'method' => 'POST'
		        ], [
		        	'form_params' => $model->attributes
		        ]);
		        return DocoHelpers::response($body);
			} else {
                return DocoHelpers::response([
                    'response' => [
                        'data' => DocoHelpers::parseError($model->errors, 'IntraOperativeAnestesiForm')
                    ]
                ], 422);
			}
	        return DocoHelpers::response([
                'message' => 'Data Berhasil di simpan',
                'status' => 200,
                'statusCode' => 200,
                'item' => $model->attributes
            ]);
		}

        $body = $this->helper->guzzleExec($this->_restBedah, [
            'url' => 'inf-pasien-anestesi/intra-operative',
            'method' => 'GET'
        ], [
        	'query' => [
        		'id' => $model->pasienmasukpenunjang_id
        	]
        ]);
        $model->attributes = $body;
		$model->length_surgery = intval($model->length_surgery);

        $lookup = Yii::$app->cache->getOrSet('lookup-intra-operative-anestesi', function() {
        	return $this->helper->guzzleExec($this->_restBedah, [
	            'url' => 'allow/lookup-intra-operative-anestesi',
	            'method' => 'GET'
	        ]);
        });
        $lookup['vitalSign'] = $this->getVitalSign();
		$mincol = 10;
		$startColumnDateTime = date('Y-m-d H:i:00', strtotime($model->start_induction));
		$generatedTableColumn = floor($model->length_anesthesia / 5);
		$vitalSignValues = ArrayHelper::index($model->vital_sign, function($el) {
			return date('H:i', strtotime($el['time']));
		});
		$monitoringValues = ArrayHelper::index($model->other_monitoring, 'monitoring_id', [
			function($el) {
				return date('H:i', strtotime($el['time']));
			}
		]);
        return $this->renderAjax('partial/_intraoperative', get_defined_vars());
	}

    public function actionModalIntraOperative()
    {
        $dataModal = [
            'title' => 'Detail Intraoperativ'
        ];
		$attr = [
			'vitalSign' => $this->getVitalSign()
		];
		$time = Yii::$app->request->get('time');
		$hr = Yii::$app->request->get('hr');
		$rr = Yii::$app->request->get('rr');
		$systolic = Yii::$app->request->get('systolic');
		$diastolic = Yii::$app->request->get('diastolic');

		$ddhr = ArrayHelper::map($attr['vitalSign'], 'hr', 'hr');
		$ddrr = ArrayHelper::map($attr['vitalSign'], 'rr', 'rr');
		$ddbp = ArrayHelper::map($attr['vitalSign'], 'bp', 'bp');

        return $this->renderAjax('partial/modal/intraoperative.php', get_defined_vars());
    }

    private function getVitalSign()
    {
		$vitalSign = [];
		$rrmax = 50;
		$hrmax = 220;
		$bpmax = 220;
		$inc = 0;
		for ($i = 10; $i >= 0; $i--) {
			$inc++;
			$vitalSign[] = ['id' => $inc, 'rr' => $rrmax, 'hr' => $hrmax, 'bp' => $bpmax];
			$rrmax -= 5;
			$hrmax -= 20;
			$bpmax -= 20;
		}
		return $vitalSign;
    }
}