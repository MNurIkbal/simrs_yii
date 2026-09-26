<?php

/**
 * @author : Budi (budi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\radiologi;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class FormExpertise extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
        $path = '@app/extensions/radiologi/views/index';
        return $path;
	}
}