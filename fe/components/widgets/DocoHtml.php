<?php

namespace app\components\widgets;

use Yii;
use yii\bootstrap\Html;

class DocoHtml extends Html
{
	public static function activeStaticDate($model, $attribute, $options = [])
    {
        if (isset($options['value'])) {
            $value = $options['value'];
            unset($options['value']);
        } else {
            $value = parent::getAttributeValue($model, $attribute);
        }
        $value = date('d F Y', strtotime($value));
        return parent::staticControl($value, $options);
    }
}