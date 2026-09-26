<?php

namespace Doco\components;

use Yii;
use Doco\components\DocoHelpers;

class DocoPublicAccessRule extends \yii\filters\AccessControl
{
    public function beforeAction($action)
    {
        // relate to DocoPublicAccessRule in EMR - RSPAD
        return true;
    }
}