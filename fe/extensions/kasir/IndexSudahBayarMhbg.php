<?php

namespace app\extensions\kasir;

use Yii;
use app\components\DocoBaseProcessExtension;

class IndexSudahBayarMhbg extends DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
        $path = 'index_mhbg';
        return $path;
	}
}