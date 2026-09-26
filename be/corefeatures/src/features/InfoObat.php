<?php

namespace SirsCore\features;

use Yii;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use SirsCore\models\ObatAlkesFn;

class InfoObat
{
	/**
     * @param 
     * @return array
     * @desc 
     */
	public static function getAll()
	{
		$obatAlkes = new ObatAlkesFn;
		return $obatAlkes::find()->asArray()->all();
	}
}