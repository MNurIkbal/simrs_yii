<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\extensions\kasir;

use Yii;

class LabelSimpanKramat extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
      return Yii::t('fe', 'Bayar');
	}
}