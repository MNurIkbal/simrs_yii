<?php

namespace app\modules\v1\components\accounting;

use Yii;

class BaseModel
{
    protected $db;
    
    public function __construct()
    {
        $this->db = !empty(Yii::$app->dbslave->username) ? Yii::$app->dbslave : Yii::$app->db;
    }
}