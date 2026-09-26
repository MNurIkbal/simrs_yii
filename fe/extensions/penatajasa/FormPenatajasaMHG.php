<?php

/**
 * @author : Ded Herdiana (dede.herdiana@sirs.co.id)
 * Powered by Sirs
 */

namespace app\extensions\penatajasa;

use Yii;

class FormPenatajasaMHG extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
        $path = '@app/extensions/penatajasa/views/_form_mhg';
        return $path;
	}
}