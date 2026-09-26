<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\widgets;

use Yii;
use yii\base\InvalidConfigException;

class DocoActiveForm extends \yii\bootstrap\ActiveForm
{
    public $fieldClass = 'app\components\widgets\DocoActiveField';

    public $options = [];

    public $layout = 'default';
}
