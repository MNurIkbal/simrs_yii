<?php

/**
 * @author : iqbal.rukmana@sirs.co.id
 * Powered by Sirs
 */
	
namespace app\modules\rajal\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\rajal\models\AsesmenKeperawatan;

class ModelAsesmenKeperawatanProcess extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		$model = new AsesmenKeperawatan;
		return $model;
	}
}