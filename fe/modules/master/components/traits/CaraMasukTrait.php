<?php

namespace app\modules\master\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\master\models\CaraMasukForm;

trait CaraMasukTrait 
{
	public function actionCaraMasuk()
	{
		$gol = new CaraMasukForm;
		$status = ['t' => 'Aktif', 'f' => 'Tidak Aktif'];
		return $this->renderPartial('cara-masuk/index',get_defined_vars());
	}
}