<?php

namespace app\modules\master\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\master\models\GolonganUmurForm;

trait GolonganUmurTrait 
{
	public function actionGolUmur()
	{
		$gol = new GolonganUmurForm;
		$status = [1 => 'Aktif', 0 => 'Tidak Aktif'];
		return $this->renderPartial('gol-umur/index',get_defined_vars());
	}
}