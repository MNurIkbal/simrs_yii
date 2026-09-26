<?php
namespace app\modules\v1\components;

use Yii;

Class IdentifyUser 
{
    public static function getIdentity()
    {
        return Yii::$app->jwt;
    }
}