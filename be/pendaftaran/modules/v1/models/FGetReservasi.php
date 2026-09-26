<?php

namespace app\modules\v1\models;

use Yii;

class FGetReservasi extends \Doco\components\DocoPostgreFunctionAR
{
    public static function getDb() 
    {
        return !empty(Yii::$app->dbslave->username) ? Yii::$app->dbslave : Yii::$app->db;
    }
    
    public static function functionName()
    {
        return "get_reservasi";
    }

    public static function attributSchema()
    {
        return [];
    }
}
