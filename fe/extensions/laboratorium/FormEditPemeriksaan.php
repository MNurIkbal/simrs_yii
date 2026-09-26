<?php

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace app\extensions\laboratorium;

use Yii;

class FormEditPemeriksaan extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
        $path = '@app/extensions/laboratorium/views/index';
        return $path;
	}
}